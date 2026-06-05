@include('include/header')
@include('include/sidebar')
<link href="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/jquery-confirmation/css/jquery-confirm.min.css">
<style>
    dl {
        margin-top: 0;
        margin-bottom: 20px;
    }

    ul,
    ol,
    dl {
        padding-left: 0px !important;
    }

    .dl-horizontal dt {
        float: left;
        width: 87px;
        clear: left;
        text-align: right;
        /* overflow: hidden; */
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    h6.fm_1 {
        /* text-align: end;*/
        font-size: 14px;
    }

    dt {
        font-weight: 700;
    }

    .dl-horizontal dd {
        margin-left: 115px;
    }

    .ml-3,
    .rtl .settings-panel .sidebar-bg-options .rounded-circle,
    .rtl .settings-panel .sidebar-bg-options .color-tiles .tiles,
    .rtl .settings-panel .color-tiles .sidebar-bg-options .tiles,
    .mx-3 {
        margin-left: 1rem !important;
        width: 100%;
    }

    #hr2 .dl-horizontal dd {
        margin-left: 130px;
    }

    #hr2 .dl-horizontal dt {
        width: 101px;
    }

    .label {
        display: inline;
        padding: .2em .6em .3em;
        font-size: 100%;
        font-weight: 700;
        line-height: 1;
        color: #fff;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: .25em;
    }

    .label-success {
        background-color: #5cb85c;
    }

    .label-danger {
        background-color: #d9534f;
    }

    .label-warning {
        background-color: #f0ad4e;
    }

    .label-default {
        background-color: #777;
    }



    .custom-toggle-switch .switch {
        position: relative;
        display: inline-block;
        width: 53px;
        height: 28px;
    }

    .custom-toggle-switch .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .custom-toggle-switch .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .custom-toggle-switch .slider:before {
        position: absolute;
        content: "";
        height: 20px;
        width: 20px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .custom-toggle-switch input:checked+.slider {
        background-color: #2196F3;
    }

    .custom-toggle-switch input:focus+.slider {
        -webkit-box-shadow: 0 0 1px #2196F3;
        box-shadow: 0 0 1px #2196F3;
    }

    .custom-toggle-switch input:checked+.slider:before {
        -webkit-transform: translateX(26px);
        transform: translateX(26px);
    }

    .custom-toggle-switch .slider.round {
        border-radius: 34px;
    }

    .custom-toggle-switch .slider.round:before {
        border-radius: 50%;
    }

    .two-factor-toggle {
        width: max-content !important;
    }
</style>
<!--main-container-part-->

<div class="main-panel">
    <div class="content-wrapper">
        <div class="dashboard-header d-flex flex-column grid-margin">
            <div class="d-flex align-items-center justify-content-between flex-wrap border-bottom pb-3 mb-3">
                <div class="d-flex align-items-center">
                    <h4 class="mb-0 font-weight-bold">Agency #
                        <?= $agencyDetails->id . ' - ' . ucwords($agencyDetails->agency_name) . ' ' ?> </h4>

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
            <div class="col-12">
                <div class="card">

                    <div class="card-body">
                        <h4>Agency Details</h4>
                        <div class="row">

                            <div class="col-lg-12">
                                <div class="profile-feed">
                                    <div class="d-flex align-items-start profile-feed-item">


                                        <div class="ml-3">
                                            <!-- <h5>Agency Details</h5> -->
                                            <hr>
                                            <dl class="dl-horizontal">
                                                <dt> Agency Name</dt>
                                                <dd> <?= $agencyDetails->agency_name != '' ? ucwords($agencyDetails->agency_name) : '-' ?>
                                                </dd>

                                                <dt> Email</dt>
                                                <dd> <?= $agencyDetails->email != '' ? $agencyDetails->email : '-' ?>
                                                </dd>
                                                <dt> Phone</dt>
                                                <dd> <?= $agencyDetails->phone != '' ? $agencyDetails->phone : '-' ?>
                                                </dd>
                                                <dt> Address1</dt>
                                                <dd> <?= $agencyDetails->address1 != '' ? $agencyDetails->address1 : '-' ?>
                                                </dd>
                                                <dt> Notification Email</dt>
                                                <dd style="height: 100px !important;overflow: auto !important;">
                                                    <?= $agencyDetails->notification_email != '' ? str_replace(',', '<br>', $agencyDetails->notification_email) : '-' ?>
                                                </dd>
                                                <!-- <dt class="two-factor-toggle">Two Factor Authentication </dt>
                                                <dd>
                                                    <div class="custom-toggle-switch" data-toggle="tooltip" title="Two Factor Authentication">
                                                        <label class="switch ml-3">
                                                            <input type="checkbox" class="two_factor_auth" data-id="{{ $agencyDetails->id}}" name="enable" value="Y" <?= ($agencyDetails->two_factor_auth == 'Y') ? 'checked' : ''; ?>>
                                                            <span class="slider round"></span>
                                                        </label>
                                                    </div>
                                                </dd> -->

                                            </dl>
                                        </div>
                                        <div class="ml-3">
                                            <!--   <h5>Agency Details</h5> -->
                                            <hr>
                                            <dl class="dl-horizontal">

                                                <dt> State</dt>
                                                <dd> <?= $agencyDetails->state != '' ? $agencyDetails->state : '-' ?>
                                                </dd>
                                                <dt> City</dt>
                                                <dd> <?= $agencyDetails->city != '' ? $agencyDetails->city : '-' ?>
                                                </dd>
                                                <dt> Zip Code</dt>
                                                <dd> <?= $agencyDetails->zip_code != '' ? $agencyDetails->zip_code : '-' ?>
                                                </dd>
                                                <dt> County</dt>
                                                <dd> <?= $agencyDetails->county != '' ? $agencyDetails->county : '-' ?>
                                                </dd>
                                                <dt>Nybest Email</dt>
                                                <dd style="height: 100px !important;overflow: auto !important;">
                                                    <?= $agencyDetails->nybest_email_notification != '' ? str_replace(',', '<br>', $agencyDetails->nybest_email_notification) : '-' ?>
                                                </dd>
                                                <!-- <dt class="two-factor-toggle">Password Expired</dt>
                                                <dd>
                                                    <div class="custom-toggle-switch" data-toggle="tooltip" title="Password Expired">
                                                        <label class="switch ml-3">
                                                            <input type="checkbox" class="password_expired" data-id="{{ $agencyDetails->id}}" name="enable" value="Y" <?= ($agencyDetails->password_expired == 'Y') ? 'checked' : ''; ?>>
                                                            <span class="slider round"></span>
                                                        </label>
                                                    </div>
                                                </dd> -->
                                            </dl>
                                        </div>
                                        <div class="ml-3">

                                            <hr>
                                            <?php /* 
                                            <dl class="dl-horizontal">
                                                <dt> Billing Email</dt>
                                                <dd> <?= $agencyDetails->billing_email != '' ? $agencyDetails->billing_email : '-' ?>
                                                </dd>

                                                <dt> Bill Date </dt>
                                                <dd> <?= $agencyDetails->bill_date != '' && $agencyDetails->bill_date != '00-000' ? date('d F', strtotime($agencyDetails->bill_date)) : '-' ?>
                                                </dd>
                                                <dt> Monthly Bill</dt>
                                                <dd> <?= $agencyDetails->monthly_bill != '' ? $agencyDetails->monthly_bill : '-' ?>
                                                </dd>
                                                <dt> Other Email</dt>
                                                <dd><?= $agencyDetails->other_email != '' ? str_replace(',', '<br>', $agencyDetails->other_email) : '-' ?>
                                                </dd>

                                                <dt> Notes Email</dt>
                                                <dd><?= $agencyDetails->notes_email_notification != '' ? str_replace(',', '<br>', $agencyDetails->notes_email_notification) : '-' ?>
                                                </dd>


                                            </dl>
                                            */?>

                                        </div>

                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-wrapper">

        <div class="card" style="margin-bottom:20px;">
            <div class="row list-name">
                <div class="col-sm-6 card-title">
                    <h4 class="card-title">Users List</h4>
                </div>
                <div class="col-sm-6">
                    <a href="javascript:void(0)" data-toggle="modal" data-target="#exampleModal-user" data-whatever="@mdo" class="btn btn-primary btn-rounded btn-sm btn-fw pull-right"><i class="mdi mdi-plus"> </i> Add
                        User</a>
                </div>

            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <span id="user_list_id"></span>
                        </div>
                    </div>
                </div>
            </div>

        </div>



    </div>



    <div class="content-wrapper">
        <div class="card" style="margin-bottom:20px;">
            <div class="row list-name">
                <div class="col-sm-6 card-title">
                    <h4 class="card-title">Domain List</h4>
                </div>
                <div class="col-sm-6">
                    <a data-toggle="modal" class="btn btn-success  btn-rounded btn-sm btn-fw pull-right" data-target="#exampleModal-4" data-whatever="@mdo" href="javascript:void(0)"><i class="mdi mdi-plus"></i> Add Domain</a>
                </div>

            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <span id="domain_list_id"></span>


                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>


    <!-- Script rate start -->
    <!-- Date Picker -->


    <div class="modal fade" id="exampleModal-4" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel">Add Domain</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="forms-sample" enctype="multipart/form-data" action='{{ url("agency-wise-domain-save")}}' name="adduser" method="post" id="submitId">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="agency_id" value="{{ $id }}">
                        <input type="hidden" name="id" value="" id="mid">
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Domain Name:</label>
                            <input type="text" name="domain" id="domain_id" class="form-control" placeHolder="Enter Domain Name">
                            <span id="domain_error" class="error mt-2 text-danger" for="document_type"></span>
                        </div>

                        <div class="modal-footer">
                            <button type="button" id="saveId" class="btn btn-success">Save</button>
                            <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="exampleModal-user" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel">Add User</h5>
                    <button type="button" class="close user_closed" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="forms-sample" enctype="multipart/form-data" action='' name="adduser" method="post" id="userSubmitId">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="agency_id" value="{{ $id }}">

                        <div class="form-group">
                            <label for="recipient-name" class="">First Name:</label>
                            <input type="text" name="first_name" id="first_name_id" class="form-control" placeHolder="Enter First Name">
                            <span id="first_name_error" class="error text-danger" for="document_type"></span>
                        </div>
                        <div class="form-group">
                            <label for="recipient-name" class="">Last Name:</label>
                            <input type="text" name="last_name" id="last_name_id" class="form-control" placeHolder="Enter Last Name">
                            <span id="last_name_error" class="error text-danger" for="document_type"></span>
                        </div>
                        <div class="form-group">
                            <label for="recipient-name" class="">Email:</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="text" name="domain_email" id="email_id" class="form-control" placeHolder="Enter Email">
                                    <span id="user_email_error" class="error text-danger" for="document_type"></span>
                                </div>
                                <div class="col-md-6">
                                    <select name="domain_id" id="domain_id_user" class="form-control">
                                        <option value="">Select</option>
                                        @foreach($domainName as $val)
                                        <option value="{{ $val->id }}">{{ $val->domain }}</option>
                                        @endforeach
                                    </select>
                                    <span id="user_domain_error" class="error text-danger" for="document_type"></span>
                                </div>
                            </div>


                        </div>
                        <div class="form-group">
                            <label for="recipient-name" class="">Phone:</label>
                            <input type="text" name="phone" id="phone" class="form-control" placeHolder="Enter Phone Name">
                            <span id="phone_error" class="error text-danger" for="document_type"></span>
                        </div>
                        <div class="form-group">
                            <label for="recipient-name" class="">Ext:</label>
                            <input type="text" name="ext" class="form-control" placeHolder="Enter Ext">

                        </div>
                        <div class="modal-footer">
                            <button type="button" id="saveIdUser" class="btn btn-success">Save</button>
                            <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>






    @include('include/footer')

    <script type="text/javascript" src="<?php echo URL::to('/'); ?>/assets/js/moment.min.js"></script>
    <script type="text/javascript" src="<?php echo URL::to('/'); ?>/assets/js/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo URL::to('/'); ?>/assets/css/vertical-layout-light/daterangepicker.css" />
    <script src="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.js"></script>
    <script src="<?= URL::to('assets/jquery-confirmation/js/jquery-confirm.min.js') ?>"></script>

    <script>
        $("#start_date, #end_date").datepicker();
        $("#end_date").change(function() {
            var startDate = document.getElementById("start_date").value;
            var endDate = document.getElementById("end_date").value;

            if ((Date.parse(endDate) <= Date.parse(startDate))) {
                alert("End date should be greater than Start date");
                //  $("#end_date_error_mess").html('End date should be greater than Start date');
                document.getElementById("end_date").value = "";

            }
        });
    </script>
    <script>
        function validation() {

            var agency_fk = $('#agency_fk').val();
            var item = $('#item').val();
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();
            if (agency_fk == '' && item == '' && start_date == '' && end_date == '') {
                alert('please select any one');
                return false;
            } else {
                return true;
            }
        }

        function export_data() {

            var agency_fk = $('#agency_fk').val();
            var item = $('#item').val();
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();
            var temp1 = '<?php echo URL::to('/'); ?>/rate-export?agency_fk=' + agency_fk + '&item=' + item + '&start_date=' +
                start_date + '&end_date=' + end_date;
            //  var temp = temp1.replace("http://", "https://");
            $('#test_rate').attr("style", '');
            $('#test_rate').attr("href", temp1);
        }
        $('input[name="daterange"]').daterangepicker({
            opens: 'left'
        }, function(start, end, label) {
            console.log("A new date selection was made: " + start.format('MM-DD-YYYY') + ' to ' + end.format(
                'MM-DD-YYYY'));
        });





        function domainList(page) {
            $.ajax({
                url: "{{ url('agency-wise-domain-list')}}",
                type: "GET",
                data: {
                    'type': 'domain',
                    'agency_id': "{{ $id }}",
                    'page': page,

                },
                success: function(response) {
                    $('#domain_list_id').html("");
                    $('#domain_list_id').html(response);
                }
            });

            return false;
        }
        domainList(1);

        $('#saveId').click(function(e) {
            var domain = $('#domain_id').val();
            var cnt = 0;
            $('#domain_error').html('');
            var regex = /^[a-zA-Z0-9][a-zA-Z0-9-]{1,61}[a-zA-Z0-9]\.[a-zA-Z]{2,}$/;
            if (domain.trim() == '') {
                $('#domain_error').html("Required");
                cnt = 1;
            }
            if (domain.trim() != '') {
                if (!regex.test(domain)) {
                    $('#domain_error').html("Invalid Domain");
                    cnt = 1;
                }
            }

            if (cnt == 1) {
                return false;
            } else {
                var forms = $('#submitId')[0];
                var newForms = new FormData(forms);
                newForms.append('_token', '{{ csrf_token() }}');
                newForms.append('agency_name', '{{ $agencyDetails->agency_name }}');

                $.ajax({
                    url: "{{ url('agency-wise-domain-save')}}",
                    type: "POST",
                    data: newForms,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        toastr.success(response.error_msg);
                        $('#exampleModal-4').modal('hide');
                        $('#submitId')[0].reset();
                        domainList(1);
                    },
                    error: function(xhr, status, error) {
                        toastr.error(xhr.responseJSON.error_msg);
                    }
                });

            }
        })
        $('#exampleModal-4').on('hidden.bs.modal', function() {
            $('#submitId')[0].reset();
            $('#domain_error').html("");
            $('#mid').val("");
        })
        $('body').on('click', '.pagination a', function(event) {
            $('li').removeClass('active');
            $(this).parent('li').addClass('active');
            event.preventDefault();
            var myurl = $(this).attr('href');
            var page = $(this).attr('href').split('page=')[1];
            var explode = $(this).attr('href').split('?');
            console.log(explode)
            var explodes = explode[1].split('&');
            var type = explodes[0].split('type=')[1];

            if (type == 'domain') {

                domainList(page);
            }
            if (type == 'users') {
                AjaxList(page);
            }

        });
        $('body').on('click', '.edit-detail', function(e) {
            var dataId = $(this).attr('data-id');
            var texts = $('#domain' + dataId).html();
            $('#mid').val(dataId);
            $('#ModalLabel').html('Edit Domain');
            $('#domain_id').val(texts);
            $('#exampleModal-4').modal('show');
        })

        $('body').on('click', '.delete-detail', function(e) {
            var msg = "you want to delete this domain?";
            var id = $(this).attr('data-id');
            $.confirm({
                title: 'Are you sure?',
                columnClass: "col-md-6",

                content: msg,
                buttons: {
                    formSubmit: {
                        text: 'Submit',
                        btnClass: 'btn-danger',
                        action: function() {
                            $.ajax({
                                url: '{{ url("agency-domain-delete")}}',
                                type: "POST",
                                data: {
                                    'id': id,
                                    '_token': "{{ csrf_token()}}"
                                },
                                success: function(res) {
                                    toastr.success(res.error_msg);
                                    domainList(1);
                                }
                            })
                        }
                    },
                    cancel: function() {
                        //close
                    },
                },
                onContentReady: function() {

                }
            });
        });

        $(".two_factor_auth").change(function() {
            var status = "N";
            var id = $(this).attr("data-id");
            if (this.checked) {
                status = "Y";
            }

            $.ajax({
                async: false,
                global: false,
                url: "{{ url('agency-two-factor-enable-disable') }}",
                data: {
                    'id': id,
                    'status': status
                },
                success: function(response) {
                    toastr.success(response.error_msg);
                },
                error: function(xhr, status, error) {
                    toastr.error(xhr.responseJSON.error_msg);
                }
            })

        });

        $(".password_expired").change(function() {
            var status = "N";
            var id = $(this).attr("data-id");
            if (this.checked) {
                status = "Y";
            }

            $.ajax({
                async: false,
                global: false,
                url: "{{ url('agency-password-expired-enable-disable') }}",
                data: {
                    'id': id,
                    'status': status
                },
                success: function(response) {
                    toastr.success(response.error_msg);
                },
                error: function(xhr, status, error) {
                    toastr.error(xhr.responseJSON.error_msg);
                }
            })

        });
        $('#saveIdUser').click(function(e) {
            var fname = $('#first_name_id').val();
            var lname = $('#last_name_id').val();
            var email = $('#email_id').val();
            var domain = $('#domain_id_user').val();
            var phone = $('#phone').val();

            $('#first_name_error').html("");
            $('#last_name_error').html("");
            $('#user_email_error').html("");
            $('#phone_error').html("");
            $('#user_domain_error').html("");
            $('#saveIdUser').prop('disabled', true);
            var cnt = 0;
            if (fname.trim() == '') {
                $('#first_name_error').html("Required");
                cnt = 1;
            }
            if (lname.trim() == '') {
                $('#last_name_error').html("Required");
                cnt = 1;
            }
            if (email.trim() == '') {
                $('#user_email_error').html("Required");
                cnt = 1;
            }
            if (domain == '') {
                $('#user_domain_error').html("Required");
                cnt = 1;
            }
            if (phone == '') {
                $('#phone_error').html("Required");
                cnt = 1;
            }

            if (cnt == 1) {
                $('#saveIdUser').prop('disabled', false);
                return false;
            } else {
                var formData = $('#userSubmitId')[0];
                var newData = new FormData(formData);
                    newData.append('_token','{{ csrf_token()}}');
                $.ajax({
                    type: "POST",
                    url: '{{ url("/nybest-agency/user-save")}}',
                    data: newData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        toastr.success(res.error_msg);
                        $('#userSubmitId')[0].reset();
                        $('#saveIdUser').prop('disabled', false);
                        $('.user_closed').click();
                        AjaxList(1);
                        
                    },
                    error: function(xhr, status, error) {
                        $('#saveIdUser').prop('disabled', false);
                        toastr.error(xhr.responseJSON.error_msg);
                    }

                })
            }

        })

        function AjaxList(page){
            $.ajax({
                type: "GET",
                url: '{{ url("nybest-user-list")}}',
                data: {
                    'type': 'users',
                    'agency_id': "{{ $id }}",
                    'page': page,

                },
                success: function(res) {
                    $('#user_list_id').html("");
                    $('#user_list_id').html(res);
                }
            });
            return false;
        }  
        AjaxList(1);

        function HospitalChangeStatus(record_id,userid) {
                var id = userid;
                if (record_id == 1) {
                    var status = record_id;
                    var msg = 'Yes';
                } else {
                    var status = record_id;
                    var msg = 'No';
                }
                if (record_id != '') {
                    var msg = "NyBest Access? : " + msg + "";
                    $.confirm({
                        title: 'Are you sure?',
                        columnClass: "col-md-6",
                        content: msg,
                        buttons: {
                            formSubmit: {
                                text: 'Submit',
                                btnClass: 'btn-danger',
                                action: function() {
                                    $.ajax({
                                        url: "{{ url('hospital-chnage-status') }}",
                                        type: "POST",
                                        data: {
                                            'status': record_id,
                                            'user_id': id,
                                            '_token': "{{ csrf_token() }}"
                                        },
                                        success: function(res) {
                                            if (res.data.status == 2) {
                                                status =
                                                    '<span class="badge badge-danger" onclick="HospitalChangeStatus(1)">No</span>';
                                            }
                                            if (res.data.status == 1) {
                                                status =
                                                    '<span class="badge badge-success" onclick="HospitalChangeStatus(2)">Yes</span>';
                                            }
                                            $('#hospitalchnagestatus').html(status);
                                            AjaxList(1);
                                        }
                                    })
                                }
                            },
                            cancel: function() {},
                        },
                        onContentReady: function() {}
                    });
                }
            }
    </script>
    <!-- Script rate end -->