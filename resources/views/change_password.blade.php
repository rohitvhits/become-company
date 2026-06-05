@include('include/header')

@include('include/sidebar')



<div class="main-panel">
  <div class="content-wrapper">
    <div class="row">
  
      <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Change Password</h4>
            <!--  <p class="card-description">
              Horizontal form layout
            </p> -->
            <form class="forms-sample" action='<?php echo URL::to('/update_password') ?>' onsubmit="return validation();" method="post">
              <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">




              <div class="form-group row">
                <label for="exampleInputUsername2" class="col-sm-3 col-form-label">Old Password</label>
                <div class="col-sm-9">
                  <input type="password" class="form-control" placeholder="Old Password" autocomplete="off" id="old_password" name="old_password" value="<?php echo old('old_password'); ?>">
                  <span class="error mt-2 text-danger" id="oerror" for="rate"><?php echo $errors->add_user->first('old_password'); ?></span>
                </div>
              </div>
              <div class="form-group row">
                <label for="exampleInputUsername2" class="col-sm-3 col-form-label">New Password</label>
                <div class="col-sm-9">
                  <input type="password" class="form-control" placeholder="New Password" autocomplete="off" id="new_password" name="new_password" value="<?php echo old('new_password'); ?>">
                  <span class="error mt-2 text-danger" id="nerror" for="rate"><?php echo $errors->add_user->first('new_password'); ?></span>
                </div>
              </div>
              <div class="form-group row">
                <label for="exampleInputUsername2" class="col-sm-3 col-form-label">Confirm Password</label>
                <div class="col-sm-9">
                  <input type="password" class="form-control" placeholder="Old Password" autocomplete="off" id="con_password" name="con_password" value="<?php echo old('con_password'); ?>">
                  <span class="error mt-2 text-danger" id="oerror" for="rate"><?php echo $errors->add_user->first('con_password'); ?></span>
                </div>
              </div>

              <button type="submit" class="btn btn-primary mr-2">Submit</button>
              <!--  <button class="btn btn-light">Cancel</button> -->
            </form>
          </div>
        </div>
      </div>



      @include('include/footer')



      <script>
        function validation() {

          var opass = $('#old_password').val();

          var npass = $('#new_password').val();

          var cpass = $('#con_password').val();

          var temp = 0;

          if (opass == "") {
            $('#oerror').html("Old password required");
            temp++;
          } else {
            $('#oerror').html("");
          }

          if (npass == "") {
            $('#nerror').html("New password required");
            temp++;
          } else {
            $("#nerror").html("");
          }

          if (opass == npass && opass != '' && npass != '') {

            $("#nerror").html("Your new password must be different from your old password.");

            temp++;

          }



          if (cpass == "") {
            $('#cerror').html("Confirm password required");
            temp++;
          } else {

            $("#cerror").html('');

            if (npass.length < 5) {

              $("#nerror").html('Password must be at least five characters long!');

              temp++;

            } else if (npass != cpass) {

              $("#nerror").html('Password and confirm password does not match!');

              $("#cerror").html('Password and confirm password does not match!');

              temp++;

            }

          }

          if (temp == 0) {

            return true;

          } else {

            return false;

          }

        }
      </script>