@include('include/header')

<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Enquiry Add</h5>
        </div>
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <form class="form-sample" action='{{ url("enquiry/save")}}' name="adduser" method="post"
                            onsubmit="return validation();">
                            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Email<span
                                                class="error mt-2">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="email"
                                                placeholder="Enter Email" name="email"
                                                value="<?php echo old('email'); ?>">
                                            <span id="email_error" class="error mt-2"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Mobile<span
                                                class="error mt-2">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" maxlength="15" onkeypress="return isNumber(event)" class="form-control" placeholder="Enter Mobile"
                                                id="mobile" name="mobile" value="<?php echo old('mobile'); ?>">
                                            <span id="mobile_error"
                                                class="error mt-2"><?php echo $errors->add_agency->first('mobile'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Subject<span
                                                class="error mt-2">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Enter Subject"
                                                id="subject" name="subject" value="<?php echo old('subject'); ?>">
                                            <span id="subject_error"
                                                class="error mt-2"><?php echo $errors->add_agency->first('subject'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Message<span
                                                class="error mt-2">*</span></label>
                                        <div class="col-sm-9">
                                            <textarea class="form-control" rows="10" placeholder="Enter Message"
                                            id="message" name="message" >{{ old('message')}}</textarea>
                                           
                                            <span id="message_error"
                                                class="error mt-2"><?php echo $errors->add_agency->first('message'); ?></span>
                                        </div>
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
    <div class="row" style='margin-top: 25px;'>
        <pre id='toastrOptions'></pre>
    </div>
@include('include/footer')
<script>
    function validation(){
        var email = $('#email').val();
        var mobile = $('#mobile').val();
        var subject = $('#subject').val();
        var message = $('#message').val();

        var cnt =0;
        $('#email_error').html("");
        $('#mobile_error').html("");
        $('#subject_error').html("");
        $('#message_error').html("");
        var regrex = /[a-z0-9\._%+!$&*=^|~#%'`?{}/\-]+@([a-z0-9\-]+\.){1,}([a-z]{2,16})/
        if(email.trim() ==""){
            $('#email_error').html("Please enter email");
            cnt =1;
        }

        if(email.trim() !=""){
            if(!regrex.test(email)){
                $('#email_error').html("Invalid Email address");
                cnt =1;
            }
        }
        if(mobile.trim() ==""){
            $('#mobile_error').html("Please enter mobile");
            cnt =1;
        }

        if(subject.trim() ==""){
            $('#subject_error').html("Please enter subject");
            cnt =1;
        }

        if(message.trim() ==""){
            $('#message_error').html("Please enter message");
            cnt =1;
        }

        if(cnt ==1){
            return false;
        }else{
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