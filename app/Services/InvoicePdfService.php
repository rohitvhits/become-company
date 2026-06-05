<?php

namespace App\Services;

use App\Model\Invoice;
use App\Model\PDF;
use Illuminate\Support\Facades\View;

class InvoicePdfService
{
    protected $leftMargin = 15;
    protected $rightMargin = 15;
    protected $pageWidth = 210; // A4 width in mm
    protected $contentWidth;

    public function __construct()
    {
        $this->contentWidth = $this->pageWidth - $this->leftMargin - $this->rightMargin;
    }

    public function generateInvoicePdf(Invoice $invoice)
    {
        $invoice->load(['agency', 'items', 'creator']);

        $company = $this->getCompanyData();
        $pdf = $this->createPdfInstance('Invoice - ' . $invoice->invoice_number);
        $pdf->AddPage();

        // Generate invoice based on type
        if ($invoice->type === 'detailed') {
            $this->buildDetailedInvoice($pdf, $invoice, $company);
        } else {
            $this->buildQuickInvoice($pdf, $invoice, $company);
        }

        return $pdf;
    }

    public function generatePaymentReceipt(Invoice $invoice, $payment)
    {
        $company = $this->getCompanyData();
        $pdf = $this->createPdfInstance('Payment Receipt - ' . $invoice->invoice_number);
        $pdf->AddPage();

        $this->buildPaymentReceipt($pdf, $invoice, $payment, $company);

        return $pdf;
    }

    protected function buildDetailedInvoice($pdf, $invoice, $company)
    {
        $y = $this->buildHeader($pdf, $company, 'INVOICE');
        $y = $this->buildInvoiceDetails($pdf, $invoice, $y);

        if ($invoice->title) {
            $pdf->SetY($y);
            $pdf->SetFont('helvetica', 'B', 16);
            $pdf->SetTextColor(44, 62, 80);
            $pdf->Cell(0, 8, $invoice->title, 0, 1, 'L');
            $y = $pdf->GetY() + 5;
        }

        if ($invoice->description) {
            $pdf->SetY($y);
            $pdf->SetFillColor(248, 249, 250);
            $pdf->SetFont('helvetica', '', 10);
            $pdf->MultiCell(0, 6, $invoice->description, 0, 'L', true);
            $y = $pdf->GetY() + 5;
        }

        $y = $this->buildItemsTable($pdf, $invoice, $y);
        $y = $this->buildSummaryTable($pdf, $invoice, $y);

        if ($invoice->terms_conditions) {
            $this->buildTerms($pdf, $invoice->terms_conditions, $y);
        }

        $this->buildFooter($pdf, $company);
    }

    protected function buildQuickInvoice($pdf, $invoice, $company)
    {
        $y = $this->buildHeader($pdf, $company, 'INVOICE');
        $y = $this->buildInvoiceDetails($pdf, $invoice, $y);

        if ($invoice->title) {
            $pdf->SetY($y);
            $pdf->SetFont('helvetica', 'B', 16);
            $pdf->SetTextColor(44, 62, 80);
            $pdf->Cell(0, 8, $invoice->title, 0, 1, 'L');
            $y = $pdf->GetY() + 5;
        }

        if ($invoice->description) {
            $pdf->SetY($y);
            $pdf->SetFillColor(248, 249, 250);
            $pdf->SetFont('helvetica', '', 10);
            $pdf->MultiCell(0, 6, $invoice->description, 0, 'L', true);
            $y = $pdf->GetY() + 5;
        }

        // Amount section
        $pdf->SetY($y);
        $pdf->SetFillColor(248, 249, 250);
        $pdf->Rect($this->leftMargin, $y, $this->contentWidth, 30, 'F');

        $pdf->SetY($y + 5);
        $pdf->SetFont('helvetica', '', 14);
        $pdf->SetTextColor(102, 102, 102);
        $pdf->Cell(0, 8, 'Total Amount Due', 0, 1, 'C');

        $pdf->SetFont('helvetica', 'B', 28);
        $pdf->SetTextColor(39, 174, 96);
        $pdf->Cell(0, 12, '$' . number_format($invoice->total_amount, 2), 0, 1, 'C');

        $y = $pdf->GetY() + 10;

        if ($invoice->tax_percentage > 0 || $invoice->discount_percentage > 0) {
            $y = $this->buildQuickSummaryTable($pdf, $invoice, $y);
        }

        if ($invoice->terms_conditions) {
            $this->buildTerms($pdf, $invoice->terms_conditions, $y);
        }

        $this->buildFooter($pdf, $company);
    }

