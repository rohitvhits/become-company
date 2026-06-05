@include('include/header')
@include('include/sidebar')

<link rel="stylesheet" href="{{ asset('assets/vendors/select2/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css') }}">
<link href="{{ asset('assets/css/toastr/toastr.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/global.css') }}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="{{ asset('css/daterangepicker.css')}}" />

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
            <h5 class="mb-0 font-weight-bold">AlayaCare Client</h5>
            <div class="page-rightbtns cust-page-rightbtns">
                <div>
                    <a href="javascript:void(0)" id="filter-btn" class="btn cust-right-btn" style="background-color: #00879E;color:#fff;">
                        <i class="mdi mdi-filter-outline"></i>Filter <span class="active-filter"></span>
                    </a>

                    @can('alayacare-client-add-appointment')
                    <a href="javascript:void(0)" class="btn btn-primary cust-right-btn"
                        onclick="addAppointment()"><i class="mdi mdi-plus"></i> Add Appointment</a>
                    @endcan
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
                                <div class="row form-row-gap mb-2">
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="">First Name</label>
                                                    <input type="text" autocomplete="off" class="form-control"
                                                        name="first_name" id="first_name" value=""
                                                        placeholder="Enter First Name">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="">Last Name</label>
                                                    <input type="text" autocomplete="off" class="form-control"
                                                        name="last_name" id="last_name" value=""
                                                        placeholder="Enter Last Name">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="">Agency Name</label>
                                                    <select name="agency_name" id="agency_name" class="form-control">
                                                        <option value="">Select Agency</option>
                                                        @foreach ($agencyList as $rwAgency)
                                                            <option value="{{$rwAgency->id}}">
                                                                {{$rwAgency->agency_name}}</option>
                                                        @endforeach
                                                    </select>
                                                    <span class="error ml-2" id="error_all"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="">Phone No</label>
                                                    <input type="text" autocomplete="off" class="form-control"
                                                        name="phone_no" id="phone_no" value=""
                                                        placeholder="Enter Phone No">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row form-row-gap mb-2">
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="">Branch Name</label>
                                                    <input autocomplete="off" type="text" class="form-control"
                                                        name="branch_name" id="branch_name" value=""
                                                        placeholder="Enter Branch Name">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="">City</label>
                                                    <input type="text" autocomplete="off" class="form-control"
                                                        name="city" id="city" value=""
                                                        placeholder="Enter City">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="">State</label>
                                                    <input type="text" autocomplete="off" class="form-control"
                                                        name="state" id="state" value=""
                                                        placeholder="Enter State">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="">Gender</label>
                                                    <input type="text" autocomplete="off" class="form-control"
                                                        name="gender" id="gender" value=""
                                                        placeholder="Enter Gender">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row form-row-gap mb-2">
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="">Client Status</label>
                                                    <select class="form-control" name="client_status" id="client_status">
                                                        <option value="">All</option>
                                                        <option value="active">Active</option>
                                                        <option value="discharged">Discharged</option>
                                                        <option value="FalsestatusALICE">FalsestatusALICE</option>
                                                        <option value="on hold">On Hold</option>
                                                        <option value="pending">Pending</option>
                                                        <option value="waiting list">Waiting List</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="status">Appointment Status</label>
                                                    <select class="form-control" name="status" id="status">
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
                                                    <label for="created_date">Created Date</label>
                                                    <input type="text" name="created_date" id="created_date" class="form-control" autocomplete="off" placeholder="Select Created Date">
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
                                                value="Search" onclick="alaycareClientList(1)">

                                            <a href="javascript:void(0)" class="btn btn-light btn-rounded btn-fw btn-sm" onclick="resetData()">
                                                <i class="mdi mdi-reload"></i> Clear
                                            </a>

                                            @can('alayacare-client-export')
                                            <a href="javascript:void(0)" id="test_employee" class="btn cust-right-btn" style="background-color: #28a745;color:#fff;" onclick="export_data()">
                                                <i class="mdi mdi-file-export"></i> Export
                                            </a>
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
            <div class="col-12">
                <div class="location-wise-data-loader shimmer_id hasClass">
                    <div class="col-md-12 pl-0">
                        <table id="" class="table table-bordered">
                            <thead>
                                <th>#</th>
                                <th>#No</th>
                                <th>Agency Name</th>
                                 <th>Branch Name</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Phone No</th>
                                <th>Group Name</th>
                                <th>City</th>
                                <th>State</th>
                                <th>Gender</th>
                                <th>Client Status</th>
                                <th>Appointment Status</th>
                                <th>Created Date</th>
                                <th>Action</th>
                            </thead>
                            <tbody class="shimmer-loader">
                                <tr>
                                    <td colspan="15"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <span id="resp"></span>
            </div>
        </div>

    </div>
    <div class="row" id="blank_div" style='margin-top: 25px;'></div>
