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
</style>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Company Add</h5>
        </div>
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <form class="form-sample" action='<?php echo URL::to('/hub-company/save'); ?>' name="adduser" method="post"
                            onsubmit="return validation();">
                            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Company Name<span
                                                class="error">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control charCls" placeholder="Enter Company Name "
                                                id="agency_name" name="agency_name" value="<?php echo old('agency_name'); ?>" maxlength="50" >
                                            <span id="agency_name_error"
                                                class="error mt-2"><?php echo $errors->add_agency->first('agency_name'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Email<span
                                            class="error">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Enter Email"
                                                id="email" name="email" value="<?php echo old('email'); ?>">
                                            <span id="email_error"
                                                class="error mt-2"><?php echo $errors->add_agency->first('email'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Phone<span
                                            class="error">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Enter Phone"
                                                maxlength="15" onkeypress="return isNumber(event)" id="phone"
                                                name="phone" value="<?php echo old('phone'); ?>">
                                            <span id="phone_error"
                                                class="error mt-2"><?php echo $errors->add_agency->first('phone'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Address 1<span
                                            class="error">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Enter Address 1"
                                                id="address1" name="address1" value="<?php echo old('address1'); ?>"  maxlength="150">
                                            <span id="address1_error"
                                                class="error mt-2"><?php echo $errors->add_agency->first('address1'); ?></span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Address 2<span
                                            class="error">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Enter Address 2"
                                                id="address2" name="address2" value="<?php echo old('address2'); ?>"  maxlength="150">
                                            <span id="address2_error"
                                                class="error mt-2"><?php echo $errors->add_agency->first('address2'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Zip Code<span
                                            class="error">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Enter Zip Code"
                                                id="zip_code" maxlength="5" onchange="getCountyByZipCode(this.value)"
                                                name="zip_code"
                                                value="<?php echo old('zip_code'); ?>">
                                            <span id="zip_code_error"
                                                class="error mt-2"><?php echo $errors->add_agency->first('zip_code'); ?></span>
                                        </div>
                                    </div>
                                </div>


                            </div>
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">State<span
                                            class="error">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control charCls" placeholder="Enter State"
                                                id="state" name="state" value="<?php echo old('state'); ?>"  maxlength="25">
                                            <span id="state_error"
                                                class="error mt-2"><?php echo $errors->add_agency->first('state'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">City<span
                                            class="error">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control charCls" placeholder="Enter City"
                                                id="city" name="city" value="<?php echo old('city'); ?>"  maxlength="25">
                                            <span id="city_error"
                                                class="error mt-2"><?php echo $errors->add_agency->first('city'); ?></span>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">County</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="county" name="county"
                                                readonly onkeypress="return isNumber(event)"
                                                value="<?php echo old('county'); ?>">
                                            <span id="zip_code_error"
                                                class="error mt-2"><?php echo $errors->add_record->first('county'); ?></span>
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
</div>

<!-- /Main Content -->

<!-- /Page Content -->

<script>

$(".charCls").keypress(function(event) {
            var regex = new RegExp("^[a-zA-Z ]+$");
            var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
            if (!regex.test(key)) {
                event.preventDefault();
                return false;
            }
        });
    function validation() {

        var temp = 0;

        var agency_name = $('#agency_name').val();
        var email = $('#email').val();
        var phone = $('#phone').val();
        var address1 = $('#address1').val();
        var address2 = $('#address2').val();
        var state = $('#state').val();
        var city = $('#city').val();
        var zip_code = $('#zip_code').val();
       
        function ValidateEmail(email) {
            var expr =
                /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
            return expr.test(email);
        }
        if (agency_name == "") {
            $('#agency_name_error').html("Please enter Agency Name");
            temp++;
        } else {
            $("#agency_name_error").html("");
        }
        if (email == "") {
            $('#email_error').html("Please enter Email");
            temp++;
        } else if (!ValidateEmail(email)) {
            $('#email_error').html("Please enter a valid email address");
            temp++;
        } else {
            $("#email_error").html("");
        }
        if (phone == "") {
            $('#phone_error').html("Please enter Phone");
            temp++;
        } else {
            $("#phone_error").html("");
        }
        if (address1 == "") {
            $('#address1_error').html("Please enter Address 1");
            temp++;
        } else {
            $("#address1_error").html("");
        }
        if (address2 == "") {
            $('#address2_error').html("Please enter Address 2");
            temp++;
        } else {
            $("#address2_error").html("");
        }
        if (state == "") {
            $('#state_error').html("Please enter State");
            temp++;
        } else {
            $("#state_error").html("");
        }
        if (city == "") {
            $('#city_error').html("Please enter City");
            temp++;
        } else {
            $("#city_error").html("");
        }
        if (zip_code == "") {
            $('#zip_code_error').html("Please enter Zip Code");
            temp++;
        } else {
            $("#zip_code_error").html("");
        }

        if (temp == 0) {

            return true;

        } else {

            return false;

        }

    }

    function isLatter(evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (!(charCode >= 65 && charCode <= 120) && (charCode != 32 && charCode != 0)) {
            return false;
        }
        return true;
    }

    function isNumber(evt) {

        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if ((charCode != 46 || $(this).val().indexOf('.') != -1) && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    }
    

    function getCountyByZipCode(val) {

        $.ajax({
            async: false,
            global: false,
            url: "<?= URL::to('get-county') ?>",
            type: "post",
            data: {
                zip_code: val,
                _token: '<?php echo csrf_token(); ?>'
            },
            success: function(response) {
                if(response!="County not found"){
                    $('#county').val(response);
                }else{
                    $('#county').val('');
                }
                
            }
        });
    }
</script>
<!-- Date Picker -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
<script>
    $("#bill_date").datepicker();
</script>


<!-- End Date Picker -->
@include('include/footer')