    protected function buildPaymentReceipt($pdf, $invoice, $payment, $company)
    {
        // Build header with green theme
        $y = 15;

        // Company info (right aligned)
        $pdf->SetY($y);
        $pdf->SetFont('helvetica', 'B', 18);
        $pdf->SetTextColor(44, 62, 80);
        $pdf->Cell(0, 8, $company['name'], 0, 1, 'R');

        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetTextColor(102, 102, 102);
        $pdf->MultiCell(0, 4,
            $company['address'] . "\n" .
            $company['city'] . ', ' . $company['state'] . ' ' . $company['zip'] . "\n" .
            'Phone: ' . $company['phone'] . ' | Email: ' . $company['email'],
            0, 'R'
        );

        $y = $pdf->GetY() + 5;

        // Green line
        $pdf->SetDrawColor(39, 174, 96);
        $pdf->SetLineWidth(0.8);
        $pdf->Line($this->leftMargin, $y, $this->pageWidth - $this->rightMargin, $y);
        $y += 10;

        // Receipt title
        $pdf->SetY($y);
        $pdf->SetFont('helvetica', 'B', 24);
        $pdf->SetTextColor(39, 174, 96);
        $pdf->Cell(0, 12, 'PAYMENT RECEIPT', 0, 1, 'C');
        $y = $pdf->GetY() + 10;

        // Receipt details (two columns)
        $pdf->SetY($y);
        $colWidth = $this->contentWidth / 2;

        // Left column - Receipt info
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetTextColor(44, 62, 80);
        $pdf->Cell($colWidth, 6, 'Receipt Information:', 0, 0, 'L');

        // Right column - Bill To
        $pdf->Cell($colWidth, 6, 'Bill To:', 0, 1, 'R');

        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetTextColor(51, 51, 51);

        $leftY = $pdf->GetY();
        $pdf->SetXY($this->leftMargin, $leftY);
        $pdf->Cell(0, 5, "Receipt #: RCP-{$payment->id}", 0, 1, 'L');
        $pdf->SetX($this->leftMargin);
        $pdf->Cell(0, 5, "Payment Date: " . $payment->paid_at->format('M d, Y H:i A'), 0, 1, 'L');
        $pdf->SetX($this->leftMargin);
        $pdf->Cell(0, 5, "Payment Method: {$payment->payment_method_label}", 0, 1, 'L');
        if ($payment->transaction_id) {
            $pdf->SetX($this->leftMargin);
            $pdf->Cell(0, 5, "Transaction ID: {$payment->transaction_id}", 0, 1, 'L');
        }

        // Status badge on left side
        $statusBadgeY = $pdf->GetY() + 2;
        $pdf->SetX($this->leftMargin);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(15, 5, "Status: ", 0, 0, 'L');
        $this->drawPaymentStatusBadge($pdf, $payment->status, $this->leftMargin + 15, $statusBadgeY);

        // Right column - Bill To
        $pdf->SetXY($this->leftMargin + $colWidth + 5, $leftY);
        $pdf->MultiCell($colWidth - 5, 5,
            "{$invoice->agency->agency_name}\n" .
            "{$invoice->agency->email}\n" .
            ($invoice->agency->phone ? "Phone: {$invoice->agency->phone}" : ''),
            0, 'R'
        );

        $y = max($pdf->GetY(), $leftY + 25) + 10;

        // Amount Paid section
        $pdf->SetY($y);
        $pdf->SetFont('helvetica', '', 14);
        $pdf->SetTextColor(102, 102, 102);
        $pdf->Cell(0, 8, 'Amount Paid', 0, 1, 'C');

        $pdf->SetFont('helvetica', 'B', 28);
        $pdf->SetTextColor(39, 174, 96);
        $pdf->Cell(0, 12, '$' . number_format($payment->amount, 2), 0, 1, 'C');
        $y = $pdf->GetY() + 10;

        // Invoice summary table
        $pdf->SetY($y);
        $pdf->SetFillColor(232, 246, 243);
        $pdf->Rect($this->leftMargin, $y, $this->contentWidth, 5, 'F');

        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetTextColor(44, 62, 80);
        $pdf->Cell(0, 5, 'Invoice Details:', 0, 1, 'L');

        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetFillColor(248, 249, 250);

        $details = [
            ['Invoice Number', $invoice->invoice_number],
            ['Invoice Date', $invoice->created_at->format('M d, Y')],
            ['Due Date', $invoice->due_date->format('M d, Y')],
            ['Invoice Amount', '$' . number_format($invoice->total_amount, 2)],
            ['Total Paid', '$' . number_format($invoice->total_paid, 2)],
            ['Balance Remaining', '$' . number_format($invoice->balance, 2)],
        ];

        if ($invoice->title) {
            $details[] = ['Description', $invoice->title];
        }

        $fill = false;
        foreach ($details as $detail) {
            $pdf->SetFillColor($fill ? 248 : 255, $fill ? 249 : 255, $fill ? 250 : 255);
            $pdf->Cell($colWidth, 6, $detail[0], 1, 0, 'L', true);
            $pdf->Cell($colWidth, 6, $detail[1], 1, 1, 'L', true);
            $fill = !$fill;
        }

        $y = $pdf->GetY() + 10;

        // Thank you message
        $pdf->SetY($y);
        $pdf->SetFillColor(232, 246, 243);
        $pdf->Rect($this->leftMargin, $y, $this->contentWidth, 20, 'F');

        $pdf->SetY($y + 5);
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->SetTextColor(39, 174, 96);
        $pdf->Cell(0, 6, 'Payment Received Successfully!', 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 6, 'Thank you for your prompt payment.', 0, 1, 'C');

        // Footer
        $y = $pdf->GetY() + 20;
        $pdf->SetY($y);
        $pdf->SetDrawColor(221, 221, 221);
        $pdf->Line($this->leftMargin, $y, $this->pageWidth - $this->rightMargin, $y);

        $pdf->SetY($y + 5);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetTextColor(102, 102, 102);
        $pdf->MultiCell(0, 4,
            "This is an official payment receipt.\n" .
            "Please keep this receipt for your records.\n" .
            "If you have any questions about this payment, please contact us at {$company['email']} or {$company['phone']}.",
            0, 'C'
        );
    }

