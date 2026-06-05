<!DOCTYPE html>
<html lang="en">

<head>

    <!-- Required meta tags -->

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>NY BEST MEDICAL</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.7.95/css/materialdesignicons.css">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.7.95/css/materialdesignicons.css.map">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.7.95/css/materialdesignicons.min.css">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.7.95/css/materialdesignicons.min.css.map">

    <link rel="stylesheet" href="{{ asset('assets/fonts/materialdesignicons-webfont.eot')}}">

    <link rel="stylesheet" href="{{ asset('assets/fonts/materialdesignicons-webfont.ttf')}}">

    <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/fonts/materialdesignicons-webfont.woff">
    <link href="{{ asset('/assets/css/vertical-layout-light/jquery-ui.css')}}" rel="stylesheet">
    <!-- base:css -->

    <link rel="stylesheet" href="{{ asset('/assets/vendors/mdi/css/materialdesignicons.min.css')}}">

    <link rel="stylesheet" href="{{ asset('/assets/vendors/css/vendor.bundle.base.css')}}">

    <!-- endinject -->

    <!-- plugin css for this page -->

    <link rel="stylesheet" href="{{ asset('/assets/vendors/jqvmap/jqvmap.min.css')}}">

    <link rel="stylesheet" href="{{ asset('/assets/vendors/flag-icon-css/css/flag-icon.min.css')}}">

    <link rel="stylesheet" href="{{ asset('/assets/css/horizontal-default-light/style.css')}}">
    <link rel="stylesheet" href="{{ asset('/assets/css/sweetalert2.min.css')}}">
    <link href="{{ asset('/assets/css/toastr/toastr.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- endinject -->
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/sweetalert2.min.js')}}"></script>
    <link rel="stylesheet" href="{{ asset('assets/jquery-confirmation/css/jquery-confirm.min.css')}}">
    <link rel="shortcut icon" href="<?= URL::to('img/favicon.png') ?>" />

    <link href="{{ asset('assets/vendors/select2/select2.min.css')}}" rel="stylesheet" />

    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
    <link href="<?= URL::to('assets/css/jquery-confirm.min.css') ?>" rel="stylesheet" />

    <style>
        .compact-view .form-control {
            padding: 0 !important;
            height: 24px;
        }

        .compact-view td {
            padding: 5px 10px;
        }

        .horizontal-menu .top-navbar {
            font-weight: 400;
            background: #1e1e2f;
            border-bottom: 1px solid #030303;
        }

        .horizontal-menu .top-navbar .navbar-menu-wrapper {
            color: #b1b1b5;
        }

        .horizontal-menu .bottom-navbar .page-navigation>.nav-item.active>.nav-link .menu-title,
        .horizontal-menu .bottom-navbar .page-navigation>.nav-item.active>.nav-link i,
        .horizontal-menu .bottom-navbar .page-navigation>.nav-item:hover>.nav-link i,
        .horizontal-menu .bottom-navbar .page-navigation>.nav-item:hover>.nav-link .menu-title {
            color: #97C229 !important;
        }

        .horizontal-menu .bottom-navbar {
            background: #FFF;
        }

        .horizontal-menu .bottom-navbar .page-navigation>.nav-item>.nav-link {
            color: #686868;
        }

        .agency-logo {
            display: flex;
            align-items: center;
            padding: 10px 0;
        }

        .agency-logo a {
            padding: 0 10px !important;
        }

        .text-danger {
            color: red !important;
        }

        .hide {
            display: none;
        }

        .select2-container--default .select2-selection--single {
            height: 39px;
        }

        @media (max-width: 991px) {

            .mobileView {
                padding-top: 60px;
            }

            .select2.select2-container {
                width: 100% !important;
            }
        }
    </style>
</head>

<body class="sidebar-toggle-display sidebar-hidden">



    <!--Header-part-->

    <div class="container-scroller">

        <div class="horizontal-menu">
            <nav class="navbar top-navbar col-lg-12 col-12 p-0">
                <!-- <div class="container"></div> -->
                <div class="container-fluid">
                    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
                        <a class="navbar-brand brand-logo" href="{{ URL::to('') }}/home"><img
                                src="<?= URL::to('img/logo-ny.png') ?>"></a>
                        <a class="navbar-brand brand-logo-mini" href="{{ URL::to('') }}/home"><img
                                src="<?= URL::to('img/favicon.png') ?>"></a>
                    </div>
                    <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">

                        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
                            data-toggle="horizontal-menu-toggle">
                            <span class="mdi mdi-menu"></span>
                        </button>
                    </div>
                </div>
            </nav>
        </div>
        <!-- partial -->
        <div class="mobileView">
            <div class="container-fluid page-body-wrapper">
                <!-- partial -->
                <div class="">
                    <div class="content-wrapper">
                   

                        <div class="col-12 grid-margin stretch-card">
                        <p> Thank you. Your Demographic details successfully updated.
                        </p>

                        </div>

                    </div>
                </div>
                <!-- content-wrapper ends -->
            </div>
            <!-- main-panel ends -->
        </div>
    </div>


</body>

</html>
<script src="{{ asset('assets/vendors/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/select2.js') }}"></script>


<script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
<script>
    function getDownloadPdf() {
        $.ajax({
            url: "{{ url('download-user-pdf')}}", // Upload Script
            type: "get",
            data:{
                'patient_id':'{{ $patient_id}}'
            },
            success: function(data) {

            }
        });

    }
    setTimeout(()=>{
      window.location.href="{{ url('save-dows')}}?patient_id={{ $patient_id}}";
    },200);
</script>