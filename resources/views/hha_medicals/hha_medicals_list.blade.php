@include('include/header')
 @include('include/sidebar')

 <link rel="stylesheet" href="{{ asset('/assets/vendors/select2/select2.min.css')}}">
 <link rel="stylesheet" href="{{ asset('/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css')}}">

<link href="{{ asset('/assets/css/toastr/toastr.min.css')}}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ asset('/assets/css/global.css')}}">
<link rel="stylesheet" href="{{ asset('assets/jquery-confirmation/css/jquery-confirm.min.css')}}">
<link rel="stylesheet" href="{{ asset('assets/vendors/fullcalendar/fullcalendar.min.css')}}">
<link rel="stylesheet" href="{{ asset('css/daterangepicker.css')}}">

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
 </style>
<div class="main-panel main-page-box">
    <?php
    $auth = auth()->user();
    ?>
    <div class="content-wrapper content-wrapper-box">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Setup Due Medical List <span id="hha_medicals_count_id">(0)</span></h5>
            <div class="page-rightbtns cust-page-rightbtns">
                <div>
                @can('sync-hha-medical-service')
                   <a href="javascript:void(0)" id="sync-medical-btn" class="btn cust-right-btn" style="background-color: #28a745;color:#fff;margin-right:10px;" data-toggle="modal" data-target="#syncMedicalModal"><i class="mdi mdi-sync"></i>Sync Medical</a>
                   @endcan
                   <a href="javascript:void(0)" id="filter-btn" class="btn cust-right-btn" style="background-color: #00879E;color:#fff;"><i class="mdi mdi-filter-outline"></i>Filter <span class="active-filter"></span></a>
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
                                                <label for="agency_fk">Agency</label>
                                                    <select name="agency_fk" id="agency_fk" class="form-control">
                                                        <option value="">Select Agency</option>
                                                        @foreach($agency_list as $agency)
                                                            <option value="{{ $agency->id }}">{{ $agency->agency_name }}</option>
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
                                                    <label for="office_fk">Office</label>
                                                    <select name="office_fk" id="office_fk" class="form-control">
                                                        <option value="">Select Office</option>
                                                        @foreach($office_table_list as $office)
                                                            <option value="{{ $office->id }}">{{ $office->office_name }}</option>
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
                                                    <label for="medical_name">Medical Name</label>
                                                    <input type="text" name="medical_name" id="medical_name" class="form-control" placeholder="Enter Medical Name">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="status">Status</label>
                                                    <select name="status" id="status" class="form-control">
                                                        <option value="">All</option>
                                                        <option value="1">Active</option>
                                                        <option value="0">Inactive</option>
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
                                            value="Search" onclick="hhaMedicalList(1)">

                                        <a href="javascript:void(0)" class="btn btn-light cust-right-btn" onclick="refresh()"><i
                                            class="mdi mdi-reload"></i>
                                        Reset</a>

                                        <a href="javascript:void(0)" class="btn btn-info cust-right-btn"
                                            onclick="hhaMedicalExport()"><i class="mdi mdi-file"></i><span id="exportText">Export CSV</span>
                                            <span class="spinner-border spinner-border-sm d-none" id="exportLoader" aria-hidden="true"></span>
                                        </a>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 grid-margin-top">
            @if (Session::has('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>{{ Session::get('success') }}</strong>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
            @endif
            @if (Session::has('error'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <strong>{{ Session::get('error') }}</strong>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
            @endif
        </div>

        <div class="row">
            <div class="col-12" >
                <input type="hidden" id="sortingColumn" value="id">
                <input type="hidden" id="sortingOrder" value="desc">
                <div class="location-wise-data-loader shimmer_id table-responsive" >
                    <div class="col-md-12 pl-0">
                        <table id="" class="table table-bordered ">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th style="white-space:nowrap">Agency Name</th>
                                    <th style="white-space:nowrap">Office Name</th>
                                    <th style="white-space:nowrap">Medical ID</th>
                                    <th style="white-space:nowrap">Medical Name</th>
                                   
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
                    <span id="resp"></span>
                </div>

            </div>
        </div>


        <!-- Caregiver View Modal -->

        <!-- Sync Medical Modal -->
        @include('hha_medicals._partial.sync_medical')
        <!-- End Sync Medical Modal -->

     </div>
     <div class="row" id="blank_div_id" style='margin-top: 10%;'>
         <pre id='toastrOptions'></pre>
     </div>

     @include('include/footer')

     <script src="{{ asset('js/jquery.min.js')}}"></script>

     <script type="text/javascript" src="{{ asset('/assets/js/moment.min.js')}}"></script>
     <script type="text/javascript" src="{{ asset('/assets/js/daterangepicker.min.js')}}"></script>
     <link rel="stylesheet" type="text/css" href="{{ asset('/css/daterangepicker.css')}}" />
     <script src="{{ asset('assets/css/toastr/toastr.min.js') }}"></script>
     <script src="{{ asset('assets/vendors/select2/select2.min.js')}}"></script>
     <script src="{{ asset('assets/js/select2.js')}}"></script>
     <script src="{{ asset('assets/jquery-confirmation/js/jquery-confirm.min.js')}}"></script>
     <script src="{{ asset('assets/vendors/inputmask/jquery.inputmask.bundle.js')}}"></script>
     <script src="{{ asset('assets/vendors/fullcalendar/fullcalendar.min.js')}}"></script>
  
    <script>
        var _LIST = "{{ url('/hha/hha-caregiver-medicals/ajax-list')}}";
        var _EXPORT_CSV = "{{ url('/hha/hha-caregiver-medicals/export-csv')}}";
        $(":input").inputmask();
        $("#filter-btn").click(function() {
            $("#search-filter-btn").slideToggle(600);
        });

        var _CSRF_TOKEN ="{{ csrf_token()}}"

        var _HHA_MEDICAL_TOOGLE = "{{ url('hha/hha-caregiver-medicals/toggle-status') }}";

        // Initialize Select2 for Sync Medical Modal
        $('#syncMedicalModal').on('shown.bs.modal', function () {
            $('#sync_agency_fk').select2({
                dropdownParent: $('#syncMedicalModal'),
                placeholder: 'Select Agency',
                allowClear: true
            });

            $('#sync_office_fk').select2({
                dropdownParent: $('#syncMedicalModal'),
                placeholder: 'Select Office',
                allowClear: true
            });

            $('#sync_medicals').select2({
                dropdownParent: $('#syncMedicalModal'),
                placeholder: 'Select Medicals',
                allowClear: true
            });

            $('#sync-medical-form')[0].reset();
        });

        // Load offices when agency changes
        $('#sync_agency_fk').on('change', function() {
            var agencyId = $(this).val();
            var officeDropdown = $('#sync_office_fk');

            // Clear office dropdown
            officeDropdown.empty().append('<option value="">Select Office</option>');
            $('#sync_medicals').empty().append('<option value="">Select Medicals</option>');

            if (agencyId) {
                // Show loading state
                officeDropdown.prop('disabled', true);

                $.ajax({
                    url: '{{ url("hha/hha-caregiver-medicals/get-offices-by-agency") }}',
                    type: 'GET',
                    data: {
                        agency_id: agencyId,
                        _token: _CSRF_TOKEN
                    },
                    success: function(response) {
                        if (response.success && response.data.length > 0) {
                            $.each(response.data, function(key, office) {
                                officeDropdown.append(
                                    $('<option></option>')
                                        .val(office.id)
                                        .text(office.office_name + (office.office_code ? ' - ' + office.office_code : ''))
                                );
                            });
                        } else {
                            officeDropdown.html('<option value="">No offices found</option>');
                        }
                        officeDropdown.prop('disabled', false);
                    },
                    error: function(xhr) {
                        toastr.error('Failed to load offices');
                        officeDropdown.prop('disabled', false);
                    }
                });
            }
        });

        // Load medicals when office changes
        $('#sync_office_fk').on('change', function() {
            var agencyId = $('#sync_agency_fk').val();
            var officeId = $(this).val();
            var medicalsDropdown = $('#sync_medicals');

            // Clear medicals dropdown
           

            if (agencyId && officeId) {
                // Show loading state
                medicalsDropdown.prop('disabled', true);
                medicalsDropdown.empty().append('<option value="">Loading medicals...</option>');

                $.ajax({
                    url: '{{ url("hha/hha-caregiver-medicals/get-medicals-by-agency-office") }}',
                    type: 'GET',
                    data: {
                        agency_id: agencyId,
                        office_id: officeId,
                        _token: _CSRF_TOKEN
                    },
                    success: function(response) {
                       

                        if (response.success && response.data && response.data.length > 0) {
                            medicalsDropdown.empty().append('<option value="">Select Medicals</option>');
                            $.each(response.data, function(key, medical) {
                                medicalsDropdown.append(
                                    $('<option></option>')
                                        .val(medical.medical_id)
                                        .text(medical.medical_name + ' (ID: ' + medical.medical_id + ')')
                                );
                            });
                            toastr.success('Loaded ' + response.data.length + ' medical(s) from external system');
                        } else {
                            medicalsDropdown.append('<option value="">No medicals found</option>');
                            toastr.info(response.message || 'No medicals found for selected agency and office');
                        }

                        medicalsDropdown.prop('disabled', false);
                        // Refresh Select2
                        medicalsDropdown.trigger('change');
                    },
                    error: function(xhr) {
                        medicalsDropdown.empty().append('<option value="">Select Medicals</option>');

                        var errorMessage = 'Failed to load medicals';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        toastr.error(errorMessage);
                        medicalsDropdown.prop('disabled', false);
                    }
                });
            }
        });

        // Reset form when modal is closed
        $('#syncMedicalModal').on('hidden.bs.modal', function () {
            $('#sync-medical-form')[0].reset();
            $('#sync_agency_fk').val(null).trigger('change');
            $('#sync_office_fk').val(null).trigger('change');
            $('#sync_medicals').val(null).trigger('change');
        });

        // Handle Sync Medical Submit
        $('#sync-medical-submit').on('click', function() {
            var agency = $('#sync_agency_fk').val();
            var office = $('#sync_office_fk').val();
            var medicals = $('#sync_medicals').val();

            if (!agency) {
                toastr.error('Please select an agency');
                return;
            }

            if (!office) {
                toastr.error('Please select an office');
                return;
            }

            if (!medicals) {
                toastr.error('Please select a medical');
                return;
            }

            // Clear previous errors
            $('.error').text('');

            // Show loader
            $('#syncLoader').removeClass('d-none');
            $(this).prop('disabled', true);

            // AJAX call to sync medical
            
            ajaxCallSyncMedical(agency,office,medicals,0)
            $('.close').click();
        });

        function ajaxCallSyncMedical(agency,office,medicals,sequence){
            $.ajax({
                url: '{{ url("hha/hha-caregiver-medicals/sync-medical") }}',
                type: 'POST',
                data: {
                    _token: _CSRF_TOKEN,
                    agency_fk: agency,
                    office_fk: office,
                    medicals: medicals,
                    sequence:sequence
                },
                success: function(response) {
                    $('#syncLoader').addClass('d-none');
                    $('#sync-medical-submit').prop('disabled', false);

                    if (response.data.length !== 0) {
                        $('#hha_medicals_count_id').html(response.data.sequence);
                        setTimeout(() => {
                            ajaxCallSyncMedical(response.data.agency_id,response.data.office_id,response.data.medicals,response.data.sequence);
                        }, 3000);
                    }else{
                        toastr.success(response.error_msg);
                    }
                },
                error: function(xhr) {
                    console.log(xhr)
                    $('#syncLoader').addClass('d-none');
                    $('#sync-medical-submit').prop('disabled', false);

                    var errorMessage = 'Failed to sync medical';

                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        // Display validation errors
                        if (xhr.responseJSON.errors) {
                            $.each(xhr.responseJSON.errors, function(field, messages) {
                                var errorField = field.replace('_fk', '');
                                $('#sync_' + field + '_error').text(messages[0]);
                            });
                        }
                    }

                    toastr.error(errorMessage);
                }
            });
        }

        $('#syncMedicalModal').on('hidden.bs.modal', function () {
            $('#sync-medical-form')[0].reset()
        })
    </script>
<script src="{{ asset('assets/css/toastr/toastr.min.js') }}"></script>
    <script src="{{ asset('assets/modulejs/hha_medical/hha_medical_documents.js')}}?time={{ env('timestamp')}}"></script>
