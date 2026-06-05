@include('include/header')
@include('include/sidebar')


<!-- Begin Page Content -->
<div class="main-panel">

    <div class="content-wrapper">
        <div class="page-title-main mb-3">
            <h5 class="mb-0 font-weight-bold">My Profile</h5>
        </div>
        <!-- Page Heading -->


        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="mb-0 font-weight-bold">Profile Detail</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <form class="user" id="my_profile_form" method="POST"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="card-body  pl-0">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">First Name<span
                                                    class="error">*</span></label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control charCls" placeholder="Enter First Name"
                                                    id="first_name" name="first_name" value="{{ auth()->user()->first_name}}" @if(auth()->user()->agency_fk !="") readonly @endif maxlength="50">
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
                                                <input type="text" class="form-control charCls" placeholder="Enter last Name"
                                                    id="last_name" name="last_name" value="{{ auth()->user()->last_name}}" @if(auth()->user()->agency_fk !="") readonly @endif maxlength="50">
                                                <span class="error mt-2"
                                                    id="last_name_error"><?php echo $errors->add_user->first('last_name'); ?></span>
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
                                                    value="{{ auth()->user()->email}}" readonly>
                                                <span class="error mt-2"
                                                    id="email_error"><?php echo $errors->add_user->first('email'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Phone No<span
                                            class="error">*</span></label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" placeholder="Enter Phone No"
                                                    onkeypress="return isNumber(event)" class="span11" id="phone"
                                                    name="phone" value="{{ auth()->user()->phone}}" @if(auth()->user()->agency_fk !="") readonly @endif>
                                                <span class="error mt-2"
                                                    id="phone_error"><?php echo $errors->add_user->first('phone'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="row">

                                            <div class="col-md-9">
                                                <div class="form-group row">
                                                    <label class="col-sm-4 col-form-label">Profile Image</label>
                                                    <div class="col-sm-8">
                                                        <input type="file" class="form-control" placeholder="Enter Profile Image"
                                                            class="profile_img" id="profile_img" accept="image/*"
                                                            name="profile_img" value="<?php echo old('profile_img'); ?>">
                                                        <span class="error mt-2"
                                                            id="profile_img_error"><?php echo $errors->add_user->first('profile_img'); ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                @if(auth()->user()->profile_img !="")
                                                <img id="user_img_id" src="{{ url('user-profile-image')}}" alt="profile" style="width:100px;height:100px">
                                                @else
                                                <img src="{{ asset('assets/images/faces/face5.jpg')}}" alt="profile" style="width:100px;height:100px">
                                                @endif
                                            
                                            </div>
                                            
                                        </div>
                                        
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label">Two Factor Auth</label>
                                            <div class="col-sm-8">
                                                <label class="toggle-switch toggle-switch-success">
                                                    <input type="checkbox" name="two_fact_auth" id="two_fact_auth" class="two_fact_auth" value="{{ auth()->user()->two_fact_auth}}" {{ auth()->user()->two_fact_auth == 'Y' ? 'checked' : ''}}>
                                                    <span class="toggle-slider round"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                        </div>
                        @if(auth()->user()->agency_fk !="")
                        @else
                        <div class="card-footer footer-btns">
                           
                            <input type="button" id="submit" value="Submit" class="btn btn-primary" onclick="updateProfile()">
                        </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row" style='margin-top: 25px;'>
        <pre id='toastrOptions'></pre>
    </div>


<!-- End of Page Wrapper -->

@include('include/footer')
<script>
    function updateProfile(){
        var first_name = $('#first_name').val();
        var middle_name =$('#middle_name').val();
        var last_name = $('#last_name').val();
        var email = $('#email').val();
        var phone = $('#phone').val();
        var profile_img = $('#profile_img').prop('files');

        $('#first_name_error').html("");
        $('#last_name_error').html("");
        $('#email_error').html("")
        $('#phone_error').html("");
        var cnt =0;
        if(first_name.trim() ==''){
            $('#first_name_error').html("Please enter First Name");
            cnt =1;    
        }
        if(last_name.trim() ==''){
            $('#last_name_error').html("Please enter Last Name");
            cnt =1;    
        }
        if(email.trim() ==''){
            $('#email_error').html("Please enter Email");
            cnt =1;    
        }

        if(email.trim() !=""){
            var filter1 = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
            if (filter1.test(email)) {

            } else {
                $("#email_error").html("Please Enter Valid Email.");
                cnt =1;
            }
        }

        if(phone.trim() ==''){
            $("#phone_error").html("Please Enter Phone No");
            cnt =1;
        }
        if (phone != "") {
            var filter = /^[0-9-+]+$/;
            var pattern = /^\d{10,14}$/;
            if (filter.test(phone)) {
                if (pattern.test(phone)) {
                   
                } else {
                    $("#phone_error").html("Please Enter Valid Phone No.");
                   cnt =1;
                }
            } else {
                $("#phone_error").html("Character Not allow.");
                cnt =1;
            }
        }

        if(profile_img.length !=0){
            const validExtensions = ["jpg", "jpeg", "png"];

            const extension = profile_img[0].name.split('.').pop().toLowerCase();
            if(validExtensions.includes(extension)){

            }else{
                $("#profile_img_error").html("Only for jpg,jpeg,png extension should be allowed");
                cnt =1;
            }
            var maxSize = 2 * 1024 * 1024; // 2MB
            if(profile_img[0].size > maxSize){
                $("#profile_img_error").html("Profile image must not exceed 2MB");
                cnt =1;
            }
        }

        if(cnt ==1){
            return false;
        }else{
            var formData = $('#my_profile_form')[0];
            var form = new FormData(formData);
            form.append('_token','{{ csrf_token()}}');

            $.ajax({
                async:false,
                global:false,
                type:"POST",
                url:"{{ url('update-my-profile')}}",
                data:form,
                processData: false,
                contentType: false,
                success:function(res){
                    toastr.success(res.error_msg);
                   location.reload();
                },
                error:function(jqr){
                    toastr.error(jqr.responseJSON.error_msg);
                }
            })
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
        $(document).on("change", ".two_fact_auth", function() {
                var two_fact_auth = $(this).prop('checked') == true ? 'Y' : 'N';
                var user_id = "{{auth()->user()->id}}";
                if(two_fact_auth == 0){
                    content = 'Would you like to disable two factor authentication?';
                }else{
                    content = 'Would you like to enable two factor authentication?';
                }
                $.confirm({
                    title: 'Are you sure?',
                    content: content,
                    columnClass: "col-md-6",
                    buttons: {
                        formSubmit: {
                            text: 'Confirm',
                            btnClass: 'btn-primary',
                            action: function() {
                                $.ajax({
                                    type: "post",
                                    dataType: "json",
                                    url: '{{url("user-two-factor-authentication")}}',
                                    data: {
                                        'two_fact_auth': two_fact_auth,
                                        '_token':"{{ csrf_token()}}"
                                    },
                                    success: function(data) {
                                        toastr.success(data.error_msg);
                                        $('.two_fact_auth').val(two_fact_auth)
                                    }
                                });
                            }
                        },
                        cancel: function() {
                            //close
                            var lastStatus = $('#two_fact_auth').val();
                            
                            if(lastStatus ==1){
                                $('#two_fact_auth').prop("checked",true);
                            }else{
                                $('#two_fact_auth').prop("checked",false);
                            }
                        },
                    },
                });
            });
</script>