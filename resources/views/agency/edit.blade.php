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
            <h5 class="mb-0 font-weight-bold">Agency Edit</h5>
        </div>
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <form class="form-sample" method="post" action='<?php echo URL::to('/agency/update'); ?>/<?php echo $id; ?>'
                            name="edituser" onsubmit="return validation();">
                            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Agency Name<span
                                                class="error">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control charCls" placeholder="Enter Agency Name "
                                                id="agency_name" name="agency_name" value="<?php echo $agency->agency_name; ?>" maxlength="50">
                                            <span id="agency_name_error" class="error mt-2"><?php echo $errors->edit_agency->first('agency_name'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Email<span
                                                class="error">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Enter Email"
                                                id="email" name="email" value="<?php echo $agency->email; ?>">
                                            <span id="email_error" class="error mt-2"><?php echo $errors->edit_agency->first('email'); ?></span>
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
                                                name="phone" value="<?php echo $agency->phone; ?>">
                                            <span id="phone_error" class="error mt-2"><?php echo $errors->edit_agency->first('phone'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Address 1<span
                                                class="error">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Enter Address 1"
                                                id="address1" name="address1" value="<?php echo $agency->address1; ?>"  maxlength="150">
                                            <span id="address1_error" class="error mt-2"><?php echo $errors->edit_agency->first('address1'); ?></span>
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
                                                id="address2" name="address2" value="<?php echo $agency->address2; ?>"  maxlength="150">
                                            <span id="address2_error" class="error mt-2"><?php echo $errors->edit_agency->first('address2'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Zip Code<span
                                                class="error">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Enter Zip Code"
                                                maxlength="5" id="zip_code" name="zip_code"
                                                onkeypress="return isNumber(event)"
                                                onchange="getCountyByZipCode(this.value)" value="<?php echo $agency->zip_code; ?>">
                                            <span id="zip_code_error" class="error mt-2"><?php echo $errors->edit_agency->first('zip_code'); ?></span>
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
                                                id="state" name="state" value="<?php echo $agency->state; ?>" maxlength="25">
                                            <span id="state_error" class="error mt-2"><?php echo $errors->edit_agency->first('state'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">City<span
                                                class="error">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control charCls" placeholder="Enter City"
                                                id="city" name="city" value="<?php echo $agency->city; ?>"  maxlength="150">
                                            <span id="city_error" class="error mt-2"><?php echo $errors->edit_agency->first('city'); ?></span>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Country</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control"
                                                readonly id="county" name="county"
                                                onkeypress="return isNumber(event)" value="{{($agency->county!='County not found') ? $agency->county : ''}}">
                                            <span id="county_error" class="error mt-2"><?php echo $errors->add_record->first('county'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Client Name</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control"
                                                 id="client_name" name="client_name"
                                             value="{{$agency->client_name}}">
                                            <span id="client_name_error" class="error mt-2"><?php echo $errors->add_record->first('client_name'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Company Name</label>
                                        <div class="col-sm-9">
                                            <select class="form-control" id="domain_config_id" name="domain_config_id">
                                                <option value="">-- Select Company --</option>
                                                @foreach($domainConfigs as $dc)
                                                    <option value="{{ $dc->id }}" {{ isset($agencyCompany) && $agencyCompany->domain_config_id == $dc->id ? 'selected' : '' }}>
                                                        {{ $dc->company_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Notification Email For NYBEST Users (commas
                                            seperate)    </label>
                                        <div class="col-sm-9">
                                            <textarea style="height: 80px !important;" class="form-control" name="notification_email" id="notification_email"><?php echo $agency->notification_email; ?></textarea>

                                            <span id="bill_date_error" class="error mt-2"><?php echo $errors->add_agency->first('notification_email'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Agency Notification Email For document and status update (commas
                                            seperate)</label>
                                        <div class="col-sm-9">
                                            <textarea style="height: 80px !important;" class="form-control" name="nybest_email_notification"
                                                id="nybest_email_notification"><?php echo $agency->nybest_email_notification; ?></textarea>

                                            <span id="bill_date_error" class="error mt-2"><?php echo $errors->add_agency->first('nybest_email_notification'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Document Email (commas
                                            seperate)</label>
                                        <div class="col-sm-9">
                                            <textarea style="height: 80px !important;" class="form-control" name="document_email_notification"
                                                id="document_email_notification"><?php echo $agency->document_email_notification; ?></textarea>

                                            <span id="bill_date_error"
                                                class="error mt-2"><?php echo $errors->add_agency->first('document_email_notification'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Efax No</label>
                                        <div class="col-sm-9">
                                        <input type="text" class="form-control" id="efax_no" name="efax_no" value="<?php echo $agency->efax_no; ?>">

                                            <span id="efax_no_error" class="error mt-2"></span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <hr>
                            <h4>HHA Credential</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">App Name</label>
                                        <div class="col-sm-9">
                                            <input class="form-control" type="text" placeholder="App Name"
                                                name="app_name" value="<?php echo $agency->app_name; ?>">

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">App Key</label>
                                        <div class="col-sm-9">
                                            <input class="form-control" type="text" placeholder="App Key"
                                                name="app_key" value="<?php echo $agency->app_key; ?>">

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">App Token</label>
                                        <div class="col-sm-9">
                                            <input class="form-control" type="text" placeholder="App Token"
                                                name="app_token" value="<?php echo $agency->app_token; ?>">

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary mr-2">Update</button>
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
    $('#type').on('change', function() {
        if (this.value == "individual") {
            $('#outletId').show();
        } else {
            $('#outletId').hide();
        }
    });

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
        };
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

    function isNumber(evt) {

        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if ((charCode != 46 || $(this).val().indexOf('.') != -1) && (charCode < 48 || charCode > 57)) {

            return false;
        }
        return true;
    }
</script>
<!-- Date Picker -->

<!-- End Date Picker -->
@include('include/footer')
