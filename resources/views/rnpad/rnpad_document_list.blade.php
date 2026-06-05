@include('include/header')
@include('include/sidebar')

<link rel="stylesheet" href="{{ asset('/assets/vendors/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{ asset('/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css')}}">
<link href="{{ asset('/assets/css/toastr/toastr.min.css')}}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ asset('/assets/css/global.css')}}">
<link rel="stylesheet" href="{{ asset('assets/jquery-confirmation/css/jquery-confirm.min.css')}}">
<link rel="stylesheet" href="{{ asset('css/jquery.fancybox.min.css')}}" />
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
</style>

<div class="main-panel main-page-box">
    <?php
    $auth = auth()->user();
    ?>
    <div class="content-wrapper content-wrapper-box">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">RNPad Documents Report</h5>
            <div class="page-rightbtns cust-page-rightbtns">
                <div>
                    <a href="javascript:void(0)" id="filter-btn" class="btn cust-right-btn" style="background-color: #00879E;color:#fff;">
                        <i class="mdi mdi-filter-outline"></i>Filter <span class="active-filter"></span>
                    </a>
                </div>
            </div>
        </div>
        <hr />

        <div class="row">
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
                                                    <label for="agency_id">Agency</label>
                                                    <select class="form-control select2_agency" name="agency_id" id="agency_id" multiple>
                                                        
                                                        @foreach($agency_list as $ag)
                                                        <option value="{{ $ag->id }}">{{ $ag->agency_name }}</option>
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
                                                    <label for="patient_name">Patient Name</label>
                                                    <input type="text" name="patient_name" id="patient_name" class="form-control" placeholder="Patient Name">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="document_name">Document Name</label>
                                                    <input type="text" name="document_name" id="document_name" class="form-control" placeholder="Document Name">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="service">Service</label>
                                                    <select class="form-control" name="service" id="service" multiple>
                                                        
                                                        @foreach($services as $service)
                                                        <option value="{{ $service->id }}">{{ $service->name }}</option>
                                                        @endforeach
                                                    </select>
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
                                                    <label for="created_date">Created Date</label>
                                                    <input type="text" readonly name="created_date" id="created_date" class="form-control" autocomplete="off" placeholder="Created Date">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="status">Status</label>
                                                    <select class="form-control" name="status" id="status" multiple>
                                                        
                                                        <option value="Pending">Pending</option>
                                                        <option value="cancelled">Cancelled</option>

                                                        <option value="booked">Booked</option>
                                                        <option value="completed">Completed</option>

                                                        <option value="noshow">No Show</option>

                                                        <option value="arrived" >Arrived</option>
                                                        <option value="processing">Processing</option>
                                                        <option value="Not interested">Not Interested
                                                        </option>
                                                        <option value="hospitalized/rehab">
                                                            Hospitalized/Rehab</option>
                                                        <option value="unableToContact">Unable To Contact
                                                        </option>
                                                        <option value="refused" >Refused</option>
                                                        <option value="checkin">Mark as CheckIn</option>

                                                        <option value="Pending Termination">Pending Termination</option>
                                                        <option value="Onhold">On Hold</option>
                                                        <option value="On Leave">On Leave</option>
                                                        <option value="Terminated">Terminated</option>
                                                        @foreach ($statuses as $key=> $status)
                                                            <option value="{{ $key }}">
                                                                {{ $status }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <div class="row form-row-gap mt-3">
                                <div class="col-md-9">
                                    <div class="appointment-btn-box" style="justify-content:left !important">
                                        <input type="button" name="search" class="btn search-btn1 searchAppoinment" id="search-data" value="Search" onclick="rnpadDocumentAjax(1)">

                                        <a href="javascript:void(0)" class="btn btn-light cust-right-btn" onclick="refresh()">
                                            <i class="mdi mdi-reload"></i> Reset
                                        </a>
                                        @can('rn-pad-export')
                                        <a href="javascript:void(0)" class="btn btn-info cust-right-btn" onclick="exportCsv()">
                                            <i class="mdi mdi-file"></i><span id="exportText">Export CSV</span>
                                            <span class="spinner-border spinner-border-sm d-none" id="exportLoader" role="status" aria-hidden="true"></span>
                                        </a>
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
                <div class="location-wise-data-loader shimmer_id table-responsive">
                    <div class="col-md-12 pl-0">
                        <table id="" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th nowrap>Patient Name</th>
                                    <th nowrap>Document Name</th>
                                    <th nowrap>Service</th>
                                    <th nowrap>Status</th>
                                    <th nowrap>Document Review Status</th>
                                    <th nowrap>Internal Use</th>
                                    <th nowrap>Created Date</th>
                                    <th nowrap>Created By</th>
                                    <th nowrap>Send Date</th>
                                </tr>
                            </thead>
                            <tbody class="shimmer-loader">
                                <tr>
                                    <td colspan="10"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="table table-responsive">
                    <span id="response_rnpad_list"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="row" style='margin-top: 10%;'>
        <pre id='toastrOptions'></pre>
    </div>

    @include('include/footer')
    @include('patient._partial.modal.patient_document.send_rnpad_document_modal')
    <script type="text/javascript" src="<?php echo URL::to('/'); ?>/assets/js/moment.min.js"></script>
    <script type="text/javascript" src="<?php echo URL::to('/'); ?>/assets/js/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo URL::to('/'); ?>/css/daterangepicker.css" />
    <script src="<?php echo URL::to('/'); ?>/assets/vendors/select2/select2.min.js"></script>
    <script src="<?php echo URL::to('/'); ?>/assets/js/select2.js"></script>
    <script src="{{ asset('assets/js/jquery.fancybox.min.js')}}"></script>
    <script type="text/javascript">
        var _RNPAD_DOCUMENT_AJAX = "{{ url('rnpad/document-ajax') }}";
        var _RNPAD_DOCUMENT_EXPORT_CSV = "{{ url('rnpad/document-export-csv') }}";
        var _CSRF_TOKEN = "{{ csrf_token() }}";
        var _DATE_TIME = "{{ date('m/d/Y') }}";
        var _GET_RNPAD_URL_SERVICES = "{{ url('rnpad/rnpad-services-list')}}";
        var _SEND_RNPAD_DOCUMENT = "{{ url('rnpad/send-rnpad-document')}}";
    </script>

    <script src="{{ asset('assets/modulejs/rnpad_document_module.js')}}?time={{ env('timestamp')}}"></script>
