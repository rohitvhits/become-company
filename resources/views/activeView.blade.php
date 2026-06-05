<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Nybest Medicals</title>
    <!-- base:css -->
    <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/css/vendor.bundle.base.css">
    <!-- endinject -->
    <!-- plugin css for this page -->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/css/vertical-layout-light/style.css">
    <!-- endinject -->

    <link rel="shortcut icon" href="../../img/logo.png" />

</head>

<body class="sidebar-dark">
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-stretch auth auth-img-bg">
                <div class="row flex-grow">
                    <div class="col-lg-6 d-flex align-items-center justify-content-center">
                        <div class="auth-form-transparent text-left p-3">
                            <div class="brand-logo">
                                <img src="../img/logo.png" alt="logo" style='width:100%'>
                            </div>
                            @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                            @endif

                            <h4>{{ __('Reset your password') }}</h4>
                            <h6 class="font-weight-light">Welcome to the NyBest Medicals Client Portal!</h6>
                            <form id="submitId" class="widget-form" method="POST" action="<?php echo URL::to('/'); ?>/active-account">
                                <input type="hidden" name="id" value="<?php echo $id; ?>">
                                <div class="form-group">
                                    <label for="exampleInputPassword">Password</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend bg-transparent">
                                            <span class="input-group-text bg-transparent border-right-0">
                                                <i class="mdi mdi-lock-outline text-primary"></i>
                                            </span>
                                        </div>
                                        <input id="password" type="password" class="md-input form-control" name="password" autocomplete="new-password"> <br>

                                    </div>
                                    <span class="password_error" style="color:red">{{ $errors->edit_user->first('password') }}</span>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputPassword">Confirm Password</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend bg-transparent">
                                            <span class="input-group-text bg-transparent border-right-0">
                                                <i class="mdi mdi-lock-outline text-primary"></i>
                                            </span>
                                        </div>
                                        <input type="password" class="md-input form-control" name="password_confirmation" autocomplete="new-password" id="cpassword">
                                        <br>

                                    </div>
                                    <span class="cpassword_error" style="color:red"></span>
                                </div>
                                <div class="my-3">
                                    <button class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn" type="submit">Reset Password</button>
                                </div>

                            </form>
                        </div>
                    </div>
                    <div class="col-lg-6 login-half-bg d-flex flex-row">
                        <p class="text-white font-weight-medium text-center flex-grow align-self-end">Copyright &copy;
                            2022 All rights reserved.</p>
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

    <script>
        $('#submitId').submit(function(e) {
            var password = $('#password').val();
            var cpassword = $('#cpassword').val();
            var passwordExpression = /^(?=[^a-z]*[a-z])(?=[^A-Z]*[A-Z])(?=\D*\d)[a-zA-Z\d!@#$%&*]+$/;
            var cnt = 0;
            if (password.trim() == '') {
                $('.password_error').html(" Please enter password");
                cnt = 1;
            } else {
                $.ajax({
                    async: false,
                    global: false,
                    url: "{{ URL::to('/')}}/check-passwords",
                    type: "POST",
                    data: {
                        password: password,
                        id: "{{request('id')}}",
                        _token: "{{ csrf_token()}}"
                    },
                    success: function(response) {
                        if (response == 1) {
                            $('.password_error').html("Password Already Used Please Try Another Password");
                            cnt = 1;
                        }

                    }
                });
            }
            if (cpassword.trim() == '') {
                $('.cpassword_error').html(" Please enter confirm password");
                cnt = 1;
            }

            if (password.trim() != '') {
                if (password.length < 8) {
                    $('.password_error').html('Password atleast eight digit allowed');
                    cnt = 1;
                } else {
                    if (passwordExpression.test(password)) {

                    } else {
                        $('.password_error').html(
                            'Password must contain at least 8 characters including 1 uppercase, 1 lowercase, a number and symbol'
                        );
                        cnt = 1;
                    }
                }

            }

            if (password.trim() != '' && cpassword.trim() != '') {
                if (password.trim() != cpassword.trim()) {
                    $('.cpassword_error').html(" Password and confirm password does not match.");
                    cnt = 1;
                }

            }

            if (cnt == 1) {
                return false;
            } else {
                return true;
            }
        });
    </script>
</body>

</html>