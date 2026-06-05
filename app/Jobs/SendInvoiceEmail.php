<?php

namespace App\Jobs;

use App\Model\Invoice;
use App\Model\InvoiceNotification;
use App\Mail\InvoiceSent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendInvoiceEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Invoice $invoice;
    public string $email;
    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(Invoice $invoice, string $email)
    {
        $this->invoice = $invoice;
        $this->email = $email;
    }

    public function handle()
    {
        try {
            // Send the invoice email
            Mail::to($this->email)->send(new InvoiceSent($this->invoice));

            // Log successful email sending (notification already logged in service)
            Log::info('Invoice email sent successfully', [
                'invoice_id' => $this->invoice->id,
                'invoice_number' => $this->invoice->invoice_number,
                'email' => $this->email,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send invoice email', [
                'invoice_id' => $this->invoice->id,
                'invoice_number' => $this->invoice->invoice_number,
                'email' => $this->email,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error('Invoice email job failed permanently', [
            'invoice_id' => $this->invoice->id,
            'invoice_number' => $this->invoice->invoice_number,
            'email' => $this->email,
            'error' => $exception->getMessage(),
        ]);
    }
}