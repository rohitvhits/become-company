<style>
    .ai-log-card { border:1px solid #e9ecef; border-radius:8px; margin-bottom:14px; overflow:hidden; }
    .ai-log-card .log-header { background:#f8f9fa; padding:10px 15px; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid #e9ecef; cursor:pointer; }
    .ai-log-card .log-body { padding:15px; }
    .ai-log-badge { padding:3px 10px; border-radius:12px; font-size:11px; font-weight:600; }
    .ai-info-grid { display:flex; flex-wrap:wrap; gap:0; border-top:1px solid #f0f0f0; }
    .ai-info-cell { flex:1 1 140px; padding:8px 12px; border-right:1px solid #f0f0f0; }
    .ai-info-cell:last-child { border-right:none; }
    .ai-info-cell .lbl { font-size:10px; color:#aaa; text-transform:uppercase; letter-spacing:.4px; }
    .ai-info-cell .val { font-size:13px; font-weight:600; color:#333; margin-top:2px; }
    .call-tabs { display:flex; border-bottom:2px solid #e9ecef; background:#fff; }
    .call-tab-btn { padding:7px 16px; font-size:12px; font-weight:600; color:#888; border:none; background:none; cursor:pointer; border-bottom:2px solid transparent; margin-bottom:-2px; }
    .call-tab-btn.active { color:#007bff; border-bottom-color:#007bff; }
    .call-tab-btn .call-dot { display:inline-block; width:7px; height:7px; border-radius:50%; margin-right:5px; vertical-align:middle; }
    .call-tab-pane { display:none; }
    .call-tab-pane.active { display:block; }
    .transcript-box { max-height:250px; overflow-y:auto; background:#fafafa; border:1px solid #e9ecef; border-radius:6px; padding:12px; }
    .msg-r { display:flex; gap:8px; margin-bottom:8px; }
    .msg-r.agent { flex-direction:row; }
    .msg-r.user  { flex-direction:row-reverse; }
    .msg-bbl { max-width:75%; padding:7px 12px; border-radius:10px; font-size:12px; line-height:1.5; }
    .msg-r.agent .msg-bbl { background:#e8f4fd; color:#1565c0; border-radius:10px 10px 10px 0; }
    .msg-r.user  .msg-bbl  { background:#e8f5e9; color:#2e7d32; border-radius:10px 10px 0 10px; }
    .msg-role-lbl { font-size:10px; font-weight:700; color:#aaa; text-transform:uppercase; margin-bottom:2px; }
</style>

@if(count($logs) > 0)
    @foreach($logs as $log)
    @php
        $statusColor = match($log->call_status) {
            'called'               => '#28a745',
            'booked','self-booked' => '#17a2b8',
            'failed'               => '#dc3545',
            'no_response'          => '#6c757d',
            default                => '#ffc107',
        };
        $statusLabel = match($log->call_status) {
            'called'      => 'Called',
            'booked'      => 'Booked',
            'self-booked' => 'Self-Booked',
            'failed'      => 'Failed',
            'no_response' => 'No Response',
            default       => 'Pending',
        };
        $linked             = $log->callAppointment;
        $transcript         = $log->transcript ? json_decode($log->transcript, true) : null;
        $reminderTranscript = $log->reminder_transcript ? json_decode($log->reminder_transcript, true) : null;
        $hasReminder        = (bool) $log->reminder_call_fired_at;
        $attempts           = $log->attempts ?? collect();
        $initialAttempts    = $attempts->where('call_type', 'initial')->values();
        $reminderAttempts   = $attempts->where('call_type', 'reminder')->values();
    @endphp
    <div class="ai-log-card">

        {{-- Header --}}
        <div class="log-header" data-toggle="collapse" data-target="#ai-log-collapse-{{ $log->id }}">
            <div class="d-flex align-items-center" style="gap:10px;">
                <span class="ai-log-badge" style="background:{{ $statusColor }};color:{{ $log->call_status === 'pending' ? '#212529' : '#fff' }};">{{ $statusLabel }}</span>
                <strong style="font-size:13px;">#{{ $log->id }}</strong>
                <span style="font-size:12px;color:#666;">{{ $log->created_at ? $log->created_at->format('m/d/Y h:i A') : '-' }}</span>
                @if($log->call_fired_at)
                    <span style="font-size:11px;color:#888;"><i class="fa fa-phone mr-1"></i>{{ $log->call_fired_at->format('m/d/Y h:i A') }}</span>
                @endif
                @if($hasReminder)
                    <span class="ai-log-badge" style="background:#fd7e14;color:#fff;font-size:10px;"><i class="fa fa-bell mr-1"></i>+Reminder</span>
                @endif
            </div>
            <div class="d-flex align-items-center" style="gap:6px;">
                @if($log->admin_verified)
                    <span class="ai-log-badge" style="background:#6f42c1;color:#fff;"><i class="fa fa-check"></i> Verified</span>
                @endif
                @if($log->converted_to_appointment)
                    <span class="ai-log-badge" style="background:#007bff;color:#fff;"><i class="fa fa-calendar-check-o"></i> Converted</span>
                @endif
                <a href="{{ url('ai-call-logs/'.$log->id) }}" class="btn btn-sm btn-outline-primary" style="font-size:11px;padding:2px 10px;" onclick="event.stopPropagation();" target="_blank">
                    <i class="fa fa-eye"></i> View
                </a>
                <i class="fa fa-chevron-down" style="font-size:11px;color:#aaa;"></i>
            </div>
        </div>

        <div class="collapse" id="ai-log-collapse-{{ $log->id }}">

            {{-- Booking bar --}}
            @if($linked)
            <div style="padding:9px 15px; background:#f0fff4; border-bottom:1px solid #e9ecef; font-size:12px; display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                <strong style="color:#28a745;"><i class="fa fa-calendar-check-o mr-1"></i>Booking #{{ $linked->id }}</strong>
                <span>{{ $linked->date ? date('m/d/Y', strtotime($linked->date)) : '-' }}</span>
                <span>{{ $linked->time_slot_display }}</span>
                @if($linked->location)
                    <span class="text-muted">{{ $linked->location->location_name }}</span>
                @endif
                <a href="{{ url('ai-call-logs/booking/'.$linked->id) }}" class="btn btn-sm btn-outline-success" style="font-size:10px;padding:1px 8px;" target="_blank">
                    <i class="fa fa-eye"></i> Booking Detail
                </a>
            </div>
            @endif

            {{-- Info grid --}}
            <div class="ai-info-grid">
                <div class="ai-info-cell">
                    <div class="lbl">Agency</div>
                    <div class="val">{{ $log->agency_name ?? '-' }}</div>
                </div>
                <div class="ai-info-cell">
                    <div class="lbl">Initial Call Attempts</div>
                    <div class="val">{{ $log->call_attempts ?? 0 }}</div>
                </div>
                <div class="ai-info-cell">
                    <div class="lbl">Reminder Call</div>
                    <div class="val">
                        @if($hasReminder)
                            {{ $log->reminder_call_fired_at->format('m/d/Y h:i A') }}
                            @if($log->reminder_call_attempts)
                                <small class="text-muted d-block">{{ $log->reminder_call_attempts }} attempt(s)</small>
                            @endif
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </div>
                </div>
                <div class="ai-info-cell">
                    <div class="lbl">SMS</div>
                    <div class="val">
                        @if($log->confirmation_sms_sent)
                            <span class="ai-log-badge" style="background:#28a745;color:#fff;font-size:10px;">Conf.</span>
                        @endif
                        @if($log->reminder_sms_sent)
                            <span class="ai-log-badge" style="background:#17a2b8;color:#fff;font-size:10px;">Reminder</span>
                        @endif
                        @if(!$log->confirmation_sms_sent && !$log->reminder_sms_sent)
                            <span class="text-muted">-</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Call tabs --}}
            <div style="border-top:1px solid #f0f0f0;">
                <div class="call-tabs" id="call-tabs-{{ $log->id }}">
                    @if($initialAttempts->count() > 0)
                        @foreach($initialAttempts as $attempt)
                        <button class="call-tab-btn {{ $loop->first ? 'active' : '' }}"
                                onclick="switchPatientCallTab({{ $log->id }}, 'attempt-{{ $attempt->id }}')">
                            <span class="call-dot" style="background:#28a745;"></span>
                            Initial #{{ $attempt->attempt_number }}
                            <span style="font-size:10px;color:#aaa;">{{ $attempt->fired_at ? $attempt->fired_at->format('m/d H:i') : '' }}</span>
                        </button>
                        @endforeach
                    @else
                        {{-- Legacy: no attempts rows yet --}}
                        <button class="call-tab-btn active" onclick="switchPatientCallTab({{ $log->id }}, 'initial-legacy')">
                            <span class="call-dot" style="background:{{ $log->call_fired_at ? '#28a745' : '#ccc' }};"></span>
                            Initial Call
                            @if(($log->call_attempts ?? 0) > 1)
                                <span style="font-size:10px;color:#aaa;">({{ $log->call_attempts }}x)</span>
                            @endif
                        </button>
                    @endif
                    @if($reminderAttempts->count() > 0)
                        @foreach($reminderAttempts as $attempt)
                        <button class="call-tab-btn"
                                onclick="switchPatientCallTab({{ $log->id }}, 'attempt-{{ $attempt->id }}')">
                            <span class="call-dot" style="background:#fd7e14;"></span>
                            Reminder #{{ $attempt->attempt_number }}
                            <span style="font-size:10px;color:#aaa;">{{ $attempt->fired_at ? $attempt->fired_at->format('m/d H:i') : '' }}</span>
                        </button>
                        @endforeach
                    @elseif($hasReminder)
                        <button class="call-tab-btn" onclick="switchPatientCallTab({{ $log->id }}, 'reminder-legacy')">
                            <span class="call-dot" style="background:#fd7e14;"></span>
                            Reminder Call
                        </button>
                    @endif
                </div>

                {{-- Per-attempt panes --}}
                @foreach($attempts as $attempt)
                @php
                    $attemptTranscript = $attempt->transcript ? json_decode($attempt->transcript, true) : null;
                @endphp
                <div class="call-tab-pane log-body {{ $loop->first ? 'active' : '' }}" id="call-pane-attempt-{{ $attempt->id }}-{{ $log->id }}">
                    <div style="font-size:11px;color:#888;margin-bottom:10px;">
                        <span class="ai-log-badge" style="background:{{ $attempt->call_type === 'initial' ? '#28a745' : '#fd7e14' }};color:#fff;font-size:10px;">
                            {{ ucfirst($attempt->call_type) }} #{{ $attempt->attempt_number }}
                        </span>
                        @if($attempt->fired_at)
                            &nbsp;{{ $attempt->fired_at->format('m/d/Y h:i A') }}
                        @endif
                        &nbsp;&middot;&nbsp;
                        <span style="color:{{ $attempt->status === 'called' ? '#28a745' : '#dc3545' }}">{{ ucfirst($attempt->status ?? '-') }}</span>
                    </div>

                    @if($attempt->conversation_id)
                    <div class="mb-3">
                        <div style="font-size:11px;font-weight:600;color:#555;margin-bottom:6px;"><i class="fa fa-volume-up mr-1 text-success"></i>Recording</div>
                        <audio controls style="width:100%;height:36px;">
                            <source src="{{ url('ai-call-logs/attempt/'.$attempt->id.'/audio') }}" type="audio/mpeg">
                        </audio>
                    </div>
                    @endif

                    <div id="attempt-transcript-wrap-{{ $attempt->id }}">
                        @if($attempt->transcript)
                            <div style="font-size:11px;font-weight:600;color:#555;margin-bottom:6px;"><i class="fa fa-comments mr-1 text-primary"></i>Transcript</div>
                            <div class="transcript-box">
                                @if(is_array($attemptTranscript))
                                    @foreach($attemptTranscript as $msg)
                                        @php
                                            $role = strtolower($msg['role'] ?? 'agent');
                                            $text = $msg['message'] ?? ($msg['text'] ?? ($msg['content'] ?? ''));
                                        @endphp
                                        <div class="msg-r {{ $role === 'user' ? 'user' : 'agent' }}">
                                            <div>
                                                <div class="msg-role-lbl">{{ $role === 'user' ? 'Patient' : 'AI Agent' }}</div>
                                                <div class="msg-bbl">{{ $text }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div style="white-space:pre-wrap;font-size:12px;">{{ $attempt->transcript }}</div>
                                @endif
                            </div>
                        @elseif($attempt->conversation_id)
                            <div class="text-muted" style="font-size:12px;margin-bottom:8px;">Transcript not loaded yet.</div>
                            <button class="btn btn-sm btn-outline-info" onclick="fetchPatientAttemptTranscript({{ $attempt->id }})">
                                <i class="fa fa-refresh mr-1"></i>Fetch Transcript
                            </button>
                        @endif
                    </div>

                    @if(!$attempt->conversation_id && !$attempt->transcript)
                    <div class="text-muted text-center py-2" style="font-size:12px;">
                        <i class="fa fa-microphone-slash mr-1"></i>No recording or transcript available.
                    </div>
                    @endif
                </div>
                @endforeach

                {{-- Legacy initial pane (no attempts rows) --}}
                @if($initialAttempts->count() === 0)
                <div class="call-tab-pane active log-body" id="call-pane-initial-legacy-{{ $log->id }}">
                    @if($log->call_fired_at)
                    <div style="font-size:11px;color:#888;margin-bottom:10px;">
                        <i class="fa fa-clock-o mr-1"></i>Fired: {{ $log->call_fired_at->format('m/d/Y h:i A') }}
                        @if($log->call_attempts) &nbsp;&middot;&nbsp; {{ $log->call_attempts }} attempt(s) @endif
                    </div>
                    @endif
                    @if($log->conversation_id)
                    <div class="mb-3">
                        <div style="font-size:11px;font-weight:600;color:#555;margin-bottom:6px;"><i class="fa fa-volume-up mr-1 text-success"></i>Recording</div>
                        <audio controls style="width:100%;height:36px;">
                            <source src="{{ url('ai-call-logs/'.$log->id.'/audio') }}" type="audio/mpeg">
                        </audio>
                    </div>
                    @endif
                    @if($log->transcript)
                    <div style="font-size:11px;font-weight:600;color:#555;margin-bottom:6px;"><i class="fa fa-comments mr-1 text-primary"></i>Transcript</div>
                    <div class="transcript-box">
                        @if(is_array($transcript))
                            @foreach($transcript as $msg)
                                @php $role = strtolower($msg['role'] ?? 'agent'); $text = $msg['message'] ?? ($msg['text'] ?? ($msg['content'] ?? '')); @endphp
                                <div class="msg-r {{ $role === 'user' ? 'user' : 'agent' }}">
                                    <div><div class="msg-role-lbl">{{ $role === 'user' ? 'Patient' : 'AI Agent' }}</div><div class="msg-bbl">{{ $text }}</div></div>
                                </div>
                            @endforeach
                        @else
                            <div style="white-space:pre-wrap;font-size:12px;">{{ $log->transcript }}</div>
                        @endif
                    </div>
                    @endif
                    @if(!$log->conversation_id && !$log->transcript)
                    <div class="text-muted text-center py-3" style="font-size:12px;"><i class="fa fa-microphone-slash mr-1"></i>No recording or transcript available.</div>
                    @endif
                </div>
                @endif

                {{-- Legacy reminder pane (has reminder but no reminder attempts rows) --}}
                @if($hasReminder && $reminderAttempts->count() === 0)
                <div class="call-tab-pane log-body" id="call-pane-reminder-legacy-{{ $log->id }}">
                    <div style="font-size:11px;color:#888;margin-bottom:10px;">
                        <i class="fa fa-clock-o mr-1"></i>Fired: {{ $log->reminder_call_fired_at->format('m/d/Y h:i A') }}
                        @if($log->reminder_call_attempts) &nbsp;&middot;&nbsp; {{ $log->reminder_call_attempts }} attempt(s) @endif
                    </div>
                    @if($log->reminder_conversation_id)
                    <div class="mb-3">
                        <div style="font-size:11px;font-weight:600;color:#555;margin-bottom:6px;"><i class="fa fa-volume-up mr-1 text-success"></i>Recording</div>
                        <audio controls style="width:100%;height:36px;">
                            <source src="{{ url('ai-call-logs/'.$log->id.'/reminder-audio') }}" type="audio/mpeg">
                        </audio>
                    </div>
                    <div id="reminder-transcript-wrap-{{ $log->id }}">
                        @if($log->reminder_transcript)
                            <div style="font-size:11px;font-weight:600;color:#555;margin-bottom:6px;"><i class="fa fa-comments mr-1 text-primary"></i>Transcript</div>
                            <div class="transcript-box">
                                @if(is_array($reminderTranscript))
                                    @foreach($reminderTranscript as $msg)
                                        @php $role = strtolower($msg['role'] ?? 'agent'); $text = $msg['message'] ?? ($msg['text'] ?? ($msg['content'] ?? '')); @endphp
                                        <div class="msg-r {{ $role === 'user' ? 'user' : 'agent' }}">
                                            <div><div class="msg-role-lbl">{{ $role === 'user' ? 'Patient' : 'AI Agent' }}</div><div class="msg-bbl">{{ $text }}</div></div>
                                        </div>
                                    @endforeach
                                @else
                                    <div style="white-space:pre-wrap;font-size:12px;">{{ $log->reminder_transcript }}</div>
                                @endif
                            </div>
                        @else
                            <div class="text-muted" style="font-size:12px;margin-bottom:8px;">Transcript not loaded yet.</div>
                            <button class="btn btn-sm btn-outline-info" onclick="fetchReminderTranscript({{ $log->id }})">
                                <i class="fa fa-refresh mr-1"></i>Fetch Transcript
                            </button>
                        @endif
                    </div>
                    @else
                    <div class="text-muted" style="font-size:12px; background:#fff8f0; border:1px solid #ffe0c0; border-radius:6px; padding:12px;">
                        <i class="fa fa-info-circle mr-1 text-warning"></i>
                        Reminder call was fired but no conversation ID was captured yet.
                        <a href="{{ url('ai-call-logs/'.$log->id) }}" target="_blank" class="ml-1" style="font-size:11px;">View full log →</a>
                    </div>
                    @endif
                </div>
                @endif

            </div>
        </div>
    </div>
    @endforeach
@else
    <div class="text-center text-muted py-5">
        <i class="mdi mdi-robot" style="font-size:36px;"></i>
        <div class="mt-2" style="font-size:13px;">No AI call logs found for this patient.</div>
    </div>
@endif

<script>
function switchPatientCallTab(logId, tabKey) {
    var $tabs = $('#call-tabs-' + logId + ' .call-tab-btn');
    $tabs.removeClass('active');
    $tabs.filter(function() {
        return $(this).attr('onclick').indexOf("'" + tabKey + "'") !== -1;
    }).addClass('active');
    $('[id^="call-pane-"][id$="-' + logId + '"]').removeClass('active');
    $('#call-pane-' + tabKey + '-' + logId).addClass('active');
}

function fetchPatientAttemptTranscript(attemptId) {
    var $wrap = $('#attempt-transcript-wrap-' + attemptId);
    var $btn = $wrap.find('button');
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i>Loading...');
    $.get('/ai-call-logs/attempt/' + attemptId + '/fetch-transcript', function(res) {
        if (res.status && res.transcript && res.transcript.length) {
            var html = '<div style="font-size:11px;font-weight:600;color:#555;margin-bottom:6px;"><i class="fa fa-comments mr-1 text-primary"></i>Transcript</div><div class="transcript-box">';
            $.each(res.transcript, function(i, msg) {
                var role = (msg.role || 'agent').toLowerCase();
                var text = msg.message || msg.text || msg.content || '';
                var isUser = role === 'user';
                html += '<div class="msg-r ' + (isUser ? 'user' : 'agent') + '">'
                    + '<div><div class="msg-role-lbl">' + (isUser ? 'Patient' : 'AI Agent') + '</div>'
                    + '<div class="msg-bbl">' + $('<div>').text(text).html() + '</div></div></div>';
            });
            html += '</div>';
            $wrap.html(html);
        } else {
            $wrap.html('<div class="text-muted" style="font-size:12px;">No transcript available.</div>');
        }
    }).fail(function() {
        $btn.prop('disabled', false).html('<i class="fa fa-refresh mr-1"></i>Fetch Transcript');
    });
}

function fetchReminderTranscript(logId) {
    var $btn = $('#call-pane-reminder-legacy-' + logId + ' button');
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i>Loading...');
    $.get('/ai-call-logs/' + logId + '/fetch-reminder-conversation', function(res) {
        if (res.status && res.transcript && res.transcript.length) {
            var html = '<div style="font-size:11px;font-weight:600;color:#555;margin-bottom:6px;"><i class="fa fa-comments mr-1 text-primary"></i>Transcript</div><div class="transcript-box">';
            $.each(res.transcript, function(i, msg) {
                var role = (msg.role || 'agent').toLowerCase();
                var text = msg.message || msg.text || msg.content || '';
                var isUser = role === 'user';
                html += '<div class="msg-r ' + (isUser ? 'user' : 'agent') + '">'
                    + '<div><div class="msg-role-lbl">' + (isUser ? 'Patient' : 'AI Agent') + '</div>'
                    + '<div class="msg-bbl">' + $('<div>').text(text).html() + '</div></div></div>';
            });
            html += '</div>';
            $('#reminder-transcript-wrap-' + logId).html(html);
        } else {
            $('#reminder-transcript-wrap-' + logId).html('<div class="text-muted" style="font-size:12px;">No transcript available for this reminder call.</div>');
        }
    }).fail(function() {
        $btn.prop('disabled', false).html('<i class="fa fa-refresh mr-1"></i>Fetch Transcript');
    });
}
</script>