    protected function buildHeader($pdf, $company, $title)
    {
        $y = 15;
        $pdf->SetY($y);

        // Logo with black background
        $logoWidth = 50;
        $logoHeight = 10;

        if (!empty($company['logo']) && file_exists(public_path($company['logo']))) {
            // Draw black background for logo
            $pdf->SetFillColor(30, 30, 47); // #1e1e2f
            $pdf->Rect($this->leftMargin, $y, $logoWidth, $logoHeight, 'F');

            // Add bottom border
            $pdf->SetDrawColor(3, 3, 3);
            $pdf->SetLineWidth(0.3);
            $pdf->Line($this->leftMargin, $y + $logoHeight, $this->leftMargin + $logoWidth, $y + $logoHeight);

            // Place logo on top of background
            $logoPath = public_path($company['logo']);
            $pdf->Image($logoPath, $this->leftMargin + 2, $y + 2, $logoWidth - 4, $logoHeight - 4, '', '', '', false, 300, '', false, false, 0, false, false, false);
        }

        // Company details (right side)
        $pdf->SetXY($this->leftMargin + $logoWidth + 10, $y);
        $pdf->SetFont('helvetica', 'B', 18);
        $pdf->SetTextColor(44, 62, 80);
        $pdf->Cell(0, 8, $company['name'], 0, 1, 'R');

        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetTextColor(102, 102, 102);
        $pdf->MultiCell(0, 4,
            $company['address'] . "\n" .
            $company['city'] . ', ' . $company['state'] . ' ' . $company['zip'] . "\n" .
            'Phone: ' . $company['phone'] . ' | Email: ' . $company['email'] .
            (!empty($company['website']) ? "\nWebsite: {$company['website']}" : '') .
            (!empty($company['tax_id']) ? "\nTax ID: {$company['tax_id']}" : ''),
            0, 'R'
        );

        $y = max($pdf->GetY(), $y + $logoHeight) + 5;

        // Separator line
        $pdf->SetDrawColor(44, 62, 80);
        $pdf->SetLineWidth(0.8);
        $pdf->Line($this->leftMargin, $y, $this->pageWidth - $this->rightMargin, $y);
        $y += 10;

        // Title
        $pdf->SetY($y);
        $pdf->SetFont('helvetica', 'B', 24);
        $pdf->SetTextColor(44, 62, 80);
        $pdf->Cell(0, 12, $title, 0, 1, 'C');

        return $pdf->GetY() + 10;
    }

