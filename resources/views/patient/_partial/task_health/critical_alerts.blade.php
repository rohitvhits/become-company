<div id="patient-critical-alerts-section">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-3">
        <span style="font-size:13px;font-weight:600;color:#343a40;">
            <i class="mdi mdi-alert-circle mr-1" style="color:#dc3545;"></i> Critical Alerts
        </span>
        <button class="btn btn-sm btn-outline-secondary" onclick="loadPatientCriticalAlerts()" style="font-size:12px;">
            <i class="mdi mdi-reload"></i> Refresh
        </button>
    </div>

    {{-- Loader --}}
    <div id="patient-ca-loader" class="text-center py-4" style="display:none;">
        <img src="{{ asset('/ajax-loader.gif') }}" alt="Loading...">
        <p class="mt-1 text-muted small">Loading critical alerts...</p>
    </div>

    {{-- Results --}}
    <div id="patient-ca-container">
        <div class="text-center text-muted py-5">
            <i class="mdi mdi-alert-circle-outline" style="font-size:48px;color:#dee2e6;"></i>
            <p class="mt-2">Click "Critical Alerts" tab to load data.</p>
        </div>
    </div>

</div>
