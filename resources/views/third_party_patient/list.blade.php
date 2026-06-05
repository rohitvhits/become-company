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
            <h5 class="mb-0 font-weight-bold">Visiting Aid List (<span id="appointment_id"></span>)</h5>
            <div class="page-rightbtns">
                <div>



                </div>
            </div>
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
                                            <input type="text" name="due_date" id="created_date" class="form-control datepickernn" autocomplete="off" value="{{ $searchData['created_date'] ?? '' }}">

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




                            </div>



                        </div>
                        <div class="card-footer">
                            <div class="search-main1">
                                <div class="search-inner">
                                    <div>
                                        <input type="button" name="search" class="btn btn-primary search-btn1" id="searchid" value="Search" onclick="visitingAidList(1)">

                                        <a type="button" name="search" class="btn btn-light btn-rounded btn-fw btn-sm" id="clear" value="Reset" onclick="resetVisitingAidList()"><i class="mdi mdi-reload"></i> Reset</a>
                                        <a type="button" class="btn btn-success btn-rounded btn-sm btn-fw ml-1 btnExport" id="third_party_patient_export">
                                            <i class="mdi mdi-file-export"></i>Export
                                        </a>
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
            {{-- <div class="card-body compact-view"> --}}
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

    <div class="modal fade" id="exampleModal-5" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-modal="true" style="padding-right: 5px;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title documens" id="ModalLabel">Add Document</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeDocumentSection()">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form class="forms-sample" enctype="multipart/form-data" name="adduser" method="post" id="formnew">
                    <div class="modal-body">

                        <input type="hidden" name="_token" value="{{ csrf_token()}}">
                        <input type="hidden" name="patient_id" id="patient_modal_id">
                        <input type="hidden" name="id" id="document_ids" value="">
                        <input type="hidden" id="requestedServiceId" name="requestedServiceId" >
                        <input type="hidden" id="document_type"  >
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="recipient-name" class="col-form-label">Document Name<span class="error">*</span>:</label>
                                        <select name="document_id" class="form-control" id="document_id" onchange="showDocument();">
                                            <option value=""> Select Document </option>
                                        </select>
                                        <span id="document_id_error" class="error mt-2" for="document_id"></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="recipient-name" class="col-form-label">Services<span class="error">*</span>:</label>
                                        <select class="js-example-basic-multiple w-100" multiple="multiple" name="service_id[]" id="service_id" readonly>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="message-text" class="col-form-label">Document Completed Date:</label>
                                        <input type="text" class="form-control document_completed_date" id="document_completed_date" name="document_completed_date">
                                        <span id="document_completed_date_error" class="error mt-2" for="document_type"></span>
                                        <img src="{{ asset('/ajax-loader.gif')}}" class="order-listing-loader1" alt="loader" id="loaderIframe" style="display:none">

                                    </div>
                                </div>
                                <div class="col-md-6">

                                    <span id="show_attachment" style="display:none">
                                        <iframe src="" id="show_attachment_ifrme" style="width:100%; height:400px;" frameborder="0"></iframe>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" id="documentSave">Save</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal" onclick="closeDocumentSection()">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

    @include('third_party_patient._partial.show_modal_popup')
    @include('third_party_patient._partial.link_patient_modal')
    @include('third_party_patient._partial.link_service_modal')
    @include('third_party_patient._partial.log_modal')
    @include('third_party_patient._partial.portal_log_modal')
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
        var _THIRD_PARTY_VISITING_AID_LIST = "{{ url('third-party-patient-ajax-list') }}";
        var _THIRD_PARTY_ADD_APPOINTMENT = "{{ url('add-appointment-third-patient') }}";
        var _CSRF_TOKEN = '{{ csrf_token() }}';
        var _CHECK_EXISTING_DATA = "{{ url('check-existing-record')}}";
        var _LINK_THIRD_PARTY_APPOINTMENT = "{{ url('link-third-party-appointment')}}";
        var _SEARCH_PATIENT = "{{ url('search-patient')}}"
        var _PATIENT_VIEW = "{{ url('patient/view')}}";
        var _UPDATE_LINK_THIRD_PARTY_APPOINTMENT = "{{ url('update-search-third-party-link')}}";
        var _LINK_PATIENT_SERVICES = "{{ url('link-patient-services')}}";
        var _UPDATE_PATIENT_SERVICES = "{{ url('update-patient-services')}}";
        var _THIRD_PART_DETAILS = "{{ url('get-patient-details-by-id')}}";
        
        var _VIEW_PORTAL_LOG = "{{ url('third-party-wise-data-show')}}";
        var _DEBUG_MODE =false;
        @if(isset($_GET['debug']) && $_GET['debug'] =='hitu9592')
        _DEBUG_MODE =true;
        @endif
       
        var _UPLOAD_DOCUMENT_LIST_LINKED ="{{ url('show-document-upload-list')}}"
        var _THIRD_PARTY_PATIENT_EXPORT = "{{ url('third-party-patient/third-party-patient-export')}}";
        var _DATE_TIME = "{{ date('m/d/Y')}}";

        $('#document_completed_date').datepicker();

        function addPatientId(patientId,requestedServiceId,mid,type){
            
            $('#patient_modal_id').val(patientId);
            $('#requestedServiceId').val(requestedServiceId);
            $('#document_ids').val(mid);
            $('#show_attachment').attr('style','display:none')
            $('#document_type').val(type);
            $('#document_id').html('');
            $('#document_id').append($('<option>', { 
                value: "",
                text : "Select Document" 
            }));
            // Document dropdown
            $.ajax({
                async: false,
                global: false,
                type: "GET",
                url: "{{ url('get-document-of-patient')}}",
                data: {
                    'id' : patientId
                },
                success:function(res){
                    $.each(res.data, function (i, item) {
                        var date = moment(item.created_date).format('MM/DD/YYYY hh:mm A');
                        $('#document_id').append($('<option>', { 
                            value: item.id,
                            text : item.document_name +" ( "+date+" ) ",
                        }));
                    });
                },
                error:function(jqXHR){
                    $("#documentSave").prop('disabled',false);
                    toastr.error(jqXHR.responseJSON.error_msg)
                }
            })

        }
        
    </script>
    <script src="{{ asset('assets/modulejs/visiting_aid.js')}}?time={{ env('timestamp')}}"></script>
    <script>
        $('#documentSave').click(function(e){
            
            var document_id = $('#document_id').val();
            // var timemew = $('#timeidnew').val();
            var document_completed_date = $('#document_completed_date').val();
            // var document_service_id = $('#document_service_id').val();
            $('#document_id_error').html("");
            $('#time_error').html("");
            $('#document_completed_date_error').html("");
            $('#document_service_id_error').html("");
            var cnt = 0;
            

            if (document_id.trim() == '') {
                $('#document_id_error').html("Please select Document");
                cnt = 1;
            }

            if (cnt == 0) {
                $("#documentSave").prop('disabled',true);
                var formData = new FormData($('#formnew')[0]);
                formData.append('_token','{{ csrf_token()}}');

                $.ajax({
                    async: false,
                    global: false,
                    type: "POST",
                    url: "{{ url('third-party-document-upload')}}",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success:function(res){
                        toastr.success(res.error_msg);
                        visitingAidList(1);
                        $("#formnew")[0].reset();
                        $("#documentSave").prop('disabled',false);
                        $('#exampleModal-5').modal('hide')
                        $('#document_service_id').val(null).change();
                        $('#service_id').val(null).change();
                       
                        closeDocumentSection();
                    },
                    error:function(jqXHR){
                        $("#documentSave").prop('disabled',false);
                        toastr.error(jqXHR.responseJSON.error_msg)
                    }
                })
            } else {
                return false;
            }
        })
        function showDocument(){
            $('#loaderIframe').attr('style','display:block')
            $('#document_completed_date').val('');
            $('#service_id').html('');
            $('#service_id').append($('<option>', { 
                            value: '',
                            text : '', 
            }));
            var doc_id = $('#document_id').val();
            $.ajax({
                async: false,
                global: false,
                type: "GET",
                url: "{{ url('get-document-third-party')}}",
                data: {
                    'doc_id': doc_id,
                    'document_type':$('#document_type').val()
                },
                success:function(res){
                    if(res.document_completed_date != null && res.document_completed_date != undefined){
                        $('#document_completed_date').val(moment(res.document_completed_date).format('YYYY-MM-DD'));
                    }

                    if(res.data.services.length !=0){
                        $.each(res.data.services, function (i, item) {
                            $('#service_id').append($('<option>', { 
                                value: item.id,
                                text : item.name, 
                                selected: true
                            }));
                        });
                    }else{
                        $.each(res.data.new_servies, function (i, item) {
                            $('#service_id').append($('<option>', { 
                                value: item.id,
                                text : item.name, 
                             
                            }));
                        });
                    }
                    
                    const pdfUrl = "{{ url('view-pdf-response')}}?id="+doc_id; // Replace with your PDF file path
                    const pdfViewer = document.getElementById("show_attachment");
                    $('#show_attachment').attr('style','display:block');
                    $('#show_attachment_ifrme').attr('src',pdfUrl);
                    $('#loaderIframe').attr('style','display:none')
                },
                error:function(jqXHR){
                    $("#documentSave").prop('disabled',false);
                    toastr.error(jqXHR.responseJSON.error_msg)
                }
            })
        } 

        function updateFlag(id,flag){
            $.confirm({
                title: "Are you sure?",
                content: "You want to change status",
                type: 'blue',
                columnClass: 'col-md-6',
                buttons: {
                    confirm: {
                        text: 'Confirm',
                        btnClass: 'btn-blue',
                        action: function () {
                            $.ajax({
                                async: false,
                                global: false,
                                type: "post",
                                url: "{{ url('update-third-party-flag')}}",
                                data: {
                                    'id' : id,
                                    'flag':flag,
                                    '_token':"{{ csrf_token()}}"
                                },
                                success:function(res){
                                    toastr.success(res.error_msg);
                                    visitingAidList(1);
                                },
                                error:function(jqXHR){
                              
                                    toastr.error(jqXHR.responseJSON.error_msg)
                                }
                            })
                        }
                    },
                    cancel: {
                       
                    }
                }
            });
        }
    </script>
