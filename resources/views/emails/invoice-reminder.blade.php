<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $reminderType === 'overdue' ? 'OVERDUE' : 'Payment Reminder' }} - {{ $invoice->invoice_number }}</title>
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
            background-color: {{ $reminderType === 'overdue' ? '#e74c3c' : '#f39c12' }};
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
        .alert-message {
            background-color: {{ $reminderType === 'overdue' ? '#f8d7da' : '#fff3cd' }};
            color: {{ $reminderType === 'overdue' ? '#721c24' : '#856404' }};
            padding: 20px;
            border-radius: 5px;
            text-align: center;
            margin: 20px 0;
            border: 1px solid {{ $reminderType === 'overdue' ? '#f5c6cb' : '#ffeaa7' }};
        }
        .invoice-details {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid {{ $reminderType === 'overdue' ? '#e74c3c' : '#f39c12' }};
        }
        .amount {
            font-size: 24px;
            font-weight: bold;
            color: {{ $reminderType === 'overdue' ? '#e74c3c' : '#f39c12' }};
            text-align: center;
            margin: 20px 0;
            padding: 15px;
            background-color: {{ $reminderType === 'overdue' ? '#fff5f5' : '#fffbf0' }};
            border-radius: 5px;
        }
        .button {
            display: inline-block;
            background-color: #27ae60;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 10px 5px;
            font-size: 16px;
        }
        .button.urgent {
            background-color: #e74c3c;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
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
        .warning-icon {
            font-size: 48px;
            color: {{ $reminderType === 'overdue' ? '#e74c3c' : '#f39c12' }};
            text-align: center;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $company['name'] }}</h1>
        <p>{{ $reminderType === 'overdue' ? '⚠️ OVERDUE INVOICE' : '📅 Payment Reminder' }}</p>
    </div>

    <div class="content">
        <div class="warning-icon">{{ $reminderType === 'overdue' ? '⚠️' : '📅' }}</div>

        <div class="alert-message">
            @if($reminderType === 'overdue')
                <h2 style="margin: 0 0 10px 0;">URGENT: Payment Overdue</h2>
                <p style="margin: 0;">This invoice is {{ $daysOverdue }} day{{ $daysOverdue !== 1 ? 's' : '' }} overdue. Immediate payment is required.</p>
            @else
                <h2 style="margin: 0 0 10px 0;">Payment Reminder</h2>
                <p style="margin: 0;">This invoice is due in {{ $daysTillDue }} day{{ $daysTillDue !== 1 ? 's' : '' }}. Please arrange payment to avoid late fees.</p>
            @endif
        </div>

        <h2>Hello {{ $invoice->agency->agency_name }},</h2>

        @if($reminderType === 'overdue')
            <p><strong>This is an urgent notice regarding an overdue payment.</strong> Our records indicate that the following invoice remains unpaid despite being past its due date.</p>

            <p>To avoid any potential service disruptions or additional late fees, please arrange immediate payment for this invoice.</p>
        @else
            <p>We hope this email finds you well. This is a friendly reminder that the following invoice will be due soon.</p>

            <p>To ensure uninterrupted service and avoid any late fees, please arrange payment by the due date.</p>
        @endif

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
                    <td style="padding: 8px 0; {{ $reminderType === 'overdue' ? 'color: #e74c3c; font-weight: bold;' : '' }}">
                        {{ $invoice->due_date->format('M d, Y') }}
                        @if($reminderType === 'overdue')
                            <span style="color: #e74c3c;">({{ $daysOverdue }} days ago)</span>
                        @endif
                    </td>
                </tr>
                @if($invoice->title)
                <tr>
                    <td style="padding: 8px 0; font-weight: bold;">Description:</td>
                    <td style="padding: 8px 0;">{{ $invoice->title }}</td>
                </tr>
                @endif
                <tr>
                    <td style="padding: 8px 0; font-weight: bold;">Outstanding Balance:</td>
                    <td style="padding: 8px 0; color: {{ $reminderType === 'overdue' ? '#e74c3c' : '#f39c12' }}; font-weight: bold; font-size: 16px;">
                        ${{ number_format($invoice->balance, 2) }}
                    </td>
                </tr>
            </table>
        </div>

        <div class="amount">
            Amount Due: ${{ number_format($invoice->balance, 2) }}
            @if($reminderType === 'overdue')
                <br><small style="font-size: 14px;">OVERDUE - IMMEDIATE PAYMENT REQUIRED</small>
            @endif
        </div>

        <div class="actions">
            <a href="{{ $paymentUrl }}" class="button {{ $reminderType === 'overdue' ? 'urgent' : '' }}">
                {{ $reminderType === 'overdue' ? 'PAY NOW - URGENT' : 'Pay Now' }}
            </a>
            <a href="{{ $invoiceUrl }}" class="button" style="background-color: #95a5a6;">View Invoice</a>
        </div>

        <div style="background-color: {{ $reminderType === 'overdue' ? '#f8d7da' : '#d1ecf1' }}; padding: 20px; border-radius: 5px; margin: 20px 0;">
            <h4 style="margin-top: 0;">Payment Options:</h4>
            <ul style="margin-bottom: 0;">
                <li>Online payment via credit/debit card (fastest)</li>
                <li>Bank transfer or ACH</li>
                <li>Contact us for alternative payment arrangements</li>
            </ul>
        </div>

        @if($reminderType === 'overdue')
            <div style="background-color: #fff3cd; padding: 20px; border-radius: 5px; margin: 20px 0; border: 1px solid #ffeaa7;">
                <h4 style="margin-top: 0; color: #856404;">⚠️ Important Notice:</h4>
                <ul style="margin-bottom: 0; color: #856404;">
                    <li>Late fees may apply to overdue invoices</li>
                    <li>Continued non-payment may result in service suspension</li>
                    <li>Contact us immediately if you're experiencing payment difficulties</li>
                    <li>We're here to work with you on payment arrangements if needed</li>
                </ul>
            </div>
        @endif

        <p><strong>Need Help?</strong></p>
        <p>If you have any questions about this invoice, need to discuss payment arrangements, or believe this notice was sent in error, please contact us immediately:</p>
        <ul>
            <li>Email: {{ $company['email'] }}</li>
            <li>Phone: {{ $company['phone'] }}</li>
        </ul>
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