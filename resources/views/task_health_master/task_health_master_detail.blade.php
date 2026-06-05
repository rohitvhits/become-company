@include('include/header')
@include('include/sidebar')
<link rel="stylesheet" href="{{ asset('assets/modulejs/css/patient-new-design.css') }}?time={{ env('timestamp') }}">
<link href="{{ asset('assets/css/toastr/toastr.min.css') }}" rel="stylesheet" type="text/css" />

<style>
/* ── Page wrapper ── */
.thd-page { padding: 24px 24px 40px; }

/* ── Page title bar ── */
.thd-page-title {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 20px; padding: 0 2px;
}
.thd-page-title-inner {
    display: flex; align-items: center; gap: 10px;
}
.thd-page-title-icon {
    color: #1a73e8; font-size: 28px; line-height: 1; flex-shrink: 0;
}
.thd-page-title-text { display: flex; flex-direction: column; gap: 3px; }
.thd-page-title-text .title-main {
    font-size: 20px; font-weight: 700; color: #1e2d40; line-height: 1.2;
}
.thd-page-title-text .title-sub {
    font-size: 13px; font-weight: 400; color: #8c9db5; letter-spacing: .2px;
}

/* ── Header card ── */
.thd-header-card {
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 12px rgba(0,0,0,.08);
    border: 1px solid #e8edf5;
    margin-bottom: 22px;
    border-top: 4px solid #1a73e8;
}
.thd-header-top {
    background: #fff;
    padding: 22px 28px 18px;
    display: flex;
    align-items: center;
    gap: 18px;
    flex-wrap: wrap;
}
.thd-avatar {
    width: 56px; height: 56px;
    border-radius: 50%;
    background: #e8f0fe;
    border: 2px solid #c5d8fb;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px; font-weight: 700; color: #1a73e8; flex-shrink: 0;
    letter-spacing: 1px;
}
.thd-header-meta { flex: 1; min-width: 0; }
.thd-header-name {
    font-size: 21px; font-weight: 700; color: #1e2d40;
    margin-bottom: 9px; line-height: 1.2;
}
.thd-header-badges { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; }

