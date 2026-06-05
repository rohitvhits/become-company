@include('include/header')
@include('include/sidebar')

<link rel="stylesheet" href="{{ asset('/assets/css/global.css')}}">

<style>
    .detail-card { border-radius:10px; border:1px solid #e9ecef; margin-bottom:20px; }
    .detail-card .card-header { background:#f8f9fa; border-radius:10px 10px 0 0; font-weight:700; font-size:14px; padding:12px 18px; border-bottom:1px solid #e9ecef; }
    .detail-card .card-body { padding:18px; }
    .info-row { display:flex; flex-wrap:wrap; }
    .info-item { flex:1 1 180px; padding:10px 15px; border-right:1px solid #f0f0f0; }
    .info-item:last-child { border-right:none; }
    .info-item .lbl { font-size:11px; color:#888; text-transform:uppercase; letter-spacing:.5px; }
    .info-item .val { font-size:14px; font-weight:600; color:#222; margin-top:3px; }
    .link-banner { background:#e8f5e9; border-left:4px solid #28a745; padding:14px 18px; border-radius:0 8px 8px 0; margin-bottom:18px; }
    .link-banner .lbl { font-size:11px;color:#28a745;text-transform:uppercase;font-weight:700; }
</style>

<div class="main-panel main-page-box">
    <div class="content-wrapper content-wrapper-box">

        <div class="d-flex align-items-center mb-3">
            <a href="{{ url('ai-call-logs') }}" class="btn btn-secondary btn-sm mr-2"><i class="fa fa-arrow-left"></i> Back</a>
            <strong style="font-size:16px;"><i class="mdi mdi-calendar-check text-primary mr-1"></i> AI Booking #{{ $booking->id }}</strong>
            @php $logStatus = $booking->autoCallLog->call_status ?? null; @endphp
            <span class="ml-2 badge" style="background:{{ $logStatus === 'booked' ? '#17a2b8' : ($logStatus === 'called' ? '#28a745' : ($logStatus === 'failed' ? '#dc3545' : '#ffc107')) }};color:{{ $logStatus === 'pending' ? '#212529' : '#fff' }};padding:5px 14px;border-radius:20px;font-size:12px;">
                {{ $logStatus ?? '-' }}
            </span>
        </div>

        @if($booking->autoCallLog)
        <div class="link-banner">
            <div class="lbl"><i class="fa fa-link mr-1"></i> Linked to Auto Call Log</div>
            <div class="d-flex align-items-center mt-1 flex-wrap" style="gap:12px;">
                <strong>Log #{{ $booking->autoCallLog->id }}</strong>
                <span>{{ $booking->autoCallLog->patient_name }}</span>
                <span class="badge" style="background:{{ $booking->autoCallLog->call_status === 'booked' ? '#17a2b8' : '#6c757d' }};color:#fff;">{{ $booking->autoCallLog->call_status }}</span>
                @if($booking->autoCallLog->call_fired_at)
                    <span style="font-size:12px;color:#555;">Called: {{ $booking->autoCallLog->call_fired_at->format('m/d/Y h:i A') }}</span>
                @endif
                <a href="{{ url('ai-call-logs/'.$booking->autoCallLog->id) }}" class="btn btn-sm btn-outline-primary" style="font-size:11px;padding:2px 10px;">
                    <i class="fa fa-eye"></i> View Call Log
                </a>
            </div>
        </div>
        @endif

        <div class="row">
            <div class="col-md-6">
                <div class="detail-card">
                    <div class="card-header"><i class="fa fa-calendar mr-2 text-primary"></i>Booking Details</div>
                    <div class="card-body p-0">
                        <div class="info-row">
                            <div class="info-item">
                                <div class="lbl">Name</div>
                                <div class="val">{{ $booking->name ?? '-' }}</div>
                            </div>
                            <div class="info-item">
                                <div class="lbl">Mobile</div>
                                <div class="val">{{ $booking->mobile ?? '-' }}</div>
                            </div>
                            <div class="info-item">
                                <div class="lbl">Language</div>
                                <div class="val">{{ $booking->language ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-item">
                                <div class="lbl">Date</div>
                                <div class="val">{{ $booking->date ? date('m/d/Y', strtotime($booking->date)) : '-' }}</div>
                            </div>
                            <div class="info-item">
                                <div class="lbl">Time Slot</div>
                                <div class="val">{{ $booking->time_slot_display }}</div>
                            </div>
                            <div class="info-item">
                                <div class="lbl">Created</div>
                                <div class="val">{{ $booking->created_at ? $booking->created_at->format('m/d/Y h:i A') : '-' }}</div>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-item">
                                <div class="lbl">Location</div>
                                <div class="val">{{ $locationName ?? '-' }}</div>
                                @if($booking->location_id)<div style="font-size:11px;color:#aaa;">ID: {{ $booking->location_id }}</div>@endif
                            </div>
                            <div class="info-item">
                                <div class="lbl">Service</div>
                                <div class="val">
                                    @if(!empty($serviceNames) && count($serviceNames) > 0)
                                        @foreach($serviceNames as $svcId => $svcName)
                                            <span class="badge" style="background:#e9ecef;color:#333;font-size:12px;margin-bottom:3px;display:inline-block;">{{ $svcName }}</span>
                                        @endforeach
                                    @else
                                        -
                                    @endif
                                </div>
                                @if($booking->service_id)<div style="font-size:11px;color:#aaa;">ID: {{ $booking->service_id }}</div>@endif
                            </div>
                            <div class="info-item">
                                <div class="lbl">Nurse</div>
                                <div class="val">{{ $nurseName ?? '-' }}</div>
                                @if($booking->nurse_id)<div style="font-size:11px;color:#aaa;">ID: {{ $booking->nurse_id }}</div>@endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="detail-card">
                    <div class="card-header"><i class="fa fa-info-circle mr-2 text-info"></i>Admin Status</div>
                    @php $log = $booking->autoCallLog; @endphp
                    <div class="card-body p-0">
                        <div class="info-row">
                            <div class="info-item">
                                <div class="lbl">Verified</div>
                                <div class="val">
                                    @if($log && $log->admin_verified)
                                        <span class="badge" style="background:#6f42c1;color:#fff;"><i class="fa fa-check"></i> Yes</span>
                                        @if($log->admin_verified_at)
                                            <small class="text-muted d-block">{{ Common::convertMDYTime($log->admin_verified_at) }}</small>
                                        @endif
                                    @else
                                        <span class="badge" style="background:#e9ecef;color:#495057;">No</span>
                                    @endif
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="lbl">Converted</div>
                                <div class="val">
                                    @if($log && $log->converted_to_appointment)
                                        <span class="badge" style="background:#007bff;color:#fff;"><i class="fa fa-calendar-check-o"></i> Yes</span>
                                        @if($log->converted_at)
                                            <small class="text-muted d-block">{{
                                            Common::convertMDYTime($log->converted_at) }}</small>
                                            
                                        @endif
                                    @else
                                        <span class="badge" style="background:#e9ecef;color:#495057;">No</span>
                                    @endif
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="lbl">SMS Sent</div>
                                <div class="val">
                                    @if($log && $log->confirmation_sms_sent)
                                        <span class="badge" style="background:#28a745;color:#fff;"><i class="fa fa-comment"></i> Confirmed</span>
                                    @endif
                                    @if($log && $log->reminder_sms_sent)
                                        <span class="badge" style="background:#17a2b8;color:#fff;"><i class="fa fa-bell"></i> Reminder</span>
                                    @endif
                                    @if(!$log || (!$log->confirmation_sms_sent && !$log->reminder_sms_sent))
                                        <span class="text-muted">None</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if($booking->notes)
                        <div style="padding:12px 15px; border-top:1px solid #f0f0f0;">
                            <div class="lbl">Notes</div>
                            <div style="font-size:13px;margin-top:4px;">{{ $booking->notes }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@include('include/footer')
