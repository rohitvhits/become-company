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
    <link href="<?= URL::to('assets/css/vertical-layout-light/jquery-ui.css') ?>" rel="stylesheet">
    <!-- base:css -->

    <link rel="stylesheet" href="<?= URL::to('assets/vendors/mdi/css/materialdesignicons.min.css') ?>">

    <link rel="stylesheet" href="<?= URL::to('assets/vendors/css/vendor.bundle.base.css') ?>">

    <!-- endinject -->

    <!-- plugin css for this page -->

    <link rel="stylesheet" href="<?= URL::to('assets/vendors/jqvmap/jqvmap.min.css') ?>">

    <link rel="stylesheet" href="<?= URL::to('assets/vendors/flag-icon-css/css/flag-icon.min.css') ?>">

    <!-- End plugin css for this page -->

    <!-- inject:css -->

    <!-- <link rel="stylesheet" href="<?= URL::to('assets/css/vertical-layout-light/style.css') ?>"> -->
    <link rel="stylesheet" href="<?= URL::to('assets/css/horizontal-default-light/style.css') ?>">
    <link rel="stylesheet" href="<?= URL::to('assets/css/sweetalert2.min.css') ?>">
    <link href="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
    <!-- endinject -->
    <script src="<?= URL::to('assets/js/jquery.min.js') ?>"></script>
    <script src="<?= URL::to('assets/js/sweetalert2.min.js') ?>"></script>
    <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/jquery-confirmation/css/jquery-confirm.min.css">
    <link rel="shortcut icon" href="<?= URL::to('img/favicon.png') ?>" />

    <link href="<?= URL::to('assets/css/select2.min.css') ?>" rel="stylesheet" />

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

        li.select2-selection__choice {
            padding: 5px !important;
            font-size: 1rem !important;
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
        @media (max-width: 991px) {

    .mobileView {
        padding-top: 60px;
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
                        <a class="navbar-brand brand-logo" href="javascript:void(0)"><img
                                src="<?= URL::to('img/logo-ny.png') ?>"></a>
                        <a class="navbar-brand brand-logo-mini" href="javascript:void(0)"><img
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
                        <h5 class="card-title">Search Patient</h5>

                        <div class="col-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <form class="form-inline">
                                        <label class="sr-only" for="inlineFormInputGroupUsername2">Patient Name</label>
                                        <div class="input-group mb-2 mr-sm-2">
                                            <input type="text" class="form-control" id="name" placeholder="Search ....">
                                        </div>
                                        <button type="button" onclick="loadPatientData(1);" class="btn btn-primary btn-rounded  ml-1">Submit</button>
                                        <a href="{{url('patient-details-search')}}" class="btn btn-light btn-rounded btn-fw btn-sm ml-1"><i class="mdi mdi-reload"></i>
                                            Reset</a>
                                            <img src="{{ asset('/ajax-loader.gif') }}" class="order-listing-loader1" alt="loader" id="loaderDashboardGraph" style="display:none">
                                    </form>
                                </div>
                                <div class="table-responsive cvh-330">
                                    <span id="patient-data">

                                    </span>
                                </div>
                            </div>

                        </div>
                    </div>
                    <!-- content-wrapper ends -->
                </div>

                <!-- main-panel ends -->
            </div>
        </div>

        <!-- page-body-wrapper ends -->


</body>

</html>
<!-- container-scroller -->
<!-- base:js -->
<script src="{{ asset('/assets/vendors/js/vendor.bundle.base.js')}}"></script>

<script>
    var _PATIENT_LIST_SEARCH = "{{ url('patient-ajax-list-data')}}";

    function loadPatientData(page = 1) {
        var name = $('#name').val();
        $('.order-listing-loader1').attr('style','')
        $.ajax({
            type: "GET",
            url: _PATIENT_LIST_SEARCH,
            data: {
                'name': name,
                'page': page
            },
            success: function(response) {
                $('.order-listing-loader1').attr('style','display:none')
                $('#patient-data').html("")
                $('#patient-data').html(response);
            }
        })
    }
</script>