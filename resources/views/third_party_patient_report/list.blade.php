@include('include/header')
@include('include/sidebar')

<style>
    .search-btn1,
    .search-btn1:hover,
    .search-btn1:active,
    .search-btn1:focus {
        background: #007bff !important;
        border: #007bff !important;
        border-radius: 20px;
        height: 36px;
    }
    
.modal-table {
  width: 100%;
  margin: 0;
  font-size: 14px;
}

.modal-table th {
  background-color: #f8f9fa;
  font-weight: bold;
  text-align: left;
  width: 30%;
}

.modal-table td {
  text-align: left;
  width: 70%;
}

.modal-body {
  padding: 20px;
}

.table-container {
  max-height: 300px; /* Set your desired fixed height */
  overflow-y: auto; /* Enable vertical scrolling */
  border: 1px solid #ddd; /* Optional: Add a border for better visuals */
  padding: 10px;
  border-radius: 5px;
}
</style>
<link rel="stylesheet" href="{{ asset('/assets/modulejs/css/visiting_aid.css')}}?time={{ env('timestamp')}}">
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2/select2.min.css">
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
<div class="main-panel">
    <?php
    $auth = auth()->user();
    ?>
    <div class="content-wrapper">


        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Third Party Report List (<span id="appointment_id"></span>)</h5>
        </div>

        <div class="row ">
            <div class="col-sm-12">
                <div class="card " id="search-div">
                    <form method="get" id="formsubmit">
                        <div class="card-body">

                            @csrf

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Agency</label>
                                        <div class="col-sm-12">
                                            <select name="agency_id" class="form-control" id="agency_id">
                                                <option value="">Select Agency</option>
                                                @foreach($agencyList as $val)
                                                <option value="{{ $val->id}}" >{{ $val->agency_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                    </div>
                                </div>


                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Full Name</label>
                                        <div class="col-sm-12">
                                            <input type="text" name="full_name" id="full_name" class="form-control" value="{{ $searchData['full_name'] ?? ''}}">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Mobile</label>
                                        <div class="col-sm-12">
                                            <input autocomplete="off" type="text" class="form-control" name="mobile" id="mobile">
                                        </div>
                                    </div>
                                </div>


                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Date of Birth</label>
                                        <div class="col-sm-12">
                                            <input type="text" name="code" id="dob" class="form-control dob" value="{{ $searchData['dob'] ?? ''}}">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Gender</label>
                                        <div class="col-sm-12">
                                            <select name="gender" class="form-control" id="gender">
                                                <option value="">Select Gender</option>
                                                <option value="Male" @if(isset($searchData['gender']) && $searchData['gender']=='Male' ) selected @endif>Male</option>
                                                <option value="Female" @if(isset($searchData['gender']) && $searchData['gender']=='Female' ) selected @endif>Female</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Status</label>
                                        <div class="col-sm-12">
                                            <select name="gender" class="form-control" id="patient_status">
                                                <option value="">Select Status</option>
                                                <option value="na" @if(isset($searchData['patient_status']) && $searchData['patient_status']=='na' ) selected @endif>Blank</option>
                                                <option value="Pending" @if(isset($searchData['patient_status']) && $searchData['patient_status']=='Pending' ) selected @endif>Pending</option>
                                                <option value="cancelled" @if(isset($searchData['patient_status']) && $searchData['patient_status']=='cancelled' ) selected @endif>Cancelled</option>
                                                <option value="booked" @if(isset($searchData['patient_status']) && $searchData['patient_status']=='booked' ) selected @endif>Booked</option>
                                                <option value="completed" @if(isset($searchData['patient_status']) && $searchData['patient_status']=='completed' ) selected @endif>Completed</option>
                                                <option value="noshow" @if(isset($searchData['patient_status']) && $searchData['patient_status']=='noshow' ) selected @endif>No Show</option>
                                                <option value="arrived" @if(isset($searchData['patient_status']) && $searchData['patient_status']=='arrived' ) selected @endif>Arrived</option>
                                                <option value="processing" @if(isset($searchData['patient_status']) && $searchData['patient_status']=='processing' ) selected @endif>Processing</option>
                                                <option value="Not interested" @if(isset($searchData['patient_status']) && $searchData['patient_status']=='Not interested' ) selected @endif>Not Interested</option>
                                                <option value="hospitalized/rehab" @if(isset($searchData['patient_status']) && $searchData['patient_status']=='hospitalized/rehab' ) selected @endif>Hospitalized/Rehab</option>
                                                <option value="unableToContact" @if(isset($searchData['patient_status']) && $searchData['patient_status']=='unableToContact' ) selected @endif>Unable To Contact</option>
                                                <option value="refused" @if(isset($searchData['patient_status']) && $searchData['patient_status']=='refused' ) selected @endif>Refused</option>
                                                <option value="checkin" @if(isset($searchData['patient_status']) && $searchData['patient_status']=='checkin' ) selected @endif>Mark As Clockin</option>
                                                <option value="PendingTermination" @if(isset($searchData['patient_status']) && $searchData['patient_status']=='PendingTermination' ) selected @endif>Pending Terminated</option>
                                                <option value="Onhold" @if(isset($searchData['patient_status']) && $searchData['patient_status']=='Onhold' ) selected @endif>On Hold</option>
                                                <option value="Onleave" @if(isset($searchData['patient_status']) && $searchData['patient_status']=='Onleave' ) selected @endif>On Leave</option>
                                                <option value="Terminated" @if(isset($searchData['patient_status']) && $searchData['patient_status']=='Terminated' ) selected @endif>Terminated</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Created Date</label>
                                        <div class="col-sm-12">
                                            <input type="text" name="created_date" id="created_date" class="form-control datepickernn" autocomplete="off" value="{{ $searchData['created_date'] ?? '' }}">

                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Due Date</label>
                                        <div class="col-sm-12">
                                            <input type="text" name="due_date" id="due_date" class="form-control due_date" autocomplete="off" value="{{ $searchData['due_date'] ?? '' }}">

                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Patient Linked</label>
                                        <div class="col-sm-12">
                                            <select name="patient_linked_status" class="form-control" id="patient_linked_status">
                                                <option value="">Select Patient Linked</option>
                                                <option value="1" selected>Pending</option>
                                                <option value="2">Added</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group row">
                                    <label class="col-sm-12 ">Service Linked</label>
                                        <div class="col-sm-12">
                                            <select name="service_linked_status" class="form-control" id="service_linked_status">
                                                <option value="">Select Service Linked</option>
                                                <option value="1">Pending</option>
                                                <option value="2">Added</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="search-main1">
                                <div class="search-inner">
                                    <div>
                                        <input type="button" name="search" class="btn btn-primary search-btn1" id="searchid" value="Search" onclick="visitingAidReportList(1)">
                                        <a type="button" name="search" class="btn btn-light btn-rounded btn-fw btn-sm" id="clear" value="Reset" onclick="resetVisitingAidList()"><i class="mdi mdi-reload"></i> Reset</a>
                                        @can('third-party-report-export')
                                        <a type="button" class="btn btn-success btn-rounded btn-sm btn-fw ml-1 btnExport" id="third_party_patient_export">
                                            <i class="mdi mdi-file-export"></i>Export
                                        </a>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </form>
                </div>
            </div>
        </div>

        <div class="card" style="margin-top: 10px">
            <input type="hidden" id="sorting_column" value="id">
            <input type="hidden" id="sorting_order" value="desc">
            <input type="hidden" id="appointment_type" value="">
            <input type="hidden" id="appointment_ids" value="">
            <div class="row">
                <div class="col-12">

                    <div class="wmd-view">
                        <div>
                            <span id="resp"></span>
                        </div>
                    </div>
                </div>
            </div>
            {{-- </div> --}}
        </div>
    </div>

    <div class="row" style='margin-top: 25px;'>
        <pre id='toastrOptions'></pre>
    </div>

    @include('third_party_patient._partial.link_patient_modal')
    @include('third_party_patient._partial.link_service_modal')
    @include('include/footer')

    <script src="{{ asset('assets/js/jquery-ui.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/moment.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/daterangepicker.min.js')}}"></script>
    <link rel="stylesheet" type="text/css" href="{{ asset('css/daterangepicker.css')}}" />
    <script src="{{ asset('assets/css/toastr/toastr.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/jquery.tokeninput.js')}}"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/token-input.css')}}" type="text/css" />
    <script src="{{ asset('assets/vendors/select2/select2.min.js')}}"></script>
    <script src="{{ asset('assets/js/select2.js') }}"></script>
    <script type="text/javascript">
        var _THIRD_PARTY_VISITING_AID_LIST = "{{ url('third-party-ajax-report-list') }}";
        var _CSRF_TOKEN = '{{ csrf_token() }}';
        var _LINK_THIRD_PARTY_APPOINTMENT = "{{ url('link-third-party-appointment')}}";
        var _SEARCH_PATIENT = "{{ url('search-patient')}}"
        var _PATIENT_VIEW = "{{ url('patient/view')}}";
        var _UPDATE_LINK_THIRD_PARTY_APPOINTMENT = "{{ url('update-search-third-party-link')}}";
        var _LINK_PATIENT_SERVICES = "{{ url('link-patient-services')}}";
        var _UPDATE_PATIENT_SERVICES = "{{ url('update-patient-services')}}";
        var _THIRD_PART_DETAILS = "{{ url('get-patient-details-by-id')}}";
        var _THIRD_PARTY_PATIENT_EXPORT = "{{ url('third-party-patient-report-export')}}";
        var _DATE_TIME = "{{ date('Y-m-d')}}";

        $('#document_completed_date').datepicker();
    </script>
    <script src="{{ asset('assets/modulejs/third_party_report/third_party_report.js')}}?time={{ env('timestamp')}}"></script>
