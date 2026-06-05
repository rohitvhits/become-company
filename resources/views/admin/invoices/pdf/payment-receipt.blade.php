<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Receipt - {{ $invoice->invoice_number }}</title>
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
            border-bottom: 2px solid #27ae60;
        }

        .company-info {
            text-align: right;
            margin-bottom: 20px;
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

        .receipt-title {
            font-size: 32px;
            font-weight: bold;
            color: #27ae60;
            text-align: center;
            margin: 20px 0;
        }

        .receipt-details {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }

        .receipt-details-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .receipt-details-right {
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

        .payment-summary {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 30px 0;
            border-left: 4px solid #27ae60;
        }

        .payment-amount {
            text-align: center;
            margin: 20px 0;
        }

        .amount-label {
            font-size: 18px;
            color: #666;
            margin-bottom: 10px;
        }

        .amount-value {
            font-size: 36px;
            font-weight: bold;
            color: #27ae60;
        }

        .details-table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }

        .details-table th {
            background-color: #f8f9fa;
            padding: 12px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #ddd;
        }

        .details-table td {
            padding: 12px;
            border: 1px solid #ddd;
        }

        .details-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .invoice-summary {
            background-color: #e8f6f3;
            padding: 20px;
            border-radius: 5px;
            margin: 30px 0;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            background-color: #27ae60;
            color: white;
            vertical-align: middle;
            line-height: 1.4;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }

        .thank-you {
            text-align: center;
            font-size: 18px;
            color: #27ae60;
            font-weight: bold;
            margin: 25px 0;
            padding: 20px;
            background-color: #e8f6f3;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-info">
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
        </div>

        <div class="receipt-title">PAYMENT RECEIPT</div>
    </div>

    <table style="width: 100%; margin-bottom: 30px;" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <div class="section-title">Receipt Information:</div>
                <strong>Receipt #:</strong> RCP-{{ $payment->id }}<br>
                <strong>Payment Date:</strong> {{ $payment->paid_at->format('M d, Y H:i A') }}<br>
                <strong>Payment Method:</strong> {{ $payment->payment_method_label }}<br>
                @if($payment->transaction_id)
                    <strong>Transaction ID:</strong> {{ $payment->transaction_id }}<br>
                @endif
                <div style="margin-top: 5px;">
                    <strong>Status:</strong> <span class="status-badge" style="display: inline-block; padding: 5px 10px; background-color: #27ae60; color: white; font-size: 12px;">{{ ucfirst($payment->status) }}</span>
                </div>
            </td>
            <td style="width: 50%; vertical-align: top; text-align: right;">
                <div class="section-title" style="text-align: right;">Bill To:</div>
                <strong>{{ $invoice->agency->agency_name }}</strong><br>
                {{ $invoice->agency->email }}<br>
                @if($invoice->agency->phone)
                    Phone: {{ $invoice->agency->phone }}<br>
                @endif
            </td>
        </tr>
    </table>

    <div class="payment-amount">
        <div class="amount-label">Amount Paid</div>
        <div class="amount-value">${{ number_format($payment->amount, 2) }}</div>
    </div>

    <div class="invoice-summary">
        <div class="section-title">Invoice Details:</div>
        <table class="details-table">
            <tr>
                <th>Invoice Number</th>
                <td>{{ $invoice->invoice_number }}</td>
            </tr>
            <tr>
                <th>Invoice Date</th>
                <td>{{ $invoice->created_at->format('M d, Y') }}</td>
            </tr>
            <tr>
                <th>Due Date</th>
                <td>{{ $invoice->due_date->format('M d, Y') }}</td>
            </tr>
            <tr>
                <th>Invoice Amount</th>
                <td>${{ number_format($invoice->total_amount, 2) }}</td>
            </tr>
            <tr>
                <th>Total Paid</th>
                <td>${{ number_format($invoice->total_paid, 2) }}</td>
            </tr>
            <tr>
                <th>Balance Remaining</th>
                <td>${{ number_format($invoice->balance, 2) }}</td>
            </tr>
            @if($invoice->title)
                <tr>
                    <th>Description</th>
                    <td>{{ $invoice->title }}</td>
                </tr>
            @endif
        </table>
    </div>

    @if($payment->payment_gateway_response && isset($payment->payment_gateway_response['receipt_url']))
        <div class="payment-summary">
            <div class="section-title">Payment Gateway Receipt:</div>
            <p>You can also view your payment receipt at the following link:</p>
            <p><a href="{{ $payment->payment_gateway_response['receipt_url'] }}">{{ $payment->payment_gateway_response['receipt_url'] }}</a></p>
        </div>
    @endif

    <div class="thank-you">
        <i class="mdi mdi check"></i> Payment Received Successfully!<br>
        Thank you for your prompt payment.
    </div>

    <div class="footer">
        <p><strong>This is an official payment receipt.</strong></p>
        <p>Please keep this receipt for your records.</p>
        <p>If you have any questions about this payment, please contact us at {{ $company['email'] }} or {{ $company['phone'] }}.</p>
        <p>Generated on {{ now()->format('M d, Y H:i A') }}</p>
    </div>
</body>
</html>