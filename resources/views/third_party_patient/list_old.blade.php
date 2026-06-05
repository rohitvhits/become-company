@include('include/header')
 @include('include/sidebar')

 <link rel="stylesheet" href="{{ asset('/assets/modulejs/css/visiting_aid.css')}}?time={{ time()}}">

 <div class="main-panel">
     <?php
     $auth = auth()->user();
     ?>
     <div class="content-wrapper">
         <div class="page-title-main">
             <h5 class="mb-0 font-weight-bold">Visiting Aid List (<span id="appointment_id"></span>)</h5>
             <div class="page-rightbtns">
                @can('add-robort-appointment')
                <div>
                    <!-- <a href="javascript:void(0)" class="btn btn-primary btn-rounded btn-fw btn-sm"
                         onclick="addAppointment('','multiple')"><i class="mdi mdi-plus"></i> Add Appointment</a> -->
                </div>
                @endcan
            </div>
         </div>
        
         <div class="card">
            <input type="hidden" id="sorting_column" value="id">
            <input type="hidden" id="sorting_order" value="desc">
            <input type="hidden" id="appointment_type" value="">
            <input type="hidden" id="appointment_ids" value="">
                <div class="card-body compact-view">
                    <div class="row">
                        <div class="col-12">
                            <div class="wmd-view-topscroll">
                                <div class="scroll-div1">
                                </div>
                            </div>
                            <div class="wmd-view">
                                <div class="scroll-div2">
                                    <span id="resp"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
     
     <div class="row" style='margin-top: 25px;'>
         <pre id='toastrOptions'></pre>
     </div>
     @include('third_party_patient._partial.show_modal_popup')
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
     <script type="text/javascript">
        var _THIRD_PARTY_VISITING_AID_LIST="{{ url('third-party-patient-ajax-list') }}";
        var _THIRD_PARTY_ADD_APPOINTMENT ="{{ url('add-appointment-third-patient') }}";
        var _CSRF_TOKEN ='{{ csrf_token() }}';
        var _CHECK_EXISTING_DATA ="{{ url('check-existing-record')}}";
        var _LINK_THIRD_PARTY_APPOINTMENT ="{{ url('link-third-party-appointment')}}";
        var _SEARCH_PATIENT = "{{ url('search-patient')}}"
        var _PATIENT_VIEW ="{{ url('patient/view')}}";
        var _UPDATE_LINK_THIRD_PARTY_APPOINTMENT ="{{ url('update-search-third-party-link')}}";
        var _LINK_PATIENT_SERVICES ="{{ url('link-patient-services')}}";
        var _UPDATE_PATIENT_SERVICES ="{{ url('update-patient-services')}}";
        var _THIRD_PART_DETAILS ="{{ url('get-patient-details-by-id')}}";
        var _THIRD_PART_DETAILS ="{{ url('get-patient-details-by-id')}}";
     </script>
     <script src="{{ asset('assets/modulejs/visiting_aid.js')}}?time={{ time()}}"></script>
