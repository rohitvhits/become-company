<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Model\Invoice;
use App\Model\InvoicePayment;
use App\Services\PaymentService;
use App\Services\LogsService;
use App\Helpers\Utility;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
        $this->middleware('auth');
        $this->middleware('agency');
    }

    public function process(Request $request, Invoice $invoice): JsonResponse
    {
        // Ensure the invoice belongs to the current agency
        $agencyIds = Utility::getUserWiseAgency();
        $authorized = in_array($invoice->agency_id, $agencyIds) || $invoice->agency_id === Auth::user()->agency_fk;
        if (!$authorized) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to invoice.',
            ], 403);
        }

        // Validate invoice status
        if ($invoice->status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'This invoice has already been paid.',
            ], 400);
        }

        if ($invoice->status === 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'This invoice is not yet ready for payment.',
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'payment_method' => 'required|in:stripe,paypal,valor',
            'amount' => 'nullable|numeric',
            'payment_intent_id' => 'required_if:payment_method,stripe|string',
            'paypal_order_id' => 'required_if:payment_method,paypal|string',
            'valor_token' => 'required_if:payment_method,valor|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid payment data.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Force full payment only - no partial payments allowed
            $amount = $invoice->balance;

            // Validate that if amount is provided, it must be the full balance
            if ($request->has('amount') && $request->amount != $invoice->balance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Partial payments are not allowed. You must pay the full invoice amount of $' . number_format($invoice->balance, 2),
                ], 400);
            }

            // Create payment record
            $payment = $invoice->payments()->create([
                'payment_method' => $request->payment_method,
                'amount' => $amount,
                'status' => 'pending',
                'agency_id' => $invoice->agency_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Process payment based on method
            switch ($request->payment_method) {
                case 'stripe':
                    $result = $this->paymentService->processStripePayment($payment, $request);
                    break;
                case 'paypal':
                    $result = $this->paymentService->processPaypalPayment($payment, $request);
                    break;
                case 'valor':
                    $result = $this->paymentService->processValorPayment($payment, $request);
                    break;
                default:
                    throw new \Exception('Unsupported payment method.');
            }

            if ($result['success']) {
                self::handleLogs('Payment Invoice',url("invoices/{$invoice->id}/pay"),'Agency Invoice',$invoice->id,$request->all(),NULL,'completed payment processing');
                return response()->json([
                    'success' => true,
                    'message' => 'Payment processed successfully!',
                    'payment_id' => $payment->id,
                    'redirect_url' => route('agency.invoices.show', $invoice),
                ]);
            } else {
                $payment->markAsFailed($result['error']);
                self::handleLogs('Payment Invoice',url("invoices/{$invoice->id}/pay"),'Agency Invoice',$invoice->id,$request->all(),NULL,'attempted a payment but it failed');
                return response()->json([
                    'success' => false,
                    'message' => $result['error'],
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('Payment processing failed', [
                'invoice_id' => $invoice->id,
                'agency_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            if (isset($payment)) {
                $payment->markAsFailed($e->getMessage());
            }

            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function createStripeIntent(Request $request, Invoice $invoice): JsonResponse
    {
        // Ensure the invoice belongs to the current agency
        $agencyIds = Utility::getUserWiseAgency();
        $authorized = in_array($invoice->agency_id, $agencyIds) || $invoice->agency_id === Auth::user()->agency_fk;
        if (!$authorized) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to invoice.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'amount' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid amount.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Force full payment only - no partial payments allowed
            $amount = $invoice->balance;

            // Validate that if amount is provided, it must be the full balance
            if ($request->has('amount') && $request->amount != $invoice->balance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Partial payments are not allowed. You must pay the full invoice amount of $' . number_format($invoice->balance, 2),
                ], 400);
            }

            $intent = $this->paymentService->createStripePaymentIntent($invoice, $amount);

            return response()->json([
                'success' => true,
                'client_secret' => $intent->client_secret,
                'amount' => $amount,
            ]);

        } catch (\Exception $e) {
            Log::error('Stripe intent creation failed', [
                'invoice_id' => $invoice->id,
                'agency_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment intent: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function webhook(Request $request, string $provider)
    {
        try {
            switch ($provider) {
                case 'stripe':
                    return $this->handleStripeWebhook($request);
                case 'paypal':
                    return $this->handlePaypalWebhook($request);
                default:
                    return response('Invalid provider', 400);
            }
        } catch (\Exception $e) {
            Log::error("Webhook handling failed for {$provider}", [
                'error' => $e->getMessage(),
                'payload' => $request->all(),
            ]);

            return response('Webhook handling failed', 500);
        }
    }

    protected function handleStripeWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret');

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Stripe webhook signature verification failed', ['error' => $e->getMessage()]);
            return response('Invalid signature', 400);
        }

        if ($event->type === 'payment_intent.succeeded') {
            $paymentIntent = $event->data->object;
            $this->paymentService->handleStripePaymentSuccess($paymentIntent);
        } elseif ($event->type === 'payment_intent.payment_failed') {
            $paymentIntent = $event->data->object;
            $this->paymentService->handleStripePaymentFailure($paymentIntent);
        }

        return response('Webhook handled', 200);
    }


    protected function handlePaypalWebhook(Request $request)
    {
        $payload = $request->all();

        // PayPal webhook verification would go here
        // For now, we'll assume the webhook is valid

        if ($payload['event_type'] === 'PAYMENT.CAPTURE.COMPLETED') {
            $this->paymentService->handlePaypalPaymentSuccess($payload['resource']);
        } elseif ($payload['event_type'] === 'PAYMENT.CAPTURE.DENIED') {
            $this->paymentService->handlePaypalPaymentFailure($payload['resource']);
        }

        return response('Webhook handled', 200);
    }

    public function status(InvoicePayment $payment): JsonResponse
    {
        // Ensure the payment belongs to an invoice of the current agency
        $agencyIds = Utility::getUserWiseAgency();
        $authorized = in_array($payment->invoice->agency_id, $agencyIds) || $payment->invoice->agency_id === Auth::user()->agency_fk;
        if (!$authorized) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to payment.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'payment' => [
                'id' => $payment->id,
                'status' => $payment->status,
                'amount' => $payment->amount,
                'payment_method' => $payment->payment_method,
                'transaction_id' => $payment->transaction_id,
                'paid_at' => $payment->paid_at?->format('Y-m-d H:i:s'),
                'invoice' => [
                    'id' => $payment->invoice->id,
                    'invoice_number' => $payment->invoice->invoice_number,
                    'status' => $payment->invoice->status,
                    'balance' => $payment->invoice->balance,
                ],
            ],
        ]);
    }

    public function handleLogs($type,$link,$module,$object_id,$new_response,$old_response,$message){
        $user = auth()->user();
        $uFname = $user->first_name??'';
        $uLname = $user->last_name??'';
        $ipaddress = Utility::getIP();
        $insertLog = [
            'type' => $type,
            'link' => $link,
            'module' => $module,
            'object_id' => $object_id,
            'message' => $uFname . ' ' . $uLname . ' '.$message,
            'new_response' => serialize($new_response),
            'old_response' => serialize($old_response),
            'ip' => $ipaddress,
        ];
        LogsService::save($insertLog);
    }
}