</div>

@include('alaycare-client._partial.create_client_appointment')
@include('include/footer')
<script src="{{ asset('assets/vendors/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/select2.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/moment.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/daterangepicker.min.js')}}"></script>
<script>

    $('#filter-btn').click(function() {
        $("#search-filter-btn").toggle();
    });

    $('body').on('click', '#cboxid', function(e) {
        var checked = $(this).is(":checked");
        if (checked == true) {
            $('.cbox').prop('checked', true);
        } else {
            $('.cbox').prop('checked', false);
        }
    });

    function addAppointment() {
        var checked = $('.cbox').is(":checked");
        if (checked == false) {
            toastr.error("Please select checkbox");
            return false;
        } else {
            $('#exampleModal-alayacare-client').modal('show');

            getResponse('Patient');
            clearClientData();
        }
    }

    $("#service_id").select2({
        placeholder: "Select Service"
    });

    $('#close-modal-popup').click(function() {
        clearClientData();
    });

    function PatientAddAppointment(id, clientId) {
        $("#displine_error").html("");
        $("#radio_type_error").html("");
        $("#service_id_error").html("");

        var Id = $('#client_id').val(id);
        $('#alaycare-client-id').val('single');
        getResponse('Patient');
        $('#exampleModal-alayacare-client').modal('show');
    }

    $('#saveId').click(function() {
        var temp = 0;
        var diciplin_id = $('#diciplin_id').val();
        var service_id = $('#service_id').val();
        var selectedType = $('#alaycare-client-id').val();
        $('#create-alayacare-client').removeClass('d-none');
        $('#btn-save-text').text('Saving...')
        $("#displine_error").html("");
        $("#service_id_error").html("");
        $('#saveId').attr('disabled',true);
        if (service_id == "") {
            $('#service_id_error').html("Please select Service");
            temp++;
        }
        if (diciplin_id == '') {
            $("#displine_error").html("Please select Discipline");
            temp++;
        }

        var final_array = [];
        if (selectedType == 'single') {
            final_array.push($('#client_id').val());
        } else {
            $('.cbox').each(function(i, v) {
                var schecked = $(this).is(":checked");
                if (schecked == true) {
                    var values = $(this).val();
                    final_array.push(values);
                }
            });
        }

        if (temp == 0) {
            var forms = $('#submitId')[0];
            var newForms = new FormData(forms);
            newForms.append('ids', final_array);

            $.ajax({
                url: "{{ url('alayacare/alayacare-client/client-add-appointment')}}",
                type: "POST",
                data: newForms,
                processData: false,
                contentType: false,
                success: function(response) {
                    toastr.success(response.error_msg);
                    $('#exampleModal-alayacare-client').modal('hide');
                    $('#submitId')[0].reset();
                    $("#service_id").trigger("reset");
                     $('#btn-save-text').text('Save')
                    $('#create-alayacare-client').addClass('d-none');
                    $('#saveId').attr('disabled',false);
                    $('#alaycare-client-id').val('');
                    alaycareClientList(1);
                },
                error: function(xhr, status, error) {
                    $('#create-alayacare-client').addClass('d-none');
                    $('#saveId').attr('disabled',false);
                    showErrorAndLoginRedirection(xhr);
                }
            });
            return true;
        } else {
            $('#create-alayacare-client').addClass('d-none');
            $('#btn-save-text').text('Save')
            $('#saveId').attr('disabled',false);
            return false;
        }
    });

    function getResponse(id) {
        if (id != '') {
            var jsonencode = <?php echo json_encode(old('service_id')); ?>;
            $.ajax({
                async: false,
                global: false,
                type: "GET",
                url: "<?php echo URL::to('/'); ?>/ajax-service",
                data: {
                    "id": id,
                    "jsonencode": jsonencode
                },
                success: function(res) {
                    if (res != '') {
                        htmlsresp = res;
                    } else {
                        htmlsresp += '<option value="">No record available</option>';
                    }
                    $('#service_id').html(htmlsresp);
                }
            });
        }
    }

    $(document).ready(function() {
        let start = moment().startOf('day');
        let end = moment().endOf('day');
        // Initialize date range picker with predefined ranges
        $('#created_date').daterangepicker({
            startDate: start,
            endDate: end,
            autoUpdateInput: false,
            startOfWeek: 'sunday',
            ranges: {
                
                'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'Next Month': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month').endOf('month')],
                    'Next Week': [moment().add(1, 'week').startOf('week'), moment().add(1, 'week').endOf('week')],
                    'Last Week': [moment().subtract(1, 'week').startOf('week'), moment().subtract(1, 'week').endOf('week')],
            }
        }, function (chosen_date, end_date) {

            $('#created_date').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
                'MM/DD/YYYY'));
        })

        alaycareClientList(1);
    });

    function resetData() {
        $('#first_name').val(null);
        $('#last_name').val(null);
        $('#branch_name').val(null);
        $('#phone_no').val(null);
        $('#city').val(null);
        $('#state').val(null);
        $('#gender').val(null);
        $('#status').val(null);
        $('#client_status').val(null);
        $('#agency_name').val(null);
        $('#created_date').val('');
        alaycareClientList(1);
    }

    function export_data() {
        var firstName = $('#first_name').val();
        var lastName = $('#last_name').val();
        var branchName = $('#branch_name').val();
        var phoneNo = $('#phone_no').val();
        var city = $('#city').val();
        var state = $('#state').val();
        var gender = $('#gender').val();
        var status = $('#status').val();
        var clientStatus = $('#client_status').val();
        var createdDate = $('#created_date').val();
        var temp1 = '{{ url("/alayacare/alayacare-client/alaycare-client-export")}}?first_name=' + firstName + '&last_name=' +
            lastName + '&branch_name=' + branchName + '&phone_no=' + phoneNo + '&city=' + city + '&state=' + state + '&gender=' + gender + '&status=' + status + '&client_status=' + clientStatus + '&created_date=' + encodeURIComponent(createdDate);

        window.open(temp1, '_blank');
    }

    function alaycareClientList(page) {
        var firstName = $('#first_name').val();
        var lastName = $('#last_name').val();
        var branchName = $('#branch_name').val();
        var phoneNo = $('#phone_no').val();
        var city = $('#city').val();
        var state = $('#state').val();
        var gender = $('#gender').val();
        var status = $('#status').val();
        var clientStatus = $('#client_status').val();
        var agencyName = $('#agency_name').val();
        var createdDate = $('#created_date').val();
        $('.location-wise-data-loader').attr('style', 'display:flex');
        $('.shimmer_id').removeClass('hide')
        $('#resp').html('');
            $.ajax({
                url: "{{ url('alayacare/alayacare-client/alaycare-client-ajax-list') }}?page=" + page,
                type: "GET",
                data: {
                    'first_name': firstName,
                    'last_name': lastName,
                    'branch_name': branchName,
                    'phone_no': phoneNo,
                    'city': city,
                    'state': state,
                    'gender': gender,
                    'status': status,
                    'client_status': clientStatus,
                    'agency_name': agencyName,
                    'created_date': createdDate,
                },
                success: function(res) {
                    $('.shimmer_id').addClass('hide');
                    $('.location-wise-data-loader').attr('style', 'display:none');
                    $('#resp').html(res);
                },error:function(jqr){
                    showErrorAndLoginRedirection(jqr);
                }
            });
        return false;
    }

    $('body').on('click', '.pagination a', function(event) {
        $('li').removeClass('active');
        $(this).parent('li').addClass('active');
        event.preventDefault();
        var myurl = $(this).attr('href');
        var page = $(this).attr('href').split('page=')[1];
        alaycareClientList(page);
    });

    function clearClientData(){
        $('#submitId')[0].reset();
        $("#service_id").trigger("reset");
        $('#displine_error').html("");
        $('#service_id_error').html("");
        $('#alaycare-client-id').val('');
    }
</script>

