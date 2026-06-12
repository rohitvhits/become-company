<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daily eSign SMS Report - {{ $reportDate }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #eef2f7;
            color: #333;
        }
        .container {
            max-width: 960px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        .header {
            background: linear-gradient(135deg, #1a73e8, #0d47a1);
            color: #ffffff;
            text-align: center;
            padding: 28px 30px;
        }
        .header h1 {
            margin: 0 0 6px;
            font-size: 24px;
            font-weight: 600;
            letter-spacing: 0.3px;
        }
        .header p {
            margin: 0;
            font-size: 13px;
            opacity: 0.85;
        }
        .body-content {
            padding: 30px;
        }

        /* Summary Cards */
        .summary-grid {
            display: table;
            width: 100%;
            border-spacing: 12px 0;
            margin-bottom: 28px;
        }
        .summary-grid-row {
            display: table-row;
        }
        .summary-card {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 18px 12px;
            border-radius: 10px;
            vertical-align: middle;
        }
        .summary-card .count {
            font-size: 32px;
            font-weight: 700;
            line-height: 1;
            display: block;
        }
        .summary-card .label {
            font-size: 11px;
            margin-top: 6px;
            display: block;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            font-weight: 600;
        }
        .card-total {
            background-color: #e8f0fe;
            color: #1a73e8;
        }
        .card-success {
            background-color: #e6f4ea;
            color: #1e8e3e;
        }
        .card-failed {
            background-color: #fce8e6;
            color: #d93025;
        }

        /* Section Label */
        .section-label {
            font-size: 15px;
            font-weight: 600;
            color: #1a73e8;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e8f0fe;
        }

        /* Table */
        .data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 13px;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e0e0e0;
        }
        .data-table th {
            background-color: #1a73e8;
            color: #fff;
            font-weight: 600;
            padding: 12px 14px;
            text-align: left;
            white-space: nowrap;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .data-table td {
            padding: 10px 14px;
            text-align: left;
            vertical-align: top;
            border-bottom: 1px solid #f0f0f0;
        }
        .data-table tr:last-child td {
            border-bottom: none;
        }
        .data-table tr:nth-child(even) td {
            background-color: #fafbfc;
        }
        .data-table tr:hover td {
            background-color: #f0f6ff;
        }
        .data-table tr.row-failed td {
            background-color: #fff0ee;
            border-bottom-color: #fce8e6;
        }
        .data-table tr.row-failed:hover td {
            background-color: #fce8e6;
        }

        /* Status Badges */
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            white-space: nowrap;
            text-transform: capitalize;
        }
        .badge-success {
            background-color: #e6f4ea;
            color: #1e8e3e;
        }
        .badge-failed {
            background-color: #fce8e6;
            color: #d93025;
        }
        .badge-pending {
            background-color: #fef7e0;
            color: #e37400;
        }
        .badge-default {
            background-color: #f1f3f4;
            color: #5f6368;
        }

        /* No Records */
        .no-records {
            text-align: center;
            padding: 40px 20px;
            color: #80868b;
        }
        .no-records .icon {
            font-size: 48px;
            display: block;
            margin-bottom: 12px;
        }
        .no-records .text {
            font-size: 16px;
            font-weight: 500;
            margin-bottom: 4px;
        }
        .no-records .sub-text {
            font-size: 13px;
        }

        /* Error column */
        .error-text {
            color: #d93025;
            font-size: 12px;
        }

        /* Footer */
        .footer {
            background-color: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #80868b;
            border-top: 1px solid #e8eaed;
        }
        .footer p {
            margin: 3px 0;
        }

        @media (max-width: 600px) {
            .summary-card { display: block; width: 100%; margin-bottom: 8px; }
            .data-table { font-size: 11px; }
            .data-table th, .data-table td { padding: 8px 10px; }
            .body-content { padding: 20px 15px; }
        }
    </style>
</head>
<body>
<div class="container">

    <div class="header">
        <h1>Daily eSign SMS Report</h1>
        <p>Report Date: {{ $reportDate }} &nbsp;&bull;&nbsp; Generated: {{ date('m/d/Y h:i A') }}</p>
    </div>

    <div class="body-content">

        {{-- Summary Cards --}}
        <div class="summary-grid">
            <div class="summary-grid-row">
                <div class="summary-card card-total">
                    <span class="count">{{ $totalCount }}</span>
                    <span class="label">Total Records</span>
                </div>
                <div class="summary-card card-success">
                    <span class="count">{{ collect($rows)->where('sms_status', '!=', '-')->where('sms_status', '!=', 'failed')->count() }}</span>
                    <span class="label">SMS Sent</span>
                </div>
                <div class="summary-card card-failed">
                    <span class="count">{{ collect($rows)->filter(fn($r) => $r['sms_status'] === '-' || $r['sms_status'] === 'failed')->count() }}</span>
                    <span class="label">Failed / Pending</span>
                </div>
            </div>
        </div>

        @if($totalCount > 0)
            <div class="section-label">Record Details ({{ $totalCount }})</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Mobile</th>
                        <th>Message</th>
                        <th>SMS Status</th>
                        <th>SMS Date</th>
                        <th>Created Date</th>
                        <th>Error Message</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $i => $row)
                    @php
                        $isFailed = in_array(strtolower($row['sms_status'] ?? ''), ['failed', 'undelivered']);
                    @endphp
                    <tr class="{{ $isFailed ? 'row-failed' : '' }}">
                        <td>{{ $i + 1 }}</td>
                        <td><strong>{{ $row['id'] }}</strong></td>
                        <td>{{ $row['name'] ?: '-' }}</td>
                        <td style="white-space:nowrap;">{{ $row['mobile'] }}</td>
                        <td>{{ $row['message'] }}</td>
                        <td>
                            @php
                                $status = strtolower($row['sms_status'] ?? '');
                                $badgeClass = 'badge-default';
                                if (in_array($status, ['sent', 'delivered', 'success', 'queued'])) {
                                    $badgeClass = 'badge-success';
                                } elseif (in_array($status, ['failed', 'undelivered'])) {
                                    $badgeClass = 'badge-failed';
                                } elseif (in_array($status, ['pending', '-'])) {
                                    $badgeClass = 'badge-pending';
                                }
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ $row['sms_status'] }}</span>
                        </td>
                        <td style="white-space:nowrap;">{{ $row['sms_date'] }}</td>
                        <td style="white-space:nowrap;">{{ $row['created_date'] }}</td>
                        <td>
                            @if(!empty($row['error_message']))
                                <span class="error-text">{{ $row['error_message'] }}</span>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-records">
                <span class="icon">&#128233;</span>
                <div class="text">No Records Found</div>
                <div class="sub-text">No records with import_status "Success" were found for {{ $reportDate }}.</div>
            </div>
        @endif

    </div>

    <div class="footer">
        <p>This report was automatically generated by the NYBEST ERP system.</p>
        <p>NY Best Medical &mdash; Daily eSign SMS Report</p>
    </div>

</div>
</body>
</html>
