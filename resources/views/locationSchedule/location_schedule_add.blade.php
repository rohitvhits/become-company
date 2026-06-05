@include('include/header')
@include('include/sidebar')
<style>
    .error {
        color: Red;
    }
</style>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Add Location Schedule</h4>
                        <form class="form-sample" action='<?php echo URL::to('/location-schedule/save'); ?>' name="adduser" method="post"
                            onsubmit="return validation();">
                            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                            <input type="hidden" name="location_id" value="<?php echo $location_id; ?>">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Day<span
                                                class="error">*</span></label>
                                        <div class="col-sm-9">
                                            <select name="day" class="form-control" id="day_id">
                                                <option value="">Select Day</option>
                                                <option value="monday">Monday</option>
                                                <option value="tuesday">Tuesday</option>
                                                <option value="wednesday">Wednesday</option>
                                                <option value="thursday">Thursday</option>
                                                <option value="friday">Friday</option>
                                                <option value="saturday">Saturday</option>
                                                <option value="sunday">Sunday</option>
                                            </select>
                                            <span id="address1_error"
                                                class="error mt-2"><?php echo $errors->add_agency->first('day'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Start Time<span
                                            class="error">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="time" class="form-control" placeholder="Enter state"
                                                id="state" name="state_time" value="<?php echo old('state_time'); ?>">
                                            <span id="state_error"
                                                class="error mt-2"><?php echo $errors->add_agency->first('state_time'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">End Time<span
                                            class="error">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="time" class="form-control" placeholder="Enter City"
                                                id="city" name="end_time" value="<?php echo old('end_time'); ?>">
                                            <span id="city_error"
                                                class="error mt-2"><?php echo $errors->add_agency->first('end_time'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Slot<span
                                            class="error">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="type" class="form-control" placeholder="Enter slot"
                                                id="slot" name="slot" onkeypress="return isNumberKey(event)"
                                                value="<?php echo old('slot'); ?>">
                                            <span id="slot_error"
                                                class="error mt-2"><?php echo $errors->add_agency->first('slot'); ?></span>
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

            var day_id = $('#day_id').val();
            var end_time = $('#city').val();
            var state = $('#state').val();
            var slot = $('#slot').val();

            $("#address1_error").html("");
            $("#city_error").html("");
            $("#state_error").html("");
            $("#slot_error").html("");
            if (slot == '') {
                $("#slot_error").html("Please enter Slot");
                temp++;
            }
            if (day_id == "") {
                $('#address1_error').html("Please select Day");
                temp++;
            }
            if (end_time == "") {
                $('#city_error').html("Please select End Time");
                temp++;
            } else {

            }
            if (state == "") {
                $('#state_error').html("Please select Start Time");
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

        function isNumberKey(evt) {
            var charCode = (evt.which) ? evt.which : evt.keyCode
            if (charCode > 31 && (charCode < 48 || charCode > 57))
                return false;
            return true;
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
