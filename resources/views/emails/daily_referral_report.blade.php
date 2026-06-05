<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daily Referral Report - {{ $report_date }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }

        .containeremail {
            max-width: 800px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            color: #333;
            border-bottom: 3px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .summary {
            background-color: #e7f3ff;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 5px solid #007bff;
        }

        .section {
            margin-bottom: 25px;
        }

        .section-title {
            color: #007bff;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 5px;
        }

        .forms-breakdown {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }

        .form-item {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
        }

        .form-name {
            font-weight: 500;
        }

        .form-count {
            font-weight: bold;
            color: #007bff;
        }

        .highlight {
            background-color: #fff3cd;
            padding: 15px;
            border-radius: 5px;
            border-left: 5px solid #ffc107;
            margin: 15px 0;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .table th,
        .table td {
            border: 1px solid #dee2e6;
            padding: 12px;
            text-align: left;
        }

        .table th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }

        .table tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e9ecef;
            font-size: 14px;
            color: #6c757d;
            text-align: center;
        }

        .important-note {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            font-weight: bold;
        }

        .agency-highlight {
            color: #007bff;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="containeremail">
        <div class="header">
            <h1>New Services Requested & Charts Created</h1>
            <h2>Daily Report for {{ $report_date }}</h2>
        </div>

        <div class="summary">
            <h3>📊 Daily Summary</h3>
            <p><strong>Total of {{ number_format($total_portal_updates) }} New Requests</strong> received with
                <strong>{{ number_format($total_new_charts) }} New Charts</strong> created on the portal and <strong>{{
                    number_format($total_forms_requested) }} Forms</strong> Requested
            </p>
        </div>

        @if($show_forms_breakdown ?? true)
        <div class="section">
            <div class="section-title">🔄 Break down of the forms included in the new charts created :</div>
            <div class="summary" style="background-color: #f0f8ff;">
                <p><strong>Total of {{ number_format($total_portal_updates) }} new charts created</strong> as follows:
                </p>
            </div>
            <div class="portal-processing">
                <table class="table">
                    <tbody>
                        @foreach($portal_processing as $status => $count)
                        <tr>
                            <td> {{ $status }}</td>
                            <td>{{ number_format($count) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
        @if($show_referral_sources ?? true)
        <div class="section">
            <div class="section-title">🔄 Break down of where each of these referrals came from :</div>
            <p><em>Where did the referral originate from for each of these portals:</em></p>
            <table class="table">
                <thead>
                    <tr>
                        <th>Referral Type</th>
                        <th>Count of Full Name</th>
                    </tr>
                </thead>
                <tbody>
                    @php $referralBreakdownTotal=0; @endphp
                    @foreach($referral_breakdown as $type => $count)
                    @php $referralBreakdownTotal += $count; @endphp
                    <tr>
                        <td>{{ $type }}</td>
                        <td>{{ number_format($count) }}</td>
                    </tr>
                    @endforeach
                    <tr style="background-color: #e9ecef; font-weight: bold;">
                        <td><strong>Grand Total</strong></td>
                        <td><strong>{{ number_format($referralBreakdownTotal) }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
        @endif
        @if($show_resolution ?? true)
        <div class="section">
            <div class="section-title">✅ The Resolution of each of those charts as of today :</div>
            <p><em>What action was taken on each of those created charts by the time this report is generated:</em></p>
            <table class="table">
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Count of Full Name</th>
                    </tr>
                </thead>
                <tbody>
                    @php $resolutionBreakdownTotal=0; @endphp
                    @foreach($resolution_breakdown as $status => $count)
                    @php $resolutionBreakdownTotal += $count; @endphp
                    <tr>
                        <td>{{ $status }}</td>
                        <td>{{ number_format($count) }}</td>
                    </tr>
                    @endforeach
                    <tr style="background-color: #e9ecef; font-weight: bold;">
                        <td><strong>Grand Total</strong></td>
                        <td><strong>{{ number_format($resolutionBreakdownTotal) }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
        @endif
        @if($show_requests_per_agency ?? true)
        <div class="section">
            <div class="section-title">🏢 New Requests Per Agency</div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Agency Name</th>
                        <th>Count of Full Name</th>
                    </tr>
                </thead>
                <tbody>
                    @php $agencyRequestsTotal = 0; @endphp
                    @foreach($agency_requests as $agencyName => $count)
                    @php $agencyRequestsTotal += $count; @endphp
                    <tr>
                        <td>{{ $agencyName }}</td>
                        <td>{{ number_format($count) }}</td>
                    </tr>
                    @endforeach
                    <tr style="background-color: #e9ecef; font-weight: bold;">
                        <td><strong>Grand Total</strong></td>
                        <td><strong>{{ number_format($agencyRequestsTotal) }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
        @endif

        @if($show_portal_processing ?? true)
        <div class="section">
            <div class="section-title">🔄 Portal Processings: Total of {{ collect($portal_processing2)->sum() }} Updates
                were done as follows:</div>
            <div class="portal-processing">
                <table class="table">
                    <tbody>
                        @foreach($portal_processing2 as $status => $count)
                        <tr>
                            <td> {{ $status }}</td>
                            <td>{{ number_format($count) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        @if($show_highest_weight ?? true)
        @if($top_agencies_text)
        <div class="highlight">
            <strong>🏆 Highest Weight of New Requests:</strong><br>
            <span class="agency-highlight">{{ $top_agencies_text }}</span>
        </div>
        @endif
        @endif

        @if($show_outliers ?? true)
        <div class="section">
            <div class="section-title">📈 Outliers Based on Portal Processing</div>
            <p><em>Outliers based on the ratio of the total no. of portals changed to a specific status to the number of
                    portals in a specific agency:</em></p>

            <div class="highlight">
                <strong>🚫 Highest weight of Cancellations:</strong><br>
                <span class="agency-highlight">{{ $outliers['cancellations']['agency'] }} at {{
                    $outliers['cancellations']['percentage'] }} or {{ number_format($outliers['cancellations']['count'])
                    }} cancellations out of a total of {{ number_format($outliers['cancellations']['total']) }}.</span>
            </div>

            <div class="highlight">
                <strong>❌ Highest weight of Refusals:</strong><br>
                <span class="agency-highlight">{{ $outliers['refusals']['agency'] }} at {{
                    $outliers['refusals']['percentage'] }} or {{ number_format($outliers['refusals']['count']) }} out of
                    a total of {{ number_format($outliers['refusals']['total']) }} refusals.</span>
            </div>

            <div class="highlight">
                <strong>📞 Highest weight of 1st Attempt Unable to Contact:</strong><br>
                <span class="agency-highlight">{{ $outliers['first_attempt_unable_to_contact']['agency'] }} at {{
                    $outliers['first_attempt_unable_to_contact']['percentage'] }} or {{
                    number_format($outliers['first_attempt_unable_to_contact']['count']) }} out of a total of {{
                    number_format($outliers['first_attempt_unable_to_contact']['total']) }} 1st attempts Unable to
                    contact.</span>
                <br><small>(Was tried today and will be tried again for two more days)</small>
            </div>

            <div class="highlight">
                <strong>📞 Highest weight of 2nd Attempt Unable to Contact:</strong><br>
                <span class="agency-highlight">{{ $outliers['second_attempt_unable_to_contact']['agency'] }} at {{
                    $outliers['second_attempt_unable_to_contact']['percentage'] }} or {{
                    number_format($outliers['second_attempt_unable_to_contact']['count']) }} out of a total of {{
                    number_format($outliers['second_attempt_unable_to_contact']['total']) }} 2nd attempts Unable to
                    contact.</span>
                <br><small>(Was tried 1 day before, and was tried again today, will be tried again for 1 more
                    day)</small>
            </div>

            <div class="highlight">
                <strong>📞 Highest weight of 3rd Attempt Unable to Contact:</strong><br>
                <span class="agency-highlight">{{ $outliers['third_attempt_unable_to_contact']['agency'] }} at {{
                    $outliers['third_attempt_unable_to_contact']['percentage'] }} or {{
                    number_format($outliers['third_attempt_unable_to_contact']['count']) }} out of a total of {{
                    number_format($outliers['third_attempt_unable_to_contact']['total']) }} 3rd attempts Unable to
                    contact.</span>
                <br><small>(Was tried 2 days before, and was tried again today, will not be tried again)</small>
            </div>

            <div class="highlight">
                <strong>⚙️ Highest weight of Processing charts:</strong><br>
                <span class="agency-highlight">Only {{ number_format($outliers['processing_charts']['total']) }} charts
                    were marked as Processing and with only {{ $outliers['processing_charts']['agency'] }} that stood
                    out at {{ $outliers['processing_charts']['percentage'] }} out of those {{
                    number_format($outliers['processing_charts']['total']) }}.</span>
                <br><small>(T/H Completed within the past 30 days but pending NEW forms)</small>
            </div>

            <div class="highlight">
                <strong>🩺 Highest weight of MDO T/H visits completed:</strong><br>
                <span class="agency-highlight">{{ $outliers['mdo_telehealth_completed']['agency'] }} at {{
                    $outliers['mdo_telehealth_completed']['percentage'] }} or {{
                    number_format($outliers['mdo_telehealth_completed']['count']) }} completed MDO T/H visits out of a
                    total {{ number_format($outliers['mdo_telehealth_completed']['total']) }}.</span>
                <br><small>(MDO Specific)</small>
            </div>

            <div class="highlight">
                <strong>✍️ Highest weight of Signed MDO Forms:</strong><br>
                <span class="agency-highlight">{{ $outliers['signed_mdo_forms']['agency'] }} at {{
                    $outliers['signed_mdo_forms']['percentage'] }} or {{
                    number_format($outliers['signed_mdo_forms']['count']) }} out of a total of {{
                    number_format($outliers['signed_mdo_forms']['total']) }} signed MDOs.</span>
                <br><small>(MDO Specific)</small>
            </div>

            <div class="highlight">
                <strong>📤 Highest weight of Signed MDO Forms that were also sent back to the Agency:</strong><br>
                <span class="agency-highlight">{{ $outliers['signed_mdo_sent_back']['agency'] }} at {{
                    $outliers['signed_mdo_sent_back']['percentage'] }} or {{
                    number_format($outliers['signed_mdo_sent_back']['count']) }} of a total of {{
                    number_format($outliers['signed_mdo_sent_back']['total']) }} out of signed MDOs that were sent
                    back.</span>
                <br><small>(MDO Specific)</small>
            </div>

            <div class="highlight">
                <strong>✅ Highest weight of Forms Completed:</strong><br>
                <span class="agency-highlight">{{ $outliers['forms_completed']['agency'] }} at {{
                    $outliers['forms_completed']['percentage'] }} or {{
                    number_format($outliers['forms_completed']['count']) }} out of a total of {{
                    number_format($outliers['forms_completed']['total']) }} forms completed and uploaded to the
                    portal.</span>
                <br><small>(Non-MDO Specific - Forms signed and uploaded to the Portal)</small>
            </div>

            <div class="highlight">
                <strong>❌ Highest weight of missed appointments from the day before:</strong><br>
                <span class="agency-highlight">Only {{ number_format($outliers['missed_appointments']['total']) }}
                    charts were marked as missed appointments, with only {{ $outliers['missed_appointments']['agency']
                    }} standing out at {{ $outliers['missed_appointments']['percentage'] }} or {{
                    number_format($outliers['missed_appointments']['count']) }} out of those {{
                    number_format($outliers['missed_appointments']['total']) }}.</span>
                <br><small>(Non-MDO Specific)</small>
            </div>

            <div class="highlight">
                <strong>📅 Highest weight of Booking:</strong><br>
                <span class="agency-highlight">{{ $outliers['booking_non_mdo']['primary']['agency'] }} at {{
                    $outliers['booking_non_mdo']['primary']['percentage'] }} or {{
                    number_format($outliers['booking_non_mdo']['primary']['count']) }} out of a total of {{
                    number_format($outliers['booking_non_mdo']['primary']['total']) }} booked appointments, followed by
                    {{ $outliers['booking_non_mdo']['secondary']['agency'] }} at {{
                    $outliers['booking_non_mdo']['secondary']['percentage'] }} or {{
                    number_format($outliers['booking_non_mdo']['secondary']['count']) }} out of a total of {{
                    number_format($outliers['booking_non_mdo']['secondary']['total']) }} booked appointments.</span>
                <br><small>(Non-MDO Specific)</small>
            </div>
        </div>
        @endif



        @if($show_refusals_insights ?? true)
        <div class="section">
            <div class="section-title">📊 Refusals Insights</div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Refuse Reason</th>
                        <th>Count of Portal Name</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($insights['refusal_reasons'] as $reason => $count)
                    <tr>
                        <td>{{ $reason }}</td>
                        <td>{{ number_format($count) }}</td>
                    </tr>
                    @endforeach
                    <tr style="background-color: #e9ecef; font-weight: bold;">
                        <td><strong>Grand Total</strong></td>
                        <td><strong>{{ number_format(array_sum($insights['refusal_reasons'])) }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
        @endif

        @if($show_cancellations_insights ?? true)
        <div class="section">
            <div class="section-title">🚫 Cancellations Insights</div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Cancel Reason</th>
                        <th>Count of Portal Name</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($insights['cancellation_reasons'] as $reason => $count)
                    <tr>
                        <td>{{ $reason }}</td>
                        <td>{{ number_format($count) }}</td>
                    </tr>
                    @endforeach
                    <tr style="background-color: #e9ecef; font-weight: bold;">
                        <td><strong>Grand Total</strong></td>
                        <td><strong>{{ number_format(array_sum($insights['cancellation_reasons'])) }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
        @endif

        @if($show_non_mdo_forms ?? true)
        <div class="section">
            <div class="section-title">📋 Non-MDO Forms Completed Per Agency</div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Agency Name</th>
                        <th>Count of Resolution</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($insights['non_mdo_forms_per_agency'] as $agencyName => $count)
                    <tr>
                        <td>{{ $agencyName }}</td>
                        <td>{{ number_format($count) }}</td>
                    </tr>
                    @endforeach
                    <tr style="background-color: #e9ecef; font-weight: bold;">
                        <td><strong>Grand Total</strong></td>
                        <td><strong>{{ number_format(array_sum($insights['non_mdo_forms_per_agency'])) }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
        @endif

        @if($show_mdo_completed ?? true)
        <div class="section">
            <div class="section-title">✍️ Total MDOs Completed Per Agency</div>
            <p><em>(Signed and Sent back to the agency only)</em></p>
            <table class="table">
                <thead>
                    <tr>
                        <th>Agency Name</th>
                        <th>Count of Resolution</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($insights['mdo_completed_per_agency'] as $agencyName => $count)
                    <tr>
                        <td>{{ $agencyName }}</td>
                        <td>{{ number_format($count) }}</td>
                    </tr>
                    @endforeach
                    <tr style="background-color: #e9ecef; font-weight: bold;">
                        <td><strong>Grand Total</strong></td>
                        <td><strong>{{ number_format(array_sum($insights['mdo_completed_per_agency'])) }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
        @endif

        @if($show_updates_per_agency ?? true)
        <div class="section">
            <div class="section-title">🔄 Updates Per Agency</div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Agency Name</th>
                        <th>Count of Portal Name</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($updates_per_agency as $agencyName => $count)
                    <tr>
                        <td>{{ $agencyName }}</td>
                        <td>{{ number_format($count) }}</td>
                    </tr>
                    @endforeach
                    <tr style="background-color: #e9ecef; font-weight: bold;">
                        <td><strong>Grand Total</strong></td>
                        <td><strong>{{ number_format(array_sum($updates_per_agency)) }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
        @endif



        <div class="important-note">
            <strong>⚠️ Important:</strong> Please remember to always include
            <strong>telehealthreferrals@nybestmedical.com</strong> in all referral email correspondences. This will
            allow my team to assist if I'm unavailable.
        </div>

        <div class="footer">
            <p>📧 This report was automatically generated on {{ date('Y-m-d H:i:s') }}</p>
            <p>🏥 NY Best Medical - Daily Referral Report System</p>
        </div>
    </div>
</body>

</html>