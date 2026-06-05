<!DOCTYPE html>

<html lang="en">





<!-- Mirrored from www.urbanui.com/yoraui/template/demo/vertical-default-light/pages/samples/login.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 13 Dec 2019 05:53:31 GMT -->

<head>

  <!-- Required meta tags -->

  <meta charset="utf-8">

  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <title>Exmedc</title>

  <!-- base:css -->

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.7.95/css/materialdesignicons.css">

  <link rel="stylesheet" href="<?= URL::to('assets/vendors/css/vendor.bundle.base.css')?>">

  <!-- endinject -->

  <!-- plugin css for this page -->

  <!-- End plugin css for this page -->

  <!-- inject:css -->

  <link rel="stylesheet" href="<?= URL::to('assets/css/vertical-layout-light/style.css')?>">

  <!-- endinject -->

  <link rel="shortcut icon" href="img/logo.png" />

</head>



<body>

   <div class="container-scroller">

    <div class="container-fluid page-body-wrapper full-page-wrapper">

      <div class="content-wrapper d-flex align-items-center auth px-0 login-form-bg">

        <div class="row w-100 mx-0">

          <div class="col-lg-4 mx-auto">

            <div class="auth-form-light text-left py-5 px-4 px-sm-5 border login-border">

              <center>

                

              <div class="brand-logo">

                <img src="img/logo.png" alt="logo">

              </div>

              </center>

              

              <center><h4>Welcome back!</h4>

              <h6 class="font-weight-light">Happy to see you again!</h6></center>

              <form class="pt-3" method="POST" id="loginform" action="{{ route('login') }}">

                   {{ csrf_field() }}

                <div class="form-group">

                  <label for="exampleInputEmail">Email</label>

                  <div class="input-group">

                    <div class="input-group-prepend bg-transparent">

                      <span class="input-group-text bg-transparent border-right-0">

                        <i class="mdi mdi-account-outline text-primary"></i>

                      </span>

                    </div>

                    <input type="email" class="form-control form-control-lg border-left-0" id="email" name="email" value="{{ old('email') }}"  placeholder="Email"  required="" data-validation="length alphanumeric" data-validation-length="3-12" data-validation-error-msg="User name has to be an alphanumeric value (3-12 chars)" data-validation-has-keyup-event="true">

                    @if ($errors->has('email'))



                    <span class="help-block">



                      <strong style="color: red;">{{ $errors->first('email') }}</strong>



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

                    <input type="password" class="form-control form-control-lg border-left-0" placeholder="Password" name="password"  data-validation="strength" data-validation-strength="2" data-validation-has-keyup-event="true">

                                       

                  </div>

                    @if ($errors->has('password'))



                    <span class="help-block">



                      <strong style="color: red;">{{ $errors->first('password') }}</strong>



                    </span>



                    @endif  

                </div>

                <!-- <div class="my-2 d-flex justify-content-between align-items-center">

                  <div class="form-check">

                    <label class="form-check-label text-muted">

                      <input type="checkbox" class="form-check-input">

                      Keep me signed in

                    </label>

                  </div>

                  <a href="#" class="auth-link text-black">Forgot password?</a>

                </div> -->

                <div class="mt-3">

                  <button class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn"  type="submit">LOGIN</button>

                </div>

              <!--   <div class="my-2 d-flex justify-content-between align-items-center">

                  <div class="form-check">

                    <label class="form-check-label text-muted">

                      <input type="checkbox" class="form-check-input">

                      Keep me signed in

                    </label>

                  </div>

                  <a href="#" class="auth-link text-black">Forgot password?</a>

                </div> -->

                <!-- <div class="mb-2 d-flex">

                  <button type="button" class="btn btn-facebook auth-form-btn flex-grow mr-1">

                    <i class="mdi mdi-facebook mr-2"></i>Facebook

                  </button>

                  <button type="button" class="btn btn-google auth-form-btn flex-grow ml-1">

                    <i class="mdi mdi-google mr-2"></i>Google

                  </button>

                </div> -->

             <!--    <div class="text-center mt-4 font-weight-light">

                  Don't have an account? <a href="register-2.html" class="text-primary">Create</a>

                </div> -->

              </form>

            </div>

          </div>

        </div>

      </div>

      <!-- content-wrapper ends -->

    </div>

    <!-- page-body-wrapper ends -->

  </div>

  <!-- container-scroller -->

  <!-- base:js -->

  <script src="<?= URL::to('assets/vendors/js/vendor.bundle.base.js')?>"></script>

  <!-- endinject -->

  <!-- inject:js -->

  <script src="<?= URL::to('assets/js/off-canvas.js')?>"></script>

  <script src="<?= URL::to('assets/js/hoverable-collapse.js')?>"></script>

  <script src="<?= URL::to('assets/js/template.js')?>"></script>

  <script src="<?= URL::to('assets/js/settings.js')?>"></script>

  <script src="<?= URL::to('assets/js/todolist.js')?>"></script>

  <!-- endinject -->

</body>





<!-- Mirrored from www.urbanui.com/yoraui/template/demo/vertical-default-light/pages/samples/login.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 13 Dec 2019 05:53:31 GMT -->

</html>

