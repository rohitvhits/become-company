@include('include/header')
@include('include/sidebar')
<style>
    .error {
        color: Red;
    }
    .page-title-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .d-none {
        display: none;
    }
    span.select2.select2-container.select2-container--default{
        width : 100% !important;
    }
</style>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">User Edit</h5>
        </div>
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <form class="form-sample" method="post"
                            action='<?php echo URL::to('/update_user'); ?>?i=<?php echo $id; ?>&flag=<?php echo $flag; ?>' name="edituser"
                            onsubmit="return validation();">
                            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">First Name<span
                                            class="error">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control charCls" placeholder="Enter First Name"
                                                id="first_name" name="first_name" value="<?php echo $userDetail->first_name; ?>" maxlength="50">
                                            <span class="error mt-2"
                                                id="first_name_error"><?php echo $errors->edit_user->first('first_name'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Last Name<span
                                            class="error">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control charCls" placeholder="Enter last Name"
                                                id="last_name" name="last_name" value="<?php echo $userDetail->last_name; ?>" maxlength="50">
                                            <span class="error mt-2"
                                                id="last_name_error"><?php echo $errors->edit_user->first('last_name'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Email<span
                                            class="error">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="email" class="form-control" placeholder="Enter Email"
                                                class="span11" id="email" name="email"
                                                value="<?php echo $userDetail->email; ?>">
                                            <span class="error mt-2"
                                                id="email_error"><?php echo $errors->edit_user->first('email'); ?></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Phone No</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Enter Phone No"
                                                onkeypress="return isNumber(event)" class="span11" id="phone"
                                                name="phone" value="<?php echo $userDetail->phone; ?>">
                                            <span class="error mt-2"
                                                id="phone_error"><?php echo $errors->edit_user->first('phone'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Ext</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Enter Ext"
                                                class="span11" id="ext" name="ext"
                                                value="<?php echo $userDetail->ext; ?>">
                                            <span class="error mt-2"
                                                id="ext_error"><?php echo $errors->edit_user->first('ext'); ?></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Role<span
                                            class="error">*</span></label>
                                        <div class="col-sm-9">
                                            <select name="roles[]" id="role" class="form-control js-example-basic-multiple select2-design" multiple>
                                                @foreach ($roles as $key => $role)
                                                    <option value="{{ $key }}" {{ in_array($key, $userRole ?? []) ? 'selected' : '' }}>{{ $role }}</option>
                                                @endforeach
                                            </select>
                                            <span class="error mt-2"
                                                id="role_error"><?php echo $errors->add_user->first('roles'); ?></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Record Type</label>
                                        <div class="col-sm-9">
                                            <select class="form-control" id="record_access" name="record_access">
                                                <option value="All" @if($userDetail->record_access =='All') selected @endif>All</option>
                                                <option value="Patient" @if($userDetail->record_access =='Patient') selected @endif>Patient</option>
                                                <option value="Caregiver" @if($userDetail->record_access =='Caregiver') selected @endif>Caregiver</option>
                                            </select>
                                        </div>
                                    </div>
                                
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Department</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="department" placeholder="Enter Department" value="{{ $userDetail->department}}">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Is Nurse</label>
                                        <div class="col-sm-9">
                                            <div class="form-check custom-check table-check">
                                                <label class="form-check-label">
                                                    <input type="checkbox" class="form-check-input checkinput" name="is_nurse" id="is_nurse" value="1" @if($userDetail->is_nurse == 1) checked @endif><i class="input-helper"></i><i class="input-helper"></i>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">MDO File Access</label>
                                        <div class="col-sm-9">
                                            <div class="form-check custom-check table-check">
                                                <label class="form-check-label">
                                                    <input type="checkbox" class="form-check-input checkinput" name="is_mdo" id="is_mdo" value="1" @if($userDetail->is_mdo == 1) checked @endif><i class="input-helper"></i><i class="input-helper"></i>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Telehealth File Access</label>
                                        <div class="col-sm-9">
                                            <div class="form-check custom-check table-check">
                                                <label class="form-check-label">
                                                    <input type="checkbox" class="form-check-input checkinput" name="is_telehealth" id="is_telehealth" value="1" @if($userDetail->is_telehealth == 1) checked @endif><i class="input-helper"></i><i class="input-helper"></i>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 d-none language_div">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Language Name</label>
                                        <div class="col-sm-9">
                                            <select name="language_id[]" id="language_id" class="form-control js-example-basic-multiple select2-design" multiple>
                                                <option value="">Select Language </option>
                                                @foreach($language as $key => $value)
                                                    <option value="{{ $value->id }}" @if(in_array($value->id,$userDetail->language)) selected @endif>{{ $value->name }}</option>
                                                @endforeach
                                            </select>
                                            <span class="error mt-2"
                                                id="language_error"></span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <button type="submit" class="btn btn-primary mr-2">Update</button>
                            <a type="button" class="btn btn-secondary mr-2" href="{{url('user-view')}}/{{$userDetail->id}}">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row" style='margin-top: 25px;'>
        <pre id='toastrOptions'></pre>
    </div>

    <!-- /Main Content -->

    <!-- /Page Content -->
    <script src="<?php echo URL::to('/'); ?>/assets/vendors/select2/select2.min.js"></script>
    <script src="<?php echo URL::to('/'); ?>/assets/js/select2.js"></script>
    <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2/select2.min.css">

    <script>
        $('#type').on('change', function() {

            if (this.value == "individual") {

                $('#outletId').show();

            } else {

                $('#outletId').hide();

            }

        });

        function validation() {

            var temp = 0;

            var first_name = $('#first_name').val();
            var last_name = $('#last_name').val();
            var email = $('#email').val();
            var phone = $('#phone').val();
            var role = $('#role').val();
            var is_nurse = $('#is_nurse').prop("checked");
            if (first_name == "") {
                $('#first_name_error').html("Please enter First Name");
                temp++;
            } else {
                $("#first_name_error").html("");
            }
            if (last_name == "") {
                $('#last_name_error').html("Plese enter Last Name");
                temp++;
            } else {
                $("#last_name_error").html("");
            }

            if (email == "") {
                $("#email_error").html("Please enter Email");
                temp++;
            } else {

                var filter1 = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;

                if (filter1.test(email)) {

                    $("#email_error").html("");

                } else {

                    $("#email_error").html("Please Enter Valid Email.");

                    temp++;

                }

            }

            if (phone != "") {
                var filter = /^[0-9-+]+$/;
                var pattern = /^\d{10,14}$/;
                if (filter.test(phone)) {
                    if (pattern.test(phone)) {
                        $("#phone_error").html("");
                    } else {
                        $("#phone_error").html("Please Enter Valid Phone No.");
                        temp++;
                    }
                } else {
                    $("#phone_error").html("Character Not allow.");
                    temp++;
                }
            }

            if (role == "") {
                $('#role_error').html("Please select Role");
                temp++;
            } else {
                $("#role_error").html("");
            }

            if(is_nurse){
                if($('#language_id').val() == ''){
                    $('#language_error').html("Please select Language");
                    temp++;
                }
            }
            if (temp == 0) {
                return true;
            } else {
                return false;
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
        $('#show_user_type').hide();

        function getUserType(val) {

            var id = val;

            $.ajax({
                type: "POST",
                url: "{{ url('getUserType') }}?id=" + id,
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                success: function(data) {
                    if (data != '') {
                        $('#user_type').html(data);

                    } else {
                        $('#user_type').html(
                            '<input disabled type="text" class="form-control" value="Data not found" >');
                    }
                }
            });
        }

        $('#is_nurse').change(function(){
            $('.language_div').addClass('d-none')
            if($('#is_nurse').prop("checked")) {
                $('.language_div').removeClass('d-none')
            }
        });

        $(document).ready(function(){
            $('.language_div').addClass('d-none')
            if($('#is_nurse').prop("checked")) {
                $('.language_div').removeClass('d-none')
            }
        });

    </script>

    @include('include/footer')
