<?php

namespace App\Mail;

use App\Model\Invoice;
use App\Model\InvoicePayment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentReceived extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Invoice $invoice;
    public InvoicePayment $payment;

    public function __construct(Invoice $invoice, InvoicePayment $payment)
    {
        $this->invoice = $invoice;
        $this->payment = $payment;
    }

    public function build()
    {
        $this->invoice->load(['agency']);

        return $this->subject("Payment Received - Invoice {$this->invoice->invoice_number}")
                    ->view('emails.payment-received')
                    ->with([
                        'invoice' => $this->invoice,
                        'payment' => $this->payment,
                        'company' => $this->getCompanyData(),
                        'receiptUrl' => route('agency.invoices.download-receipt', $this->payment),
                        'invoiceUrl' => route('agency.invoices.show', $this->invoice),
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