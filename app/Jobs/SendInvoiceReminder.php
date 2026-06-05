<?php

namespace App\Jobs;

use App\Model\Invoice;
use App\Model\InvoiceNotification;
use App\Mail\InvoiceReminder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendInvoiceReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Invoice $invoice;
    public string $reminderType;
    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(Invoice $invoice, string $reminderType = 'reminder')
    {
        $this->invoice = $invoice;
        $this->reminderType = $reminderType; // 'reminder' or 'overdue'
    }

    public function handle()
    {
        try {
            // Only send if invoice is still unpaid
            if ($this->invoice->status === 'paid') {
                Log::info('Skipping reminder for paid invoice', [
                    'invoice_id' => $this->invoice->id,
                    'invoice_number' => $this->invoice->invoice_number,
                ]);
                return;
            }

            // Send reminder email
            Mail::to($this->invoice->agency->email)
                ->send(new InvoiceReminder($this->invoice, $this->reminderType));

            // Log the notification
            if ($this->reminderType === 'overdue') {
                InvoiceNotification::logOverdue($this->invoice, $this->invoice->agency->email);

                // Update invoice status to overdue
                $this->invoice->update(['status' => 'overdue']);
            } else {
                InvoiceNotification::logReminder($this->invoice, $this->invoice->agency->email);
            }

            Log::info('Invoice reminder sent successfully', [
                'invoice_id' => $this->invoice->id,
                'invoice_number' => $this->invoice->invoice_number,
                'reminder_type' => $this->reminderType,
                'email' => $this->invoice->agency->email,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send invoice reminder', [
                'invoice_id' => $this->invoice->id,
                'invoice_number' => $this->invoice->invoice_number,
                'reminder_type' => $this->reminderType,
                'email' => $this->invoice->agency->email,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error('Invoice reminder job failed permanently', [
            'invoice_id' => $this->invoice->id,
            'invoice_number' => $this->invoice->invoice_number,
            'reminder_type' => $this->reminderType,
            'email' => $this->invoice->agency->email,
            'error' => $exception->getMessage(),
        ]);
    }
}