    protected function buildInvoiceDetails($pdf, $invoice, $y)
    {
        $pdf->SetY($y);
        $colWidth = $this->contentWidth / 2;

        // Draw section titles
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetTextColor(44, 62, 80);
        $pdf->SetDrawColor(189, 195, 199);

        // Bill To title
        $pdf->Cell($colWidth, 6, 'Bill To:', 'B', 0, 'L');

        // Invoice Details title (right aligned)
        $pdf->Cell($colWidth, 6, 'Invoice Details:', 'B', 1, 'R');

        $leftY = $pdf->GetY() + 3;

        // Left column - Bill To
        $pdf->SetXY($this->leftMargin, $leftY);
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->SetTextColor(51, 51, 51);
        $pdf->Cell($colWidth, 5, $invoice->agency->agency_name, 0, 1, 'L');

        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetX($this->leftMargin);
        $pdf->MultiCell($colWidth - 5, 5,
            $invoice->agency->email . "\n" .
            ($invoice->agency->phone ? 'Phone: ' . $invoice->agency->phone : ''),
            0, 'L'
        );

        // Right column - Invoice Details
        $rightX = $this->leftMargin + $colWidth + 5;
        $pdf->SetXY($rightX, $leftY);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetTextColor(51, 51, 51);

        $pdf->Cell(0, 5, "Invoice #: {$invoice->invoice_number}", 0, 1, 'R');
        $pdf->SetX($rightX);
        $pdf->Cell(0, 5, "Date: " . $invoice->created_at->format('M d, Y'), 0, 1, 'R');
        $pdf->SetX($rightX);
        $pdf->Cell(0, 5, "Due Date: " . $invoice->due_date->format('M d, Y'), 0, 1, 'R');

        // Status label and badge on same line
        $statusY = $pdf->GetY();
        $pdf->SetXY($rightX, $statusY);
        $pdf->SetFont('helvetica', 'B', 10);
        $statusLabelWidth = $pdf->GetStringWidth("Status: ");

        // Calculate position for "Status:" label (right aligned before badge)
        $statusText = strtoupper($invoice->status);
        $pdf->SetFont('helvetica', 'B', 9);
        $badgeTextWidth = $pdf->GetStringWidth($statusText);
        $badgeWidth = $badgeTextWidth + 8;

        // Position "Status:" label
        $badgePosX = $this->pageWidth - $this->rightMargin - $badgeWidth;
        $labelPosX = $badgePosX - $statusLabelWidth - 2;

        $pdf->SetXY($labelPosX, $statusY);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell($statusLabelWidth, 5, "Status: ", 0, 0, 'R');

        // Draw status badge right after label (slightly down for better alignment)
        $this->drawStatusBadge($pdf, $invoice->status, $statusY + 0.5, $badgePosX);

        return max($pdf->GetY() + 5, $leftY + 25) + 10;
    }

