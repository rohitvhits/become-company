@include('include/header')
 @include('include/sidebar')

 <link rel="stylesheet" href="{{ asset('/assets/vendors/select2/select2.min.css')}}">
 <link rel="stylesheet" href="{{ asset('/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css')}}">

<link rel="stylesheet" href="{{ asset('/assets/css/global.css')}}">
<link rel="stylesheet" href="{{ asset('assets/jquery-confirmation/css/jquery-confirm.min.css')}}">
 <style>
    .select2-container{
        width:100% !important
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
    .no-data-message {
    padding: 20px;
}
    </style>
<div class="main-panel main-page-box">
    
    <div class="content-wrapper content-wrapper-box">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">HHA MDO Patient</h5>
            
         </div>
         <hr />
         
         <div class="row ">
            <div class="col-sm-12">
                <div id="search-filter-btn">
                    <div class="card search-card1 cust-card-box" id="search-div">
                        <div class="card-body p-0 border-0 form-patient-list-box">
                            <form id="search-form">
                                <div class="row form-row-gap">
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                <label for="template_type">Agency</label>
                                                    <select class="form-control" name="agency_fk1" id="agency_fk">
                                                        <option value="">Select Agency</option>
                                                
                                                        @foreach($agency_list as $agn)
                                                        <option value="{{ $agn['id']}}" @if($selected_agency_id ==$agn['id']) selected @endif>{{ $agn['agency_name']}}</option>
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
                                        <input type="button" name="search"
                                            class="btn search-btn1 searchAppoinment" id="search-data"
                                            value="Search" onclick="getAllPatientListHHAMdo(1)">
                                        
                                        <a href="javascript:void(0)" class="btn btn-light cust-right-btn" onclick="refreshMDO()"><i
                                            class="mdi mdi-reload"></i>
                                        Reset</a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12" >
                <div class="location-wise-data-loader shimmer_id table-responsive" >
                    <div class="col-md-12 pl-0">
                        <table id="" class="table table-bordered ">
                            <thead>
                                <tr>
                                <th>No</th>
                                <th style="white-space:nowrap">Patient ID</th>
                                <th style="white-space:nowrap">Patient Full Name</th>
                                <th style="white-space:nowrap">Gender</th>
                                <th style="white-space:nowrap">DOB</th>
                                <th style="white-space:nowrap">Phone Numbers</th>
                                <th style="white-space:nowrap">Address</th>
                                <th style="white-space:nowrap">Status</th>
                                <th style="white-space:nowrap">Action</th>
                                </tr>
                            </thead>
                            <tbody class="shimmer-loader">
                                <tr>
                                    <td colspan="20"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="table table-responsive">
                    <span id="response_patient_list"></span>
                </div>
                
            </div>
        </div>
        
    </div>
    <div class="row" style='margin-top: 10%;'>
        <pre id='toastrOptions'></pre>
    </div>
@include('hha_mdo_patient._partial.create_hha_mdo_patient_modal')

@include('include/footer')
  
<script src="{{ asset('assets/vendors/select2/select2.min.js')}}"></script>
<script src="{{ asset('assets/js/select2.js')}}"></script>
<script src="{{ asset('assets/modulejs/hhaMDO/hha_mdo_order.js')}}?time={{ env('timestamp')}}"></script>
<script type="text/javascript">
    var _HHA_MDO_PATIENT_AJAX_LIST = "{{ url('hha/hha-mdo/ajax-hha-mdo-patient-list') }}";
    var SAVE_HHA_MDO_PATIENT = "{{ url('hha/hha-mdo/save-hha-mdo-patient')}}";
    var _CSRF_TOKEN = "{{ csrf_token() }}";
    var _PATIENT_VIEW = "{{ url('patient/view')}}";
    var _CREATE_APPOINTMENT = @json(auth()->user()->can('hha-patient-md-order-create'));
    
    getAllPatientListHHAMdo();

</script>