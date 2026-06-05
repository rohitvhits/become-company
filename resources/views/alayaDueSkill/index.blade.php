@include('include/header')
@include('include/sidebar')

<link rel="stylesheet" href="{{ asset('assets/vendors/select2/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css')}}">

<link href="{{ asset('assets/css/toastr/toastr.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/global.css')}}" rel="stylesheet" type="text/css" />
<style>
    .page-title-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    span.select2.select2-container.select2-container--default {
        width: 100% !important;
    }

    .select2-container--default .select2-selection--multiple {
        border-radius: 0px !important;
        border: 1px solid #e3e7ed !important;
    }
</style>
<div class="main-panel main-page-box">

    <div class="content-wrapper content-wrapper-box">

        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">AlayaCare Due Skill (<span id="appointment_id"></span>)</h5>
            <div class="page-rightbtns cust-page-rightbtns">
                <div>
                    <a href="javascript:void(0)" id="filter-btn" class="btn cust-right-btn" style="background-color: #00879E;color:#fff;"><i
                            class="mdi mdi-filter-outline"></i>Filter <span class="active-filter"></span></a>

                    

                    @can('add-appointment-alayacare-due-skill')
                    <a href="javascript:void(0)" class="btn btn-primary cust-right-btn"
                        onclick="addAppointment()"><i class="mdi mdi-plus"></i> Add Appointment</a>
                    @endcan
                </div>
            </div>
        </div>
        <hr />
        <div class="row ">
            <div class="col-sm-12">
                <div id="search-filter-btn" style="display: none;">
                    <div class="card search-card1 cust-card-box" id="search-div">
                        <div class="card-body p-0 border-0 form-patient-list-box">
                            <form id="search-form">
                                <div class="row form-row-gap mb-2">
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Agency Name</label>
                                                    <select name="agency_fk[]" id="agency_fk"
                                                        class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100"
                                                        multiple="multiple" data-placeholder="Select Agency Name">
                                                        <?php foreach ($agencyList as $rwAgency) { ?>
                                                            <option value="<?php echo $rwAgency->id; ?>">
                                                                <?php echo $rwAgency->agency_name; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Employee Name</label>
                                                    <input type="text" name="full_name" id="full_name" class="form-control" value="" placeholder="Enter Employee Name">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Employee Code</label>
                                                    <input type="text" name="code" id="code" class="form-control" value="" placeholder="Enter Employee Code">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Employee Phone</label>
                                                    <input type="text" name="caregiver_phone" id="caregiver_phone" class="form-control" value="" placeholder="Enter Employee Phone">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row form-row-gap">
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Skill Name</label>
                                                    <input type="text" name="medical_name" id="medical_name" class="form-control" value="" placeholder="Enter Employee Skill">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Due Date</label>
                                                    <input type="text" name="due_date" id="due_date" class="form-control datepickernn" autocomplete="off" value=""  placeholder="Enter Due Date">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="">Appointment Status</label>
                                                    <select name="status" class="form-control" id="status">
                                                        <option value="">All</option>
                                                        <option value="Pending">Pending</option>
                                                        <option value="Booked">Added</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Employee Status</label>
                                                    <select name="employee_status" class="form-control" id="employee_status">
                                                        <option value="">All</option>
                                                        <option value="active">Active</option>
                                                        <option value="inactive">Inactive</option>
                                                        <option value="applicant">Applicant</option>
                                                        <option value="on_hold">On Hold</option>
                                                        <option value="pending">Pending</option>
                                                        <option value="suspended">Suspended</option>
                                                        <option value="rejected">Rejected</option>
                                                        <option value="terminated">Terminated</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="">Created Date</label>
                                                    <input type="text" name="created_date" id="created_date" class="form-control" autocomplete="off" value=""  placeholder="Enter Created Date">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                               
                                <div class="row form-row-gap mt-3">
                                    <div class="col-md-9">
                                        <div class="appointment-btn-box" style="justify-content:left !important">
                                            <input type="button" name="search"
                                                class="btn search-btn1 searchAppoinment" id="search-data"
                                                value="Search" onclick="dueSkillList(1)">

                                            <a href="javascript:void(0)" class="btn btn-light btn-rounded btn-fw btn-sm" onclick="refresh()"><i class="mdi mdi-reload"></i> Clear</a>
                                            @can('alayacare-due-skill-export')
                                            <a href="javascript:void(0)" id="export-csv-btn" class="btn cust-right-btn" style="background-color: #28a745;color:#fff;" onclick="exportToCSV()"><i
                                            class="mdi mdi-file-export"></i> Export CSV</a>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 ">
                <div class="location-wise-data-loader shimmer_id hasClass">
                    <div class="col-md-12 pl-0">
                        <table id="" class="table table-bordered ">
                            <thead>
                                <th>#</th>
                                <th style="white-space:nowrap">Agency Name</th>
                                <th style="white-space:nowrap">Employee Name</th>
                                <th style="white-space:nowrap">Employee Code</th>
                                <th style="white-space:nowrap">Date of Birth</th>
                                <th style="white-space:nowrap">Employee Phone</th>
                                <th style="white-space:nowrap">Gender</th>
                                <th style="white-space:nowrap">Employee Status</th>
                                <th style="white-space:nowrap">Skill Name</th>
                                <th style="white-space:nowrap">Due Date</th>
                                <th style="white-space:nowrap">Appointment Status</th>
                                <th style="white-space:nowrap">Created Date</th>
                                <th style="white-space:nowrap">Action</th>
                                
                            </thead>
                            <tbody class="shimmer-loader">
                                <tr>
                                    <td colspan="16"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
                <span id="resp">

                </span>



            </div>
        </div>
    </div>
    <div class="row" id="blank_div" style='margin-top: 25px;'>
       
    </div>
</div>

@include('alayaDueSkill._partial.add_due_skill_appointment_modal')
@include('include/footer')



<script src="{{ asset('assets/js/jquery-ui.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/moment.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/daterangepicker.min.js')}}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('css/daterangepicker.css')}}" />
<script src="{{ asset('assets/css/toastr/toastr.min.js')}}"></script>

<script src="{{ asset('assets/vendors/select2/select2.min.js')}}"></script>
<script src="{{ asset('assets/js/select2.js')}}"></script>

<script>
    var _DUESKILL_LIST = '{{ url("alayacare/alayacare-skill/due-skill-ajax-list")}}';
    var _ADDPATIENTAPPOINTMENT = '{{  url("alayacare/alayacare-skill/add-alayacare-patient-appointment")  }}'
    var _CSRF_TOKEN = '{{  csrf_token()  }}';
    var _GET_SERVICE = '{{ url("/ajax-service")}}';
    var _EXISTING_SERVICES = '<?php echo json_encode(old('service_id')); ?>';
    var _EXPORT_CSV = '{{ url("alayacare/alayacare-skill/due-skill-export-csv")}}';

    function exportToCSV() {
        // Get search form data
        var formData = new FormData(document.getElementById('search-form'));
        var queryParams = new URLSearchParams();

        // Convert FormData to query parameters
        for (var pair of formData.entries()) {
            if (pair[1]) { // Only add if value is not empty
                queryParams.append(pair[0], pair[1]);
            }
        }

        // Open the export URL with search parameters
        var exportUrl = _EXPORT_CSV;
        if (queryParams.toString()) {
            exportUrl += '?' + queryParams.toString();
        }

        window.open(exportUrl, '_blank');
    }

</script>
<script src="{{ asset('assets/modulejs/patient_alayacare_due_skill.js')}}?time={{ env('timestamp')}}"></script>