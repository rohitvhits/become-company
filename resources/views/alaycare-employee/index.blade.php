@include('include/header')
@include('include/sidebar')

<link rel="stylesheet" href="{{ asset('assets/vendors/select2/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css') }}">
<link href="{{ asset('assets/css/toastr/toastr.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/global.css') }}" rel="stylesheet" type="text/css" />

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
            <h5 class="mb-0 font-weight-bold">AlayaCare Employee</h5>
            <div class="page-rightbtns cust-page-rightbtns">
                <div>
                    <a href="javascript:void(0)" id="filter-btn" class="btn cust-right-btn" style="background-color: #00879E;color:#fff;">
                        <i class="mdi mdi-filter-outline"></i>Filter <span class="active-filter"></span>
                    </a>

                    @can('alayacare-employee-add-appointment')
                    <a href="javascript:void(0)" title="" class="btn btn-primary cust-right-btn"
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
                                                    <label for="first_name">First Name</label>
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
                                                    <label for="last_name">Last Name</label>
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
                                                    <label for="agency_name">Agency Name</label>
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
                                                    <label for="phone_no">Phone No</label>
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
                                                    <label for="email">Email</label>
                                                    <input autocomplete="off" type="text" class="form-control"
                                                        name="email" id="email" value=""
                                                        placeholder="Enter Email">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="branch_name">Branch Name</label>
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
                                                    <label for="job_title">Job Title</label>
                                                    <input autocomplete="off" type="text" class="form-control"
                                                        name="job_title" id="job_title" value=""
                                                        placeholder="Enter Job Title">
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
                                </div>

                                <div class="row form-row-gap mb-2">
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="employee_status">Employee Status</label>
                                                    <select class="form-control" name="employee_status" id="employee_status">
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
                                                    <label for="created_date">Created Date</label>
                                                    <input type="text" name="created_date" id="created_date" class="form-control" autocomplete="off" placeholder="Select Created Date">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="last_skill_sync_date">Last Skill Sync Date</label>
                                                    <input type="text" name="last_skill_sync_date" id="last_skill_sync_date" class="form-control" autocomplete="off" placeholder="Select Last Skill Sync Date">
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
                                                value="Search" onclick="alaycareEmployyeList(1)">

                                            <a href="javascript:void(0)" class="btn btn-light btn-rounded btn-fw btn-sm" onclick="resetData()">
                                                <i class="mdi mdi-reload"></i> Clear
                                            </a>

                                            @can('alayacare-employee-export')
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
                                <tr>
                                    <th>#</th>
                                    <th>#No</th>
                                    <th>Agency Name</th>
                                    <th>Branch Name</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Mobile No</th>
                                    <th>Email</th>
                                    <th>Job Title</th>
                                    <th>Employee Status</th>
                                    <th>Appointment Status</th>
                                    <th>Created Date</th>
                                    <th>Last Skill Sync Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="shimmer-loader">
                                <tr>
                                    <td colspan="14"></td>
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
@include('alaycare-employee._partial.create_alayacare_emp_appointment')
@include('include/footer')

