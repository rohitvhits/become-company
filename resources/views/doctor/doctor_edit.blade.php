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
            <h5 class="mb-0 font-weight-bold">Doctor Edit</h5>
        </div>
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <form class="form-sample" action='<?php echo URL::to('/doctor/update/' . $doctor->id); ?>' name="adduser" method="post"
                            enctype="multipart/form-data" onsubmit="return validation();">
                            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Full Name<span
                                                class="error">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control charCls"
                                                placeholder="Enter Full Name " id="agency_name" name="full_name"
                                                value="<?php echo $doctor->full_name; ?>" maxlength="50">
                                            <span id="agency_name_error" class="error mt-2"><?php echo $errors->add_agency->first('full_name'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Email<span
                                                class="error">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="email" class="form-control" placeholder="Enter Email"
                                                id="email" name="email" value="<?php echo $doctor->email; ?>"
                                                maxlength="50">
                                            <span id="email_error" class="error mt-2"><?php echo $errors->add_agency->first('email'); ?></span>
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
                                                name="phone" value="<?php echo $doctor->phone; ?>">
                                            <span id="phone_error" class="error mt-2"><?php echo $errors->add_agency->first('phone'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Gender<span
                                                class="error">*</span></label>

                                        <div class="col-sm-4">
                                            <div class="form-check">
                                                <label class="form-check-label">
                                                    <input type="radio" class="form-check-input" id="msp"
                                                        name="gender" value="male" <?php if ($doctor->gender == 'male') {
                                                            echo "checked='checked'";
                                                        } ?>> Male <i
                                                        class="input-helper"></i></label>
                                            </div>
                                        </div>
                                        <div class="col-sm-5">
                                            <div class="form-check">
                                                <label class="form-check-label">
                                                    <input type="radio" class="form-check-input" id="msp"
                                                        name="gender" value="female" <?php if ($doctor->gender == 'female') {
                                                            echo "checked='checked'";
                                                        } ?>> Female<i
                                                        class="input-helper"></i></label>
                                            </div>
                                        </div>
                                        <span id="address2_error" class="error mt-2"
                                            style="margin-left:27%;"><?php echo $errors->add_agency->first('gender'); ?></span>
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Notes</label>
                                        <div class="col-sm-9">
                                            <textarea class="form-control" placeholder="Notes" name="message" style="height: 50px"><?php echo $doctor->remarks; ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">License<span
                                                class="error">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Enter Licence "
                                                id="license" name="license" value="<?php echo $doctor->license; ?>">
                                            <span id="license_error" class="error mt-2"><?php echo $errors->add_agency->first('license'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Address<span
                                                class="error">*</span></label>
                                        <div class="col-sm-9">
                                            <textarea id="address" name="address" class="form-control " rows="6" cols="50"
                                                placeholder="Enter Address"><?php echo $doctor->address; ?></textarea>
                                            <span id="address_error" class="error mt-2"><?php echo $errors->add_agency->first('address'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">State<span
                                                class="error">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Enter State "
                                                id="state" name="state" value="<?php echo $doctor->state; ?>">
                                            <span id="state_error" class="error mt-2"><?php echo $errors->add_agency->first('state'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">

                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">City<span
                                                class="error">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Enter City "
                                                id="city" name="city" value="<?php echo $doctor->city; ?>">
                                            <span id="city_error" class="error mt-2"><?php echo $errors->add_agency->first('city'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Zipcode<span
                                                class="error">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="number" class="form-control charCls"
                                                placeholder="Enter Zipcode " id="zipcode" name="zipcode"
                                                value="<?php echo $doctor->zipcode; ?>">
                                            <span id="zipcode_error" class="error mt-2"><?php echo $errors->add_agency->first('zipcode'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Place Of Examination<span
                                                class="error">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control"
                                                placeholder="Enter Place Of Examination " id="place_of_examination"
                                                name="place_of_examination" value="<?php echo $doctor->place_of_examination; ?>">
                                            <span id="place_of_examination_error"
                                                class="error mt-2"><?php echo $errors->add_agency->first('place_of_examination'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Date Of Examination<span
                                                class="error">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control"
                                                placeholder="Enter Date Of Examination " id="date_of_examination"
                                                name="date_of_examination" value="<?php echo $doctor->date_of_examination; ?>">
                                            <span id="date_of_examination_error"
                                                class="error mt-2"><?php echo $errors->add_agency->first('date_of_examination'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Signature Upload</label>
                                        <div class="col-sm-7">
                                            <input type="file" class="form-control" value=""
                                                id="signature_upload" name="signature_upload">
                                            <span id="signature_upload_error"
                                                class="error mt-2"><?php echo $errors->add_agency->first('signature_upload'); ?></span>
                                        </div>
                                        @if ($doctor->signature_upload != '')
                                            <div class="col-sm-2">
                                                <img src="{{ url('/doctor-image-show-aws') }}/{{ $doctor->id }}?type=signature"
                                                    alt="Signature Image" title="Signature Image"
                                                    style="height:100px;width:100px;">
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Stamp Upload</label>
                                        <div class="col-sm-7">
                                            <input type="file" class="form-control" value=""
                                                id="stamp_upload" name="stamp_upload">
                                            <span id="stamp_upload_error"
                                                class="error mt-2"><?php echo $errors->add_agency->first('stamp_upload'); ?></span>
                                        </div>
                                        @if ($doctor->stamp_upload != '')
                                            <div class="col-sm-2">
                                                <img src="{{ url('/doctor-image-show-aws') }}/{{ $doctor->id }}?type=stamp"
                                                    alt="Stamp Image" title="Stamp Image"
                                                    style="height:100px;width:100px;">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Specialty<span
                                                class="error">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control charCls"
                                                placeholder="Enter Specialty " id="specialty" name="specialty"
                                                value="<?php echo $doctor->specialty; ?>" maxlength="50">
                                            <span id="specialty_error" class="error mt-2"><?php echo $errors->add_agency->first('specialty'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Registry Number<span
                                                class="error">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" value="<?php echo $doctor->registry_number; ?>"
                                                placeholder="Enter Registry Number " id="registry_number"
                                                name="registry_number" onkeypress="return isNumber(event)" maxlength="10">
                                            <span id="registry_number_error"
                                                class="error mt-2"><?php echo $errors->add_agency->first('registry_number'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">NPI Number<span
                                                class="error">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" value="<?php echo $doctor->npi_number; ?>"
                                                placeholder="Enter NPI Number " id="npi_number" name="npi_number"
                                                onkeypress="return isNumber(event)" maxlength="10">
                                            <span id="npi_number_error" class="error mt-2"><?php echo $errors->add_agency->first('npi_number'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">

                            </div>

                            <button type="submit" class="btn btn-primary mr-2">Update</button>
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

    <script>
        function notStartWithZero(phone) {
            var expr = /^[1-9]\d*$/;
            return expr.test(phone);
        };

        function validation() {

            var temp = 0;

            var agency_name = $('#agency_name').val();
            var email = $('#email').val();
            var phone = $('#phone').val();
            var gender = $('input[name="gender"]').is(":checked");
            var license = $('#license').val();
            var address = $('#address').val();
            var state = $('#state').val();
            var city = $('#city').val();
            var zipcode = $('#zipcode').val();
            var place_of_examination = $('#place_of_examination').val();
            var date_of_examination = $('#date_of_examination').val();
            var specialty = $('#specialty').val();
            var registry_number = $('#registry_number').val();
            var npi_number = $('#npi_number').val();

            $("#agency_name_error").html("");
            $("#email_error").html("");
            $("#phone_error").html("");
            $("#address2_error").html("");
            $('#license_error').html('');
            $('#address_error').html('');
            $('#state_error').html('');
            $('#city_error').html('');
            $('#zipcode_error').html('');
            $('#place_of_examination_error').html('');
            $('#date_of_examination_error').html('');
            $('#specialty_error').html('');
            $('#registry_number_error').html('');
            $('#npi_number_error').html('');
            $('#signature_upload_error').html('');
            $('#stamp_upload_error').html('');

            function ValidateEmail(email) {
                var expr =
                    /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
                return expr.test(email);
            };
            if (agency_name.trim() == "") {
                $('#agency_name_error').html("Please enter Full Name");
                temp++;
            }
            if (email.trim() == "") {
                $('#email_error').html("Please enter Email");
                temp++;
            } else if (!ValidateEmail(email)) {
                $('#email_error').html("Please enter a valid email address..");
                temp++;
            }
            if (phone.trim() == "") {
                $('#phone_error').html("Please enter Phone");
                temp++;
            } else if (!notStartWithZero(phone)) {
                $('#phone_error').html("Please enter valid Phone");
                temp++;
            } else if (phone.length < 10 || phone.length > 15) {
                $('#phone_error').html("Please enter minimum 10 to 15 digits Phone");
                temp++;
            } else {
                $('#phone_error').html("");
            }
            if (gender == false) {
                $('#address2_error').html("Please select Gender");
                temp++;
            }
            if (license.trim() == "") {
                $('#license_error').html("Please enter License");
                temp++;
            }
            if (address.trim() == "") {
                $('#address_error').html("Please enter Address");
                temp++;
            }
            if (state.trim() == "") {
                $('#state_error').html("Please enter State");
                temp++;
            }
            if (city.trim() == "") {
                $('#city_error').html("Please enter City");
                temp++;
            }
            if (zipcode.trim() == "") {
                $('#zipcode_error').html("Please enter Zipcode");
                temp++;
            }
            if (place_of_examination.trim() == "") {
                $('#place_of_examination_error').html("Please enter Place Of Examination");
                temp++;
            }
            if (date_of_examination.trim() == "") {
                $('#date_of_examination_error').html("Please enter Date Of Examination");
                temp++;
            }

            if (specialty.trim() == "") {
                $('#specialty_error').html("Please enter Specialty");
                temp++;
            }
            if (registry_number.trim() == "") {
                $('#registry_number_error').html("Please enter Registry Number");
                temp++;
            }
            if (npi_number.trim() == "") {
                $('#npi_number_error').html("Please enter NPI Number");
                temp++;
            }

            var existingFile = "{{ $doctor->signature_upload }}";
            var files = $('input[name="signature_upload"]')[0].files;
            if (files.length > 0) {
                var fileExtensionType = ["jpg", "jpeg", "png"];
                var fileName = files[0].name;
                if ($.inArray(fileName.split(".").pop().toLowerCase(), fileExtensionType) == -1) {
                    $("#signature_upload_error").html("Please select only jpg, jpeg, or png file");
                    temp++;
                }
            }
            var existingStampUpload = "{{ $doctor->stamp_upload }}";
            var files = $('input[name="stamp_upload"]')[0].files;
            if (files.length > 0) {
                    var fileExtensionType = ["jpg", "jpeg", "png"];
                    var fileName = files[0].name;
                    if ($.inArray(fileName.split(".").pop().toLowerCase(), fileExtensionType) == -1) {
                        $("#stamp_upload_error").html("Please select only jpg, jpeg, or png file");
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

                    $('#county').val(response);
                }
            });
        }
    </script>

    <!-- Date Picker -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <link rel="stylesheet"
        href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
    <script>
        $("#date_of_examination").datepicker();
    </script>

    <!-- End Date Picker -->
    @include('include/footer')
