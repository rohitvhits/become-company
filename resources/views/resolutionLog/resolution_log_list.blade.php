@include('include/header')
@include('include/sidebar')
<link rel="stylesheet" href="{{ asset('/assets/vendors/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{ asset('/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('/css/daterangepicker.css')}}" />
<link rel="stylesheet" href="{{ asset('/assets/css/global.css')}}">
<style>
.page-title-main {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.agency-filter-toggle-wrapper {
    display: inline-flex;
    align-items: center;
    margin-left: 8px;
    gap: 6px;
}

.agency-toggle-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 26px;
    height: 26px;
    border-radius: 4px;
    border: 2px solid #ddd;
    background: #fff;
    cursor: pointer;
    transition: all 0.25s ease;
    padding: 0;
    vertical-align: middle;
    position: relative;
    flex-shrink: 0;
}

.agency-toggle-btn i {
    font-size: 18px;
    line-height: 1;
    pointer-events: none;
    display: block;
}

/* Blue/Grey - Professional & Clear (Current Active) */
.agency-toggle-btn[data-mode="include"] {
    background-color: #cfe2ff !important;
    border-color: #0d6efd !important;
    color: #084298 !important;
}

.agency-toggle-btn[data-mode="include"]:hover {
    background-color: #b6d4fe !important;
    transform: scale(1.05);
}

.agency-toggle-btn[data-mode="exclude"] {
    background-color: #e9ecef !important;
    border-color: #6c757d !important;
    color: #495057 !important;
}

.agency-toggle-btn[data-mode="exclude"]:hover {
    background-color: #dee2e6 !important;
    transform: scale(1.05);
}
.agency-toggle-btn:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25);
}

.agency-toggle-btn:active {
    transform: scale(0.95);
}

.agency-toggle-label {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: color 0.25s ease;
    user-select: none;
    white-space: nowrap;
}

/* Match label colors with button colors */
.agency-toggle-label.mode-include {
    color: #0d6efd;
}

