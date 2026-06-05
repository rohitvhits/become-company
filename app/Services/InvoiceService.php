<?php

namespace App\Services;

use App\Model\Invoice;
use App\Model\InvoiceItem;
use App\Model\InvoiceNotification;
use App\Jobs\SendInvoiceEmail;
use App\Jobs\SendPaymentReceipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class InvoiceService
{
    protected InvoicePdfService $pdfService;

    public function __construct(InvoicePdfService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    public function createUploadedPdfInvoice(Request $request): Invoice
    {
        $pdfPath = null;

        if ($request->hasFile('pdf_file')) {
            $file = $request->file('pdf_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $pdfPath = $file->storeAs('invoices', $filename, 'public');
        }

        $invoice = Invoice::create([
            'invoice_number' => $request->invoice_number ?: $this->generateInvoiceNumber(),
            'agency_id' => $request->agency_id,
            'type' => 'uploaded_pdf',
            'status' => 'draft',
            'title' => $request->title,
            'description' => $request->description,
            'total_amount' => $request->pdf_total_amount ?? 0,
            'due_date' => $request->due_date,
            'terms_conditions' => $request->terms_conditions,
            'pdf_path' => $pdfPath,
            'created_by' => Auth::id(),
        ]);

        return $invoice;
    }

    public function createQuickInvoice(Request $request): Invoice
    {
        $subtotal = $request->quick_total_amount ?? 0;
        $taxPercentage = $request->tax_percentage ?? 0;
        $discountPercentage = $request->discount_percentage ?? 0;

        $taxAmount = ($subtotal * $taxPercentage) / 100;
        $discountAmount = ($subtotal * $discountPercentage) / 100;
        $totalAmount = $subtotal + $taxAmount - $discountAmount;

        $invoice = Invoice::create([
            'invoice_number' => $request->invoice_number ?: $this->generateInvoiceNumber(),
            'agency_id' => $request->agency_id,
            'type' => 'quick',
            'status' => 'draft',
            'title' => $request->title,
            'description' => $request->description,
            'subtotal' => $subtotal,
            'tax_percentage' => $taxPercentage,
            'tax_amount' => $taxAmount,
            'discount_percentage' => $discountPercentage,
            'discount_amount' => $discountAmount,
            'total_amount' => $totalAmount,
            'due_date' => $request->due_date,
            'terms_conditions' => $request->terms_conditions,
            'created_by' => Auth::id(),
        ]);

        // Generate PDF
        $this->generateAndStorePdf($invoice);

        return $invoice;
    }

    public function createDetailedInvoice(Request $request): Invoice
    {
        $invoice = Invoice::create([ 
            'invoice_number' => $request->invoice_number ?: $this->generateInvoiceNumber(),
            'agency_id' => $request->agency_id,
            'type' => 'detailed',
            'status' => 'draft',
            'title' => $request->title,
            'description' => $request->description??'',
            'tax_percentage' => $request->tax_percentage ?? 0,
            'discount_percentage' => $request->discount_percentage ?? 0,
            'total_amount' => 0, // Will be calculated
            'due_date' => $request->due_date,
            'terms_conditions' => $request->terms_conditions,
            'created_by' => Auth::id(),
        ]);

        // Add items
        if ($request->has('items') && is_array($request->items)) {
            foreach ($request->items as $itemData) {
                if (empty($itemData['description'])) continue; // Skip empty items

                $item = new InvoiceItem([
                    'description' => $itemData['description'],
                    'quantity' => $itemData['quantity'] ?? 1,
                    'unit_price' => $itemData['unit_price'] ?? 0,
                    'tax_percentage' => $itemData['tax_percentage'] ?? 0,
                    'discount_percentage' => $itemData['discount_percentage'] ?? 0,
                ]);

                $item->calculateLineTotal();
                $invoice->items()->save($item);
            }
        }

        // Calculate totals
        $invoice->calculateTotals();
        $invoice->save();

        // Generate PDF
        $this->generateAndStorePdf($invoice);

        return $invoice;
    }

    public function updateInvoice(Invoice $invoice, Request $request): Invoice
    {
        $invoice->update([
            'agency_id' => $request->agency_id,
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'terms_conditions' => $request->terms_conditions,
            'tax_percentage' => $request->tax_percentage ?? 0,
            'discount_percentage' => $request->discount_percentage ?? 0,
        ]);

        if ($invoice->type === 'quick') {
            $subtotal = $request->total_amount ?? 0;
            $taxAmount = ($subtotal * $invoice->tax_percentage) / 100;
            $discountAmount = ($subtotal * $invoice->discount_percentage) / 100;
            $totalAmount = $subtotal + $taxAmount - $discountAmount;

            $invoice->update([
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
            ]);
        }

        if ($invoice->type === 'detailed' && $request->has('items') && is_array($request->items)) {
            // Delete existing items
            $invoice->items()->delete();

            // Add new items
            foreach ($request->items as $itemData) {
                if (empty($itemData['description'])) continue; // Skip empty items

                $item = new InvoiceItem([
                    'description' => $itemData['description'],
                    'quantity' => $itemData['quantity'] ?? 1,
                    'unit_price' => $itemData['unit_price'] ?? 0,
                    'tax_percentage' => $itemData['tax_percentage'] ?? 0,
                    'discount_percentage' => $itemData['discount_percentage'] ?? 0,
                ]);

                $item->calculateLineTotal();
                $invoice->items()->save($item);
            }

            // Recalculate totals
            $invoice->calculateTotals();
            $invoice->save();
        }

        // Regenerate PDF if not uploaded type
        if ($invoice->type !== 'uploaded_pdf') {
            $this->generateAndStorePdf($invoice);
        }

        return $invoice;
    }

    public function sendInvoice(Invoice $invoice, string $email = null): void
    {
        // Ensure agency relationship is loaded
        if (!$invoice->relationLoaded('agency')) {
            $invoice->load('agency');
        }

        // Validate recipient email
        $recipientEmail = $email ?: ($invoice->agency->email ?? null);

        if (!$recipientEmail || !filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception('No valid email address found for the agency. Please update the agency email address.');
        }

        // Ensure PDF exists
        if (!$invoice->pdf_path || !Storage::exists($invoice->pdf_path)) {
            $this->generateAndStorePdf($invoice);
        }

        // Update status
        $invoice->markAsSent();

        // Log notification
        InvoiceNotification::logInvoiceSent($invoice, $recipientEmail);

        // Send email
        SendInvoiceEmail::dispatch($invoice, $recipientEmail);
    }

    public function markAsPaid(Invoice $invoice,$amount = "",$paymentMethod = 'manual',$transactionId = "",$notes = ""): void {
        $amount = $amount ?: $invoice->total_amount;
        // Create payment record
        $payment = $invoice->payments()->create([
            'payment_method' => $paymentMethod,
            'agency_id' => $invoice->agency_id,
            'transaction_id' => $transactionId??NULL,
            'amount' => $amount,
            'status' => 'completed',
            'paid_at' => now(),
            'payment_gateway_response' => $notes ? ['notes' => $notes] : null,
        ]);

        // Update invoice status
        $invoice->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        // Log notification
        InvoiceNotification::logPaymentReceived($invoice, $invoice->agency->email);

        // Send payment receipt
        SendPaymentReceipt::dispatch($invoice, $payment);
    }

    public function duplicateInvoice(Invoice $invoice): Invoice
    {
        $newInvoice = $invoice->replicate([
            'invoice_number',
            'status',
            'sent_at',
            'paid_at',
            'pdf_path',
        ]);

        $newInvoice->invoice_number = $this->generateInvoiceNumber();
        $newInvoice->status = 'draft';
        $newInvoice->due_date = Carbon::now()->addDays(30);
        $newInvoice->created_by = Auth::id();
        $newInvoice->save();

        // Duplicate items if detailed invoice
        if ($invoice->type === 'detailed') {
            foreach ($invoice->items as $item) {
                $newItem = $item->replicate();
                $newInvoice->items()->save($newItem);
            }

            $newInvoice->calculateTotals();
            $newInvoice->save();
        }

        return $newInvoice;
    }

    public function bulkAction(iterable $invoices, string $action): array
    {
        $results = ['success' => 0, 'failed' => 0, 'errors' => []];

        foreach ($invoices as $invoice) {
            try {
                switch ($action) {
                    case 'send':
                        if ($invoice->status === 'draft') {
                            $this->sendInvoice($invoice);
                            $results['success']++;
                        } else {
                            $results['errors'][] = "Invoice {$invoice->invoice_number} is not in draft status";
                            $results['failed']++;
                        }
                        break;

                    case 'mark_paid':
                        if ($invoice->status !== 'paid') {
                            $this->markAsPaid($invoice);
                            $results['success']++;
                        } else {
                            $results['errors'][] = "Invoice {$invoice->invoice_number} is already paid";
                            $results['failed']++;
                        }
                        break;

                    case 'delete':
                        if ($invoice->canBeDeleted()) {
                            $invoice->delete();
                            $results['success']++;
                        } else {
                            $results['errors'][] = "Invoice {$invoice->invoice_number} cannot be deleted";
                            $results['failed']++;
                        }
                        break;
                }
            } catch (\Exception $e) {
                $results['errors'][] = "Invoice {$invoice->invoice_number}: " . $e->getMessage();
                $results['failed']++;
            }
        }

        return $results;
    }

    protected function generateInvoiceNumber(): string
    {
        $year = Carbon::now()->year;
        $lastInvoice = Invoice::whereYear('created_at', $year)
                           ->orderBy('id', 'desc')
                           ->first();

        $nextNumber = $lastInvoice ?
                     (int) substr($lastInvoice->invoice_number, -4) + 1 :
                     1;

        return 'INV-' . $year . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    protected function generateAndStorePdf(Invoice $invoice): void
    {
        try {
            $pdf = $this->pdfService->generateInvoicePdf($invoice);
            $filename = $invoice->invoice_number . '_' . time() . '.pdf';
            $path = 'invoices/' . $filename;

            // TCPDF Output method: 'S' = return as string
            $pdfContent = $pdf->Output('', 'S');

            if (empty($pdfContent)) {
                throw new \Exception('PDF generation failed - empty content');
            }

            Storage::disk('public')->put($path, $pdfContent);

            // Verify the file was created
            if (!Storage::disk('public')->exists($path)) {
                throw new \Exception('PDF file was not saved to storage');
            }

            $invoice->update(['pdf_path' => $path]);

        } catch (\Exception $e) {
            \Log::error('PDF generation failed', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Failed to generate invoice PDF: ' . $e->getMessage());
        }
    }

    public function getInvoiceStatistics(array $filters = []): array
    {
        $query = Invoice::query();

        // Apply filters
        if (isset($filters['agency_id'])) {
            $query->where('agency_id', $filters['agency_id']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return [
            'total_invoices' => $query->count(),
            'draft_invoices' => (clone $query)->where('status', 'draft')->count(),
            'sent_invoices' => (clone $query)->where('status', 'sent')->count(),
            'paid_invoices' => (clone $query)->where('status', 'paid')->count(),
            'overdue_invoices' => (clone $query)->overdue()->count(),
            'total_amount' => $query->sum('total_amount'),
            'paid_amount' => (clone $query)->where('status', 'paid')->sum('total_amount'),
            'pending_amount' => (clone $query)->whereIn('status', ['sent', 'overdue'])->sum('total_amount'),
            'average_invoice_value' => $query->avg('total_amount'),
        ];
    }
}