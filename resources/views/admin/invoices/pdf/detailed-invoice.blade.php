<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .header {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #2c3e50;
        }

        .company-info {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .company-logo {
            display: table-cell;
            width: 200px;
            vertical-align: top;
            padding-right: 20px;
        }

        .company-logo img {
            max-width: 180px;
            max-height: 80px;
            height: auto;
        }

        .company-details-section {
            display: table-cell;
            text-align: right;
            vertical-align: top;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .company-details {
            font-size: 12px;
            color: #666;
        }

        .invoice-title {
            font-size: 32px;
            font-weight: bold;
            color: #2c3e50;
            text-align: center;
            margin: 20px 0;
        }

        .invoice-details {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }

        .invoice-details-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .invoice-details-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            text-align: right;
        }

        .section-title {
            font-weight: bold;
            font-size: 16px;
            color: #2c3e50;
            margin-bottom: 10px;
            border-bottom: 1px solid #bdc3c7;
            padding-bottom: 5px;
        }

        .bill-to, .invoice-info {
            margin-bottom: 20px;
        }

        .items-table {
            width: 100%;
            margin: 30px 0;
            border-collapse: collapse;
            border: 1px solid #ddd;
        }

        .items-table th {
            background-color: #2c3e50;
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
        }

        .items-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #ddd;
            vertical-align: top;
        }

        .items-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .summary-table {
            width: 50%;
            margin: 30px 0 30px auto;
            border-collapse: collapse;
        }

        .summary-table td {
            padding: 8px 12px;
            border-bottom: 1px solid #ddd;
        }

        .summary-table .label {
            text-align: right;
            font-weight: bold;
            background-color: #f8f9fa;
        }

        .summary-table .value {
            text-align: right;
            width: 120px;
        }

        .total-row {
            font-weight: bold;
            font-size: 16px;
            background-color: #2c3e50 !important;
            color: white !important;
        }

        .description-section {
            margin: 30px 0;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }

        .terms {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #666;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }

        .status-badge {
            display: inline-block;
            padding: 7px 14px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            white-space: nowrap;
            vertical-align: middle;
        }

        .status-draft { background-color: #95a5a6; color: white; }
        .status-sent { background-color: #3498db; color: white; }
        .status-paid { background-color: #27ae60; color: white; }
        .status-overdue { background-color: #e74c3c; color: white; }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <table style="width: 100%; margin-bottom: 20px;" cellpadding="0" cellspacing="0">
            <tr>
                <td style="width: 200px; vertical-align: top; padding-right: 20px;">
                    @if($company['logo'] && file_exists(public_path($company['logo'])))
                        @php
                            $logoPath = public_path($company['logo']);
                            $logoData = base64_encode(file_get_contents($logoPath));
                            $logoMime = mime_content_type($logoPath);
                        @endphp
                        <img style="max-width: 180px; max-height: 80px; background: #1e1e2f; border-bottom: 1px solid #030303;" src="data:{{ $logoMime }};base64,{{ $logoData }}" alt="{{ $company['name'] }}">
                    @endif
                </td>
                <td style="text-align: right; vertical-align: top;">
                    <div class="company-name">{{ $company['name'] }}</div>
                    <div class="company-details">
                        {{ $company['address'] }}<br>
                        {{ $company['city'] }}, {{ $company['state'] }} {{ $company['zip'] }}<br>
                        Phone: {{ $company['phone'] }} | Email: {{ $company['email'] }}<br>
                        @if($company['website'])
                            Website: {{ $company['website'] }}<br>
                        @endif
                        @if($company['tax_id'])
                            Tax ID: {{ $company['tax_id'] }}
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        <div class="invoice-title">INVOICE</div>
    </div>

    <table style="width: 100%; margin-bottom: 30px;" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <div class="bill-to">
                    <div class="section-title">Bill To:</div>
                    <strong>{{ $invoice->agency->agency_name }}</strong><br>
                    {{ $invoice->agency->email }}<br>
                    @if($invoice->agency->phone)
                        Phone: {{ $invoice->agency->phone }}<br>
                    @endif
                </div>
            </td>
            <td style="width: 50%; vertical-align: top; text-align: right;">
                <div class="invoice-info">
                    <div class="section-title" style="text-align: right;">Invoice Details:</div>
                    <strong>Invoice #:</strong> {{ $invoice->invoice_number }}<br>
                    <strong>Date:</strong> {{ $invoice->created_at->format('M d, Y') }}<br>
                    <strong>Due Date:</strong> {{ $invoice->due_date->format('M d, Y') }}<br>
                    <strong>Status:</strong>
                    <span class="status-badge status-{{ $invoice->status }}" style="display: inline-block; padding: 5px 12px; background-color: #6c757d; color: white; font-size: 11px;">
                        {{ ucfirst($invoice->status) }}
                    </span>
                </div>
            </td>
        </tr>
    </table>

    @if($invoice->title)
        <div class="section-title">{{ $invoice->title }}</div>
    @endif

    @if($invoice->description)
        <div class="description-section">
            <div class="section-title">Description:</div>
            {{ $invoice->description }}
        </div>
    @endif

    <table class="items-table">
        <thead>
            <tr>
                <th width="50%">Description</th>
                <th width="10%" class="text-center">Qty</th>
                <th width="15%" class="text-right">Unit Price</th>
                @if($invoice->items->where('tax_percentage', '>', 0)->count() > 0)
                    <th width="10%" class="text-right">Tax</th>
                @endif
                @if($invoice->items->where('discount_percentage', '>', 0)->count() > 0)
                    <th width="10%" class="text-right">Discount</th>
                @endif
                <th width="15%" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td class="text-center">{{ number_format($item->quantity, 2) }}</td>
                    <td class="text-right">${{ number_format($item->unit_price, 2) }}</td>
                    @if($invoice->items->where('tax_percentage', '>', 0)->count() > 0)
                        <td class="text-right">
                            @if($item->tax_percentage > 0)
                                {{ number_format($item->tax_percentage, 1) }}%
                            @else
                                -
                            @endif
                        </td>
                    @endif
                    @if($invoice->items->where('discount_percentage', '>', 0)->count() > 0)
                        <td class="text-right">
                            @if($item->discount_percentage > 0)
                                {{ number_format($item->discount_percentage, 1) }}%
                            @else
                                -
                            @endif
                        </td>
                    @endif
                    <td class="text-right">${{ number_format($item->line_total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="summary-table">
        <tr>
            <td class="label">Subtotal:</td>
            <td class="value">${{ number_format($invoice->subtotal, 2) }}</td>
        </tr>
        @if($invoice->tax_percentage > 0)
            <tr>
                <td class="label">Tax ({{ number_format($invoice->tax_percentage, 2) }}%):</td>
                <td class="value">${{ number_format($invoice->tax_amount, 2) }}</td>
            </tr>
        @endif
        @if($invoice->discount_percentage > 0)
            <tr>
                <td class="label">Discount ({{ number_format($invoice->discount_percentage, 2) }}%):</td>
                <td class="value">-${{ number_format($invoice->discount_amount, 2) }}</td>
            </tr>
        @endif
        <tr class="total-row">
            <td class="label">Total:</td>
            <td class="value">${{ number_format($invoice->total_amount, 2) }}</td>
        </tr>
    </table>

    @if($invoice->terms_conditions)
        <div class="terms">
            <div class="section-title">Terms & Conditions:</div>
            {{ $invoice->terms_conditions }}
        </div>
    @endif

    <div class="footer">
        <p>Thank you for your business!</p>
        <p>If you have any questions about this invoice, please contact us at {{ $company['email'] }} or {{ $company['phone'] }}.</p>
    </div>
</body>
</html>