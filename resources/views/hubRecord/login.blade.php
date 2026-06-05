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

    <link rel="shortcut icon" href="img/favicon.png" />
    <style>
      
    </style>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light btn-grad3">

    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow-lg p-4 btn-grad " style="width: 400px;">
            <div class="brand-logo">
              
                <!-- <img src="https://www.nybestmedical.com/wp-content/uploads/2020/12/Logo-Original-1.png" alt="logo" style="width: 100%"> -->

            </div>

            <!-- Show error message -->
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

            <h4>Welcome!</h4>
            <h6 class="font-weight-light">Happy to see you again!!</h6>
            <form class="pt-3" method="POST" id="loginform" action="{{ route('hub-authenticate') }}"
                onsubmit="return validation()">
                {{ csrf_field() }}

                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label">Email or Mobile or Phone Number</label>
                    <input type="text" class="form-control form-control-lg" placeholder="Email or Mobile or Phone Number" id="dep_phone"
                         name="phone" value="<?php echo old('phone'); ?>"
                        >
                    <span id="dep_phone_error" class="help-block"></span>
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label for="password" class="form-label">Last 4 Digits of SSN</label>
                    <input type="text" class="form-control form-control-lg" id="dep_ssn" name="ssn"
                        maxlength="11" value="XXX-XX-" pattern="XXX-XX-[0-9]{4}"
                        title="Enter last 4 digits of your SSN">
                    <span id="dep_ssn_error" class="help-block"></span>
                    </span>
                    @if ($errors->has('ssn'))
                        <span class="help-block">
                            <strong style="color: red;" id="server-ssn-err">{{ $errors->first('ssn') }}</strong>
                        </span>
                    @endif
                </div>

                <!-- Remember Me -->


                <!-- Login Button -->
                <button class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn"
                    type="submit">Continue</button>

                <!-- Forgot password link -->
                <div class="text-center mt-3">
                    {{-- <a href="{{ route('password.request') }}">Forgot your password?</a> --}}
                </div>
            </form>
        </div>
    </div>

</body>


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
<script src="{{ asset('assets/vendors/inputmask/jquery.inputmask.bundle.js') }}"></script>
<!-- endinject -->
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-9EPHQQ3SF5"></script>
<script>
    $(":input").inputmask();

    document.addEventListener('DOMContentLoaded', function() {
        const ssnField = document.getElementById('dep_ssn');

        // Always keep first part fixed
        ssnField.addEventListener('input', function(e) {
            let value = ssnField.value;

            // Force prefix
            if (!value.startsWith("XXX-XX-")) {
                ssnField.value = "XXX-XX-";
            }

            // Keep only last 4 digits numeric
            let last4 = value.replace("XXX-XX-", "").replace(/\D/g, "").substring(0, 4);
            ssnField.value = "XXX-XX-" + last4;
        });

        // Prevent cursor from going before last 4 digits
        ssnField.addEventListener('keydown', function(e) {
            if (ssnField.selectionStart < 7 && e.key !== "Tab") {
                e.preventDefault();
            }
        });
    });

    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', 'G-9EPHQQ3SF5');

    function validation() {
        $('#dep_phone_error').html('');
        $('#dep_ssn_error').html('');

        var phone = $('#dep_phone').val();
        var ssn = $('#dep_ssn').val();
        if (phone == '') {
            $('#dep_phone_error').html('<strong style="color: red;">Please enter email or mobile or phone number.</strong>');
            event.preventDefault();
        }

        if (ssn == '') {
            $('#dep_ssn_error').html(
            '<strong style="color: red;">Please enter the last 4 digits of your SSN.</strong>');
            event.preventDefault();
        }
    }
</script>
</body>

</html>
