@include('task_health_critical_alert._partial.hha_upload_modal')

<div id="visitDetailModal" class="vd-overlay">
    <div class="vd-drawer">
        <div class="vd-header">
            <div class="vd-header-left">
                <div class="vd-avatar" id="vd-avatar-initials">…</div>
                <div class="vd-header-info">
                    <h4 id="vModalPatientName">Loading...</h4>
                    <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
                        <span id="vModalTaskId" class="vd-task-id">#—</span>
                        <span id="vd-th-patient-badge" class="vd-th-patient-badge"></span>
                    </div>
                </div>
                <span id="vd-status-badge" class="vd-badge-status info" style="display:none;"></span>
                <span id="vd-type-badge"   class="vd-badge-type"        style="display:none;"></span>
            </div>
            <div class="vd-header-actions">
                <button class="vd-action-btn thf-open-flag" id="vd-flag-btn" style="display:none;background:#3263d1;color:#fff;" title="Manage Flags">
                    <i class="mdi mdi-flag-checkered"></i>
                </button>
                <button class="vd-action-btn vd-action-close" onclick="closeVisitModal()" title="Close">
                    <i class="mdi mdi-close"></i>
                </button>
            </div>
        </div>
        <div class="vd-tabs">
            <button class="vd-tab active" data-tab="general"   onclick="switchVisitTab('general',   this)">General</button>
            <button class="vd-tab"        data-tab="documents" onclick="switchVisitTab('documents', this)">Documents</button>
            @can('create-link-task-health')
            <button class="vd-tab" data-tab="patientrecord" onclick="switchVisitTab('patientrecord', this); _vEnsureMasterPanelLoaded();" id="vd-tab-patientrecord">
                <span id="vd-pr-tab-label">Patient Record</span>
                <span id="vd-pr-tab-badge" style="display:none;margin-left:5px;"></span>
            </button>
            @endcan
        </div>
        <div class="vd-body">
            <div class="vd-panel active" id="vt-general">
                <div id="vt-general-content"></div>
            </div>
            <div class="vd-panel" id="vt-documents">
                <div id="vt-documents-content"></div>
            </div>
            @can('create-link-task-health')
            <div class="vd-panel" id="vt-patientrecord">
                <div id="vt-patientrecord-content">
                    <div style="text-align:center;color:#9ca3af;padding:60px 20px;">
                        <i class="mdi mdi-link-variant" style="font-size:32px;"></i>
                        <p style="margin-top:8px;font-size:13px;">Loading patient record status…</p>
                    </div>
                </div>
            </div>
            @endcan
        </div>
    </div>
</div>