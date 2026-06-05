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
            <h5 class="mb-0 font-weight-bold">PSE Edit</h5>
        </div>
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <form class="form-sample" method="post" action='<?php echo URL::to('/pse-location/update'); ?>/<?php echo $id; ?>'
                            name="edituser" onsubmit="return validation();">
                            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Location Name<span
                                                style="color:red">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="short_name_id"
                                                placeholder="Enter Location Name" name="short_name"
                                                value="<?php echo $agency->location_name; ?>">
                                            <span id="short_name_error" class="error mt-2"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Address 1<span
                                                style="color:red">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Enter address"
                                                id="address1" name="address1" value="<?php echo $agency->address1; ?>">
                                            <span id="address1_error"
                                                class="error mt-2"><?php echo $errors->add_agency->first('address1'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Address 2<span
                                                style="color:red">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Enter address"
                                                id="address2" name="address2" value="<?php echo $agency->address2; ?>">
                                            <span id="address2_error"
                                                class="error mt-2"><?php echo $errors->add_agency->first('address2'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">State<span
                                                style="color:red">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Enter state"
                                                id="state" name="state" value="<?php echo $agency->state; ?>">
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
                                                style="color:red">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Enter City"
                                                id="city" name="city" value="<?php echo $agency->city; ?>">
                                            <span id="city_error"
                                                class="error mt-2"><?php echo $errors->add_agency->first('city'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Zip Code<span
                                                style="color:red">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Enter Zip Code"
                                                maxlength="5" id="zip_code" name="zip_code"
                                                onkeypress="return isNumber(event)"
                                                onchange="getCountyByZipCode(this.value)" value="<?php echo $agency->zip_code; ?>">
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
                                                value="<?php echo $agency->link; ?>">
                                            <span id="" class="error mt-2"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Walkin</label>
                                        <div class="col-sm-9">
                                            <input type="checkbox"  name="walkin"
                                                value="1" @if($agency->walkin ==1) checked @endif>
                                           
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Latitude</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control"
                                                placeholder="Enter Latitude" name="latitude"
                                                value="{{$agency->latitude}}" >
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
                                                value="{{$agency->longitude}}">
                                            <span id="longitude_err" class="error mt-2"></span>
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
    <div class="row" style='margin-top: 25px;'>
        <pre id='toastrOptions'></pre>
    </div>

    <!-- /Main Content -->
    <!-- /Page Content -->
    <script>
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

                    $('#county').val(response);
                }
            });
        }

        function validation() {
            var temp = 0;

            var address1 = $('#address1').val();
            var address2 = $('#address2').val();
            var state = $('#state').val();
            var city = $('#city').val();
            var zip_code = $('#zip_code').val();
            var short_name_id = $('#short_name_id').val();

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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <link rel="stylesheet"
        href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
    <script>
        $("#bill_date").datepicker();
    </script>


    <!-- End Date Picker -->
    @include('include/footer')
