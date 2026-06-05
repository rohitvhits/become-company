@include('include/header')
@include('include/sidebar')

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit Country</h4>
                        <form class="form-sample" action='<?php echo URL::to('/country/update') ?>/<?php echo $id; ?>' name="editCountry" method="post" onsubmit="return validation();">
                            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Country Name </label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Enter Country Name" id="name" name="name" value="{{$data->name}}">
                                            <span id="name_error" class="error mt-2 text-danger"><?php echo $errors->edit_country->first('name'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Status </label>
                                        <div class="col-sm-9">
                                            <select name="status" id="status" class="form-control">
                                                <option value="">Select Status</option>
                                                <option value="block" <?php if ($data->status == 'block') {
                                                                            echo "selected";
                                                                        } ?>>Block</option>
                                                <option value="unblock" <?php if ($data->status == 'unblock') {
                                                                            echo "selected";
                                                                        } ?>>UnBlock</option>
                                            </select>
                                            <span id="status_error" class="error mt-2 text-danger"><?php echo $errors->edit_country->first('status'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-primary mr-2">Save</button>
                            </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <script>
            function validation() {

                var temp = 0;
                var name = $('#name').val();
                var status = $('#status').val();

                if (name == "") {
                    $('#name_error').html("Required");
                    temp++;
                } else {
                    $("#name_error").html("");
                }

                if (status == "") {
                    $('#status_error').html("Required");
                    temp++;
                } else {
                    $("#status_error").html("");
                }

                if (temp == 0) {
                    return true;
                } else {
                    return false;
                }

            }
        </script>
        @include('include/footer')