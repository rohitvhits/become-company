<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>File Upload Notification</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: {{ $folderType === 'MDO' ? '#6f42c1' : '#17a2b8' }}; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
        .header h2 { margin: 0; font-size: 20px; }
        .header p { margin: 5px 0 0; font-size: 13px; opacity: 0.85; }
        .content { background-color: #f8f9fa; padding: 25px; border-radius: 0 0 5px 5px; border: 1px solid #dee2e6; border-top: none; }
        .detail-table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 5px; overflow: hidden; margin-bottom: 20px; }
        .detail-table td { padding: 10px 14px; border-bottom: 1px solid #f0f0f0; font-size: 14px; }
        .detail-table td:first-child { font-weight: 600; color: #555; width: 38%; background: #f8f9fa; }
        .detail-table tr:last-child td { border-bottom: none; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 3px; font-size: 12px; font-weight: 600; color: #fff; background: {{ $folderType === 'MDO' ? '#6f42c1' : '#17a2b8' }}; }
        .footer { text-align: center; font-size: 12px; color: #aaa; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h2><span class="badge">{{ $folderType }}</span> &nbsp;{{ $fileCount > 1 ? $fileCount . ' Files Uploaded' : 'New File Uploaded' }}</h2>
        <p>{{ config('app.name') }} — File Manager</p>
    </div>
    <div class="content">
        @if($fileCount > 1)
            <p><strong>{{ $fileCount }} files</strong> have been uploaded to a <strong>{{ $folderType }}</strong> folder. Details of the last uploaded file are below:</p>
        @else
            <p>A new file has been uploaded to a <strong>{{ $folderType }}</strong> folder. Details are below:</p>
        @endif

        <table class="detail-table">
            @if($fileCount > 1)
            <tr>
                <td>Files Uploaded</td>
                <td><strong>{{ $fileCount }} file(s)</strong></td>
            </tr>
            @endif
            <tr>
                <td>{{ $fileCount > 1 ? 'Last File Name' : 'File Name' }}</td>
                <td><strong>{{ $file->file_name }}</strong></td>
            </tr>
            <tr>
                <td>File Type</td>
                <td>{{ strtoupper($file->file_type) }}</td>
            </tr>
            <tr>
                <td>File Size</td>
                <td>
                    @if($file->file_size >= 1048576)
                        {{ number_format($file->file_size / 1048576, 2) }} MB
                    @elseif($file->file_size >= 1024)
                        {{ number_format($file->file_size / 1024, 2) }} KB
                    @else
                        {{ $file->file_size }} B
                    @endif
                </td>
            </tr>
            <tr>
                <td>Folder Path</td>
                <td>{{ $folderPath ?: 'Root' }}</td>
            </tr>
            <tr>
                <td>Agency</td>
                <td>{{ $agencyName }}</td>
            </tr>
            <tr>
                <td>Uploaded By</td>
                <td>{{ $uploaderName }}</td>
            </tr>
            <tr>
                <td>Uploaded At</td>
                <td>{{ \Carbon\Carbon::parse($uploadedAt)->format('M d, Y h:i A') }}</td>
            </tr>
        </table>

        <p style="font-size:13px; color:#666;">This is an automated notification from {{ config('app.name') }} File Manager.</p>
    </div>
    <div class="footer">
        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
    </div>
</body>
</html>
