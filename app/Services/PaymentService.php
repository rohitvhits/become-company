<?php

namespace App\Services;

use App\Model\Invoice;
use App\Model\InvoicePayment;
use App\Model\InvoiceNotification;
use App\Jobs\SendPaymentReceipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;
use Stripe\Exception\InvalidRequestException;
use Stripe\Exception\ApiErrorException;
use App\Agency;

class PaymentService
{
    protected ?StripeClient $stripe = null;

    public function __construct()
    {
        // Initialize Stripe
        $stripeSecret = config('services.stripe.secret');
        if ($stripeSecret) {
            try {
                $this->stripe = new StripeClient($stripeSecret);
            } catch (\Exception $e) {
                Log::error('Failed to initialize Stripe client', [
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    public function processStripePayment(InvoicePayment $payment, Request $request): array
    {
        if (!$this->stripe) {
            return ['success' => false, 'error' => 'Stripe payment gateway is not configured'];
        }

        try {
            $intent = $this->stripe->paymentIntents->retrieve($request->payment_intent_id);

            if ($intent->status === 'succeeded') {
                // Retrieve payment method details
                $paymentMethod = null;
                $cardDetails = [];

                if ($intent->payment_method) {
                    try {
                        $paymentMethod = $this->stripe->paymentMethods->retrieve($intent->payment_method);
                        if ($paymentMethod->card) {
                            $cardDetails = [
                                'card_brand' => $paymentMethod->card->brand,
                                'card_last4' => $paymentMethod->card->last4,
                                'card_exp_month' => $paymentMethod->card->exp_month,
                                'card_exp_year' => $paymentMethod->card->exp_year,
                                'card_funding' => $paymentMethod->card->funding,
                                'card_country' => $paymentMethod->card->country,
                            ];
                        }
                    } catch (\Exception $e) {
                        Log::warning('Failed to retrieve payment method details', [
                            'payment_method_id' => $intent->payment_method,
                            'error' => $e->getMessage()
                        ]);
                    }
                }

                $payment->update([
                    'transaction_id' => $intent->id,
                    'status' => 'completed',
                    'paid_at' => now(),
                    'payment_gateway_response' => array_merge([
                        'payment_intent_id' => $intent->id,
                        'payment_method_id' => $intent->payment_method,
                        'amount_received' => $intent->amount_received,
                        'currency' => $intent->currency,
                        'receipt_url' => $intent->charges->data[0]->receipt_url ?? null,
                    ], $cardDetails),
                ]);

                $this->handleSuccessfulPayment($payment);

                return ['success' => true];
            } else {
                return ['success' => false, 'error' => 'Payment was not successful'];
            }

        } catch (InvalidRequestException $e) {
            Log::error('Stripe invalid request', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);

            return ['success' => false, 'error' => 'Invalid payment request: ' . $e->getMessage()];
        } catch (ApiErrorException $e) {
            Log::error('Stripe API error', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
                'type' => get_class($e),
            ]);

            return ['success' => false, 'error' => 'Payment gateway error: ' . $e->getMessage()];
        } catch (\Exception $e) {
            Log::error('Stripe payment processing failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }


    public function processPaypalPayment(InvoicePayment $payment, Request $request): array
    {
        try {
            // PayPal payment processing would go here
            // This is a simplified implementation

            $payment->update([
                'transaction_id' => $request->paypal_order_id,
                'status' => 'completed',
                'paid_at' => now(),
                'payment_gateway_response' => [
                    'order_id' => $request->paypal_order_id,
                    'payer_id' => $request->paypal_payer_id ?? null,
                ],
            ]);

            $this->handleSuccessfulPayment($payment);

            return ['success' => true];

        } catch (\Exception $e) {
            Log::error('PayPal payment processing failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function processValorPayment(InvoicePayment $payment, Request $request): array
    {
        try {
            // Get Valor configuration
            $appId = config('services.valor.app_id');
            $appKey = config('services.valor.app_key');
            $epi = config('services.valor.epi');
            $testMode = config('services.valor.test_mode', true);

            if (!$appId || !$appKey || !$epi) {
                return ['success' => false, 'error' => 'Valor payment gateway is not configured'];
            }

            $invoice = $payment->invoice;
            $valorToken = $request->valor_token;

            // Prepare Valor API request
            $apiUrl = $testMode
                ? 'https://securelink-staging.valorpaytech.com:4430/api/transaction'
                : 'https://securelink.valorpaytech.com/api/transaction';

            $valorPayload = [
                'appid' => $appId,
                'appkey' => $appKey,
                'epi' => $epi,
                'txn_type' => 'sale',
                'amount' => number_format($payment->amount, 2, '.', ''),
                'token' => $valorToken,
                'invoice_number' => $invoice->invoice_number,
                'ref_no' => 'INV-' . $invoice->id . '-' . time(),
            ];

            // Make API call to Valor
            $ch = curl_init($apiUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($valorPayload));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Accept: application/json',
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200) {
                Log::error('Valor API request failed', [
                    'http_code' => $httpCode,
                    'response' => $response,
                ]);
                return ['success' => false, 'error' => 'Payment gateway communication error'];
            }

            $valorResponse = json_decode($response, true);

            if (isset($valorResponse['error_no']) && $valorResponse['error_no'] === 'S00') {
                // Payment successful
                $payment->update([
                    'transaction_id' => $valorResponse['txnid'] ?? null,
                    'status' => 'completed',
                    'paid_at' => now(),
                    'payment_gateway_response' => [
                        'transaction_id' => $valorResponse['txnid'] ?? null,
                        'approval_code' => $valorResponse['approval_code'] ?? null,
                        'card_type' => $valorResponse['card_type'] ?? null,
                        'card_last4' => $valorResponse['last_4'] ?? null,
                        'card_exp' => $valorResponse['card_exp'] ?? null,
                        'response_message' => $valorResponse['mesg'] ?? null,
                        'error_no' => $valorResponse['error_no'] ?? null,
                    ],
                ]);

                $this->handleSuccessfulPayment($payment);

                return ['success' => true];
            } else {
                $errorMessage = $valorResponse['mesg'] ?? $valorResponse['error_desc'] ?? 'Payment failed';
                $payment->markAsFailed($errorMessage);

                Log::warning('Valor payment declined', [
                    'payment_id' => $payment->id,
                    'error_no' => $valorResponse['error_no'] ?? null,
                    'message' => $errorMessage,
                ]);

                return ['success' => false, 'error' => $errorMessage];
            }

        } catch (\Exception $e) {
            Log::error('Valor payment processing failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function createStripePaymentIntent(Invoice $invoice, float $amount): \Stripe\PaymentIntent
    {
        if (!$this->stripe) {
            throw new \Exception('Stripe payment gateway is not configured');
        }
        $agency = Agency::getAllDetailsbyAgencyId($invoice->agency_id);
        if(isset($agency->stripe_cust_id) && !empty($agency->stripe_cust_id)){
            $stripe_customer_id = $agency->stripe_cust_id;
        }else{
            $cust_id = self::createCustomer($agency);
            $stripe_customer_id = $cust_id;
            $agency->update(['stripe_cust_id' => $stripe_customer_id]);
        }

        return $this->stripe->paymentIntents->create([
            'amount' => $amount * 100, // Convert to cents
            'currency' => 'usd',
            'automatic_payment_methods' => ['enabled' => true],
            'customer' => $stripe_customer_id,
            'metadata' => [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'agency_id' => $invoice->agency_id,
            ],
            'description' => "Payment for Invoice {$invoice->invoice_number}",
            'receipt_email' => $invoice->agency->email,
        ]);
    }

     public function createCustomer($details)
    {
        $customer = $this->stripe->customers->create([
            'name'  => $details->agency_name,
            'email' => $details->email,
            'metadata' => [
                'internal_id' => $details->id,
            ],
        ]);
        return $customer->id ?? 0;
    }

    public function handleStripePaymentSuccess($paymentIntent): void
    {
        $invoiceId = $paymentIntent->metadata->invoice_id ?? null;

        if (!$invoiceId) {
            Log::warning('Stripe payment success but no invoice ID in metadata', [
                'payment_intent_id' => $paymentIntent->id,
            ]);
            return;
        }

        $payment = InvoicePayment::where('transaction_id', $paymentIntent->id)->first();

        if (!$payment) {
            Log::warning('Stripe payment success but payment record not found', [
                'payment_intent_id' => $paymentIntent->id,
                'invoice_id' => $invoiceId,
            ]);
            return;
        }

        if ($payment->status !== 'completed') {
            $payment->update([
                'status' => 'completed',
                'paid_at' => now(),
                'payment_gateway_response' => array_merge(
                    $payment->payment_gateway_response ?? [],
                    ['webhook_processed_at' => now()->toISOString()]
                ),
            ]);

            $this->handleSuccessfulPayment($payment);
        }
    }

    public function handleStripePaymentFailure($paymentIntent): void
    {
        $payment = InvoicePayment::where('transaction_id', $paymentIntent->id)->first();

        if ($payment && $payment->status !== 'failed') {
            $payment->markAsFailed($paymentIntent->last_payment_error->message ?? 'Payment failed');
        }
    }


    public function handlePaypalPaymentSuccess($paypalPayment): void
    {
        $payment = InvoicePayment::where('transaction_id', $paypalPayment['id'])->first();

        if (!$payment) {
            Log::warning('PayPal payment success but payment record not found', [
                'payment_id' => $paypalPayment['id'],
            ]);
            return;
        }

        if ($payment->status !== 'completed') {
            $payment->update([
                'status' => 'completed',
                'paid_at' => now(),
                'payment_gateway_response' => array_merge(
                    $payment->payment_gateway_response ?? [],
                    [
                        'webhook_processed_at' => now()->toISOString(),
                        'paypal_payment' => $paypalPayment,
                    ]
                ),
            ]);

            $this->handleSuccessfulPayment($payment);
        }
    }

    public function handlePaypalPaymentFailure($paypalPayment): void
    {
        $payment = InvoicePayment::where('transaction_id', $paypalPayment['id'])->first();

        if ($payment && $payment->status !== 'failed') {
            $payment->markAsFailed('PayPal payment failed');
        }
    }


    protected function handleSuccessfulPayment(InvoicePayment $payment): void
    {
        $invoice = $payment->invoice;

        // Check if invoice is fully paid
        $totalPaid = $invoice->payments()->where('status', 'completed')->sum('amount');

        if ($totalPaid >= $invoice->total_amount) {
            $invoice->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);
        }

        // Log notification
        InvoiceNotification::logPaymentReceived($invoice, $invoice->agency->email);

        // Send payment receipt
        SendPaymentReceipt::dispatch($invoice, $payment);

        Log::info('Payment processed successfully', [
            'payment_id' => $payment->id,
            'invoice_id' => $invoice->id,
            'amount' => $payment->amount,
            'method' => $payment->payment_method,
        ]);
    }

    public function refundPayment(InvoicePayment $payment, float $amount = null): array
    {
        try {
            $refundAmount = $amount ?? $payment->amount;

            switch ($payment->payment_method) {
                case 'stripe':
                    return $this->refundStripePayment($payment, $refundAmount);
                case 'paypal':
                    return $this->refundPaypalPayment($payment, $refundAmount);
                default:
                    throw new \Exception('Unsupported payment method for refund');
            }

        } catch (\Exception $e) {
            Log::error('Payment refund failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    protected function refundStripePayment(InvoicePayment $payment, float $amount): array
    {
        $refund = $this->stripe->refunds->create([
            'payment_intent' => $payment->transaction_id,
            'amount' => $amount * 100, // Convert to cents
        ]);

        if ($refund->status === 'succeeded') {
            $payment->refund($amount);
            return ['success' => true, 'refund_id' => $refund->id];
        }

        return ['success' => false, 'error' => 'Refund failed'];
    }


    protected function refundPaypalPayment(InvoicePayment $payment, float $amount): array
    {
        // PayPal refund implementation would go here
        // This is a simplified implementation
        $payment->refund($amount);
        return ['success' => true, 'refund_id' => 'PAYPAL_REFUND_' . time()];
    }

    /**
     * Check if Stripe is properly configured and accessible
     */
    public function isStripeConfigured(): bool
    {
        return $this->stripe !== null &&
               config('services.stripe.key') !== null &&
               config('services.stripe.secret') !== null;
    }

    /**
     * Test Stripe connection
     */
    public function testStripeConnection(): array
    {
        if (!$this->isStripeConfigured()) {
            return [
                'success' => false,
                'message' => 'Stripe is not properly configured. Please check your environment variables.'
            ];
        }

        try {
            // Test connection by retrieving account information
            $account = $this->stripe->accounts->retrieve();
            return [
                'success' => true,
                'message' => 'Stripe connection successful',
                'account_id' => $account->id
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Stripe connection failed: ' . $e->getMessage()
            ];
        }
    }
}