    protected function drawStatusBadge($pdf, $status, $y, $x = null)
    {
        $statusText = strtoupper($status);

        // Set badge colors based on status
        switch ($status) {
            case 'draft':
                $bgColor = [149, 165, 166]; // #95a5a6
                break;
            case 'sent':
                $bgColor = [52, 152, 219]; // #3498db
                break;
            case 'paid':
                $bgColor = [39, 174, 96]; // #27ae60
                break;
            case 'overdue':
                $bgColor = [231, 76, 60]; // #e74c3c
                break;
            default:
                $bgColor = [108, 117, 125]; // #6c757d
        }

        // Calculate badge dimensions
        $pdf->SetFont('helvetica', 'B', 9);
        $textWidth = $pdf->GetStringWidth($statusText);
        $badgeWidth = $textWidth + 8;
        $badgeHeight = 5;

        // Position badge
        $badgeX = $x !== null ? $x : ($this->pageWidth - $this->rightMargin - $badgeWidth);

        // Draw rounded rectangle background
        $pdf->SetFillColor($bgColor[0], $bgColor[1], $bgColor[2]);
        $pdf->RoundedRect($badgeX, $y, $badgeWidth, $badgeHeight, 1.5, '1111', 'F');

        // Draw status text
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetXY($badgeX, $y);
        $pdf->Cell($badgeWidth, $badgeHeight, $statusText, 0, 0, 'C');

        // Reset text color
        $pdf->SetTextColor(51, 51, 51);
    }

    protected function drawPaymentStatusBadge($pdf, $status, $x, $y)
    {
        $statusText = strtoupper($status);

        // Payment status - always green for completed
        $bgColor = [39, 174, 96]; // #27ae60

        // Calculate badge dimensions
        $pdf->SetFont('helvetica', 'B', 9);
        $textWidth = $pdf->GetStringWidth($statusText);
        $badgeWidth = $textWidth + 8;
        $badgeHeight = 5;

        // Draw rounded rectangle background
        $pdf->SetFillColor($bgColor[0], $bgColor[1], $bgColor[2]);
        $pdf->RoundedRect($x, $y, $badgeWidth, $badgeHeight, 1.5, '1111', 'F');

        // Draw status text
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetXY($x, $y);
        $pdf->Cell($badgeWidth, $badgeHeight, $statusText, 0, 0, 'C');

        // Reset text color
        $pdf->SetTextColor(51, 51, 51);
    }

