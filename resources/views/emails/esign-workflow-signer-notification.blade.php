<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Document Signature Required</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #00879E; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
        .header h2 { margin: 0; font-size: 20px; }
        .content { background-color: #f8f9fa; padding: 25px; border-radius: 0 0 5px 5px; border: 1px solid #dee2e6; border-top: none; }
        .detail-table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 5px; overflow: hidden; margin-bottom: 20px; }
        .detail-table td { padding: 10px 14px; border-bottom: 1px solid #f0f0f0; font-size: 14px; }
        .detail-table td:first-child { font-weight: 600; color: #555; width: 38%; background: #f8f9fa; }
        .detail-table tr:last-child td { border-bottom: none; }
        .btn { display: inline-block; padding: 10px 24px; background-color: #00879E; color: #fff; text-decoration: none; border-radius: 5px; font-weight: 600; }
        .footer { text-align: center; font-size: 12px; color: #aaa; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Document Awaiting Your Signature</h2>
    </div>
    <div class="content">
        <p>Hello <strong>{{ $signerName }}</strong>,</p>
        <p>A document requires your signature. The previous signer has completed their part.</p>

        <table class="detail-table">
            <tr>
                <td>Document Name</td>
                <td><strong>{{ $documentName }}</strong></td>
            </tr>
            
        </table>

        <p style="text-align: center;">
            <a href="{{ $actionUrl }}" class="btn">Review &amp; Sign Document</a>
        </p>

        <p style="font-size:13px; color:#666;">This is an automated notification from {{ config('app.name') }}.</p>
    </div>
    <div class="footer">
        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
    </div>
</body>
</html>
