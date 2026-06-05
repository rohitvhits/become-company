<?php

namespace App\Mail;

use App\Model\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class InvoiceSent extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Invoice $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function build()
    {
        $this->invoice->load(['agency', 'creator']);

        $mail = $this->subject("Invoice {$this->invoice->invoice_number} from " . config('app.name'))
                     ->view('emails.invoice-sent')
                     ->with([
                         'invoice' => $this->invoice,
                         'company' => $this->getCompanyData(),
                         'paymentUrl' => route('agency.invoices.payment', $this->invoice),
                         'invoiceUrl' => route('agency.invoices.show', $this->invoice),
                     ]);

        // Attach PDF if exists
        if ($this->invoice->pdf_path && Storage::disk('public')->exists($this->invoice->pdf_path)) {
            $mail->attach(
                Storage::disk('public')->path($this->invoice->pdf_path),
                [
                    'as' => $this->invoice->invoice_number . '.pdf',
                    'mime' => 'application/pdf',
                ]
            );
        }

        return $mail;
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