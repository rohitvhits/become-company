<div class="d-flex align-items-center justify-content-between mb-3">
    <p class="card-title mb-0">Demographic Details</p>
</div>
<div class="row">
    <div class="col-12">
        <div class="col-12 loader-calender" id="load-caregiver-demographics" style="display:flex;justify-content:center;margin-top:10%">
            <img src="{{ asset('/ajax-loader.gif') }}" class="" alt="loader" id="loader-patient-demographic-details" style="display:none">
        </div>
    </div>
    <div class="col-12">
        <div id="hha-patient-demographic-details-id"></div>
    </div>
</div>

<style>
    /* Demographic Section Card */
    .demo-card {
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        margin-bottom: 15px;
    }
    .demo-card-header {
        padding: 8px 15px;
        border-bottom: 2px solid transparent;
        font-size: 13px;
        font-weight: 600;
        color: #2c3e50;
        background: #f8f9fa;
        display: flex;
        align-items: center;
    }
    .demo-card-header i {
        margin-right: 8px;
        font-size: 15px;
    }
    .demo-card-header.header-blue { border-bottom-color: #3498db; }
    .demo-card-header.header-blue i { color: #3498db; }
    .demo-card-header.header-green { border-bottom-color: #27ae60; }
    .demo-card-header.header-green i { color: #27ae60; }
    .demo-card-header.header-orange { border-bottom-color: #e67e22; }
    .demo-card-header.header-orange i { color: #e67e22; }
    .demo-card-header.header-purple { border-bottom-color: #8e44ad; }
    .demo-card-header.header-purple i { color: #8e44ad; }
    .demo-card-header.header-red { border-bottom-color: #e74c3c; }
    .demo-card-header.header-red i { color: #e74c3c; }
    .demo-card-header.header-teal { border-bottom-color: #16a085; }
    .demo-card-header.header-teal i { color: #16a085; }

    .demo-card-body {
        padding: 10px 15px;
    }

    /* Inline field row: label on left, value on right */
    .demo-row {
        display: flex;
        padding: 5px 0;
        font-size: 13px;
        line-height: 1.5;
    }
    .demo-row-label {
        min-width: 150px;
        color: #6c757d;
        font-weight: 500;
        flex-shrink: 0;
    }
    .demo-row-value {
        color: #212529;
        font-weight: 400;
        word-break: break-word;
    }
    .demo-row-value.na-text {
        color: #adb5bd;
    }

    /* Status / Admission badge */
    .demo-badge {
        display: inline-block;
        padding: 1px 10px;
        border-radius: 3px;
        font-size: 12px;
        font-weight: 600;
    }
    .demo-badge-green {
        background: #d4edda;
        color: #155724;
    }
    .demo-badge-orange {
        background: #fff3cd;
        color: #856404;
    }
    .demo-badge-red {
        background: #f8d7da;
        color: #721c24;
    }
    .demo-badge-gray {
        background: #e9ecef;
        color: #495057;
    }

    /* Emergency contact - inline horizontal layout */
    .demo-emergency-row {
        display: flex;
        flex-wrap: wrap;
        padding: 10px 15px;
        font-size: 13px;
        gap: 10px 30px;
    }
    .demo-emergency-item {
        display: flex;
        align-items: center;
    }
    .demo-emergency-item .demo-row-label {
        min-width: auto;
        margin-right: 8px;
    }

    /* Diagnosis table */
    .demo-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    .demo-table thead th {
        background: #f8f9fa;
        color: #495057;
        font-weight: 600;
        padding: 8px 12px;
        border-bottom: 2px solid #dee2e6;
        text-align: left;
        white-space: nowrap;
    }
    .demo-table tbody td {
        padding: 8px 12px;
        border-bottom: 1px solid #f1f3f5;
        color: #212529;
        vertical-align: top;
    }
    .demo-table tbody tr:last-child td {
        border-bottom: none;
    }
    .demo-table tbody tr:hover {
        background: #f8f9fa;
    }
    .demo-table .diag-code {
        font-weight: 600;
        color: #2c3e50;
    }
    .demo-table .diag-desc {
        color: #6c757d;
        font-size: 12px;
    }
    .demo-primary-badge {
        display: inline-block;
        padding: 1px 8px;
        border-radius: 3px;
        font-size: 11px;
        font-weight: 600;
        background: #cce5ff;
        color: #004085;
    }
    .demo-table-empty {
        text-align: center;
        padding: 20px 12px;
        color: #adb5bd;
        font-style: italic;
        font-size: 13px;
    }
</style>
