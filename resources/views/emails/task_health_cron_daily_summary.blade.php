<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Task Health HHA Link - Daily Summary - {{ $reportDate }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
            color: #333;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.08);
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #007bff;
            padding-bottom: 18px;
            margin-bottom: 25px;
        }
        .header h1 {
            margin: 0 0 6px;
            font-size: 22px;
            color: #007bff;
        }
        .header p {
            margin: 0;
            color: #6c757d;
            font-size: 14px;
        }
        /* Summary boxes */
        .summary-grid {
            display: table;
            width: 100%;
            border-spacing: 10px;
            margin-bottom: 28px;
        }
        .summary-grid-row {
            display: table-row;
        }
        .summary-box {
            display: table-cell;
            width: 20%;
            text-align: center;
            padding: 16px 10px;
            border-radius: 6px;
            vertical-align: middle;
        }
        .summary-box .count {
            font-size: 30px;
            font-weight: bold;
            line-height: 1;
            display: block;
        }
        .summary-box .label {
            font-size: 12px;
            margin-top: 4px;
            display: block;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .box-total   { background-color: #e7f3ff; color: #0056b3; }
        .box-success { background-color: #d4edda; color: #155724; }
        .box-error   { background-color: #f8d7da; color: #721c24; }
        .box-skipped { background-color: #fff3cd; color: #856404; }
        .box-other   { background-color: #e2e3e5; color: #383d41; }

        /* Section */
        .section {
            margin-bottom: 28px;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            padding: 8px 12px;
            border-radius: 4px;
            margin-bottom: 12px;
        }
        .section-title.error   { background-color: #f8d7da; color: #721c24; }
        .section-title.success { background-color: #d4edda; color: #155724; }
        .section-title.skipped { background-color: #fff3cd; color: #856404; }

        /* Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        .data-table th,
        .data-table td {
            border: 1px solid #dee2e6;
            padding: 9px 11px;
            text-align: left;
            vertical-align: top;
        }
        .data-table th {
            font-weight: bold;
            white-space: nowrap;
        }
        .data-table thead.error-head th   { background-color: #c82333; color: #fff; }
        .data-table thead.success-head th { background-color: #28a745; color: #fff; }
        .data-table thead.skipped-head th { background-color: #d39e00; color: #fff; }
        .data-table tr:nth-child(even) td { background-color: #f8f9fa; }
        .data-table td .muted { font-size: 11px; color: #6c757d; display: block; }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: bold;
            white-space: nowrap;
        }
        .badge-danger  { background-color: #dc3545; color: #fff; }
        .badge-success { background-color: #28a745; color: #fff; }
        .badge-warning { background-color: #ffc107; color: #212529; }
        .no-records {
            color: #6c757d;
            font-style: italic;
            padding: 10px 0;
        }
        /* Footer */
        .footer {
            margin-top: 30px;
            padding-top: 18px;
            border-top: 2px solid #e9ecef;
            font-size: 12px;
            color: #6c757d;
            text-align: center;
        }
        @media (max-width: 600px) {
            .summary-box { display: block; width: 100%; margin-bottom: 8px; }
            .data-table { font-size: 12px; }
        }
    </style>
</head>
<body>
<div class="container">

    {{-- Header --}}
    <div class="header">
        <h1>Task Health HHA Link &mdash; Daily Summary</h1>
        <p>Report Date: <strong>{{ $reportDate }}</strong> &nbsp;|&nbsp; Generated: {{ date('m/d/Y h:i A') }}</p>
    </div>

    {{-- Summary boxes --}}
    <div class="summary-grid">
        <div class="summary-grid-row">
            <div class="summary-box box-total">
                <span class="count">{{ $summary['total'] }}</span>
                <span class="label">Total</span>
            </div>
            <div class="summary-box box-success">
                <span class="count">{{ $summary['success'] }}</span>
                <span class="label">Success</span>
            </div>
            <div class="summary-box box-error">
                <span class="count">{{ $summary['error'] }}</span>
                <span class="label">Error</span>
            </div>
            <div class="summary-box box-skipped">
                <span class="count">{{ $summary['skipped'] }}</span>
                <span class="label">Skipped</span>
            </div>
            @if($summary['other'] > 0)
            <div class="summary-box box-other">
                <span class="count">{{ $summary['other'] }}</span>
                <span class="label">Other</span>
            </div>
            @endif
        </div>
    </div>

    {{-- Error records --}}
    <div class="section">
        <div class="section-title error">
            Error Records ({{ count($errorRows) }})
        </div>
        @if(count($errorRows) > 0)
        <table class="data-table">
            <thead class="error-head">
                <tr>
                    <th>#</th>
                    <th>Task ID</th>
                    <th>Patient</th>
                    <th>Agency</th>
                    <th>Message</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                @foreach($errorRows as $i => $row)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>
                        @if(!empty($row['task_id']))
                            #{{ $row['task_id'] }}
                        @elseif(!empty($row['task_health_id']))
                            <span class="muted">#{{ $row['task_health_id'] }}</span>
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if(!empty($row['patient_name']))
                            {{ $row['patient_name'] }}
                            @if(!empty($row['patient_id']))
                                <span class="muted">#{{ $row['patient_id'] }}</span>
                            @endif
                        @elseif(!empty($row['patient_id']))
                            #{{ $row['patient_id'] }}
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $row['agency_name'] ?? '-' }}</td>
                    <td>{{ $row['message'] ?? '-' }}</td>
                    <td style="white-space:nowrap;">{{ $row['created_at'] ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
            <p class="no-records">No error records for this date.</p>
        @endif
    </div>

    {{-- Success records --}}
    <div class="section">
        <div class="section-title success">
            Success Records ({{ count($successRows) }})
        </div>
        @if(count($successRows) > 0)
        <table class="data-table">
            <thead class="success-head">
                <tr>
                    <th>#</th>
                    <th>Task ID</th>
                    <th>Patient</th>
                    <th>Agency</th>
                    <th>Message</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                @foreach($successRows as $i => $row)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>
                        @if(!empty($row['task_id']))
                            #{{ $row['task_id'] }}
                        @elseif(!empty($row['task_health_id']))
                            <span class="muted">#{{ $row['task_health_id'] }}</span>
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if(!empty($row['patient_name']))
                            {{ $row['patient_name'] }}
                            @if(!empty($row['patient_id']))
                                <span class="muted">#{{ $row['patient_id'] }}</span>
                            @endif
                        @elseif(!empty($row['patient_id']))
                            #{{ $row['patient_id'] }}
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $row['agency_name'] ?? '-' }}</td>
                    <td>{{ $row['message'] ?? '-' }}</td>
                    <td style="white-space:nowrap;">{{ $row['created_at'] ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
            <p class="no-records">No success records for this date.</p>
        @endif
    </div>

    {{-- Skipped records --}}
    @if(count($skippedRows) > 0)
    <div class="section">
        <div class="section-title skipped">
            Skipped Records ({{ count($skippedRows) }})
        </div>
        <table class="data-table">
            <thead class="skipped-head">
                <tr>
                    <th>#</th>
                    <th>Task ID</th>
                    <th>Patient</th>
                    <th>Agency</th>
                    <th>Message</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                @foreach($skippedRows as $i => $row)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>
                        @if(!empty($row['task_id']))
                            #{{ $row['task_id'] }}
                        @elseif(!empty($row['task_health_id']))
                            <span class="muted">#{{ $row['task_health_id'] }}</span>
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if(!empty($row['patient_name']))
                            {{ $row['patient_name'] }}
                            @if(!empty($row['patient_id']))
                                <span class="muted">#{{ $row['patient_id'] }}</span>
                            @endif
                        @elseif(!empty($row['patient_id']))
                            #{{ $row['patient_id'] }}
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $row['agency_name'] ?? '-' }}</td>
                    <td>{{ $row['message'] ?? '-' }}</td>
                    <td style="white-space:nowrap;">{{ $row['created_at'] ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        <p>This report was automatically generated by the NYBEST ERP system.</p>
        <p>NY Best Medical &mdash; Task Health HHA Link Daily Summary</p>
    </div>

</div>
</body>
</html>
