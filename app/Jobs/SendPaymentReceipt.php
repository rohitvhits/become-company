<?php

namespace App\Jobs;

use App\Model\Invoice;
use App\Model\InvoicePayment;
use App\Mail\PaymentReceived;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendPaymentReceipt implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Invoice $invoice;
    public InvoicePayment $payment;
    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(Invoice $invoice, InvoicePayment $payment)
    {
        $this->invoice = $invoice;
        $this->payment = $payment;
    }

    public function handle()
    {
        try {
            // Send payment receipt to agency
            Mail::to($this->invoice->agency->email)
                ->send(new PaymentReceived($this->invoice, $this->payment));

            // Also send notification to admin if configured
            $adminEmail = config('invoice.admin_notification_email');
            if ($adminEmail) {
                Mail::to($adminEmail)
                    ->send(new PaymentReceived($this->invoice, $this->payment));
            }

            Log::info('Payment receipt sent successfully', [
                'invoice_id' => $this->invoice->id,
                'payment_id' => $this->payment->id,
                'amount' => $this->payment->amount,
                'email' => $this->invoice->agency->email,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send payment receipt', [
                'invoice_id' => $this->invoice->id,
                'payment_id' => $this->payment->id,
                'email' => $this->invoice->agency->email,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error('Payment receipt job failed permanently', [
            'invoice_id' => $this->invoice->id,
            'payment_id' => $this->payment->id,
            'email' => $this->invoice->agency->email,
            'error' => $exception->getMessage(),
        ]);
    }
}