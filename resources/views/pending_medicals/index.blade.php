@include('include/header')
@include('include/sidebar')
<link href="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />

<link rel="stylesheet" href="{{ asset('/assets/css/global.css')}}">
<style>
    .actions {
        margin-top: 20px;
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .page-title-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .badge {
        padding: 5px 10px;
        border-radius: 3px;
        font-size: 12px;
    }

    .badge-success {
        background-color: #28a745;
        color: white;
    }

    .badge-warning {
        background-color: #ffc107;
        color: #000;
    }

    .badge-danger {
        background-color: #dc3545;
        color: white;
    }

    .badge-info {
        background-color: #17a2b8;
        color: white;
    }

    /* Vertical Tabs Styling */
    #employeeTabs .nav-link {
        text-align: left;
        margin-bottom: 5px;
        border-radius: 4px;
    }

    #employeeTabs .nav-link i {
        margin-right: 8px;
    }

    #employeeTabs .nav-link.active {
        background-color: #007bff;
        color: white;
    }

    #employeeDetailsModal .modal-dialog {
        max-width: 900px;
    }

    /* Demographic Tab Styling */
    #demographic-data .form-control-plaintext {
        padding-top: 0.375rem;
        padding-bottom: 0.375rem;
        margin-bottom: 0;
        font-size: 14px;
        color: #495057;
    }

    #demographic-data .form-group {
        margin-bottom: 1rem;
    }

    /* Medical Table Styling */
    #medical-content-table thead th {
        background-color: #f8f9fa;
        font-weight: 600;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #495057;
        border-bottom: 2px solid #dee2e6;
    }

    #medical-content-table tbody td {
        font-size: 14px;
        vertical-align: middle;
        padding: 12px;
    }

    #medical-content-table tbody tr:hover {
        background-color: #f8f9fa;
    }

    /* Card Title */
    .card-title {
        font-size: 16px;
        font-weight: 600;
        color: #495057;
    }
</style>

<div class="main-panel main-page-box">

    <div class="content-wrapper content-wrapper-box">

        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Visiting Pending Medicals</h5>
            
        </div>
        <hr />

        <div class="row">
            <div class="col-sm-12">
                <div id="search-filter-btn" style="display: block;">
                    <div class="card search-card1 cust-card-box" id="search-div">
                        <div class="card-body p-0 border-0 form-patient-list-box">
                            <form id="search-form">
                                <div class="row form-row-gap">
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="agency_id">Agency <span class="text-danger">*</span></label>
                                                    <select name="agency_id" id="agency_id" class="form-control" required>
                                                        <option value="">Select Agency</option>
                                                        @if(isset($agencies) && count($agencies) > 0)
                                                            @foreach($agencies as $agency)
                                                                <option value="{{ $agency->id }}">{{ $agency->agency_name }}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>

                                                    
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="agency_id">Due Medical</label>
                                                    <input type="text" readonly="" name="medical_due_date" value="" class="medical_due_date form-control" id="medical_due_date">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <div class="row form-row-gap mt-3">
                                <div class="col-md-9">
                                    <div class="appointment-btn-box" style="justify-content:left !important">
                                        <input type="button" name="search" class="btn search-btn1 searchAppoinment" id="search-data" value="Search" onclick="loadAjaxList()">
                                        <a href="javascript:void(0)" class="btn btn-light cust-right-btn" onclick="refresh()"><i class="mdi mdi-reload"></i> Reset</a>
                                        @can('pending-visiting-medical-export')
                                            <a href="javascript:void(0)" class="btn btn-warning cust-right-btn" onclick="exportCsv()"><i class="mdi mdi-file"></i> Export Csv</a>
                                        @endcan
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
                <div class="location-wise-data-loader shimmer_id" style="display:none;">
                    <div class="col-md-12 pl-0">
                        <table id="" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Employee Code</th>
                                    <th>Employee Name</th>
                                    <th>DOB</th>
                                    <th>Gender</th>
                                    <th>Phone</th>
                                 
                                    <th>Medical Name</th>
                                    <th>Medical Due Date</th>
                                    <th>Medical Status</th>
                                   
                                   
                                </tr>
                            </thead>
                            <tbody class="shimmer-loader">
                                <tr>
                                    <td colspan="12"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <span id="response_requested_id">
                    <table id="order-listing1" class="table table-bordered table-width1">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Employee Code</th>
                                <th>Employee Name</th>
                                <th>DOB</th>
                                <th>Gender</th>
                                <th>Phone</th>
                                <th>Address</th>
                                <th>Medical Name</th>
                                <th>Medical Due Date</th>
                                <th>Medical Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="12" class="text-center">No record available</td>
                            </tr>
                        </tbody>
                    </table>
                </span>
            </div>
        </div>

    </div>
    <div style="color:red;margin-top:10%" id="blank_div">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    </div>
</div>

<!-- Employee Details Modal -->
@include('pending_medicals._partial.view_modal')
@include('include/footer')
<script type="text/javascript" src="{{ asset('assets/js/moment.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/daterangepicker.min.js')}}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('css/daterangepicker.css')}}" />
<script>
   var _LOAD_DATA_URL = "{{ url('visiting-aid/pending-medicals/data/list')}}";
   var _CSRF_TOKEN = "{{ csrf_token()}}";
   var _ADVANCED_SEARCH_THIRD_PARTY = '{{ url("third-party/advanced-search-third-party")}}';
   var _VISITING_THIRD_PARTY_CODE = $('#third_party_employee_code').val();
   var _EXPORT_CSV = "{{ url('visiting-aid/pending-medicals/export-csv')}}";
   var _GET_EMPLOYEE_PARTY_PENDING_MEDICAL = "{{ url('third-party/employee-pending-medical')}}";
   
$(function(){
    var start = moment().subtract(0, 'days');
   var end = moment();
   $('.medical_due_date').daterangepicker({
        startDate: start,
        endDate: end,
        autoUpdateInput: false,
        startOfWeek: 'sunday',
        ranges: {
            'Select Date': [start, end],
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                'month').endOf('month')],
            'Next Month': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month')
                .endOf('month')
            ],
            'Next Week': [moment().add(1, 'weeks').startOf('isoWeek'), moment().add(1, 'weeks')
                .endOf('isoWeek')
            ],
            'Last Week': [moment().subtract(1, 'weeks').startOf('isoWeek'), moment().subtract(1,
                'weeks').endOf('isoWeek')],
        }
    }, function(chosen_date, end_date) {

        $('.medical_due_date').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
            'MM/DD/YYYY'));
    })
    $('.medical_due_date').on('apply.daterangepicker', function(ev, picker) {
        // Detect "Select Date"
        if (picker.chosenLabel === 'Select Date') {
            $(this).val('');
        } else {
            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        }
    });
})
</script>

<script src="{{ asset('assets/modulejs/pending_medicals/pending_medicals.js')}}?time={{ env('timestamp') }}"></script>

