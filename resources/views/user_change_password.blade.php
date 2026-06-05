@include('include/header')

@include('include/sidebar')

<!-- main content -->
<div class="main-panel">
    <!-- <div class="content-wrapper"> -->
    <div class="card-body">
        <div class="row">

            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Change Password</h4>
                        <form class="form-sample" action="{{ URL::to('/') }}/user-change-password" onsubmit="return validate();" name="adduser" method="post" onsubmit="return validation();">
                            <input type="hidden" name="_token" value="{{ csrf_token()}}">
                            <input type="hidden" name="id" value="{{auth()->user()->id}}">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Old Password <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control" id="old_password" name="oldpassword" placeholder="Old Password" value="<?php echo old('password'); ?>">
                                        <span class="error mt-2 text-danger" id="old_passwordError">{{ $errors->Password->first('Password')}}</span>
                                    </div>
                                </div>

                            </div>
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">New Password <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control" placeholder="New Password" name="newpassword" id="new_password">
                                        <span class="error mt-2 text-danger" id="new_passwordError"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Confirm Password <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control" placeholder="Confirm Password" name="confirmpassword" id="confirm_password">
                                        <span class="error mt-2 text-danger" id="confirm_passwordError"></span>
                                    </div>
                                </div>
                            </div>



                            <button type="submit" class="btn btn-primary mr-2">Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<!-- main content -->
@include('include/footer')
<script>
    function validate() {

        var old_password = $("#old_password").val();
        var new_password = $("#new_password").val();
        var confirm_password = $("#confirm_password").val();
        var temp = 0;
        var number = /([0-9])/;
        var alphabets = /([a-zA-Z])/;
        var special_characters = /([~,!,@,#,$,%,^,&,*,-,_,+,=,?,>,<])/;
        var storngpass = "^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$";


        if (old_password.trim() == '') {
            $("#old_passwordError").html("Please enter Old Password");
            temp++;
        } else {
            $.ajax({
                async: false,
                global: false,
                url: "{{ URL::to('/')}}/check-old-password",
                type: "POST",
                data: {
                    old_password: old_password,
                    id: "{{auth()->user()->id}}",
                    _token: "{{ csrf_token()}}"
                },
                success: function(response) {
                    if (response == 1) {
                        $('#old_passwordError').html("");
                    } else {
                        $('#old_passwordError').html("Invalid Old Password");
                        temp++;
                    }
                }
            });
        }
        $('#new_passwordError').html("");
        if (new_password) {
            if (new_password.length < 8) {
                $('#new_passwordError').html("Password should be atleast 8 characters");
                temp++;
            } else {
                if (new_password.match(storngpass)) {

                } else {
                    $('#new_passwordError').html(
                        "Password must contain at least 8 characters including 1 uppercase, 1 lowercase, a number and symbol");
                    temp++;
                }
            }
        }
        if (new_password.trim() == '') {
            $("#new_passwordError").html("Please enter New Password");
            temp++;
        } else {
            $.ajax({
                async: false,
                global: false,
                url: "{{ URL::to('/')}}/check-user-old-passwords",
                type: "POST",
                data: {
                    password: new_password,
                    id: "{{auth()->user()->id}}",
                    _token: "{{ csrf_token()}}"
                },
                success: function(response) {
                    if (response == 1) {
                        $('#new_passwordError').html("New Password Already Used Please Try Another Password");
                        temp++;
                    }
                    // else {
                    //     $('#new_passwordError').html("");
                    // }
                }
            });
        }
        if (confirm_password.trim() == '') {
            $("#confirm_passwordError").html("Please enter Confirm Password");
            temp++;
        } else {
            if (new_password.trim() != confirm_password.trim()) {
                $("#confirm_passwordError").html("Password and Confirm Password must be match");
                temp++;
            } else {
                $("#confirm_passwordError").html("");
            }

        }
        if (temp == 0) {
            $("#submitBtn").prop('disabled', true);
            return true;
        } else {
            $("#submitBtn").prop('disabled', false);
            return false;
        }

    }
</script>