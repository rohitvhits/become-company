<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
 <title>NY BEST MEDICAL</title>
  <!-- base:css -->
  <link rel="stylesheet" href="<?php echo URL::to('/');?>/assets/vendors/mdi/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="<?php echo URL::to('/');?>/assets/vendors/css/vendor.bundle.base.css">
  <!-- endinject -->
  <!-- plugin css for this page -->
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link rel="stylesheet" href="<?php echo URL::to('/');?>/assets/css/vertical-layout-light/style.css">
  <!-- endinject -->
  
  <link rel="shortcut icon" href="img/favicon.png" />
<style>

  .bgimage{
    width: 500px;
    height: auto;
    position: absolute; 
  }

  .home-page-bg{
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
              @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
					@if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif
			  <div class="brand-logo">
              <img src="{{URL::to('/')}}/img/logo.png" alt="logo" style="width: 100%">
              <!-- <img src="https://www.nybestmedical.com/wp-content/uploads/2020/12/Logo-Original-1.png" alt="logo" style="width: 100%"> -->
              
              </div>
              <h4>Welcome!</h4>
              <h6 class="font-weight-light">Happy to see you again!!</h6>
              <form class="pt-3" method="POST" id="loginform" action="{{ route('login') }}" onsubmit="return validation()">
			                     {{ csrf_field() }}

                <div class="form-group">
                  <label for="exampleInputEmail">Email</label>
                  <div class="input-group">
                    <div class="input-group-prepend bg-transparent">
                      <span class="input-group-text bg-transparent border-right-0">
                        <i class="mdi mdi-account-outline text-primary"></i>
                      </span>
                    </div>
                    <input type="text" class="form-control form-control-lg border-left-0" id="email" name="email" value="{{ old('email') }}"  placeholder="Email" data-validation="length alphanumeric" data-validation-length="3-12" data-validation-error-msg="User name has to be an alphanumeric value (3-12 chars)" data-validation-has-keyup-event="true">
                    <span id="email-error" class="help-block">
                    </span>
					@if ($errors->has('email'))
                    <span class="help-block">
                      <strong style="color: red;" id="server-email-err">{{ $errors->first('email') }}</strong>
                    </span>
	                    @endif

				  </div>
                </div>
                <div class="form-group">
                  <label for="exampleInputPassword">Password</label>
                  <div class="input-group">
                    <div class="input-group-prepend bg-transparent">
                      <span class="input-group-text bg-transparent border-right-0">
                        <i class="mdi mdi-lock-outline text-primary"></i>
                      </span>
                    </div>
                    <input type="password" class="form-control form-control-lg border-left-0" class="form-control form-control-lg border-left-0" placeholder="Password" name="password" id="password" data-validation="strength" data-validation-strength="2" data-validation-has-keyup-event="true">  
                    <span id="password-error" class="help-block">
                    </span>                      
					@if ($errors->has('password'))

                    <span class="help-block">
                      <strong style="color: red;" id="server-password-err">{{ $errors->first('password') }}</strong>
                    </span>
                    @endif  
				  </div>
                </div>
                <div class="my-2 d-flex justify-content-between align-items-center">
                  <div class="form-check">
                    <label class="form-check-label text-muted">
                      <input type="checkbox" class="form-check-input">
                      Keep me signed in
                    </label>
                  </div>
                  <a ui-sref="access.forgot-password" href="{{ route('password.request') }}" class="auth-link text-white">Forgot password?</a>
                </div>
                <div class="my-3">
					        <button class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn"  type="submit">LOGIN</button>
                  <a target="_blank" href="{{ url('/term-condition')}}" class="text-center auth-link text-white">Term And Condition</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                  <a target="_blank" href="{{ url('/privacy-policy')}}" class="text-center auth-link text-white float-right">Privacy Policy</a>
                </div>
                
              </form>
            </div>
          </div>
          <div class="col-lg-6 home-page-bg">
          <img src="{{URL::to('/')}}/img/pana.png" class="bgimage" >


            <p class="text-white font-weight-medium text-center flex-grow align-self-end">Copyright &copy; {{date('Y')}}  All rights reserved.</p>

          </div>
        </div>
      </div>
      <!-- content-wrapper ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->
  <!-- base:js -->
  <script src="<?php echo URL::to('/');?>/assets/vendors/js/vendor.bundle.base.js"></script>
  <!-- endinject -->
  <!-- inject:js -->
  <script src="<?php echo URL::to('/');?>/assets/js/off-canvas.js"></script>
  <script src="<?php echo URL::to('/');?>/assets/js/hoverable-collapse.js"></script>
  <script src="<?php echo URL::to('/');?>/assets/js/template.js"></script>
  <script src="<?php echo URL::to('/');?>/assets/js/settings.js"></script>
  <script src="<?php echo URL::to('/');?>/assets/js/todolist.js"></script>
  <!-- endinject -->
   <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-9EPHQQ3SF5"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-9EPHQQ3SF5');

  function validation(){
    $('#email-error').html('');
    $('#password-error').html('');
    $('#server-email-err').html('');
    $('#server-password-err').html('');
    email = $('#email').val();
    password = $('#password').val();
    if(email == ''){
        $('#email-error').html('<strong style="color: red;">Please enter email.</strong>');
        event.preventDefault();
    }

    if(password == ''){
        $('#password-error').html('<strong style="color: red;">Please enter password.</strong>');
        event.preventDefault();
    }
  }
</script>
</body>

</html>
