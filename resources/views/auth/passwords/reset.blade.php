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
  
  
  <link rel="shortcut icon" href="/img/favicon.png" />
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
              <div class="brand-logo">
                <img src="../../img/logo.png" alt="logo">
              </div>
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
              <h4>{{ __('Reset Password') }}</h4>
              <h6 class="font-weight-light">Happy to see you again!</h6>
              <form class="widget-form"  method="POST" action="{{ route('password.request') }}">
			                  {{ csrf_field() }}
		<input type="hidden" name="token" value="{{ $token }}">
				<div class="form-group">
                  <label for="exampleInputPassword">Email</label>
                  <div class="input-group">
                    <div class="input-group-prepend bg-transparent">
                      <span class="input-group-text bg-transparent border-right-0">
                        <i class="mdi mdi-lock-outline text-primary"></i>
                      </span>
                    </div>
                    <input id="email" type="text" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus readonly>                        
					@if ($errors->has('email'))
				<span class="invalid-feedback" role="alert">
					<strong>{{ $errors->first('email') }}</strong>
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
                    <input id="password" type="password" class="md-input form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">                        
					@if ($errors->has('password'))
				<span class="invalid-feedback" role="alert">
					<strong>{{ $errors->first('password') }}</strong>
				</span>
			@endif  
				  </div>
                </div>
				<div class="form-group">
                  <label for="exampleInputPassword">Confirm Password</label>
                  <div class="input-group">
                    <div class="input-group-prepend bg-transparent">
                      <span class="input-group-text bg-transparent border-right-0">
                        <i class="mdi mdi-lock-outline text-primary"></i>
                      </span>
                    </div>
                    <input id="password" type="password" class="md-input form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation" required autocomplete="new-password">                        
					@if ($errors->has('password_confirmation'))


										<span class="help-block" style="color:red;">


											<strong>{{ $errors->first('password_confirmation') }}</strong>


										</span>


									@endif
				  </div>
                </div>
                <div class="my-3">
					<button class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn"  type="submit">Reset Password</button>
                </div>
                
              </form>
            </div>
          </div>
          <div class="col-lg-6 home-page-bg">
          <img src="{{URL::to('/')}}/img/pana.png" class="bgimage" >

            <p class="text-white font-weight-medium text-center flex-grow align-self-end">Copyright &copy; 2023  All rights reserved.</p>
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
</body>

</html>
