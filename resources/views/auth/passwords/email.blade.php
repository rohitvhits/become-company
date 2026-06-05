<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>NY BEST MEDICAL</title>
  <!-- base:css -->
  <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/mdi/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/css/vendor.bundle.base.css">
  <!-- endinject -->
  <!-- plugin css for this page -->
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/css/vertical-layout-light/style.css">
  <!-- endinject -->

  <link rel="shortcut icon" href="/img/favicon.png"   />
  <style> 
    .bgimage {
      width: 500px; 
      height: auto;
      position: absolute;
    }

    .home-page-bg {
      display: flex;
      justify-content: center;
      height: 100vh;
      align-items: center;
      position: relative;
    }
  </style>
</head>

<body class="sidebar-dark">
  <div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
      <div class="content-wrapper d-flex align-items-stretch auth auth-img-bg">
        <div class="row flex-grow">

        <div class="col-lg-6 d-flex align-items-center justify-content-center" style="color: white;background: #0F0D0B;">

            <div class="auth-form-transparent text-left p-3">
              @if (session('status'))
              <div class="alert alert-success" role="alert">
                {{ session('status') }}
              </div>
              @endif

              @if ($errors->has('email'))
                @if ($errors->has('status'))
                  <div class="alert alert-danger">
                      {{ $errors->first('email') }}
                  </div>
                @else
                  <div class="alert alert-success" role="alert">
                    We have e-mailed your password reset link!
                  </div>
                @endif
             

              @endif
              <div class="brand-logo">
                <img src="{{URL::to('/')}}/img/logo.png" alt="logo" style="width: 100%">

              </div>
              <h4> Forgot your password?</h4>
              <h6 class="font-weight-light">No need to worry, we will get you right back in!</h6>
              <form method="POST" id="forgotPasswordForm" action="{{ route('password.email') }}">
                {{ csrf_field() }}

                <div class="form-group">
                  <label for="exampleInputEmail">Email</label>
                  <div class="input-group">
                    <div class="input-group-prepend bg-transparent">
                      <span class="input-group-text bg-transparent border-right-0">
                        <i class="mdi mdi-account-outline text-primary"></i>
                      </span>
                    </div>
                    <input type="text" class="form-control form-control-lg border-left-0" id="email" name="email" value="{{ old('email') }}" placeholder="Email"  data-validation="length alphanumeric" data-validation-length="3-12" data-validation-error-msg="User name has to be an alphanumeric value (3-12 chars)" data-validation-has-keyup-event="true">
                    <span class="help-block">
                      <strong style="color: red;" id="email_error"></strong>
                    </span>

                  </div>
                </div>
                <div class="my-3">
                  <button class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn" type="submit">Submit</button>
                </div>

              </form>
            </div>
          </div>
          <div class="col-lg-6 home-page-bg">
          <img src="{{URL::to('/')}}/img/pana.png" class="bgimage" >

            <p class="text-white font-weight-medium text-center flex-grow align-self-end">Copyright &copy; 2022 All rights reserved.</p>
          </div>
        </div>
      </div>
      <!-- content-wrapper ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->
  <!-- base:js -->
  <script src="<?php echo URL::to('/'); ?>/assets/vendors/js/vendor.bundle.base.js"></script>
  <!-- endinject -->
  <!-- inject:js -->
  <script src="<?php echo URL::to('/'); ?>/assets/js/off-canvas.js"></script>
  <script src="<?php echo URL::to('/'); ?>/assets/js/hoverable-collapse.js"></script>
  <script src="<?php echo URL::to('/'); ?>/assets/js/template.js"></script>
  <script src="<?php echo URL::to('/'); ?>/assets/js/settings.js"></script>
  <script src="<?php echo URL::to('/'); ?>/assets/js/todolist.js"></script>
  <!-- endinject -->
</body>

<script>
  $('#forgotPasswordForm').submit(function(e){
    var email = $('#email').val();
    var regex = /^[a-z0-9]+([._-][a-z0-9]+)*@[a-z0-9.-]+\.[a-z]{2,6}$/gm;
    var cnt =0;
    $('#email_error').html("");
    if(email.trim() ==''){
      $('#email_error').html("Please enter Email");
      cnt =1;
    }

    if(email.trim() !=''){
      if(regex.test(email)){

      }else{
        $('#email_error').html("Invalid Email");
        cnt =1;
      }
    }

    if(cnt ==1){
      return false;
    }else{
      return true;
    }
  });
  </script>
</html>