    protected function buildItemsTable($pdf, $invoice, $y)
    {
        $pdf->SetY($y);

        // Table header
        $pdf->SetFillColor(44, 62, 80);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('helvetica', 'B', 10);

        $hasТах = $invoice->items->where('tax_percentage', '>', 0)->count() > 0;
        $hasDiscount = $invoice->items->where('discount_percentage', '>', 0)->count() > 0;

        $descWidth = 90;
        $qtyWidth = 15;
        $priceWidth = 25;
        $taxWidth = $hasТах ? 15 : 0;
        $discountWidth = $hasDiscount ? 15 : 0;
        $totalWidth = 25;

        $pdf->Cell($descWidth, 8, 'Description', 1, 0, 'L', true);
        $pdf->Cell($qtyWidth, 8, 'Qty', 1, 0, 'C', true);
        $pdf->Cell($priceWidth, 8, 'Unit Price', 1, 0, 'R', true);
        if ($hasТах) $pdf->Cell($taxWidth, 8, 'Tax', 1, 0, 'R', true);
        if ($hasDiscount) $pdf->Cell($discountWidth, 8, 'Disc.', 1, 0, 'R', true);
        $pdf->Cell($totalWidth, 8, 'Total', 1, 1, 'R', true);

        // Table rows
        $pdf->SetTextColor(51, 51, 51);
        $pdf->SetFont('helvetica', '', 10);
        $fill = false;

        foreach ($invoice->items as $item) {
            $pdf->SetFillColor($fill ? 248 : 255, $fill ? 249 : 255, $fill ? 250 : 255);

            $pdf->Cell($descWidth, 7, $item->description, 1, 0, 'L', true);
            $pdf->Cell($qtyWidth, 7, number_format($item->quantity, 2), 1, 0, 'C', true);
            $pdf->Cell($priceWidth, 7, '$' . number_format($item->unit_price, 2), 1, 0, 'R', true);

            if ($hasТах) {
                $taxText = $item->tax_percentage > 0 ? number_format($item->tax_percentage, 1) . '%' : '-';
                $pdf->Cell($taxWidth, 7, $taxText, 1, 0, 'R', true);
            }

            if ($hasDiscount) {
                $discText = $item->discount_percentage > 0 ? number_format($item->discount_percentage, 1) . '%' : '-';
                $pdf->Cell($discountWidth, 7, $discText, 1, 0, 'R', true);
            }

            $pdf->Cell($totalWidth, 7, '$' . number_format($item->line_total, 2), 1, 1, 'R', true);

            $fill = !$fill;
        }

        return $pdf->GetY() + 5;
    }

    protected function buildSummaryTable($pdf, $invoice, $y)
    {
        $pdf->SetY($y);

        $labelWidth = 90;
        $valueWidth = 40;
        $xPos = $this->pageWidth - $this->rightMargin - $labelWidth - $valueWidth;

        $pdf->SetX($xPos);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetFillColor(248, 249, 250);
        $pdf->SetTextColor(51, 51, 51);

        $pdf->Cell($labelWidth, 6, 'Subtotal:', 'B', 0, 'R', true);
        $pdf->Cell($valueWidth, 6, '$' . number_format($invoice->subtotal, 2), 'B', 1, 'R');

        if ($invoice->tax_percentage > 0) {
            $pdf->SetX($xPos);
            $pdf->Cell($labelWidth, 6, 'Tax (' . number_format($invoice->tax_percentage, 2) . '%):', 'B', 0, 'R', true);
            $pdf->Cell($valueWidth, 6, '$' . number_format($invoice->tax_amount, 2), 'B', 1, 'R');
        }

        if ($invoice->discount_percentage > 0) {
            $pdf->SetX($xPos);
            $pdf->Cell($labelWidth, 6, 'Discount (' . number_format($invoice->discount_percentage, 2) . '%):', 'B', 0, 'R', true);
            $pdf->Cell($valueWidth, 6, '-$' . number_format($invoice->discount_amount, 2), 'B', 1, 'R');
        }

        // Total row
        $pdf->SetX($xPos);
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetFillColor(44, 62, 80);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($labelWidth, 8, 'Total:', 0, 0, 'R', true);
        $pdf->Cell($valueWidth, 8, '$' . number_format($invoice->total_amount, 2), 0, 1, 'R', true);

        return $pdf->GetY() + 10;
    }