.agency-toggle-label.mode-exclude {
    color: #6c757d;
}
</style>
<div class="main-panel main-page-box" style="margin-bottom:15%">

    <div class="content-wrapper content-wrapper-box">

        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Resolution Log Report</h5>
            <div class="page-rightbtns cust-page-rightbtns">
                <div>
                    <a href="javascript:void(0)" id="filter-btn" class="btn cust-right-btn" style="background-color: #00879E;color:#fff;"><i
                            class="mdi mdi-filter-outline"></i>Filter <span></span></a>
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
                                <div class="row form-row-gap">
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Agency Name</label>
                                                    <span class="agency-filter-toggle-wrapper">
                                                        <button type="button" class="agency-toggle-btn" id="agencyToggleBtn"
                                                                data-mode="include" title="Include - Click to switch to Exclude">
                                                            <i class="mdi mdi-plus"></i>
                                                        </button>
                                                        <span class="agency-toggle-label mode-include" id="agencyToggleLabel">Include Agency</span>
                                                    </span>
                                                    <input type="hidden" name="agency_filter_type" id="agency_filter_type"
                                                           value="include">
                                                    <select name="agency_fk[]" id="agency_fk"
                                                        class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100"
                                                        multiple="multiple" data-placeholder="Select Agency Name">
                                                        <?php foreach ($agencyList as $rwAgency) { ?>
                                                            <option value="<?php echo $rwAgency['id']; ?>">
                                                                <?php echo $rwAgency['agency_name']; ?></option>
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
                                                    <label>Team</label>
                                                    <select class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100" name="team" id="team">
                                                        <option value="">Select Team</option>
                                                        <option value="Clinicians">Clinicians</option>
                                                        <option value="MDO Team">MDO Team</option>
                                                        <option value="Schedule Coordinators">Schedule Coordinators</option>
                                                        <option value="Medgen Team">Medgen Team</option>
                                                        <option value="A Manager / Supervisor">A Manager / Supervisor</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Resolution</label>
                                                    <select class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100" name="resolution" id="resolution">
                                                        <option value="">Select Resolution</option>
                                                        <option value="Cancelled">Cancelled</option>
                                                        <option value="Refused">Refused</option>
                                                        <option value="1st Attempt - Unable to Contact">1st Attempt - Unable to Contact</option>
                                                        <option value="2nd Attempt - Unable to Contact">2nd Attempt - Unable to Contact</option>
                                                        <option value="3rd Attempt - Unable to Contact">3rd Attempt - Unable to Contact</option>
                                                        <option value="Patient Deceased">Patient Deceased</option>
                                                        <option value="Telehealth Completed">Telehealth Completed</option>
                                                        <option value="Hospitalised / In Rehab">Hospitalised / In Rehab</option>
                                                        <option value="Processing">Processing</option>
                                                        <option value="Signed">Signed</option>
                                                        <option value="Signed & Sent Back to the Agency">Signed & Sent Back to the Agency</option>
                                                        <option value="Booked">Booked</option>
                                                        <option value="Telehealth Completed , Pending Forms">Telehealth Completed , Pending Forms</option>
                                                        <option value="Appointment Missed">Appointment Missed</option>
                                                        <option value="Patient Asked to Reschedule">Patient Asked to Reschedule</option>
                                                        <option value="Form Completed">Form Completed</option>
                                                        <option value="New Order Received">New Order Received</option>
                                                        <option value="New Form Requested">New Form Requested</option>
                                                        <option value="Service Provided">Service Provided</option>
                                                        <option value="unableToContact">Unable To Contact</option>
                                                        <option value="Closed Temporarily">Closed Temporarily</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Assigned To</label>
                                                    <select name="assigned_to[]" id="assigned_to"
                                                        class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100"
                                                        multiple="multiple" data-placeholder="Select Assigned To">
                                                        @foreach($userList as $user)
                                                            <option value="{{ $user->id }}">
                                                                {{ $user->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-row-gap">
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-sm-12 ">Created Date</label>
                                            <div class="col-sm-12">
                                                <input type="text" name="created_date" readonly value="" class="datepickernn form-control" id="created_date" placeHolder="Created Date">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="row form-row-gap mt-3">
                                <div class="col-md-9">
                                    <div class="appointment-btn-box" style="justify-content:left !important">
                                        <input type="button" name="search"
                                            class="btn search-btn1 searchAppoinment" id="search-data"
                                            value="Search" onclick="loadresolution(1)">

                                        <a href="javascript:void(0)" class="btn btn-light btn-rounded btn-fw btn-sm" onclick="refresh()"><i class="mdi mdi-reload"></i> Clear</a>
                                        @can('resolution-log-export')
                                            <a href="javascript:void(0)" class="btn btn-info btn-rounded" onclick="exportCsv()"><i class="mdi mdi-file"></i>Export</a>
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
            <div class="col-12 ">
                <div class="location-wise-data-loader shimmer_id hideClass" >
                    <div class="col-md-12 pl-0">
                        <table id="" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Agency Name</th>
                                    <th>Portal Id</th>
                                    <th>Portal Name</th>
                                    <th>Team</th>
                                    <th>Resolution</th>
                                    <th>Cancel Reason</th>
                                    <th>Refuse Reason</th>
                                    <th>Notes</th>
                                    <th>Created Date/Created By</th>
                                </tr>
                            </thead>

                            <tbody class="shimmer-loader">
                                <tr>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <span id="ajax_response_id"></span>
            </div>
        </div>
        
    </div>
    
</div>

@include('include/footer')
<script src="{{ asset('assets/vendors/select2/select2.min.js')}}"></script>
<script src="{{ asset('assets/js/select2.js')}}"></script>
<script type="text/javascript" src="{{ asset('/assets/js/moment.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('/assets/js/daterangepicker.min.js')}}"></script>

<script>
    var _RESOLUTION_LOG_AJAX = "{{ url('resolution-log-report-ajax')}}";
    var _RESOLUTION_LOG_EXPORT = "{{ url('resolution-log-report-export')}}";
    var _DATE_TIME = "{{ date('m/d/Y')}}";
</script>
<script type="text/javascript" src="{{ asset('/assets/modulejs/resolutionReport/resolution_report.js')}}"></script>