<script src="{{ asset('assets/vendors/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/select2.js') }}"></script>
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
            clearEmpData();
            $('#exampleModal-alayacare-emp').modal('show');
            getResponse('Caregiver');
        }
    }

    $("#service_id").select2({
        placeholder: "Select Service"
    });

    $('#close-modal-popup').click(function() {
        clearEmpData();
    });

    function PatientAddAppointment(id, empId) {
        $("#displine_error").html("");
        $("#radio_type_error").html("");
        $("#service_id_error").html("");

        $('#emp_id').val(id);
        $('#alaycare-emp-id').val('single');
        getResponse('Caregiver');
        $('#exampleModal-alayacare-emp').modal('show');
    }

    $('#saveId').click(function() {
        var temp = 0;
        var diciplin_id = $('#diciplin_id').val();
        var service_id = $('#service_id').val();
        var selectedType = $('#alaycare-emp-id').val();

        $('#create-alayacare-emp').removeClass('d-none');
        $('#btn-save-text').text('Saving...')
        $('#saveId').attr('disabled',true);

        $("#displine_error").html("");
        $("#service_id_error").html("");

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
            final_array.push($('#emp_id').val());
        } else {
            $('.cbox').each(function(i, v) {
                var schecked = $(this).is(":checked");
                if (schecked == true) {
                    var values = $(this).val();
                    final_array.push(values);
                }
            });
        }

        if (temp != 0) {
            $('#create-alayacare-emp').addClass('d-none');
            $('#btn-save-text').text('Save')
            $('#saveId').attr('disabled',false);

            return false;
        }
        var forms = $('#submitId')[0];
        var newForms = new FormData(forms);
        newForms.append('ids', final_array);

        $.ajax({
            url: "{{ url('alayacare/alayacare-employee/employee-add-appointment')}}",
            type: "POST",
            data: newForms,
            processData: false,
            contentType: false,
            success: function(response) {
                toastr.success(response.error_msg);

                $('#create-alayacare-emp').addClass('d-none');
                $('#btn-save-text').text('Save')
                $('#saveId').attr('disabled',false);

                $('#exampleModal-alayacare-emp').modal('hide');
                clearEmpData();
                alaycareEmployyeList(1);
            },error:function(jqr){
                $('#create-alayacare-emp').addClass('d-none');
                $('#btn-save-text').text('Save')
                $('#saveId').attr('disabled',false);
                showErrorAndLoginRedirection(jqr);
            }
        });
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


        alaycareEmployyeList(1);
    $(function() {
        let start = moment().startOf('day');
        let end = moment().endOf('day');
        // Initialize date range pickers with predefined ranges
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
       
      $('#last_skill_sync_date').daterangepicker({
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

            $('#last_skill_sync_date').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
                'MM/DD/YYYY'));
        })
        
    });

    function resetData() {
        $('#email').val(null);
        $('#first_name').val(null);
        $('#last_name').val(null);
        $('#job_title').val(null);
        $('#phone_no').val(null);
        $('#branch_name').val(null);
        $('#agency_name').val(null);
        $('#status').val(null);
        $('#employee_status').val(null);
        $('#created_date').val('');
        $('#last_skill_sync_date').val('');
        alaycareEmployyeList(1);
    }

    function export_data() {
        var email = $('#email').val();
        var firstName = $('#first_name').val();
        var lastName = $('#last_name').val();
        var jobTitle = $('#job_title').val();
        var phone = $('#phone_no').val();
        var branchName = $('#branch_name').val();
        var agencyName = $('#agency_name').val();
        var employeeStatus = $('#employee_status').val();
        var status = $('#status').val();
        var createdDate = $('#created_date').val();
        var lastSkillSyncDate = $('#last_skill_sync_date').val();
        var temp1 = '{{ url("/alayacare/alayacare-employee/alaycare-employee-export")}}?email=' + email + '&first_name=' + firstName + '&last_name=' +
            lastName + '&job_title=' + jobTitle + '&phone_no=' + phone + '&branch_name=' + branchName + '&agency_name=' + agencyName + '&employee_status=' + employeeStatus + '&status=' + status + '&created_date=' + encodeURIComponent(createdDate) + '&last_skill_sync_date=' + encodeURIComponent(lastSkillSyncDate);
        window.open(temp1, '_blank');
    }

    function alaycareEmployyeList(page) {
        var email = $('#email').val();
        var firstName = $('#first_name').val();
        var lastName = $('#last_name').val();
        var jobTitle = $('#job_title').val();
        var phone = $('#phone_no').val();
        var branchName = $('#branch_name').val();
        var agencyName = $('#agency_name').val();
        var status = $('#status').val();
        var employeeStatus = $('#employee_status').val();
        var createdDate = $('#created_date').val();
        var lastSkillSyncDate = $('#last_skill_sync_date').val();
        $('.location-wise-data-loader').attr('style', 'display:flex');
        $('.shimmer_id').removeClass('hide')
        $('#resp').html("");

        $.ajax({
            url: "{{ url('alayacare/alayacare-employee/alaycare-employee-ajax-list') }}?page=" + page,
            type: "GET",
            data: {
                'email': email,
                'first_name': firstName,
                'last_name': lastName,
                'job_title': jobTitle,
                'phone_no': phone,
                'branch_name': branchName,
                'agency_name': agencyName,
                'status': status,
                'employee_status': employeeStatus,
                'created_date': createdDate,
                'last_skill_sync_date': lastSkillSyncDate,
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
        alaycareEmployyeList(page);
    });

    function clearEmpData(){
        $('#submitId')[0].reset();
        $("#service_id").trigger("reset");
        $('#displine_error').html("");
        $('#service_id_error').html("");
        $('#alaycare-emp-id').val('');
    }

</script>
<script type="text/javascript" src="{{ asset('assets/js/moment.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('/assets/js/daterangepicker.min.js')}}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('/css/daterangepicker.css')}}" />