    protected function buildQuickSummaryTable($pdf, $invoice, $y)
    {
        $pdf->SetY($y);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetFillColor(248, 249, 250);
        $pdf->SetTextColor(51, 51, 51);

        $colWidth = ($this->contentWidth / 2);

        $pdf->Cell($colWidth, 6, 'Description', 1, 0, 'L', true);
        $pdf->Cell($colWidth, 6, 'Amount', 1, 1, 'R', true);

        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell($colWidth, 6, 'Subtotal', 1, 0, 'R');
        $pdf->Cell($colWidth, 6, '$' . number_format($invoice->subtotal, 2), 1, 1, 'R');

        if ($invoice->tax_percentage > 0) {
            $pdf->Cell($colWidth, 6, 'Tax (' . number_format($invoice->tax_percentage, 2) . '%)', 1, 0, 'R');
            $pdf->Cell($colWidth, 6, '$' . number_format($invoice->tax_amount, 2), 1, 1, 'R');
        }

        if ($invoice->discount_percentage > 0) {
            $pdf->Cell($colWidth, 6, 'Discount (' . number_format($invoice->discount_percentage, 2) . '%)', 1, 0, 'R');
            $pdf->Cell($colWidth, 6, '-$' . number_format($invoice->discount_amount, 2), 1, 1, 'R');
        }

        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetFillColor(44, 62, 80);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($colWidth, 8, 'Total', 0, 0, 'R', true);
        $pdf->Cell($colWidth, 8, '$' . number_format($invoice->total_amount, 2), 0, 1, 'R', true);

        return $pdf->GetY() + 10;
    }

    protected function buildTerms($pdf, $terms, $y)
    {
        $pdf->SetY($y);
        $pdf->SetDrawColor(221, 221, 221);
        $pdf->Line($this->leftMargin, $y, $this->pageWidth - $this->rightMargin, $y);

        $pdf->SetY($y + 5);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetTextColor(44, 62, 80);
        $pdf->Cell(0, 5, 'Terms & Conditions:', 0, 1, 'L');

        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetTextColor(102, 102, 102);
        $pdf->MultiCell(0, 4, $terms, 0, 'L');
    }

    protected function buildFooter($pdf, $company)
    {
        $y = $pdf->GetPageHeight() - 30;
        $pdf->SetY($y);

        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetTextColor(102, 102, 102);
        $pdf->MultiCell(0, 4,
            "Thank you for your business!\n" .
            "If you have any questions about this invoice, please contact us at {$company['email']} or {$company['phone']}.",
            0, 'C'
        );
    }

    /**
     * Create a configured TCPDF instance
     */
    protected function createPdfInstance(string $title): PDF
    {
        ini_set('memory_limit', '1024M');
        $pdf = new PDF('P', 'mm', 'A4', true, 'UTF-8', false);

        // Document metadata
        $pdf->SetCreator('NYBEST ERP');
        $pdf->SetAuthor('NYBEST ERP');
        $pdf->SetTitle($title);
        $pdf->SetSubject('Invoice Document');

        // Page settings
        $pdf->SetMargins($this->leftMargin, 15, $this->rightMargin);
        $pdf->SetHeaderMargin(0);
        $pdf->SetFooterMargin(0);
        $pdf->SetAutoPageBreak(false);

        // Font settings
        $pdf->SetFont('helvetica', '', 10);

        // Disable default header and footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        return $pdf;
    }

    protected function getCompanyData(): array
    {
        return [
            'name' => env('COMPANY_NAME', 'NY BEST CARE INC'),
            'address' => env('COMPANY_ADDRESS', '2965 Ocean Pkwy'),
            'city' => env('COMPANY_CITY', 'Brooklyn'),
            'state' => env('COMPANY_STATE', 'NY'),
            'zip' => env('COMPANY_ZIP', '11235'),
            'phone' => env('COMPANY_PHONE', '(718) 972 3693'),
            'email' => env('COMPANY_EMAIL', 'contact@nybestmedical.com'),
            'website' => env('COMPANY_WEBSITE', 'www.nybestmedical.com'),
            'logo' => env('COMPANY_LOGO_PATH', 'img/logo-ny.png'),
            'tax_id' => env('COMPANY_TAX_ID', ''),
        ];
    }
}