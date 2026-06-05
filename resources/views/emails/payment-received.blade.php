<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment Received - {{ $invoice->invoice_number }}</title>
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
            background-color: #27ae60;
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
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
            margin: 20px 0;
            border: 1px solid #c3e6cb;
        }
        .payment-details {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #27ae60;
        }
        .amount {
            font-size: 24px;
            font-weight: bold;
            color: #27ae60;
            text-align: center;
            margin: 20px 0;
            padding: 15px;
            background-color: #f0f9f0;
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
        .checkmark {
            font-size: 48px;
            color: #27ae60;
            text-align: center;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $company['name'] }}</h1>
        <p>Payment Confirmation</p>
    </div>

    <div class="content">
        <div class="checkmark">✓</div>

        <div class="success-message">
            <h2 style="margin: 0 0 10px 0;">Payment Received Successfully!</h2>
            <p style="margin: 0;">Thank you for your prompt payment.</p>
        </div>

        <h2>Hello {{ $invoice->agency->agency_name }},</h2>

        <p>We're pleased to confirm that we have received your payment for invoice {{ $invoice->invoice_number }}. Here are the details of your transaction:</p>

        <div class="payment-details">
            <h3>Payment Details</h3>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px 0; font-weight: bold;">Payment Amount:</td>
                    <td style="padding: 8px 0; color: #27ae60; font-weight: bold;">${{ number_format($payment->amount, 2) }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: bold;">Payment Date:</td>
                    <td style="padding: 8px 0;">{{ $payment->paid_at->format('M d, Y H:i A') }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: bold;">Payment Method:</td>
                    <td style="padding: 8px 0;">{{ $payment->payment_method_label }}</td>
                </tr>
                @if($payment->transaction_id)
                <tr>
                    <td style="padding: 8px 0; font-weight: bold;">Transaction ID:</td>
                    <td style="padding: 8px 0;">{{ $payment->transaction_id }}</td>
                </tr>
                @endif
                <tr>
                    <td style="padding: 8px 0; font-weight: bold;">Invoice Number:</td>
                    <td style="padding: 8px 0;">{{ $invoice->invoice_number }}</td>
                </tr>
            </table>
        </div>

        <div class="payment-details">
            <h3>Invoice Summary</h3>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px 0; font-weight: bold;">Original Amount:</td>
                    <td style="padding: 8px 0;">${{ number_format($invoice->total_amount, 2) }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: bold;">Total Paid:</td>
                    <td style="padding: 8px 0; color: #27ae60;">${{ number_format($invoice->total_paid, 2) }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: bold;">Balance Remaining:</td>
                    <td style="padding: 8px 0; {{ $invoice->balance > 0 ? 'color: #e74c3c;' : 'color: #27ae60;' }}">
                        ${{ number_format($invoice->balance, 2) }}
                        @if($invoice->balance <= 0)
                            <span style="color: #27ae60; font-weight: bold;">✓ PAID IN FULL</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        <div class="actions">
            <a href="{{ $receiptUrl }}" class="button">Download Receipt</a>
            <a href="{{ $invoiceUrl }}" class="button">View Invoice</a>
        </div>

        <p><strong>What's Next?</strong></p>
        <ul>
            @if($invoice->balance <= 0)
                <li>Your invoice has been paid in full - no further action required</li>
                <li>You can download your payment receipt using the button above</li>
                <li>Keep this email and receipt for your accounting records</li>
            @else
                <li>A balance of ${{ number_format($invoice->balance, 2) }} remains on this invoice</li>
                <li>You can make additional payments through your account portal</li>
                <li>Please contact us if you have any questions about the remaining balance</li>
            @endif
        </ul>

        <div style="background-color: #e8f6f3; padding: 15px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #27ae60;">
            <p style="margin: 0;"><strong>Important:</strong> Please keep this confirmation email and your payment receipt for your records. If you need any additional documentation, please contact our billing department.</p>
        </div>
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