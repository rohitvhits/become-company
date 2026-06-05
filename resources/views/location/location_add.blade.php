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
            <h5 class="mb-0 font-weight-bold">Location Add</h5>
        </div>
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <form class="form-sample" action='<?php echo URL::to('/location/save'); ?>' name="adduser" method="post"
                            onsubmit="return validation();">
                            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Location Name<span
                                                class="error mt-2">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="short_name_id"
                                                placeholder="Enter Location Name" name="short_name"
                                                value="<?php echo old('short_name'); ?>">
                                            <span id="short_name_error" class="error mt-2"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Address 1<span
                                                class="error mt-2">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Enter address"
                                                id="address1" name="address1" value="<?php echo old('address1'); ?>">
                                            <span id="address1_error"
                                                class="error mt-2"><?php echo $errors->add_agency->first('address1'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Address 2<span
                                                class="error mt-2">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Enter address"
                                                id="address2" name="address2" value="<?php echo old('address2'); ?>">
                                            <span id="address2_error"
                                                class="error mt-2"><?php echo $errors->add_agency->first('address2'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">State<span
                                                class="error mt-2">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Enter state"
                                                id="state" name="state" value="<?php echo old('state'); ?>">
                                            <span id="state_error"
                                                class="error mt-2"><?php echo $errors->add_agency->first('state'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">City<span
                                                class="error mt-2">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Enter City"
                                                id="city" name="city" value="<?php echo old('city'); ?>">
                                            <span id="city_error"
                                                class="error mt-2"><?php echo $errors->add_agency->first('city'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Zip Code<span
                                                class="error mt-2">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Enter Zip Code"
                                                id="zip_code" maxlength="5" onchange="getCountyByZipCode(this.value)"
                                                name="zip_code" onkeypress="return isNumber(event)"
                                                value="<?php echo old('zip_code'); ?>">
                                            <span id="zip_code_error"
                                                class="error mt-2"><?php echo $errors->add_agency->first('zip_code'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Location Link</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control"
                                                placeholder="Enter location link" name="link"
                                                value="<?php echo old('link'); ?>">
                                            <span id="" class="error mt-2"></span>
                                        </div>
                                    </div>
                                </div> 
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Walkin</label>
                                        <div class="col-sm-9">
                                            <input type="checkbox" 
                                                 name="walkin"
                                                value="1">
                                           
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Latitude</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control"
                                                placeholder="Enter Latitude" name="latitude"
                                                value="<?php echo old('latitude'); ?>">
                                            <span id="latitude_err" class="error mt-2"></span>
                                        </div>
                                    </div>
                                </div> 
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Longitude</label>
                                        <div class="col-sm-9">
                                        <input type="text" class="form-control"
                                                placeholder="Enter Longitude" name="longitude"
                                                value="<?php echo old('longitude'); ?>">
                                            <span id="longitude_err" class="error mt-2"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Telehealth Configuration</label>
                                        <div class="col-sm-9">
                                            <select name="telehealth_config" id="telehealth_config" class="form-control">
                                                <option value="" selected>None</option>
                                                <option value="caregiver">Caregiver</option>
                                                <option value="patient">Patient</option>
                                            </select>
                                            <span id="telehealth_config_error" class="error mt-2"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Stop Date</label>
                                        <div class="col-sm-9">
                                            <input type="text" data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" class="form-control bill_date form-control-sm " autocomplete="off" placeholder="Select Stop Date" id="stop_date" name="stop_date" min="1000-01-01" max="9999-12-31">
                                            <span id="stop_date_err" class="error mt-2"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Stop Time</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control time_input form-control-sm"
                                                autocomplete="off"
                                                placeholder="Select Stop Time"
                                                id="stop_time"
                                                name="stop_time"
                                                data-inputmask="'alias': 'datetime', 'inputFormat': 'HH:MM'">

                                            <span id="stop_time_err" class="error mt-2"></span>
                                            <span class="text-muted mt-2">Note: Time Format is 24 Hours (HH:MM)</span>
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

    <!-- /Main Content -->

    <!-- /Page Content -->

    <script>
        function validation() {

            var temp = 0;


            var address1 = $('#address1').val();
            var address2 = $('#address2').val();
            var state = $('#state').val();
            var city = $('#city').val();
            var zip_code = $('#zip_code').val();
            var short_name_id = $('#short_name_id').val();
            $('#short_name_error').html("");
            $("#address1_error").html("");
            $("#address2_error").html("");
            $("#state_error").html("");
            $("#city_error").html("");
            $("#zip_code_error").html("");
            if (address1 == "") {
                $('#address1_error').html("Please enter Address 1");
                temp++;
            }
            if (address2 == "") {
                $('#address2_error').html("Please enter Address 2");
                temp++;
            }
            if (state == "") {
                $('#state_error').html("Please enter State");
                temp++;
            }
            if (city == "") {
                $('#city_error').html("Please enter City");
                temp++;
            }
            if (zip_code == "") {
                $('#zip_code_error').html("Please enter Zip Code");
                temp++;
            }
            if (short_name_id.trim() == '') {
                $('#short_name_error').html("Please enter Location Name");
                temp++;
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
    <script src="{{ asset('assets/vendors/inputmask/jquery.inputmask.bundle.js')}}"></script>
    <script>
        $("#bill_date").datepicker();
        $(":input").inputmask();
    </script>


    <!-- End Date Picker -->
    @include('include/footer')
