@include('include/header')
@include('include/sidebar')

<link rel="stylesheet" href="{{ asset('/assets/vendors/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{ asset('/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css')}}">
<link href="{{ asset('/assets/css/toastr/toastr.min.css')}}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ asset('/assets/css/global.css')}}">
<link rel="stylesheet" href="{{ asset('assets/jquery-confirmation/css/jquery-confirm.min.css')}}">

<style>
    .select2-container {
        width: 100% !important
    }

    .page-title-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .select2-container--default .select2-selection--multiple {
        border-radius: 0px !important;
        border: 1px solid #e3e7ed !important;
    }

    .merge-btn-container {
        display: none;
        margin-bottom: 15px;
        padding: 15px;
        background: #e7f7f9;
        border: 1px solid #b3e0e6;
        border-radius: 5px;
    }

    .merge-btn-container.active {
        display: block;
    }

    .merge-button {
        background: #28a745 !important;
        border-color: #28a745 !important;
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
        font-weight: 500;
    }

    .merge-button:hover {
        background: #218838 !important;
        border-color: #1e7e34 !important;
    }

    .selected-count {
        margin-right: 15px;
        font-weight: 600;
        color: #333;
    }

    .checkbox-cell {
        width: 40px;
        text-align: center;
    }

    .patient-checkbox {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    #selectAllCheckbox {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    .modal-header {
        background-color: #040404;
        color: white;
    }

    .modal-header .close {
        color: white;
    }

    .shimmer-loader {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
    }

    @keyframes shimmer {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
</style>

<div class="main-panel main-page-box">
    <?php $auth = auth()->user(); ?>
    <div class="content-wrapper content-wrapper-box">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Patient Agency Merge</h5>
            <div class="page-rightbtns cust-page-rightbtns">
                <div>
                    <a href="{{ url('/appointment') }}" class="btn btn-secondary cust-right-btn">
                        <i class="mdi mdi-arrow-left"></i> Back to Patient List
                    </a>
                    <button type="button" id="sync-btn" class="btn cust-right-btn" style="background-color: #28a745;color:#fff;">
                        <i class="mdi mdi-sync"></i> Sync
                    </button>
                    <a href="javascript:void(0)" id="filter-btn" class="btn cust-right-btn" style="background-color: #00879E;color:#fff;">
                        <i class="mdi mdi-filter-outline"></i> Filter <span class="active-filter"></span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Important Note - Always Visible -->
        <div class="mb-3" style="padding: 12px 15px; position: sticky; top: 0; z-index: 1000; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
            <i class="fa fa-info-circle" style="color: #0c5460; font-size: 16px;"></i>
            <strong style="color: #0c5460; font-size: 14px;">Please note:</strong>
            <span style="color: #0c5460; font-size: 14px;">The merge process may take a few minutes to complete.</span>
        </div>

        <hr />

        <!-- Merge Button Container -->
        <div class="merge-btn-container" id="mergeBtnContainer">
            <span class="selected-count">
                <i class="fa fa-check-circle text-success"></i>
                <span id="selectedCount">0</span> record(s) selected
            </span>
            <button type="button" class="btn merge-button" id="mergeButton">
                <i class="fa fa-exchange-alt"></i> Merge with Another Agency
            </button>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div id="search-filter-btn" style="display: none;">
                    <div class="card search-card1 cust-card-box">
                        <div class="card-body p-0 border-0 form-patient-list-box">
                            <form id="filterForm">
                                <div class="row form-row-gap">
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="agency_fk"><b>Deleted Agency</b></label>
                                                    <select class="form-control" name="agency_fk" id="agency_fk">
                                                        <option value="">Select Agency</option>
                                                        @foreach($agencyList as $agency)
                                                            <option value="{{ $agency->id }}">
                                                                {{ $agency->agency_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="type"><b>Type</b></label>
                                                    <select class="form-control" name="type" id="type">
                                                        <option value="">All Types</option>
                                                        @foreach($typeList as $typeItem)
                                                            <option value="{{ $typeItem }}">
                                                                {{ $typeItem }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row form-row-gap mt-3">
                                <div class="col-md-9">
                                    <div class="appointment-btn-box" style="justify-content:left !important;margin-top: 6px;">
                                            <button type="button" class="btn btn-info search-btn1" id="searchBtn">
                                                <i class="mdi mdi-magnify"></i> Search
                                            </button>
                                            <a href="javascript:void(0)" class="btn btn-light cust-right-btn" id="resetBtn">
                                                <i class="mdi mdi-reload"></i> Reset
                                            </a>
                                        </div>
                                </div>
                            </div>

                                </div>

                                <div class="row form-row-gap mt-3">
                                    
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <!-- Loading Shimmer -->
                <div class="location-wise-data-loader shimmer_id table-responsive" id="loadingShimmer">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th><input type="checkbox" disabled></th>
                                <th>No</th>
                                <th>Patient Code</th>
                                <th>Patient Name</th>
                                <th>Agency</th>
                                <th>Mobile</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Type</th>
                                <th>Created Date</th>
                                <th>Created By</th>
                            </tr>
                        </thead>
                        <tbody class="shimmer-loader">
                            <tr>
                                <td colspan="12" style="height: 50px;"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- AJAX Content -->
                <div class="table-responsive" id="patientListContainer" style="display: none;">
                    <!-- Content will be loaded via AJAX -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Merge Modal -->
<div class="modal fade" id="mergeModal" tabindex="-1" role="dialog" aria-labelledby="mergeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title" id="mergeModalLabel">
                    <i class="fa fa-exchange-alt"></i> Merge Records to Another Agency
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info border-left-info">
                    <div class="d-flex align-items-center">
                        <i class="fa fa-info-circle fa-2x mr-3"></i>
                        <div>
                            <strong>Selected Records:</strong> <span id="modalSelectedCount" class="badge badge-primary badge-pill">0</span> patient record(s)
                            <br>
                            <small class="text-muted">These records will be merged to the agency you select below.</small>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="newAgencySelect" class="font-weight-bold">
                        Select Target Agency <span class="text-danger">*</span>
                    </label>
                    <select id="newAgencySelect" class="form-control select2-design select2-modal" style="width: 100%;">
                        <option value="">-- Select Agency --</option>
                        @foreach($agencyWithoutSelectedList as $agency)
                            <option value="{{ $agency->id }}" data-agency-name="{{ $agency->agency_name }}">
                                {{ $agency->agency_name }}
                            </option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">
                        <i class="fa fa-lightbulb"></i> Choose the active agency where patient records will be transferred.
                    </small>
                </div>

                <div class="alert alert-warning border-left-warning mt-3">
                    <div class="d-flex align-items-start">
                        <i class="fa fa-exclamation-triangle fa-lg mr-2 mt-1"></i>
                        <div>
                            <strong>Important:</strong> This action will create a merge request that will be processed by the system.
                            Patient records will be updated within a few minutes.
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times"></i> Cancel
                </button>
                <button type="button" class="btn btn-success" id="confirmMergeBtn">
                    <i class="fa fa-check"></i> Confirm Merge
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Modal Enhancements */
    #mergeModal .modal-header {
        border-bottom: 3px solid #040404;
    }

    #mergeModal .modal-footer {
        border-top: 2px solid #dee2e6;
    }

    #mergeModal .border-left-info {
        border-left: 4px solid #17a2b8;
        background-color: #d1ecf1;
    }

    #mergeModal .border-left-warning {
        border-left: 4px solid #ffc107;
        background-color: #fff3cd;
    }

    #mergeModal .select2-container {
        z-index: 9999 !important;
    }

    #mergeModal .select2-container .select2-selection--single {
        height: 45px;
        padding: 8px 12px;
        border: 2px solid #ced4da;
        border-radius: 5px;
    }

    #mergeModal .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px;
        font-size: 15px;
    }

    #mergeModal .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 43px;
    }

    #mergeModal .form-group label {
        font-size: 15px;
        margin-bottom: 8px;
    }

    #mergeModal .badge-pill {
        font-size: 16px;
        padding: 6px 12px;
    }

    #mergeModal .alert {
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    #mergeModal .btn {
        padding: 10px 24px;
        font-weight: 600;
        border-radius: 5px;
        transition: all 0.3s ease;
    }

    #mergeModal .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }

    #mergeModal .modal-content {
        border: none;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    }
