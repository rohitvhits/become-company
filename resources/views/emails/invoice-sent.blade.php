<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #2c3e50;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 5px 5px;
        }
        .invoice-details {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #3498db;
        }
        .amount {
            font-size: 24px;
            font-weight: bold;
            color: #e74c3c;
            text-align: center;
            margin: 20px 0;
            padding: 15px;
            background-color: #fff5f5;
            border-radius: 5px;
        }
        .button {
            display: inline-block;
            background-color: #3498db;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 10px 5px;
        }
        .button.primary {
            background-color: #27ae60;
        }
        .button.secondary {
            background-color: #95a5a6;
        }
        .actions {
            text-align: center;
            margin: 30px 0;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #666;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            background-color: #3498db;
            color: white;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $company['name'] }}</h1>
        <p>New Invoice Available</p>
    </div>

    <div class="content">
        <h2>Hello {{ $invoice->agency->agency_name }},</h2>

        <p>We hope this email finds you well. A new invoice has been generated for your account and is now available for your review and payment.</p>

        <div class="invoice-details">
            <h3>Invoice Details</h3>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px 0; font-weight: bold;">Invoice Number:</td>
                    <td style="padding: 8px 0;">{{ $invoice->invoice_number }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: bold;">Invoice Date:</td>
                    <td style="padding: 8px 0;">{{ $invoice->created_at->format('M d, Y') }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: bold;">Due Date:</td>
                    <td style="padding: 8px 0;">{{ $invoice->due_date->format('M d, Y') }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: bold;">Status:</td>
                    <td style="padding: 8px 0;"><span class="status-badge">{{ ucfirst($invoice->status) }}</span></td>
                </tr>
                @if($invoice->title)
                <tr>
                    <td style="padding: 8px 0; font-weight: bold;">Description:</td>
                    <td style="padding: 8px 0;">{{ $invoice->title }}</td>
                </tr>
                @endif
            </table>
        </div>

        <div class="amount">
            Total Amount: ${{ number_format($invoice->total_amount, 2) }}
        </div>

        <div class="actions">
            <a href="{{ $paymentUrl }}" class="button primary">Pay Now</a>
            <a href="{{ $invoiceUrl }}" class="button secondary">View Invoice</a>
        </div>

        <p><strong>Payment Options:</strong></p>
        <ul>
            <li>Online payment via credit/debit card</li>
            <li>Bank transfer or ACH</li>
            <li>Other payment methods as configured</li>
        </ul>

        <p><strong>Important Notes:</strong></p>
        <ul>
            <li>Please ensure payment is made by the due date to avoid any late fees</li>
            <li>The invoice PDF is attached to this email for your records</li>
            <li>If you have any questions, please don't hesitate to contact us</li>
        </ul>

        @if($invoice->terms_conditions)
        <div style="background-color: white; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <h4>Terms & Conditions:</h4>
            <p>{{ $invoice->terms_conditions }}</p>
        </div>
        @endif
    </div>

    <div class="footer">
        <p><strong>{{ $company['name'] }}</strong></p>
        <p>{{ $company['address'] }}, {{ $company['city'] }}, {{ $company['state'] }} {{ $company['zip'] }}</p>
        <p>Phone: {{ $company['phone'] }} | Email: {{ $company['email'] }}</p>
        @if($company['website'])
        <p>Website: {{ $company['website'] }}</p>
        @endif

        <p style="margin-top: 20px; font-size: 11px;">
            This is an automated email. Please do not reply to this email address.
            If you have any questions, please contact us using the information above.
        </p>
    </div>
</body>
</html>