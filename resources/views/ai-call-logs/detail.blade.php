@include('include/header')
@include('include/sidebar')

<link rel="stylesheet" href="{{ asset('/assets/css/global.css')}}">

<style>
    .detail-card { border-radius:10px; border:1px solid #e9ecef; margin-bottom:20px; }
    .detail-card .card-header { background:#f8f9fa; border-radius:10px 10px 0 0; font-weight:700; font-size:14px; padding:12px 18px; border-bottom:1px solid #e9ecef; }
    .detail-card .card-body { padding:18px; }

    .info-row { display:flex; gap:0; flex-wrap:wrap; }
    .info-item { flex:1 1 200px; padding:10px 15px; border-right:1px solid #f0f0f0; }
    .info-item:last-child { border-right:none; }
    .info-item .lbl { font-size:11px; color:#888; text-transform:uppercase; letter-spacing:.5px; }
    .info-item .val { font-size:14px; font-weight:600; color:#222; margin-top:3px; }

    .badge-step { padding:5px 14px; border-radius:20px; font-size:12px; font-weight:600; }

    /* Transcript */
    .transcript-wrap { max-height:400px; overflow-y:auto; border:1px solid #e9ecef; border-radius:8px; padding:15px; background:#fafafa; }
    .msg-row { display:flex; gap:10px; margin-bottom:12px; }
    .msg-row.agent { flex-direction:row; }
    .msg-row.user  { flex-direction:row-reverse; }
    .msg-bubble { max-width:70%; padding:9px 14px; border-radius:12px; font-size:13px; line-height:1.5; }
    .msg-row.agent .msg-bubble { background:#e8f4fd; color:#1565c0; border-radius:12px 12px 12px 0; }
    .msg-row.user  .msg-bubble { background:#e8f5e9; color:#2e7d32; border-radius:12px 12px 0 12px; }
    .msg-role { font-size:10px; font-weight:700; color:#999; text-transform:uppercase; margin-bottom:3px; }

    /* Extracted data */
    .extracted-table td:first-child { font-weight:600; color:#555; width:180px; }
    .extracted-table td { padding:8px 12px; border-bottom:1px solid #f0f0f0; font-size:13px; }

    /* Audio player */
    .audio-wrapper { background:#f8f9fa; border-radius:8px; padding:15px; }
    .audio-placeholder { text-align:center; padding:20px; color:#aaa; }

    /* Action buttons */
    .action-section { display:flex; gap:10px; flex-wrap:wrap; margin-top:10px; }
    .action-section .btn { display:flex; align-items:center; gap:6px; padding:9px 18px; font-size:14px; border-radius:8px; font-weight:600; }

    /* Step flow */
    .flow-steps { display:flex; gap:0; overflow-x:auto; margin-bottom:20px; }
    .flow-step { flex:1; text-align:center; padding:10px 5px; position:relative; min-width:90px; }
    .flow-step:not(:last-child)::after { content:'›'; position:absolute; right:-8px; top:50%; transform:translateY(-50%); font-size:20px; color:#ccc; }
    .flow-step .step-num { width:30px; height:30px; border-radius:50%; background:#e9ecef; color:#888; font-weight:700; font-size:13px; display:flex; align-items:center; justify-content:center; margin:0 auto 5px; }
    .flow-step .step-lbl { font-size:11px; color:#888; }
    .flow-step.done .step-num { background:#28a745; color:#fff; }
    .flow-step.active .step-num { background:#007bff; color:#fff; }
    .flow-step.warn .step-num { background:#ffc107; color:#212529; }
</style>

<div class="main-panel main-page-box">
    <div class="content-wrapper content-wrapper-box">

        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <a href="{{ url('ai-call-logs') }}" class="btn btn-secondary btn-sm mr-2"><i class="fa fa-arrow-left"></i> Back</a>
                <strong style="font-size:16px;"><i class="mdi mdi-robot text-primary mr-1"></i> AI Call Review #{{ $log->id }}</strong>
            </div>
            <div>
                @if($log->call_status === 'called')
                    <span class="badge badge-step" style="background:#28a745;color:#fff;">Called</span>
                @elseif( $log->call_status === 'self-booked')
                    <span class="badge badge-step" style="background:#17a2b8;color:#fff;">Self-Booked</span>
                @elseif($log->call_status === 'booked')
                    <span class="badge badge-step" style="background:#007bff;color:#fff;">Booked</span>
                @elseif($log->call_status === 'failed')
                    <span class="badge badge-step" style="background:#dc3545;color:#fff;">Failed</span>
                @elseif($log->call_status === 'no_response')
                    <span class="badge badge-step" style="background:#6c757d;color:#fff;">No Response ({{ $log->call_attempts }} attempt(s))</span>
                @else
                    <span class="badge badge-step" style="background:#ffc107;color:#212529;">Pending</span>
                @endif
                @if($log->admin_verified)
                    <span class="badge badge-step ml-1" style="background:#6f42c1;color:#fff;"><i class="fa fa-check"></i> Verified</span>
                @endif
                @if($log->converted_to_appointment)
                    <span class="badge badge-step ml-1" style="background:#007bff;color:#fff;"><i class="fa fa-calendar-check-o"></i> Converted</span>
                @endif
            </div>
        </div>

        <!-- Progress Flow -->
        <div class="flow-steps mb-3">
            <div class="flow-step {{ $log->call_fired_at ? 'done' : 'warn' }}">
                <div class="step-num">{{ $log->call_fired_at ? '✓' : '1' }}</div>
                <div class="step-lbl">Call Fired</div>
            </div>
            <div class="flow-step {{ $log->call_fired_at ? 'done' : '' }}">
                <div class="step-num">{{ $log->call_fired_at ? '✓' : '2' }}</div>
                <div class="step-lbl">Admin Panel</div>
            </div>
            <div class="flow-step {{ $log->transcript ? 'done' : ($log->call_fired_at ? 'active' : '') }}">
                <div class="step-num">{{ $log->transcript ? '✓' : '3' }}</div>
                <div class="step-lbl">Review Data</div>
            </div>
            <div class="flow-step {{ $log->admin_verified ? 'done' : ($log->transcript ? 'active' : '') }}">
                <div class="step-num">{{ $log->admin_verified ? '✓' : '4' }}</div>
                <div class="step-lbl">Verify</div>
            </div>
            <div class="flow-step {{ $log->converted_to_appointment ? 'done' : ($log->admin_verified ? 'active' : '') }}">
                <div class="step-num">{{ $log->converted_to_appointment ? '✓' : '5' }}</div>
                <div class="step-lbl">Convert</div>
            </div>
            <div class="flow-step {{ $log->confirmation_sms_sent ? 'done' : ($log->converted_to_appointment ? 'active' : '') }}">
                <div class="step-num">{{ $log->confirmation_sms_sent ? '✓' : '6' }}</div>
                <div class="step-lbl">Conf. SMS</div>
            </div>
            <div class="flow-step {{ $log->reminder_sms_sent ? 'done' : '' }}">
                <div class="step-num">{{ $log->reminder_sms_sent ? '✓' : '7' }}</div>
                <div class="step-lbl">Reminder</div>
            </div>
        </div>

        <div class="row">
            <!-- Left column -->
            <div class="col-md-6">

                <!-- Patient Info -->
                <div class="detail-card">
                    <div class="card-header"><i class="fa fa-user mr-2 text-primary"></i>Patient Information</div>
                    <div class="card-body p-0">
                        @if($log->patient_db_id)
                        <div style="padding:10px 15px 10px; border-bottom:1px solid #f0f0f0; background:#f8f9ff;">
                            <a href="{{ url('patient/view/'.$log->patient_db_id) }}" target="_blank"
                               style="display:inline-flex;align-items:center;gap:8px;font-size:13px;font-weight:700;color:#007bff;text-decoration:none;">
                                <span style="width:30px;height:30px;border-radius:50%;background:#007bff;color:#fff;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;">
                                    {{ strtoupper(substr($log->first_name,0,1).substr($log->last_name,0,1)) }}
                                </span>
                                {{ $log->first_name }} {{ $log->last_name }}
                                <span style="font-size:11px;color:#888;font-weight:400;">#{{ $log->patient_db_id }}</span>
                                <i class="fa fa-external-link" style="font-size:11px;color:#aaa;"></i>
                            </a>
                        </div>
                        @endif
                        <div class="info-row">
                            <div class="info-item">
                                <div class="lbl">Patient Name</div>
                                <div class="val">{{ $log->patient_name ?? '-' }}</div>
                            </div>
                            <div class="info-item">
                                <div class="lbl">Mobile</div>
                                <div class="val">{{ $log->mobile ?? '-' }}</div>
                            </div>
                            <div class="info-item">
                                <div class="lbl">Agency</div>
                                <div class="val">{{ $log->agency_name ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-item">
                                <div class="lbl">SMS Sent At</div>
                                <div class="val">{{ $log->sms_sent_at ? $log->sms_sent_at->format('m/d/Y h:i A') : '-' }}</div>
                            </div>
                            <div class="info-item">
                                <div class="lbl">Call Fired At</div>
                                <div class="val">{{ $log->call_fired_at ? $log->call_fired_at->format('m/d/Y h:i A') : '-' }}</div>
                            </div>
                            <div class="info-item">
                                <div class="lbl">Deadline</div>
                                <div class="val">{{ $log->appointment_deadline ? $log->appointment_deadline->format('m/d/Y h:i A') : '-' }}</div>
                            </div>
                        </div>
                        @if($log->sms_link)
                        <div style="padding:10px 15px; border-top:1px solid #f0f0f0;">
                            <div class="lbl" style="font-size:11px;color:#888;text-transform:uppercase;">SMS Link</div>
                            <div style="font-size:12px; color:#007bff; word-break:break-all; margin-top:3px;">{{ $log->sms_link }}</div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Automate Booking Call (Accordion) -->
                @php $linked = $log->callAppointment; @endphp
                @if($linked)
                <div class="detail-card" style="border:2px solid #28a745;">
                    <div class="card-header d-flex align-items-center justify-content-between" style="background:#f0fff4; cursor:pointer;" data-toggle="collapse" data-target="#bookingAccordion" aria-expanded="true">
                        <span style="color:#28a745;">
                            <i class="fa fa-calendar-check-o mr-2"></i>Automate Booking Call
                            <span style="font-weight:400;font-size:12px;">#{{ $linked->id }}</span>
                        </span>
                        <div class="d-flex align-items-center" style="gap:8px;">
                          
                            <a href="{{ url('ai-call-logs/booking/'.$linked->id) }}" class="btn btn-sm btn-outline-success" style="font-size:11px;padding:2px 10px;" onclick="event.stopPropagation();">
                                <i class="fa fa-eye"></i> View Detail
                            </a>
                             @if(!$log->converted_to_appointment)
                            <button class="btn btn-sm btn-outline-warning" style="font-size:11px;padding:2px 10px;" onclick="event.stopPropagation(); openEditBooking();">
                                <i class="fa fa-pencil"></i> Edit
                            </button>
                            @endif
                            <i class="fa fa-chevron-up accordion-arrow" style="color:#28a745;font-size:12px;transition:transform .3s;"></i>
                        </div>
                    </div>
                    <div id="bookingAccordion" class="collapse show">
                        <div class="card-body p-0">
                            <div class="info-row">
                                <div class="info-item">
                                    <div class="lbl">Name</div>
                                    <div class="val">{{ $linked->name ?? '-' }}</div>
                                </div>
                                <div class="info-item">
                                    <div class="lbl">Mobile</div>
                                    <div class="val">{{ $linked->mobile ?? '-' }}</div>
                                </div>
                                <div class="info-item">
                                    <div class="lbl">Language</div>
                                    <div class="val">{{ $linked->language ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="info-row">
                                <div class="info-item">
                                    <div class="lbl">Date</div>
                                    <div class="val">{{ $linked->date ? date('m/d/Y', strtotime($linked->date)) : '-' }}</div>
                                </div>
                                <div class="info-item">
                                    <div class="lbl">Time Slot</div>
                                    <div class="val">{{ $linked->time_slot_display }}</div>
                                </div>
                                <div class="info-item">
                                    <div class="lbl">Created At</div>
                                    <div class="val">{{ $linked->created_at ? $linked->created_at->format('m/d/Y h:i A') : '-' }}</div>
                                </div>
                            </div>
                            @if($linked->location_id || $linked->service_id)
                            <div class="info-row">
                                <div class="info-item">
                                    <div class="lbl">Location</div>
                                    <div class="val">{{ $locationName ?? '-' }}</div>
                                    @if($linked->location_id)<div style="font-size:11px;color:#aaa;">ID: {{ $linked->location_id }}</div>@endif
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
                                    @if($linked->service_id)<div style="font-size:11px;color:#aaa;">ID: {{ $linked->service_id }}</div>@endif
                                </div>
                                 <div class="info-item">
                                   
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <!-- Call History (all attempts) -->
                <div class="detail-card">
                    <div class="card-header"><i class="fa fa-history mr-2 text-success"></i>Call History
                        <span class="badge badge-secondary ml-2" style="font-size:11px;">{{ count($attempts) }} attempt(s)</span>
                    </div>
                    @if(count($attempts) > 0)
                        {{-- Tab nav --}}
                        <div style="display:flex;flex-wrap:wrap;border-bottom:2px solid #e9ecef;background:#fff;">
                            @foreach($attempts as $i => $attempt)
                            @php
                                $tabColor  = $attempt->call_type === 'reminder' ? '#fd7e14' : '#28a745';
                                $tabLabel  = $attempt->call_type === 'reminder' ? 'Reminder' : 'Initial';
                            @endphp
                            <button class="attempt-tab-btn {{ $i === 0 ? 'active' : '' }}"
                                    style="padding:8px 14px;font-size:12px;font-weight:600;color:{{ $i === 0 ? $tabColor : '#888' }};border:none;background:none;cursor:pointer;border-bottom:2px solid {{ $i === 0 ? $tabColor : 'transparent' }};margin-bottom:-2px;"
                                    onclick="switchAttemptTab({{ $attempt->id }}, this, '{{ $tabColor }}')">
                                <span style="display:inline-block;width:7px;height:7px;border-radius:50%;background:{{ $tabColor }};margin-right:5px;vertical-align:middle;"></span>
                                {{ $tabLabel }} #{{ $attempt->attempt_number }}
                                <span style="font-size:10px;color:#aaa;margin-left:3px;">{{ $attempt->fired_at ? $attempt->fired_at->format('m/d h:i A') : '' }}</span>
                            </button>
                            @endforeach
                        </div>
                        {{-- Tab panes --}}
                        @foreach($attempts as $i => $attempt)
                        @php $attTrans = $attempt->transcript ? json_decode($attempt->transcript, true) : null; @endphp
                        <div class="attempt-tab-pane {{ $i === 0 ? '' : 'd-none' }}" id="attempt-pane-{{ $attempt->id }}">
                            <div class="card-body pb-2">
                                <div style="font-size:11px;color:#888;margin-bottom:10px;">
                                    @if($attempt->call_type === 'reminder')
                                        <span class="badge" style="background:#fd7e14;color:#fff;font-size:10px;">Reminder Call</span>
                                    @else
                                        <span class="badge" style="background:#28a745;color:#fff;font-size:10px;">Initial Call</span>
                                    @endif
                                    &nbsp;Attempt #{{ $attempt->attempt_number }}
                                    &nbsp;&middot;&nbsp; {{ $attempt->fired_at ? $attempt->fired_at->format('m/d/Y h:i A') : '-' }}
                                    &nbsp;&middot;&nbsp; Status:
                                    <strong>{{ ucfirst($attempt->status ?? '-') }}</strong>
                                    @if($attempt->conversation_id)
                                        &nbsp;&middot;&nbsp; Conv: <code style="font-size:10px;">{{ $attempt->conversation_id }}</code>
                                    @endif
                                </div>

                                {{-- Recording --}}
                                @if($attempt->conversation_id)
                                <div class="audio-wrapper mb-3">
                                    <div style="font-size:11px;font-weight:600;color:#555;margin-bottom:6px;"><i class="fa fa-volume-up mr-1 text-success"></i>Recording</div>
                                    <audio controls style="width:100%;height:36px;">
                                        <source src="{{ url('ai-call-logs/attempt/'.$attempt->id.'/audio') }}" type="audio/mpeg">
                                    </audio>
                                </div>
                                @else
                                <div class="audio-placeholder mb-2">
                                    <i class="fa fa-microphone-slash" style="font-size:22px;"></i>
                                    <div class="mt-1" style="font-size:12px;">No recording available for this attempt</div>
                                </div>
                                @endif
                            </div>

                            {{-- Transcript --}}
                            <div style="border-top:1px solid #f0f0f0;">
                                <div style="padding:10px 18px 6px;font-size:11px;font-weight:600;color:#555;display:flex;align-items:center;justify-content:space-between;">
                                    <span><i class="fa fa-comments mr-1 text-primary"></i>Transcript</span>
                                    @if($attempt->conversation_id)
                                    <button class="btn btn-sm btn-outline-info" style="font-size:11px;padding:2px 10px;" onclick="fetchAttemptTranscript({{ $attempt->id }})">
                                        <i class="fa fa-refresh"></i> Refresh
                                    </button>
                                    @endif
                                </div>
                                <div class="transcript-wrap" id="attempt-transcript-{{ $attempt->id }}">
                                    @if($attempt->transcript)
                                        @if(is_array($attTrans))
                                            @foreach($attTrans as $msg)
                                                @php
                                                    $role = strtolower($msg['role'] ?? 'agent');
                                                    $text = $msg['message'] ?? ($msg['text'] ?? ($msg['content'] ?? ''));
                                                @endphp
                                                <div class="msg-row {{ $role === 'user' ? 'user' : 'agent' }}">
                                                    <div>
                                                        <div class="msg-role">{{ $role === 'user' ? 'Patient' : 'AI Agent' }}</div>
                                                        <div class="msg-bubble">{{ $text }}</div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="p-3" style="white-space:pre-wrap;font-size:13px;">{{ $attempt->transcript }}</div>
                                        @endif
                                    @else
                                        <div class="text-muted text-center p-3" style="font-size:12px;">
                                            <i class="fa fa-comment-o mr-1"></i>Transcript not loaded — click Refresh above
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        {{-- No attempts recorded yet (legacy log or call not fired) --}}
                        <div class="card-body">
                            @if($log->conversation_id)
                                <div class="audio-wrapper mb-3">
                                    <div style="font-size:11px;font-weight:600;color:#555;margin-bottom:6px;"><i class="fa fa-volume-up mr-1 text-success"></i>Recording</div>
                                    <audio id="callAudioPlayer" controls style="width:100%;">
                                        <source src="{{ url('ai-call-logs/'.$log->id.'/audio') }}" type="audio/mpeg">
                                    </audio>
                                    <div style="font-size:11px;color:#888;margin-top:6px;">Conv ID: <code>{{ $log->conversation_id }}</code></div>
                                </div>
                            @else
                                <div class="audio-placeholder">
                                    <i class="fa fa-microphone-slash" style="font-size:30px;"></i>
                                    <div class="mt-2">No conversation ID found</div>
                                    <button class="btn btn-sm btn-outline-primary mt-2" onclick="fetchConversation()">
                                        <i class="fa fa-refresh"></i> Fetch from Response
                                    </button>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Admin Notes -->
                <div class="detail-card">
                    <div class="card-header"><i class="fa fa-sticky-note mr-2 text-warning"></i>Admin Notes</div>
                    <div class="card-body">
                        <textarea class="form-control" id="adminNotes" rows="3" placeholder="Add notes about this call...">{{ $log->notes ?? '' }}</textarea>
                        <button class="btn btn-sm btn-warning mt-2" onclick="saveNotes()">
                            <i class="fa fa-save"></i> Save Notes
                        </button>
                    </div>
                </div>

            </div>

            <!-- Right column -->
            <div class="col-md-6">

                <!-- Extracted Data -->
                <div class="detail-card">
                    <div class="card-header d-flex justify-content-between align-items-center" style="cursor:pointer;" data-toggle="collapse" data-target="#extractedAccordion" aria-expanded="false">
                        <span><i class="fa fa-database mr-2 text-info"></i>Extracted Data</span>
                        <div class="d-flex align-items-center" style="gap:8px;">
                            <button class="btn btn-sm btn-outline-info" onclick="event.stopPropagation(); fetchConversation();" id="fetchBtn">
                                <i class="fa fa-refresh"></i> Refresh
                            </button>
                            <i class="fa fa-chevron-up extracted-arrow" style="color:#17a2b8;font-size:12px;transition:transform .3s;transform:rotate(180deg);"></i>
                        </div>
                    </div>
                    <div id="extractedAccordion" class="collapse">
                        <div class="card-body" id="extractedDataSection">
                            @if($log->extracted_data)
                                @php $extracted = json_decode($log->extracted_data, true); @endphp
                                @if(is_array($extracted) && count($extracted))
                                    <table class="table table-sm extracted-table mb-0">
                                        @foreach($extracted as $key => $value)
                                            <tr>
                                                <td>{{ ucwords(str_replace('_',' ',$key)) }}</td>
                                                <td>
                                                    @if(is_array($value))
                                                        <pre style="font-size:11px;margin:0;">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                                    @else
                                                        {{ $value }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                @else
                                    <div class="text-muted text-center py-3">No structured data extracted</div>
                                @endif
                            @else
                                <div class="text-muted text-center py-3" id="noExtractedMsg">
                                    <i class="fa fa-search" style="font-size:22px;"></i>
                                    <div class="mt-1">No data yet — click Refresh to fetch from ElevenLabs</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Transcript shown inside Call History tabs above -->

            </div>
        </div>

        <!-- Action Buttons -->
        <div class="detail-card">
            <div class="card-header"><i class="fa fa-cogs mr-2"></i>Actions</div>
            <div class="card-body">
                @if( $log->call_status === 'self-booked')
                <div class="alert alert-info mb-0">
                    <i class="fa fa-info-circle mr-1"></i>
                    This appointment was <strong>self-booked</strong> by the patient. No admin action required.
                </div>
                @else
                <div class="action-section">

                
                        {{-- Booked via admin call or other status --}}

                        @if($log->call_status !== 'called')
                        <button class="btn btn-danger" id="fireCallBtn" onclick="fireCall()">
                            <i class="fa fa-phone"></i> Fire Call Now
                        </button>
                        @else
                        <button class="btn btn-outline-danger" id="fireCallBtn" onclick="fireCall()">
                            <i class="fa fa-phone"></i> Re-fire Call
                        </button>
                        @endif

                        @if(!$log->admin_verified)
                        <button class="btn btn-success" id="verifyBtn"
                            @if(!$linked) disabled title="Automate Booking Call data is required" @else onclick="verifyCall()" @endif>
                            <i class="fa fa-check-circle"></i> Verify Data
                        </button>
                        @else
                        <button class="btn btn-success" disabled>
                            <i class="fa fa-check-circle"></i> Verified ✓
                        </button>
                        @endif

                        @if(!$log->converted_to_appointment)
                        <button class="btn btn-primary" id="convertBtn"
                            @if(!$linked || !$log->admin_verified) disabled title="{{ !$linked ? 'Automate Booking Call data is required' : 'Please verify data first' }}" @else onclick="convertToAppointment()" @endif>
                            <i class="fa fa-calendar-plus-o"></i> Convert to Appointment
                        </button>
                        @else
                        <button class="btn btn-primary" disabled>
                            <i class="fa fa-calendar-check-o"></i> Converted ✓
                        </button>
                        @endif

                        @if(!$log->reminder_sms_sent)
                        <button class="btn btn-warning" id="reminderBtn"
                            @if(!$linked || !$log->converted_to_appointment) disabled title="{{ !$linked ? 'Automate Booking Call data is required' : 'Please convert to appointment first' }}" @else onclick="sendReminder()" @endif>
                            <i class="fa fa-bell"></i> Send Reminder SMS
                        </button>
                        @else
                        <button class="btn btn-warning" disabled>
                            <i class="fa fa-bell"></i> Reminder Sent ✓
                        </button>
                        @endif

                        @if(!$log->reminder_call_fired_at)
                        <button class="btn btn-info" id="reminderCallBtn"
                            @if(!$linked || !$log->converted_to_appointment) disabled title="{{ !$linked ? 'Automate Booking Call data is required' : 'Please convert to appointment first' }}" @else onclick="sendReminderCall()" @endif>
                            <i class="fa fa-phone"></i> Send Reminder Call
                        </button>
                        @else
                        <button class="btn btn-info" disabled>
                            <i class="fa fa-phone"></i> Reminder Call Sent ✓ ({{ $log->reminder_call_fired_at->format('m/d h:i A') }})
                        </button>
                        @endif

                    @endif

                </div>

                @if($log->converted_to_appointment)
                <div class="alert alert-success mt-3 mb-0">
                    <i class="fa fa-check-circle mr-1"></i>
                    This call was converted to an appointment on {{ $log->converted_at ? $log->converted_at->format('m/d/Y h:i A') : '' }}.
                    @if($log->confirmation_sms_sent) Confirmation SMS was sent. @endif
                </div>
                @endif
              
            </div>
        </div>

    </div>
</div>

<!-- Edit Booking Modal -->
@if($log->callAppointment)
@php $linked = $log->callAppointment; @endphp
<div class="modal fade" id="editBookingModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius:10px;overflow:hidden;">
            <div class="modal-header" style="background:#f0fff4;border-bottom:2px solid #28a745;">
                <h6 class="modal-title" style="font-weight:700;color:#28a745;"><i class="fa fa-pencil mr-2"></i>Edit Booking #{{ $linked->id }}</h6>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;color:#888;font-weight:600;">Mobile <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_mobile" value="{{ $linked->mobile }}">
                            <div class="invalid-feedback" id="err_mobile"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;color:#888;font-weight:600;">Language <span class="text-danger">*</span></label>
                            <select class="form-control" id="edit_language">
                                <option value="">-- Select --</option>
                                @foreach($languageList as $lang)
                                    <option value="{{ $lang->name }}" {{ $linked->language == $lang->name ? 'selected' : '' }}>{{ $lang->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="err_language"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;color:#888;font-weight:600;">Date <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_date" autocomplete="off" readonly
                                value="{{ $linked->date ? date('m/d/Y', strtotime($linked->date)) : '' }}"
                                placeholder="MM/DD/YYYY">
                            <div class="invalid-feedback" id="err_date"></div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;color:#888;font-weight:600;">Location <span class="text-danger">*</span></label>
                            <select class="form-control" id="edit_location_id" onchange="loadEditTimeSlots()">
                                <option value="">-- Select --</option>
                                @foreach($locationList as $loc)
                                    <option value="{{ $loc['id'] }}" {{ $linked->location_id == $loc['id'] ? 'selected' : '' }}>{{ $loc['name'] }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="err_location_id"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;color:#888;font-weight:600;">Time Slot <span class="text-danger">*</span></label>
                            <select class="form-control" id="edit_time_slot">
                                <option value="{{ $linked->time_slot }}">{{ $linked->time_slot ? $linked->time_slot_display : 'Select Appointment Time' }}</option>
                            </select>
                            <div class="invalid-feedback" id="err_time_slot"></div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;color:#888;font-weight:600;">Service <span class="text-danger">*</span></label>
                            @php
                                $selectedServiceIds = $linked ? array_filter(array_map('trim', explode(',', $linked->service_id ?? ''))) : [];
                            @endphp
                            <select class="form-control" id="edit_service_id" multiple="multiple" style="width:100%;">
                                @foreach($serviceList as $svc)
                                    <option value="{{ $svc->id }}" {{ in_array((string)$svc->id, array_map('strval', $selectedServiceIds)) ? 'selected' : '' }}>{{ $svc->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="err_service_id"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid #f0f0f0;">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success btn-sm" id="saveBookingBtn" onclick="saveBooking()">
                    <i class="fa fa-save mr-1"></i> Save Changes
                </button>
            </div>
        </div>
    </div>
</div>
@endif

@include('include/footer')

<link rel="stylesheet" href="{{ asset('assets/vendors/select2/select2.min.css') }}">
<script src="{{ asset('assets/vendors/select2/select2.min.js') }}"></script>

<script>
var logId = {{ $log->id }};

$(document).ready(function() {
    $('#edit_service_id').select2({
        placeholder: '-- Select Service(s) --',
        allowClear: true,
        width: '100%',
    });
});

// Accordion arrow rotation
$('#bookingAccordion').on('show.bs.collapse', function() {
    $(this).closest('.detail-card').find('.accordion-arrow').css('transform', 'rotate(0deg)');
}).on('hide.bs.collapse', function() {
    $(this).closest('.detail-card').find('.accordion-arrow').css('transform', 'rotate(180deg)');
});

$('#extractedAccordion').on('show.bs.collapse', function() {
    $(this).closest('.detail-card').find('.extracted-arrow').css('transform', 'rotate(0deg)');
}).on('hide.bs.collapse', function() {
    $(this).closest('.detail-card').find('.extracted-arrow').css('transform', 'rotate(180deg)');
});

function renderTranscriptHtml(transcript) {
    var html = '';
    transcript.forEach(function(msg) {
        var role = (msg.role || 'agent').toLowerCase();
        var text = msg.message || msg.text || msg.content || '';
        var cls  = role === 'user' ? 'user' : 'agent';
        var lbl  = role === 'user' ? 'Patient' : 'AI Agent';
        html += '<div class="msg-row ' + cls + '"><div><div class="msg-role">' + lbl + '</div><div class="msg-bubble">' + $('<div>').text(text).html() + '</div></div></div>';
    });
    return html;
}

function fetchConversation() {
    var btn = $('#fetchBtn');
    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Fetching...');

    $.ajax({
        url: "{{ url('ai-call-logs/'.$log->id.'/fetch-conversation') }}",
        success: function(res) {
            if (res.status) {
                // Update transcript in the active attempt pane (first initial attempt visible)
                var $activePane = $('.attempt-tab-pane:not(.d-none)').first();
                if (res.transcript && Array.isArray(res.transcript) && $activePane.length) {
                    $activePane.find('.transcript-wrap').html(renderTranscriptHtml(res.transcript));
                }

                // Re-render extracted data
                if (res.extracted_data) {
                    var eHtml = '<table class="table table-sm extracted-table mb-0">';
                    Object.keys(res.extracted_data).forEach(function(k) {
                        var v = res.extracted_data[k];
                        var displayVal = (typeof v === 'object') ? '<pre style="font-size:11px;margin:0;">' + JSON.stringify(v, null, 2) + '</pre>' : $('<div>').text(String(v)).html();
                        var label = k.replace(/_/g, ' ').replace(/\b\w/g, function(c) { return c.toUpperCase(); });
                        eHtml += '<tr><td>' + label + '</td><td>' + displayVal + '</td></tr>';
                    });
                    eHtml += '</table>';
                    $('#extractedDataSection').html(eHtml);
                }

                toastr.success('Conversation data fetched successfully');
            } else {
                toastr.warning(res.message || 'No data available');
            }
        },
        error: function() {
            toastr.error('Failed to fetch conversation data');
        },
        complete: function() {
            btn.prop('disabled', false).html('<i class="fa fa-refresh"></i> Refresh');
        }
    });
}

function verifyCall() {
    var btn = $('#verifyBtn');
    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Verifying...');

    $.ajax({
        url: "{{ url('ai-call-logs/'.$log->id.'/verify') }}",
        type: 'POST',
        data: { _token: '{{ csrf_token() }}', notes: $('#adminNotes').val() },
        success: function(res) {
            if (res.status) {
                toastr.success(res.message);
                btn.html('<i class="fa fa-check-circle"></i> Verified ✓').addClass('disabled');
                $('#convertBtn').prop('disabled', false).removeAttr('title').attr('onclick', 'convertToAppointment()');
            } else {
                toastr.error(res.message);
                btn.prop('disabled', false).html('<i class="fa fa-check-circle"></i> Verify Data');
            }
        },
        error: function() {
            toastr.error('Verification failed');
            btn.prop('disabled', false).html('<i class="fa fa-check-circle"></i> Verify Data');
        }
    });
}

function convertToAppointment() {
    $.confirm({
        title: 'Convert to Appointment',
        content: 'Are you sure you want to convert this AI call to an official appointment? A confirmation SMS will be sent to the patient.',
        columnClass: 'col-md-6',
        buttons: {
            confirm: {
                text: 'CONFIRM',
                btnClass: 'btn-primary',
                action: function() {
                    var btn = $('#convertBtn');
                    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Converting...');
                    $.ajax({
                        url: "{{ url('ai-call-logs/'.$log->id.'/convert') }}",
                        type: 'POST',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(res) {
                            if (res.status) {
                                toastr.success(res.message);
                                btn.html('<i class="fa fa-calendar-check-o"></i> Converted ✓').addClass('disabled');
                                setTimeout(function() { location.reload(); }, 1500);
                            } else {
                                toastr.error(res.message);
                                btn.prop('disabled', false).html('<i class="fa fa-calendar-plus-o"></i> Convert to Appointment');
                            }
                        },
                        error: function(xhr) {
                            var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Conversion failed';
                            toastr.error(msg);
                            btn.prop('disabled', false).html('<i class="fa fa-calendar-plus-o"></i> Convert to Appointment');
                        }
                    });
                }
            },
            cancel: {
                text: 'CANCEL',
            }
        }
    });
}

function sendReminder() {
    var btn = $('#reminderBtn');
    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Sending...');

    $.ajax({
        url: "{{ url('ai-call-logs/'.$log->id.'/reminder') }}",
        type: 'POST',
        data: { _token: '{{ csrf_token() }}' },
        success: function(res) {
            if (res.status) {
                toastr.success(res.message);
                btn.html('<i class="fa fa-bell"></i> Reminder Sent ✓').addClass('disabled');
            } else {
                toastr.error(res.message);
                btn.prop('disabled', false).html('<i class="fa fa-bell"></i> Send Reminder SMS');
            }
        },
        error: function() {
            toastr.error('Failed to send reminder');
            btn.prop('disabled', false).html('<i class="fa fa-bell"></i> Send Reminder SMS');
        }
    });
}

function saveNotes() {
    $.ajax({
        url: "{{ url('ai-call-logs/'.$log->id.'/notes') }}",
        type: 'POST',
        data: { _token: '{{ csrf_token() }}', notes: $('#adminNotes').val() },
        success: function() { toastr.success('Notes saved'); },
        error: function() { toastr.error('Failed to save notes'); }
    });
}

function sendReminderCall() {
    $.confirm({
        title: 'Send Reminder Call',
        content: 'Are you sure you want to fire a reminder call to <b>{{ $log->patient_name }}</b> ({{ $log->mobile }})?',
        columnClass: 'col-md-6',
        buttons: {
            confirm: {
                text: 'CONFIRM',
                btnClass: 'btn-primary',
                action: function() {
                    var btn = $('#reminderCallBtn');
                    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Calling...');
                    $.ajax({
                        url: "{{ url('ai-call-logs/'.$log->id.'/reminder-call') }}",
                        type: 'POST',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(res) {
                            if (res.status) {
                                toastr.success(res.message);
                                btn.html('<i class="fa fa-phone"></i> Reminder Call Sent ✓').addClass('disabled');
                            } else {
                                toastr.error(res.message);
                                btn.prop('disabled', false).html('<i class="fa fa-phone"></i> Send Reminder Call');
                            }
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON ? xhr.responseJSON.message : 'Failed to fire reminder call');
                            btn.prop('disabled', false).html('<i class="fa fa-phone"></i> Send Reminder Call');
                        }
                    });
                }
            },
            cancel: {
                text: 'CANCEL',
            }
        }
    });
}

function fireCall() {
    $.confirm({
        title: 'Fire AI Call',
        content: 'Are you sure you want to fire an AI call to <b>{{ $log->patient_name }}</b> ({{ $log->mobile }})?',
        columnClass: 'col-md-6',
        buttons: {
            confirm: {
                text: 'CONFIRM',
                btnClass: 'btn-danger',
                action: function() {
                    var btn = $('#fireCallBtn');
                    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Firing...');
                    $.ajax({
                        url: "{{ url('ai-call-logs/'.$log->id.'/fire-call') }}",
                        type: 'POST',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(res) {
                            if (res.status) {
                                toastr.success(res.message);
                                setTimeout(function() { location.reload(); }, 1500);
                            } else {
                                toastr.error(res.message);
                                btn.prop('disabled', false).html('<i class="fa fa-phone"></i> Fire Call Now');
                            }
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON ? xhr.responseJSON.message : 'Failed to fire call');
                            btn.prop('disabled', false).html('<i class="fa fa-phone"></i> Fire Call Now');
                        }
                    });
                }
            },
            cancel: {
                text: 'CANCEL',
            }
        }
    });
}

var _editDatepickerInit = false;
function openEditBooking() {
    // Reset validation state on open
    $('.invalid-feedback').text('').hide();
    $('.form-control').removeClass('is-invalid');
    $('#edit_service_id').next('.select2').find('.select2-selection').css('border-color', '');

    $('#editBookingModal').modal('show');
    if (!_editDatepickerInit) {
        _editDatepickerInit = true;
        $('#editBookingModal').on('shown.bs.modal', function() {
            $('#edit_date').datepicker({
                dateFormat: 'mm/dd/yy',
                minDate: new Date(),
                onSelect: function() { loadEditTimeSlots(); }
            });
        });
    }
    loadEditTimeSlots();
}

function loadEditTimeSlots() {
    var locationId = $('#edit_location_id').val();
    var date = $('#edit_date').val();
    var currentSlot = $('#edit_time_slot').val();

    if (!locationId || !date) return;

    $('#edit_time_slot').html('<option value="">Loading...</option>').prop('disabled', true);

    $.ajax({
        url: "{{ url('/') }}/location-schedule-search1",
        type: 'GET',
        data: { location_id: locationId, start_time: date },
        success: function(resp) {
            var slots = typeof resp === 'string' ? JSON.parse(resp) : resp;
            var html = '<option value="">Select Appointment Time</option>';
            if (slots && slots.length) {
                $.each(slots, function(i, v) {
                    var label = v.start_time + ' - ' + v.end_time + ' (' + v.slots + ' slots)';
                    var val = v.start_time;
                    var selected = (currentSlot === val) ? 'selected' : '';
                    html += '<option value="' + val + '" ' + selected + '>' + label + '</option>';
                });
            } else {
                html = '<option value="">No slots available</option>';
            }
            $('#edit_time_slot').html(html).prop('disabled', false);
        },
        error: function() {
            $('#edit_time_slot').html('<option value="">Failed to load slots</option>').prop('disabled', false);
        }
    });
}

function saveBooking() {
    // Clear previous errors
    $('.invalid-feedback').text('').hide();
    $('.form-control').removeClass('is-invalid');

    var valid = true;
    function fieldError(id, errId, msg) {
        $('#' + id).addClass('is-invalid');
        $('#' + errId).text(msg).show();
        valid = false;
    }

    var mobile = $.trim($('#edit_mobile').val());
    if (!mobile) {
        fieldError('edit_mobile', 'err_mobile', 'Mobile number is required.');
    } else if (!/^\d{7,15}$/.test(mobile.replace(/[\s\-\(\)\+]/g, ''))) {
        fieldError('edit_mobile', 'err_mobile', 'Enter a valid mobile number.');
    }

    if (!$('#edit_language').val()) {
        fieldError('edit_language', 'err_language', 'Please select a language.');
    }

    if (!$.trim($('#edit_date').val())) {
        fieldError('edit_date', 'err_date', 'Date is required.');
    }

    if (!$('#edit_location_id').val()) {
        fieldError('edit_location_id', 'err_location_id', 'Please select a location.');
    }

    if (!$('#edit_time_slot').val()) {
        fieldError('edit_time_slot', 'err_time_slot', 'Please select a time slot.');
    }

    if (!$('#edit_service_id').val() || $('#edit_service_id').val().length === 0) {
        $('#edit_service_id').next('.select2').find('.select2-selection').css('border-color', '#dc3545');
        $('#err_service_id').text('Please select at least one service.').show();
        valid = false;
    }

    if (!valid) return;

    var btn = $('#saveBookingBtn');
    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');

    $.ajax({
        url: "{{ url('ai-call-logs/'.$log->id.'/update-booking') }}",
        type: 'POST',
        data: {
            _token:      '{{ csrf_token() }}',
            mobile:      $('#edit_mobile').val(),
            language:    $('#edit_language').val(),
            date:        $('#edit_date').val(),
            time_slot:   $('#edit_time_slot').val(),
            location_id: $('#edit_location_id').val(),
            service_id:  $('#edit_service_id').val(),
        },
        success: function(res) {
            if (res.status) {
                toastr.success(res.message);
                $('#editBookingModal').modal('hide');
                setTimeout(function() { location.reload(); }, 1000);
            } else {
                toastr.error(res.message);
                btn.prop('disabled', false).html('<i class="fa fa-save mr-1"></i> Save Changes');
            }
        },
        error: function() {
            toastr.error('Failed to save booking');
            btn.prop('disabled', false).html('<i class="fa fa-save mr-1"></i> Save Changes');
        }
    });
}

function switchAttemptTab(attemptId, el, color) {
    // Reset all tabs
    $('.attempt-tab-btn').css({ color: '#888', borderBottomColor: 'transparent' });
    // Activate clicked tab
    $(el).css({ color: color, borderBottomColor: color });
    // Hide all panes, show target
    $('.attempt-tab-pane').addClass('d-none');
    $('#attempt-pane-' + attemptId).removeClass('d-none');
}

function fetchAttemptTranscript(attemptId) {
    var $btn = $('#attempt-pane-' + attemptId + ' button');
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i>Loading...');
    $.get('{{ url("ai-call-logs/attempt") }}/' + attemptId + '/fetch-transcript', function(res) {
        $btn.prop('disabled', false).html('<i class="fa fa-refresh"></i> Refresh');
        if (!res.status || !res.transcript || !res.transcript.length) {
            $('#attempt-transcript-' + attemptId).html('<div class="text-muted text-center p-3" style="font-size:12px;"><i class="fa fa-comment-o mr-1"></i>No transcript available yet</div>');
            return;
        }
        var html = '';
        $.each(res.transcript, function(i, msg) {
            var role  = (msg.role || 'agent').toLowerCase();
            var text  = msg.message || msg.text || msg.content || '';
            var isUser = role === 'user';
            html += '<div class="msg-row ' + (isUser ? 'user' : 'agent') + '">'
                + '<div><div class="msg-role">' + (isUser ? 'Patient' : 'AI Agent') + '</div>'
                + '<div class="msg-bubble">' + $('<div>').text(text).html() + '</div></div></div>';
        });
        $('#attempt-transcript-' + attemptId).html(html);
    }).fail(function() {
        $btn.prop('disabled', false).html('<i class="fa fa-refresh"></i> Refresh');
    });
}
</script>