/* ── Outlined badges ── */
.thd-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 12px; border-radius: 20px;
    font-size: 12px; font-weight: 600; letter-spacing: .3px;
    background: transparent; border: 1.5px solid;
}
.thd-badge-id    { border-color: #c5d8fb; color: #1a73e8; }
.thd-badge-type  { border-color: #c9d1db; color: #4a5568; }
.thd-badge-agency { border-color: #b2dfdb; color: #00695c; }
/* status outlines */
.thd-badge-success    { border-color: #4caf7d; color: #1b6b3a; }
.thd-badge-danger     { border-color: #e57373; color: #b71c1c; }
.thd-badge-warning    { border-color: #f0ad4e; color: #7d4e00; }
.thd-badge-info       { border-color: #64b5f6; color: #0d47a1; }
.thd-badge-default    { border-color: #c9d1db; color: #4a5568; }

.thd-header-actions { display: flex; gap: 8px; align-items: center; flex-shrink: 0; }
.thd-btn-back {
    background: #f5f7fa; border: 1px solid #dde3ed;
    color: #4a5568; border-radius: 6px; padding: 7px 16px; font-size: 13px;
    text-decoration: none; display: inline-flex; align-items: center; gap: 5px;
    transition: all .15s; font-weight: 500;
}
.thd-btn-back:hover { background: #e8edf5; color: #1a73e8; border-color: #c5d8fb; text-decoration: none; }

/* ── Info strip ── */
.thd-info-strip {
    background: #fff;
    padding: 14px 28px;
    display: flex; flex-wrap: wrap; gap: 0;
    border-top: 1px solid #e3eaf5;
}
.thd-info-pill {
    display: flex; flex-direction: column; padding: 6px 22px 6px 0;
    min-width: 140px;
    border-right: 1px solid #eee;
    margin-right: 22px;
}
.thd-info-pill:last-child { border-right: none; margin-right: 0; }
.thd-info-label {
    font-size: 11px; font-weight: 600; text-transform: uppercase;
    letter-spacing: .5px; color: #8c9db5; margin-bottom: 4px;
}
.thd-info-value { font-size: 14px; font-weight: 600; color: #1e2d40; }
.thd-info-value a { color: #1a73e8; text-decoration: none; }
.thd-info-value a:hover { text-decoration: underline; }

/* ── Left vertical tab layout ── */
.thd-body { display: flex; gap: 0; align-items: flex-start; }
.thd-sidebar {
    width: 190px; flex-shrink: 0;
    background: #fff; border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,.07);
    padding: 10px 0; overflow: hidden;
}
.thd-sidebar-item {
    display: flex; align-items: center; gap: 9px;
    padding: 12px 18px; cursor: pointer; font-size: 14px;
    color: #4a5568; border-left: 3px solid transparent;
    transition: all .15s; text-decoration: none;
    border-bottom: 1px solid #f5f5f5;
}
.thd-sidebar-item:last-child { border-bottom: none; }
.thd-sidebar-item:hover { background: #f0f4ff; color: #1a73e8; }
.thd-sidebar-item.active {
    background: #e8f0fe; color: #1a73e8; font-weight: 600;
    border-left-color: #1a73e8;
}
.thd-sidebar-item i { font-size: 17px; width: 20px; text-align: center; }

/* ── Content area ── */
.thd-content { flex: 1; min-width: 0; padding-left: 20px; }
.thd-tab-pane { display: none; }
.thd-tab-pane.active { display: block; }

/* ── Section cards ── */
.thd-section-card {
    background: #fff; border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,.06);
    margin-bottom: 18px; overflow: hidden;
}
.thd-section-header {
    padding: 13px 20px 11px;
    border-bottom: 1px solid #f0f0f0;
    display: flex; align-items: center; gap: 8px;
    background: #fafbfc;
}
.thd-section-title {
    font-size: 13px; font-weight: 700; color: #2d3748;
    text-transform: uppercase; letter-spacing: .5px;
}
.thd-section-icon { color: #1a73e8; font-size: 18px; }
.thd-section-body { padding: 20px 22px; }

/* ── Detail grid ── */
.thd-detail-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px 24px;
}
.thd-detail-grid-2 {
    grid-template-columns: repeat(2, 1fr);
}
.thd-detail-item {}
.thd-detail-label {
    font-size: 11px; font-weight: 600; text-transform: uppercase;
    letter-spacing: .5px; color: #8c9db5; margin-bottom: 5px;
}
.thd-detail-value {
    font-size: 14px; font-weight: 500; color: #1e2d40;
    word-break: break-word;
}
.thd-detail-value a { color: #1a73e8; text-decoration: none; }
.thd-detail-value a:hover { text-decoration: underline; }
.thd-detail-value .badge { font-size: 11px; font-weight: 600; }

/* ── ID pills row ── */
.thd-id-row {
    display: flex; gap: 12px; flex-wrap: wrap; padding: 14px 20px;
    background: #f8faff; border-bottom: 1px solid #eef2fb;
}
.thd-id-chip {
    display: flex; flex-direction: column;
    background: #fff; border: 1px solid #dde8f8;
    border-radius: 8px; padding: 8px 14px; min-width: 110px;
}
.thd-id-chip-label {
    font-size: 9px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .5px; color: #8c9db5; margin-bottom: 3px;
}
.thd-id-chip-value {
    font-size: 13px; font-weight: 700; color: #1e2d40;
}
.thd-id-chip-value a { color: #1a73e8; text-decoration: none; }
.thd-id-chip-value a:hover { text-decoration: underline; }
.thd-id-chip.linked { border-color: #28a745; }
.thd-id-chip.linked .thd-id-chip-label { color: #28a745; }

/* ── Status badge helper ── */
.thd-status-badge {
    display: inline-block; padding: 3px 12px; border-radius: 20px;
    font-size: 11px; font-weight: 600;
}
</style>

<div class="main-panel">
    <div class="content-wrapper thd-page">

        {{-- ── Page Title ──────────────────────────────────────────────────── --}}
        <div class="thd-page-title">
            <div class="thd-page-title-inner">
                <div class="thd-page-title-text">
                    <span class="title-main">
                        ID #{{ $record->id }} &nbsp;{{ trim(($record->first_name ?? '') . ' ' . ($record->middle_name ?? '') . ' ' . ($record->last_name ?? '')) ?: '—' }}
                    </span>
                    <span class="title-sub">
                        <i class="mdi mdi-cellphone" style="font-size:11px;vertical-align:middle;"></i>
                        {{ $record->mobile ?: '—' }}
                    </span>
                </div>
                
            </div>
            {{-- Back button --}}
                <div class="thd-header-actions">
                    <a href="{{ url('task-health') }}" class="thd-btn-back">
                        <i class="mdi mdi-arrow-left"></i> Back to List
                    </a>
                </div>
        </div>

        {{-- ── Header Card ────────────────────────────────────────────────── --}}
        <div class="thd-header-card">
            {{-- Info strip --}}
            <div class="thd-info-strip">
                @if($record->type)
                    <div class="thd-info-pill">
                        <div class="thd-info-label"><i class="mdi mdi-tag-outline" style="font-size:13px;"></i></i> Type</div>
                        <div class="thd-info-value">{{ $record->type }}</div>
                    </div>
                @endif
                @if($record->agencyDetails)
                    <div class="thd-info-pill">
                        <div class="thd-info-label"><i class="mdi mdi-hospital-building" style="font-size:13px;"></i></i></i> Agency</div>
                        <div class="thd-info-value">{{ $record->agencyDetails->agency_name }}</div>
                    </div>
                @endif
                <div class="thd-info-pill">
                    <div class="thd-info-label"><i class="mdi mdi-cake-variant" style="font-size:13px;"></i> Date of Birth</div>
                    <div class="thd-info-value">{{ $record->dob ? date('m/d/Y', strtotime($record->dob)) : '—' }}</div>
                </div>
                <div class="thd-info-pill">
                    <div class="thd-info-label"><i class="mdi mdi-gender-male-female" style="font-size:13px;"></i> Gender</div>
                    <div class="thd-info-value">{{ $record->gender ? ucfirst($record->gender) : '—' }}</div>
                </div>
                <div class="thd-info-pill">
                    <div class="thd-info-label"><i class="mdi mdi-cellphone" style="font-size:13px;"></i> Mobile</div>
                    <div class="thd-info-value">{{ $record->mobile ?: '—' }}</div>
                </div>
                <div class="thd-info-pill">
                    <div class="thd-info-label"><i class="mdi mdi-phone" style="font-size:13px;"></i> Phone</div>
                    <div class="thd-info-value">{{ $record->phone ?: '—' }}</div>
                </div>
                <div class="thd-info-pill">
                    <div class="thd-info-label"><i class="mdi mdi-calendar-plus" style="font-size:13px;"></i> Created</div>
                    <div class="thd-info-value">{{ $record->created_date ? date('m/d/Y', strtotime($record->created_date)) : '—' }}</div>
                </div>
                @if($record->patient_id)
                <div class="thd-info-pill">
                    <div class="thd-info-label"><i class="mdi mdi-account-check" style="font-size:13px;"></i> Portal Patient</div>
                    <div class="thd-info-value">
                        <a href="{{ url('patient/view/' . $record->patient_id) }}" target="_blank">
                            #{{ $record->patient_id }} <i class="mdi mdi-open-in-new" style="font-size:13px;vertical-align:middle;"></i>
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- ── Body: sidebar + content ─────────────────────────────────────── --}}
        <div class="thd-body">

            {{-- Sidebar --}}
            <div class="thd-sidebar">
                <a href="javascript:void(0)" class="thd-sidebar-item active" onclick="thdSwitchTab('info', this)">
                    <i class="mdi mdi-account-details-outline"></i> Patient Details
                </a>
                <a href="javascript:void(0)" class="thd-sidebar-item" onclick="thdSwitchTab('ids', this)">
                    <i class="mdi mdi-identifier"></i> IDs & Links
                </a>
            </div>

            {{-- Content --}}
            <div class="thd-content">

                {{-- ── Patient Details tab ── --}}
                <div class="thd-tab-pane active" id="thd-tab-info">

                    {{-- Contact --}}
                    <div class="thd-section-card">
                        <div class="thd-section-header">
                            <i class="thd-section-icon mdi mdi-phone-outline"></i>
                            <span class="thd-section-title">Contact Information</span>
                        </div>
                        <div class="thd-section-body">
                            <div class="thd-detail-grid">
                                <div class="thd-detail-item">
                                    <div class="thd-detail-label">Mobile</div>
                                    <div class="thd-detail-value">{{ $record->mobile ?: '—' }}</div>
                                </div>
                                <div class="thd-detail-item">
                                    <div class="thd-detail-label">Phone</div>
                                    <div class="thd-detail-value">{{ $record->phone ?: '—' }}</div>
                                </div>
                                <div class="thd-detail-item">
                                    <div class="thd-detail-label">Language</div>
                                    <div class="thd-detail-value">{{ $record->language ?: '—' }}</div>
                                </div>
                                <div class="thd-detail-item">
                                    <div class="thd-detail-label">Emergency Contact</div>
                                    <div class="thd-detail-value">{{ $record->emergency_contact_name ?: '—' }}</div>
                                </div>
                                <div class="thd-detail-item">
                                    <div class="thd-detail-label">Emergency Phone</div>
                                    <div class="thd-detail-value">{{ $record->emergency_phone ?: '—' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Address --}}
                    <div class="thd-section-card">
                        <div class="thd-section-header">
                            <i class="thd-section-icon mdi mdi-map-marker-outline"></i>
                            <span class="thd-section-title">Address</span>
                        </div>
                        <div class="thd-section-body">
                            <div class="thd-detail-grid">
                                <div class="thd-detail-item" style="grid-column: span 2;">
                                    <div class="thd-detail-label">Address</div>
                                    <div class="thd-detail-value">
                                        {{ trim(($record->address1 ?? '') . ' ' . ($record->address2 ?? '')) ?: '—' }}
                                    </div>
                                </div>
                                <div class="thd-detail-item">
                                    <div class="thd-detail-label">City</div>
                                    <div class="thd-detail-value">{{ $record->city ?: '—' }}</div>
                                </div>
                                <div class="thd-detail-item">
                                    <div class="thd-detail-label">State</div>
                                    <div class="thd-detail-value">{{ $record->state ?: '—' }}</div>
                                </div>
                                <div class="thd-detail-item">
                                    <div class="thd-detail-label">Zip Code</div>
                                    <div class="thd-detail-value">{{ $record->zip_code ?: '—' }}</div>
                                </div>
                                @if(!empty($record->county))
                                <div class="thd-detail-item">
                                    <div class="thd-detail-label">County</div>
                                    <div class="thd-detail-value">{{ $record->county }}</div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Insurance & Services --}}
                    <div class="thd-section-card">
                        <div class="thd-section-header">
                            <i class="thd-section-icon mdi mdi-shield-check-outline"></i>
                            <span class="thd-section-title">Insurance &amp; Services</span>
                        </div>
                        <div class="thd-section-body">
                            <div class="thd-detail-grid">
                                <div class="thd-detail-item">
                                    <div class="thd-detail-label">Insurance</div>
                                    <div class="thd-detail-value">{{ $record->insurance_name ?: '—' }}</div>
                                </div>
                                <div class="thd-detail-item">
                                    <div class="thd-detail-label">CIN</div>
                                    <div class="thd-detail-value">{{ $record->cin ?: '—' }}</div>
                                </div>
                                <div class="thd-detail-item">
                                    <div class="thd-detail-label">SSN</div>
                                    <div class="thd-detail-value">
                                        {{ $record->ssn ? '***-**-' . substr($record->ssn, -4) : '—' }}
                                    </div>
                                </div>
                                <div class="thd-detail-item">
                                    <div class="thd-detail-label">Service Start Date</div>
                                    <div class="thd-detail-value">
                                        {{ $record->service_start_date ? date('m/d/Y', strtotime($record->service_start_date)) : '—' }}
                                    </div>
                                </div>
                                <div class="thd-detail-item">
                                    <div class="thd-detail-label">Due Date</div>
                                    <div class="thd-detail-value">
                                        {{ $record->due_date ? date('m/d/Y', strtotime($record->due_date)) : '—' }}
                                    </div>
                                </div>
                                @if(!empty($record->fu_date))
                                <div class="thd-detail-item">
                                    <div class="thd-detail-label">Follow-up Date</div>
                                    <div class="thd-detail-value">{{ date('m/d/Y', strtotime($record->fu_date)) }}</div>
                                </div>
                                @endif
                                <div class="thd-detail-item">
                                    <div class="thd-detail-label">Patient Code</div>
                                    <div class="thd-detail-value">{{ $record->patient_code ?: '—' }}</div>
                                </div>
                                <div class="thd-detail-item">
                                    <div class="thd-detail-label">Created Date</div>
                                    <div class="thd-detail-value">
                                        {{ $record->created_date ? date('m/d/Y h:i A', strtotime($record->created_date)) : '—' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- ── IDs & Links tab ── --}}
                <div class="thd-tab-pane" id="thd-tab-ids">
                    <div class="thd-section-card">
                        <div class="thd-section-header">
                            <i class="thd-section-icon mdi mdi-identifier"></i>
                            <span class="thd-section-title">System IDs &amp; External Links</span>
                        </div>
                        <div class="thd-section-body">
                            <div class="thd-detail-grid thd-detail-grid-2">
                                <div class="thd-detail-item">
                                    <div class="thd-detail-label">Task Health Master ID</div>
                                    <div class="thd-detail-value"><strong>#{{ $record->id }}</strong></div>
                                </div>
                                <div class="thd-detail-item">
                                    <div class="thd-detail-label">Task Health Task ID</div>
                                    <div class="thd-detail-value">{{ $record->task_id ?: '—' }}</div>
                                </div>
                                <div class="thd-detail-item">
                                    <div class="thd-detail-label">TH Patient ID</div>
                                    <div class="thd-detail-value">{{ $record->task_health_patient_id ?: '—' }}</div>
                                </div>
                                <div class="thd-detail-item">
                                    <div class="thd-detail-label">Portal Patient ID</div>
                                    <div class="thd-detail-value">
                                        @if($record->patient_id)
                                            <a href="{{ url('patient/view/' . $record->patient_id) }}" target="_blank">
                                                #{{ $record->patient_id }}
                                                <i class="mdi mdi-open-in-new" style="font-size:11px;vertical-align:middle;"></i>
                                            </a>
                                        @else
                                            <span style="color:#9ca3af;">Not linked</span>
                                        @endif
                                    </div>
                                </div>
                                @if($record->old_patient_id)
                                <div class="thd-detail-item">
                                    <div class="thd-detail-label">Old Patient ID</div>
                                    <div class="thd-detail-value">
                                        <a href="{{ url('patient/view/' . $record->old_patient_id) }}" target="_blank">
                                            #{{ $record->old_patient_id }}
                                            <i class="mdi mdi-open-in-new" style="font-size:11px;vertical-align:middle;"></i>
                                        </a>
                                    </div>
                                </div>
                                @endif
                                <div class="thd-detail-item">
                                    <div class="thd-detail-label">Agency</div>
                                    <div class="thd-detail-value">{{ $record->agencyDetails->agency_name ?? '—' }}</div>
                                </div>
                                @if($record->third_party_callback_url)
                                <div class="thd-detail-item" style="grid-column: span 2;">
                                    <div class="thd-detail-label">Third-Party Callback URL</div>
                                    <div class="thd-detail-value" style="word-break:break-all;font-size:12px;color:#6c757d;">
                                        {{ $record->third_party_callback_url }}
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            </div>{{-- /.thd-content --}}
        </div>{{-- /.thd-body --}}

    </div>
    @include('include/footer')
</div>

<script src="{{ asset('assets/css/toastr/toastr.min.js') }}"></script>
<script>
function thdSwitchTab(tabId, el) {
    // Hide all panes
    document.querySelectorAll('.thd-tab-pane').forEach(function(p) { p.classList.remove('active'); });
    // Deactivate all sidebar items
    document.querySelectorAll('.thd-sidebar-item').forEach(function(s) { s.classList.remove('active'); });
    // Activate selected
    var pane = document.getElementById('thd-tab-' + tabId);
    if (pane) pane.classList.add('active');
    if (el) el.classList.add('active');
}
</script>
