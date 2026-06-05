@include('include/header')
@include('include/sidebar')

<link href="{{ asset('/assets/css/toastr/toastr.min.css')}}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ asset('/assets/css/global.css')}}">

<style>
    .page-title-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    /* ── Toggle switch ── */
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 46px;
        height: 24px;
        flex-shrink: 0;
    }
    .toggle-switch input { opacity: 0; width: 0; height: 0; }
    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: #cbd5e1;
        border-radius: 24px;
        transition: .25s;
    }
    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 18px; width: 18px;
        left: 3px; bottom: 3px;
        background-color: #fff;
        border-radius: 50%;
        transition: .25s;
        box-shadow: 0 1px 4px rgba(0,0,0,.2);
    }
    input:checked + .toggle-slider { background-color: #00879E; }
    input:checked + .toggle-slider:before { transform: translateX(22px); }

    /* ── Group card ── */
    .cfg-group-section {
        border: 1px solid #dee2e6;
        border-radius: 10px;
        margin-bottom: 16px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,.06);
        background: #fff;
    }

    /* ── Group title bar ── */
    .cfg-group-title {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #ffffff;
        background: #00879E;
        border-bottom: none;
        padding: 8px 14px;
        margin: 0;
        line-height: 1.4;
    }

    /* ── Toggle grid: uses background-as-gap trick for dividers ── */
    .cfg-toggle-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 1px;
        background: #e9ecef;
    }
    .cfg-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 12px 14px;
        background: #ffffff;
        margin: 0;
        flex: 1 1 260px;
        transition: background .15s;
        position: relative;
    }
    .cfg-row:hover { background: #f8fffe; }
    .cfg-row.cfg-row--active { background: #eaf8fa; }
    .cfg-row.cfg-row--active::before {
        content: '';
        position: absolute;
        top: 0; left: 0; bottom: 0;
        width: 3px;
        background: #00879E;
        border-radius: 0;
    }
    .cfg-row.cfg-row--active .cfg-label { color: #00789e; }

    /* ── Labels ── */
    .cfg-label { font-size: 12.5px; font-weight: 600; color: #1e293b; margin-bottom: 2px; }
    .cfg-desc  { font-size: 11px; color: #64748b; line-height: 1.5; }

    /* ── Sub-rows: doc-type & notes ── */
    .cfg-sub-row {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        padding: 10px 14px;
        background: #f8fafc;
        border-top: 1px solid #e9ecef;
    }
    .cfg-sub-row + .cfg-sub-row { border-top: 1px solid #e9ecef; }
    .cfg-sub-row--notes { background: #fffdf5; border-top-color: #fde68a55; }

    .cfg-sub-row-left { flex: 1; min-width: 160px; }
    .cfg-sub-row-left .cfg-label { font-size: 12px; margin-bottom: 1px; }
    .cfg-sub-row-left .cfg-desc  { font-size: 10.5px; }

    .cfg-sub-row-right {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-shrink: 0;
    }

    /* full-width form row */
    .cfg-sub-row-form {
        width: 100%;
        padding-top: 8px;
    }
    .cfg-sub-row-form select,
    .cfg-sub-row-form textarea {
        font-size: 12px;
        margin-bottom: 6px;
    }
    .cfg-sub-row-form select { max-width: 340px; }
    .cfg-sub-row-form textarea { resize: vertical; }

    /* ── Value badge ── */
    .cfg-val {
        display: inline-flex;
        align-items: center;
        font-size: 11.5px;
        padding: 3px 10px;
        border-radius: 5px;
        border: 1px solid #dee2e6;
        background: #f1f5f9;
        color: #64748b;
        max-width: 260px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        font-weight: 500;
    }
    .cfg-val.is-set {
        background: #e0f7fa;
        color: #006e82;
        border-color: #80deea;
        font-weight: 600;
    }

    /* ── Edit button — matches .cfg-val size exactly ── */
    .cfg-edit-btn {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        font-size: 11.5px;
        font-weight: 500;
        padding: 3px 10px;
        line-height: inherit;
        border-radius: 5px;
        border: 1px solid #adb5bd;
        background: #fff;
        color: #495057;
        cursor: pointer;
        white-space: nowrap;
        text-decoration: none;
        box-shadow: none;
        outline: none;
    }
    .cfg-edit-btn:hover {
        background: #f1f5f9;
        border-color: #6c757d;
        color: #212529;
    }
</style>

<div class="main-panel main-page-box">
    <div class="content-wrapper content-wrapper-box">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Agency Task Health Settings</h5>
            <div class="page-rightbtns cust-page-rightbtns">
                <a href="javascript:void(0)" id="filter-btn" class="btn cust-right-btn" style="background-color: #00879E; color: #fff;">
                    <i class="mdi mdi-filter-outline"></i> Filter <span class="active-filter"></span>
                </a>
            </div>
        </div>

        <hr />

        <div class="row">
            <div class="col-sm-12">
                <div id="search-filter-btn" style="display: none;">
                    <div class="card search-card1 cust-card-box" id="search-div">
                        <div class="card-body p-0 border-0 form-patient-list-box">
                            <div class="row form-row-gap">
                                <div class="col-md-3">
                                    <div class="form-group cust-select-box">
                                        <label for="agency_name">Agency Name</label>
                                        <input autocomplete="off" type="text" class="form-control"
                                            name="agency_name" id="agency_name" placeholder="Agency Name">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group cust-select-box">
                                        <label for="email">Email</label>
                                        <input autocomplete="off" type="text" class="form-control"
                                            name="email" id="email" placeholder="Email">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group cust-select-box">
                                        <label for="phone">Phone</label>
                                        <input autocomplete="off" type="text" class="form-control"
                                            name="phone" id="phone" placeholder="Phone">
                                    </div>
                                </div>
                            </div>
                            <div class="row form-row-gap mt-3">
                                <div class="col-md-9">
                                    <div class="appointment-btn-box" style="justify-content: left !important;">
                                        <input type="button" class="btn search-btn1 searchAppoinment" id="search-data" value="Search">
                                        <a href="javascript:void(0)" class="btn btn-light cust-right-btn" onclick="agencySettingsReset()">
                                            <i class="mdi mdi-reload"></i> Reset
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="agency-settings-loader shimmer_id table-responsive">
                    <div class="col-md-12 pl-0">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 20px;">#</th>
                                    <th>ID</th>
                                    <th>Agency Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Configure</th>
                                </tr>
                            </thead>
                            <tbody class="shimmer-loader">
                                <tr><td colspan="6"></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="table-responsive">
                    <span id="response_agency_settings_list"></span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Configure modal ── --}}
    <div class="modal fade" id="agencyConfigModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" style="max-width:920px;">
            <div class="modal-content" style="border:none;border-radius:12px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.2);">

                <div class="modal-header" style="background:linear-gradient(135deg,#00879E,#005f70);border-bottom:none;padding:14px 20px;">
                    <div>
                        <h5 class="modal-title mb-0" style="font-size:15px;font-weight:700;color:#fff;">
                            <i class="mdi mdi-cog-outline"></i> Agency Configuration
                        </h5>
                        <div id="cfg-modal-agency-name" style="font-size:12px;color:rgba(255,255,255,.75);margin-top:2px;"></div>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:.8;">&times;</button>
                </div>

                <div class="modal-body" style="padding:24px;max-height:80vh;overflow-y:auto;">
                    <div id="cfg-modal-body"></div>
                </div>

                <div class="modal-footer" style="padding:10px 20px;background:#f8f9fa;border-top:1px solid #e9ecef;">
                    <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>

    <script type="text/javascript">
        var _AGENCY_SETTINGS_AJAX   = "{{ url('agency-task-health-setting-ajax-list') }}";
        var _AGENCY_SETTINGS_TOGGLE = "{{ url('agency-task-health-setting-toggle-update') }}";
        var _CSRF_TOKEN             = "{{ csrf_token() }}";
        var _AGENCY_SETTING_FIELDS  = @json($settingFields);
        var _CFG_POC_SYNC_URL       = "{{ url('hha/hha-patient/document-poc-type') }}";
        var _CFG_POC_SAVE_URL       = "{{ url('save-poc-document-type') }}";
        var _CFG_SUP_TYPES_URL      = "{{ url('hha-document-type') }}";
        var _CFG_SUP_SAVE_URL       = "{{ url('save-supervision-document-type') }}";
        var _CFG_ASSESSMENT_SAVE_URL = "{{ url('save-patient-assessment-document-type') }}";
        var _CFG_PACKAGE_SAVE_URL    = "{{ url('save-patient-package-document-type') }}";
        var _CFG_CMS485_SAVE_URL     = "{{ url('save-cms485-document-type') }}";
        var _CFG_KARDEX_SAVE_URL     = "{{ url('save-emergency-kardex-document-type') }}";
        var _CFG_POC_NOTES_SAVE_URL  = "{{ url('agency-task-health-setting-poc-notes') }}";
    </script>
    <script src="{{ asset('assets/modulejs/agency_settings/agency_task_health_setting_list_module.js') }}?time={{ env('timestamp') }}"></script>

    @include('include/footer')
