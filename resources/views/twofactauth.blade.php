<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Two Fact Auth</title>
    <link href="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
    <!-- base:css -->
    <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/jquery-confirmation/css/jquery-confirm.min.css">
   
    <!-- endinject -->
    <!-- plugin css for this page -->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/css/vertical-layout-light/style.css">
    <!-- endinject -->

    <link rel="shortcut icon" href="img/favicon.png" />
    <style>
        .hide{
            display:none
        }
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
        @-webkit-keyframes blinker {
        from {opacity: 1.0;}
        to {opacity: 0.0;}
        }
        .blink{
            text-decoration: blink;
            -webkit-animation-name: blinker;
            -webkit-animation-duration: 0.6s;
            -webkit-animation-iteration-count:infinite;
            -webkit-animation-timing-function:ease-in-out;
            -webkit-animation-direction: alternate;
        }
        .toast.toast-error{
            background-color:red
        }
        .toast.toast-success{
            background-color:green
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

                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            @endif
                            @if (session('error'))

                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            @endif
                            <div class="brand-logo">
                                <img src="{{URL::to('/')}}/img/logo.png" alt="logo" style="width: 100%">
                            </div>
                            <h4>Help us protect your account</h4>
                            <h6 class="font-weight-light">To continue, check your email and enter the code we just sent you.</h6>
                            <form class="pt-3" method="POST" id="loginform" action="{{ route('verifyotp') }}">
                                {{ csrf_field() }}

                                <div class="form-group">
                                    <!-- <label for="exampleInputEmail">Otp</label> -->
                                    <div class="input-group">
                                        <div class="input-group-prepend bg-transparent">
                                            <span class="input-group-text bg-transparent border-right-0">
                                                <i class="mdi mdi-account-outline text-primary"></i>
                                            </span>
                                        </div>
                                        <input autocomplete="off" type="text" class="form-control form-control-lg border-left-0" id="otp" name="otp" value="{{ old('otp') }}" placeholder="Enter your code">
                                        <input type="hidden" name="id" value="{{request('id')}}">
                                        <span class="help-block">
                                            <strong style="color: red;" id="otp_error">{{ $errors->first('otp') }}</strong>
                                        </span>

                                    </div>
                                    <span id="otpnew_error" style="color:red"></span>

                                </div>
                                <div class="my-2 d-flex justify-content-between align-items-center">
                                    <div class="form-check">
                                    <div id="countdown-timer" data-end-time="{{ $otp_expired_time }}"></div>
                                    </div>
                                    <a onclick="resendOtp()" href="javascript:void(0)" class="auth-link text-white @if($otp_expired_time < now()) @else hide @endif" >Resend OTP?</a>
                                </div>


                                <div class="my-3">
                                    <button class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn" id="submitbtn" type="button">Verify</button>
                                </div>

                            </form>
                        </div>
                    </div>
                    <div class="col-lg-6 home-page-bg">
                        <img src="{{URL::to('/')}}/img/pana.png" class="bgimage">
                        <p class="text-white font-weight-medium text-center flex-grow align-self-end">Copyright &copy; 2020 All rights reserved.</p>
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
    <script src="{{URL::to('/')}}/assets/js/jquery.min.js"></script>
    <script src="<?= URL::to('assets/jquery-confirmation/js/jquery-confirm.min.js') ?>"></script>
    <script src="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.js"></script>
    <!-- new validation -->
    <script>
        toastr.options.closeButton = true;
  toastr.options.tapToDismiss = false;
  toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": false,
    "progressBar": false,
    "positionClass": "toast-top-right",
    "preventDuplicates": false,
    "onclick": null,
    "showDuration": "1000",
    "hideDuration": "500",
    "timeOut": "3000",
    "extendedTimeOut": 0,
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut",
    "tapToDismiss": false
  };
        $('#submitbtn').click(function() {
            var otp = $('#otp').val();

            var cnt = 0;
            var f = 0;

            $('#emailnew_error').html("");


            if (otp.trim() == '') {
                $('#otp_error').html("Please enter Code");
                cnt = 1;
                f++;
                if (f == 1) {
                    $('#otp').focus();
                }
            }else{
                $.ajax({
                    async:false,
                    global:false,
                    url: '{{ url("auth/check-otp-valid")}}',
                    type: "POST",
                    data: {
                        '_token':"{{ csrf_token()}}",
                        'id':"{{$id}}",
                        'otp':otp.trim()
                    },
                    success: function (response) {
                        cnt =0;
                    },
                    error:function(jqr){
                    
                        toastr.error(jqr.responseJSON.error_msg)
                        cnt =1;
                    }
                })
            }
       
            if (cnt == 1) {
                return false;
            } else {
                $("#loginform").submit();
            }

        })
        
        let endTime;
        endTime = new Date(document.getElementById('countdown-timer').dataset.endTime).getTime();
        function counterDownTimer(){
      
            if(!isNaN(endTime)){
                const now = getUSTimezoneNow();
            
                const distance = endTime - now;

                const days ='00';
                let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                
                if(hours < 10){
                    hours = '0'+hours;
                }
                let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                if(minutes <10){
                    minutes = '0'+minutes;
                }
                let seconds = Math.floor((distance % (1000 * 60)) / 1000);
                if(seconds <10){
                    seconds = '0'+seconds;
                }
                const timerEl =  document.getElementById("countdown-timer");
                timerEl.innerHTML =  minutes + ":" + seconds;
            
                if(minutes ==00 && seconds <=10){
                    $('#countdown-timer').addClass('blink')
                 
                }
           
                if (distance < 0) {
                    clearInterval(x);
                    timerEl.innerHTML = "";
                    $('#countdown-timer').removeClass('blink');
                    $('.auth-link').removeClass('hide');
                    
                }
            }
            
        }

        const x = setInterval(function() {
            counterDownTimer();
        }, 1000);

        function getUSTimezoneNow(timeZone = 'America/New_York') {
            const options = {
                timeZone,
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            };
            const formatter = new Intl.DateTimeFormat('en-US', options);
            const parts = formatter.formatToParts(new Date());

            const dateTime = {};
            for (const part of parts) {
                if (part.type !== 'literal') {
                    dateTime[part.type] = part.value;
                }
            }

            return new Date(
                `${dateTime.year}-${dateTime.month}-${dateTime.day}T${dateTime.hour}:${dateTime.minute}:${dateTime.second}`
            ).getTime();
        }

        function resendOtp(){
            $.confirm({
                    title: 'Resend OTP',
                    columnClass: "col-md-6",
                    content: 'Are you sure you want to resend the OTP?',
                    buttons: {
                        submit:{
                            text: 'Send',
                            btnClass: 'btn-primary',
                            action: function() {
                                $.ajax({
                                    
                                    url: '{{ url("auth/resend-otp")}}',
                                    type: "POST",
                                    data: {
                                        '_token':"{{ csrf_token()}}",
                                        'id':"{{$id}}"
                                    },
                                    success: function (response) {
                                       $('#countdown-timer').attr('data-end-time',response.data.otp_expired_time);
                                       endTime = "";
                                        endTime = new Date(response.data.otp_expired_time).getTime();
                                       counterDownTimer();
                                        toastr.success(response.error_msg);
                                        $('.auth-link').addClass('hide');
                                        
                                    },
                                    error: function (xhr, status, error) {
                                        toastr.error(xhr.responseJSON.error_msg);
                                    }
                                });
                            }
                        },
                        cancel:function(){
                           
                        }
                    }

            })
        }
    </script>
</body>

</html>