</style>

@include('include/footer')

<script src="{{ asset('/assets/vendors/select2/select2.min.js')}}"></script>
<script src="{{ asset('/assets/css/toastr/toastr.min.js')}}"></script>
<script src="{{ asset('assets/jquery-confirmation/js/jquery-confirm.min.js')}}"></script>

<script>
    var _PATIENT_AGENCY_MERGE_AJAX = "{{ url('patient-agency-merge/ajax-list') }}";
    var _PATIENT_AGENCY_MERGE_UPDATE = "{{ url('patient-agency-merge/update') }}";
    var _PATIENT_AGENCY_MERGE_SYNC = "{{ url('patient-agency-merge/sync') }}";
    var _CSRF_TOKEN = "{{ csrf_token() }}";
</script>

<script>
    $(document).ready(function() {
        // Initialize Select2
        $('.select2-design').select2();
        // $('#agency_fk').select2();

        // Initialize Datepicker
        $('.datepicker').datepicker({
            format: 'mm/dd/yyyy',
            autoclose: true,
            todayHighlight: true
        });

        // Toggle filter panel
        $('#filter-btn').on('click', function() {
            $('#search-filter-btn').slideToggle('fast');
            $(this).toggleClass('active');
        });

        // Show filter panel by default
        $('#search-filter-btn').show();
        $('#filter-btn').addClass('active');
    });
</script>

<script src="{{ asset('assets/modulejs/patient_agency_merge.js') }}"></script>
