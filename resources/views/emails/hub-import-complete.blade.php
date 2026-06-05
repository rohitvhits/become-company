<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hub Record Import {{ ucfirst($status) }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 620px; margin: 0 auto; padding: 20px; background: #f4f6f8; }
        .wrapper { background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .header { padding: 20px 28px; text-align: center; background: #000000; }
        .header img { width: 190px; vertical-align: middle; }
        .subheader { padding: 20px 28px 4px; }
        .subheader-completed h2 { color: #0e4902; }
        .subheader-failed    h2 { color: #e53935; }
        .subheader h2 { margin: 0; font-size: 20px; font-family: Arial, sans-serif; line-height: 20px; }
        .subheader p  { margin: 6px 0 0; font-size: 13px; color: #777; }
        .content { padding: 28px; }
        .greeting { font-size: 15px; margin-bottom: 18px; }
        .summary-grid { display: table; width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        .summary-row  { display: table-row; }
        .summary-cell { display: table-cell; padding: 10px 14px; border-bottom: 1px solid #f0f0f0; font-size: 14px; vertical-align: middle; }
        .summary-cell:first-child { font-weight: 600; color: #555; width: 42%; background: #f8f9fa; }
        .num-success { color: #43a047; font-weight: 700; font-size: 16px; }
        .num-failed  { color: #e53935; font-weight: 700; font-size: 16px; }
        .num-updated { color: #1a73e8; font-weight: 700; font-size: 16px; }
        .num-deactivated { color: #f57c00; font-weight: 700; font-size: 16px; }
        .num-total   { font-weight: 700; font-size: 16px; }
        .error-box { background: #fff3f3; border: 1px solid #ffcdd2; border-radius: 5px; padding: 14px 16px; margin-top: 20px; }
        .error-box h4 { margin: 0 0 8px; color: #c62828; font-size: 14px; }
        .error-box pre { margin: 0; font-size: 12px; color: #555; white-space: pre-wrap; word-break: break-word; max-height: 200px; overflow: auto; }
        .footer { text-align: center; font-size: 12px; color: #aaa; padding: 16px 28px 20px; border-top: 1px solid #f0f0f0; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <img src="{{ asset('img/logo-ny.png') }}" alt="NY BEST MEDICAL">
    </div>
    <div class="subheader subheader-{{ $status }}">
        <h2><b>Hub Record Import {{ $status === 'completed' ? 'Completed.' : 'Failed.' }}</b></h2>
    </div>

    <div class="content">
        <p class="greeting">Hi <strong>{{ $userName }}</strong>,</p>

        @if($status === 'completed')
            <p>Your hub record import has been <strong>completed successfully</strong>. Here is a summary of the results:</p>
        @else
            <p>Your hub record import has <strong>failed</strong>. Please review the error details below and try again.</p>
        @endif

        <table class="summary-grid">
            <tr class="summary-row">
                <td class="summary-cell">File Name</td>
                <td class="summary-cell"><strong>{{ $fileName }}</strong></td>
            </tr>
            <tr class="summary-row">
                <td class="summary-cell">Imported At</td>
                <td class="summary-cell">{{ $importedAt }}</td>
            </tr>
            <tr class="summary-row">
                <td class="summary-cell">Total Records</td>
                <td class="summary-cell"><span class="num-total">{{ number_format($total) }}</span></td>
            </tr>
            @if($status === 'completed')
            <tr class="summary-row">
                <td class="summary-cell">Successfully Imported</td>
                <td class="summary-cell"><span class="num-success">{{ number_format($success) }}</span></td>
            </tr>
            <tr class="summary-row">
                <td class="summary-cell">Updated</td>
                <td class="summary-cell"><span class="num-updated">{{ number_format($updated) }}</span></td>
            </tr>
            <tr class="summary-row">
                <td class="summary-cell">Deactivated</td>
                <td class="summary-cell"><span class="num-deactivated">{{ number_format($deactivated) }}</span></td>
            </tr>
            <tr class="summary-row">
                <td class="summary-cell">Failed / Skipped</td>
                <td class="summary-cell"><span class="num-failed">{{ number_format($failed) }}</span></td>
            </tr>
            @endif
        </table>

        @if(!empty($errorDetails))
        <div class="error-box">
            <h4>{{ $status === 'completed' ? 'Row Errors / Warnings' : 'Error Details' }}</h4>
            <pre>{{ is_array(json_decode($errorDetails, true)) ? implode("\n", json_decode($errorDetails, true)) : $errorDetails }}</pre>
        </div>
        @endif

        <p style="font-size:13px; color:#777; margin-top:20px;">
            This is an automated notification from {{ config('app.name') }}. Please do not reply to this email.
        </p>
    </div>

    <div class="footer">
        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
    </div>
</div>
</body>
</html>
