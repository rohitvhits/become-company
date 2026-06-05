@include('include/header')
@include('include/sidebar')
<style>
    .error {
        color: Red;
    }
    .addusertable th:nth-child(1),
    .addusertable td:nth-child(1) {
        min-width: 100px;
        max-width: 100px;
        width: 100px;
    }

    .addusertable th:nth-child(2),
    .addusertable td:nth-child(2) {
        min-width: 200px;
        max-width: 200px;
        width: 200px;
    }

    .addusertable th:nth-child(3),
    .addusertable td:nth-child(3) {
        min-width: 200px;
        max-width: 200px;
        width: 200px;
    }

    .addusertable th:nth-child(4),
    .addusertable td:nth-child(4) {
        min-width: 400px;
        max-width: 400px;
        width: 400px;
    }

    .addusertable th:nth-child(5),
    .addusertable td:nth-child(5) {
        min-width: 150px;
        max-width: 150px;
        width: 150px;
    }

    .addusertable th:nth-child(1),
    .addusertable td:nth-child(1) {
        min-width: 60px;

    }

    .minus-btn {
        max-height: 38px;
    }

    .formcontrol {
        border: 1px solid #cdd4e0;
        font-weight: 400;
        font-size: 0.875rem;
        height: 37px;
        padding: 0.375rem 0.75rem;
    }

    .page-title-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
</style>

<div class="main-panel main-page-box">
    <div class="content-wrapper">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">User Add - {{ @$agencyName->agency_name }}</h5>
        </div>
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <form class="form-sample" action='<?php echo URL::to('/agency/add_user'); ?>' name="adduser" method="post"
                        onsubmit="return validation();">
                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                        <input type="hidden" name="uid" value="{{ $agencyId->id }}">
                        <div class="card-body">
                        <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">First Name<span
                                                class="error">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control charCls" placeholder="Enter First Name"
                                                id="first_name" name="first_name" value="<?php echo old('first_name'); ?>" maxlength="50">
                                            <span class="error mt-2"
                                                id="first_name_error"><?php echo $errors->add_user->first('first_name'); ?></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Last Name<span
                                            class="error">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control charCls" placeholder="Enter Last Name"
                                                id="last_name" name="last_name" value="<?php echo old('last_name'); ?>" maxlength="50">
                                            <span class="error mt-2"
                                                id="last_name_error"><?php echo $errors->add_user->first('last_name'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Email<span
                                            class="error">*</span></label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" placeholder="Enter Email"
                                                class="span11" id="email" name="email"
                                                value="<?php echo old('email'); ?>">
                                            <span class="error mt-2"
                                                id="email_error"><?php echo $errors->add_user->first('email'); ?></span>
                                        </div>
                                        <div class="col-md-5">
                                            @if (request('id') == '' && in_array($user['user_type_fk'], [184, 4]))
                                                <select id="domain_id"
                                                    data-id=""
                                                    class="form-control ml-0" name="domain">
                                                    <option value="">Select Domain</option>
                                                </select>
                                                <span style="white-space: initial"
                                                    class="error ml-3 mt-2 "
                                                    id="domain_error"><?php echo $errors->add_user->first('domain'); ?></span>
                                            @else
                                                <select id="domain_id"
                                                    data-id=""
                                                    class="form-control ml-0" name="domain">
                                                    <option value="">Select Domain</option>
                                                    @if (isset($domainName))
                                                        @foreach ($domainName as $data)
                                                            <option value="{{ $data->id }}">
                                                                {{ '@' . $data->domain }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                <span class="error ml-3 mt-2"
                                                    style="white-space: initial"
                                                    id="domain_error"><?php echo $errors->add_user->first('domain'); ?></span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Phone No</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Enter Phone No"
                                                onkeypress="return isNumber(event)" class="span11" id="phone"
                                                name="phone" value="<?php echo old('phone'); ?>">
                                            <span class="error mt-2"
                                                id="phone_error"><?php echo $errors->add_user->first('phone'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Ext</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Enter Ext"
                                                onkeypress="return isNumber(event)" class="span11" id="ext"
                                                name="ext" value="<?php echo old('ext'); ?>">
                                            <span class="error mt-2"
                                                id="ext_error"><?php echo $errors->add_user->first('ext'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Record Type</label>
                                        <div class="col-sm-9">
                                            <select class="form-control" id="record_access" name="record_access">
                                                <option value="All" selected="">All</option>
                                                <option value="Patient">Patient</option>
                                                <option value="Caregiver">Caregiver</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Department</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="department" placeholder="Enter Department">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Is Admin</label>
                                        <div class="col-sm-9">
                                            <input type="checkbox" name="role_access" value="1" class="notification_checkbox patient_checkbox">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Send Invitaion</button>
                            <a href="{{url('agency-setting')}}"
                                class="btn btn-secondary ml-2">Cancel </a>
                            <input type="hidden" name="current_agency_id"
                                value="@if (isset($agencyId->id)) {{ sha1($agencyId->id) }} @endif">
                            <!-- <a href="javascript:void(0)" onclick="addNewItemRow()" style="float:right"
                                class="btn btn-primary btn-sm mt-2">Add </a> -->
                        </div>

                    </form>

                </div>

            </div>
        </div>
    </div>
    <div class="row" style='margin-top: 25px;'>
        <pre id='toastrOptions'></pre>
    </div>

    @include('include/footer')
    <!-- /Main Content -->
    <!-- doamin names -->
    <span id="domain_names" style="display:none">
        @if (isset($domainName))
            @foreach ($domainName as $data)
                <option value="{{ $data->id }}">{{ '@' . $data->domain }}</option>
            @endforeach
        @endif
    </span>
    <!-- domain names -->
    <!-- /Page Content -->
    <span id="login_type" style="display:none">
        @foreach ($loginType as $value)
            <option value="{{ $value->id }}">{{ $value->name }}
            </option>
        @endforeach
    </span>
    <span id="agencyas" style="display:none">
        <option value="">Select Agency</option>
        @foreach ($agencyList as $rwAgency)
            <option value="{{ $rwAgency->id }}">
                {{ $rwAgency->agency_name }}
            </option>
        @endforeach
    </span>
    <script>
        function validation() {
            var temp = 0;
            var emailRegex = /^[A-Za-z0-9`!#$%^&*()_=+\\';:\/?>.<,-]*$/;
            $('#first_name_error').html("");
            $('#last_name_error').html("");
            $('#email_error').html("");
            
            if ($('#first_name').val().trim() == '') {
                $('#first_name_error').html("Please enter First Name");
                temp = 1;
            }
            if ($('#last_name').val().trim() == '') {
                $('#last_name_error').html("Please enter Last Name");
                temp = 1;
            }

                
            if ($('#email').val().trim() == '') {
                $('#email_error').html("Please enter Email");
                temp = 1;
            } else {
                if ($('#email').val().trim() != '') {
                    if (!$('#email').val().match(emailRegex)) {
                        $('#email_error').html("Only name allowed");
                        temp = 1;
                    }
                }
                if($('#email').val().trim().length > 50){
                    $('#email_error').html("Please enter valid Email");
                    temp = 1;
                }

            }
            if ($('#domain_id').val() == '') {
                $('#domain_error').html("Please select Domain");
                temp = 1;
            }

            if (temp == 1) {
                return false;
            } else {
                return true;
            }

        }

        function isNumber(evt) {
            evt = (evt) ? evt : window.event;
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if ((charCode != 46 || $(this).val().indexOf('.') != -1) && (charCode < 48 || charCode > 57)) {
                return false;
            }
            return true;
        }
    </script>
