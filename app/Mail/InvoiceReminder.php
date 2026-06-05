<?php

namespace App\Mail;

use App\Model\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceReminder extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Invoice $invoice;
    public string $reminderType;

    public function __construct(Invoice $invoice, string $reminderType = 'reminder')
    {
        $this->invoice = $invoice;
        $this->reminderType = $reminderType; // 'reminder' or 'overdue'
    }

    public function build()
    {
        $this->invoice->load(['agency']);

        $subject = $this->reminderType === 'overdue'
            ? "OVERDUE: Invoice {$this->invoice->invoice_number} - Immediate Payment Required"
            : "Payment Reminder: Invoice {$this->invoice->invoice_number} Due Soon";

        return $this->subject($subject)
                    ->view('emails.invoice-reminder')
                    ->with([
                        'invoice' => $this->invoice,
                        'reminderType' => $this->reminderType,
                        'company' => $this->getCompanyData(),
                        'paymentUrl' => route('agency.invoices.payment', $this->invoice),
                        'invoiceUrl' => route('agency.invoices.show', $this->invoice),
                        'daysOverdue' => $this->reminderType === 'overdue' ? now()->diffInDays($this->invoice->due_date) : 0,
                        'daysTillDue' => $this->reminderType === 'reminder' ? $this->invoice->due_date->diffInDays(now()) : 0,
                    ]);
    }

    protected function getCompanyData(): array
    {
        return [
            'name' => env('COMPANY_NAME', 'NYBEST ERP'),
            'address' => env('COMPANY_ADDRESS', '123 Business St, Suite 100'),
            'city' => env('COMPANY_CITY', 'New York'),
            'state' => env('COMPANY_STATE', 'NY'),
            'zip' => env('COMPANY_ZIP', '10001'),
            'phone' => env('COMPANY_PHONE', '(555) 123-4567'),
            'email' => env('COMPANY_EMAIL', 'billing@nybesterp.com'),
            'website' => env('COMPANY_WEBSITE', 'www.nybesterp.com'),
        ];
    }
}