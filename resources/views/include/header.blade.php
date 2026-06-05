<!DOCTYPE html>

<html lang="en">



<head>

    <!-- Required meta tags -->

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>NY BEST MEDICAL</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.7.95/css/materialdesignicons.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.7.95/css/materialdesignicons.css.map">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.7.95/css/materialdesignicons.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.7.95/css/materialdesignicons.min.css.map">

    <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/fonts/materialdesignicons-webfont.eot">

    <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/fonts/materialdesignicons-webfont.ttf">

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
    <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/modulejs/css/header.css">
    <link rel="shortcut icon" href="<?= URL::to('img/favicon.png') ?>" />



</head>

<?php

use App\Helpers\Common;
use App\Helpers\Utility;
$agencyObj = Common::getAgencyDetails();
?>
<div id="location-right-sidebar" class="loc-settings-panel">
    <i class="settings-close mdi mdi-close"></i>
    <ul class="nav nav-tabs" id="setting-panel">
        <li class="nav-item">
            <a class="nav-link active" id="todo-tab" data-toggle="tab" href="#todo-section" role="tab"
                aria-controls="todo-section" aria-expanded="true">Location</a>
        </li>

    </ul>
    <div class="tab-content" id="setting-content">
        <div class="tab-pane fade show active scroll-wrapper ps ps--active-y" id="todo-section" role="tabpanel"
            aria-labelledby="todo-section">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for=""><b>Location Name</b> </label>
                            <input id="right-panel-ship-address" class="form-control pac-target-input" name="right-panel-ship-address" required=""
                                autocomplete="off" placeholder="Enter a location">

                        </div>
                    </div>
                    <div class="col-md-4 mt-2">

                        <a href="javascript::void(0);" class="btn btn-light mt-4 btn-sm reset-location-search">Reset</a> <img src="{{ asset('/ajax-loader.gif')}}" class="order-listing-loader1new mt-4" alt="loader" style="display:none;">
                    </div>
                </div>
            </div>
            <div class="add-items d-flex px-3 mb-0">
                <div class="row">

                </div>

                <div class="col-md-6" style="margin-top: 31px;">


                </div>
            </div>
            &nbsp;&nbsp;&nbsp;
            <div class="list-wrapper px-3" style="max-height:500px; overflow-y:auto">
            </div>
        </div>
    </div>
</div>

<body class="sidebar-toggle-display sidebar-hidden">



    <!--Header-part-->

    <div class="container-scroller">

        <div class="horizontal-menu">
            <nav class="navbar top-navbar col-lg-12 col-12 p-0">

                <div class="container-fluid">
                    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
                        <a class="navbar-brand brand-logo" href="{{ URL::to('') }}/home"><img src="<?= URL::to('img/logo-ny.png') ?>">
                        </a>
                        <a class="navbar-brand brand-logo-mini" href="{{ URL::to('') }}/home"><img src="<?= URL::to('img/favicon.png') ?>"></a>
                    </div>
                    <div class="navbar-menu-wrapper d-flex align-items-center">
                        <div class="col-md-12 row ">
                            <div class="col-md-10" style="margin-top: 5px;margin-right: -25px;">
                                <ul class="navbar-nav mr-lg-2">

                                    <li class="nav-item nav-search d-none d-lg-block">
                                        <form class="navbar-form navbar-left" method="GET" action="<?php echo URL::to('/'); ?>/search" role="search" id="searchAlls">
                                            <div class="input-group">

                                                <div class="input-group-prepend">

                                                    <span class="input-group-text" id="search" onclick="$('#searchAlls').submit();">

                                                        <i class="mdi mdi-magnify"></i>

                                                    </span>

                                                </div>

                                                <input type="text" class="form-control" placeholder="Search for anything..." name="search_global" id="vsearch_id" value="<?php if (isset($search) && $search != '') {
                                                                                                                                                                                echo $search;
                                                                                                                                                                            } ?>" aria-label="search" aria-describedby="search">

                                            </div>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-2">
                                <ul class="navbar-nav navbar-nav-right" style="justify-content:space-around">
                                    @can('analytics-dashboard')
                                    <li class="nav-item dropdown">
                                        <a class="nav-link count-indicator dropdown-toggle d-flex align-items-center justify-content-center" id="notificationDropdown" href="#" data-toggle="dropdown" aria-expanded="true">
                                            <i class="fa fa-bar-chart-o mx-0"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="notificationDropdown" style="border-radius: 10px !important;">
                                            <a class="dropdown-item" href="{{url('analytics-dashboard')}}"><i class="mdi mdi-chart-areaspline"></i> Dashbaord</a>
                                        </div>
                                    </li>
                                    @endcan
                                    @can('location-search-list')
                                    <li class="nav-item dropdown ">
                                        <a class="nav-link count-indicator dropdown-toggle d-flex align-items-center justify-content-center" onclick="openLocation()" target="_blank">
                                            <i class="mdi mdi-directions mx-0"></i>
                                        </a>
                                    </li>
                                    @endcan
                                    <li class="nav-item dropdown notifications">

                                        <a class="nav-link count-indicator dropdown-toggle d-flex align-items-center justify-content-center" id="notificationDropdown" href="#" onclick="getUnreadNotification();" data-toggle="dropdown" aria-expanded="true">
                                            <i class="mdi mdi-bell-outline mx-0"></i>
                                            <span class="count" id="count_notification" style="display:none"></span>

                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list hiten" aria-labelledby="notificationDropdown" style="border-radius: 10px !important;">
                                            <h4 class="mb-0 font-weight-normal float-left dropdown-header"><b>Notifications</b></h4>

                                            <div class="noti-listing-loader1" style="display:flex;">
                                                <i class="fa fa-spinner fa-spin"></i>
                                            </div>
                                            <div class="notification_div" style="width:393px;"></div>
                                            <hr />
                                            <a class="view_all" target="_blank" href="{{url('user-notification')}}">View All Notification</a>
                                        </div>
                                    </li>
                                @if(Auth()->user()->agency_fk == "")
                                    <!-- Announcement Dropdown -->
                                    <li class="nav-item dropdown announcements">
                                        <a class="nav-link count-indicator dropdown-toggle d-flex align-items-center justify-content-center"
                                           id="announcementDropdown" href="#" onclick="loadAnnouncementDropdown();"
                                           data-toggle="dropdown" aria-expanded="true">
                                            <i class="mdi mdi-bullhorn-outline mx-0"></i>
                                            <span class="count" id="announcement_count" style="display:none"></span>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list hiten"
                                             aria-labelledby="announcementDropdown" style="border-radius: 10px !important;">
                                            <h4 class="mb-0 font-weight-normal float-left dropdown-header"><b>Announcements</b></h4>

                                            <div class="announcement-listing-loader" style="display:flex;">
                                                <i class="fa fa-spinner fa-spin"></i>
                                            </div>
                                            <div class="announcement_dropdown_div" style="width:393px;max-height:400px;overflow-y:auto;"></div>
                                            <hr />
                                            <a class="view_all" target="_blank" href="{{url('announcement-list')}}">View All Announcements</a>
                                        </div>
                                    </li>
                                @endif

                                    <li class="nav-item nav-profile dropdown">
                                        <a class="nav-link" href="#" data-toggle="dropdown" id="profileDropdown" aria-expanded="true">
                                            @if(auth()->user()->profile_img !="")

                                            <img src="{{ url('user-profile-image')}}" alt="profile">
                                            @else
                                            <img src="{{ asset('assets/images/faces/face5.jpg')}}" alt="profile">
                                            @endif
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
                                            <a class="dropdown-item" href="<?php echo URL::to('/my-profile'); ?>">

                                                <i class="mdi mdi-face-profile "></i>

                                                My Profile
                                            </a>
                                            <a class="dropdown-item" href="<?php echo URL::to('/change-password'); ?>">

                                                <i class="mdi mdi-settings "></i>

                                                Change Password
                                            </a>
                                            <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();

                                            document.getElementById('logout-form').submit();">

                                                <i class="mdi mdi-logout"></i>

                                                Logout </a>

                                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">

                                                {{ csrf_field() }}

                                            </form>
                                        </div>
                                    </li>
                                    <li class="nav-item">
                                        {{auth()->user()->first_name}}
                                    </li>

                                </ul>
                            </div>
                        </div>


                        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="horizontal-menu-toggle">
                            <span class="mdi mdi-menu"></span>
                        </button>
                    </div>
                </div>
            </nav>
            <nav class="bottom-navbar">

                <div class="container-fluid">
                    <ul class="nav page-navigation">
                        <?php
                        $auth = auth()->user();
                        $user_type_fk = $auth['user_type_fk'];

                        if (in_array($user_type_fk, array(184))) { ?>
                            @can('calendar-list')
                            <li class="nav-item {{ request()->is('dashboard/calendar-hospital-v2') ? 'active' : '' }}">
                                <a class="nav-link" href="<?php echo URL::to('/dashboard/calendar-hospital-v2'); ?>">
                                    <i class="mdi mdi-calendar menu-icon"></i>
                                    <span class="menu-title">Calendar</span>
                                </a>
                            </li>
                            {{-- <li class="nav-item {{ request()->is('patient-calendar') ? 'active' : '' }}">
                                <a class="nav-link" href="<?php echo URL::to('/patient-calendar'); ?>">
                                    <i class="mdi mdi-calendar menu-icon"></i>
                                    <span class="menu-title">Telehealth Calendar</span>
                                </a>
                            </li> --}}
                            @endcan
                            @can('my-dashboard')
                            <li class="nav-item {{ request()->is('my-dashboard') ? 'active' : '' }}">
                                <a class="nav-link" href="<?php echo URL::to('/my-dashboard'); ?>">
                                    <i class="mdi mdi-view-dashboard menu-icon"></i>
                                    <span class="menu-title">My Dashboard</span>
                                </a>
                            </li>
                            @endcan
                            @can('appointments-list')
                            <li class="nav-item {{ (request()->is('appointment/*') || request()->is('appointment')) ? 'active' : '' }}">
                                <a class="nav-link" href="<?php echo URL::to('/appointment'); ?>">
                                    <i class="mdi mdi-clipboard-text-outline menu-icon"></i>
                                    <span class="menu-title">Appointment</span>
                                </a>
                            </li>
                            @endcan
                            @can('chart-list')
                            <li class="nav-item {{ (request()->is('patient/*') || request()->is('patient')) ? 'active' : '' }}">
                                <a class="nav-link" href="<?php echo URL::to('/patient'); ?>">
                                    <i class="mdi mdi-clipboard-text-outline menu-icon"></i>
                                    <span class="menu-title">Chart</span>
                                </a>
                            </li>
                            @endcan
                            @can('hub-list')
                            @if(Auth()->user()->show_hub == 1)
                            <li class="nav-item {{ (request()->is('hub-record/*') || request()->is('hub-record')) ? 'active' : '' }}">
                                <a class="nav-link" href="<?php echo URL::to('/hub-record'); ?>">
                                    <i class="mdi mdi-hubspot menu-icon"></i>
                                    <span class="menu-title">Hub Record</span>
                                </a>
                            </li>
                            @endif
                            @endcan

                            @canany(['hha-medical','hha-other-compliance','hha-patient-appointment','view-hha-caregiver-list','hha-patient-md-order-list','hha-due-medical'])
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="mdi mdi-clipboard-text-outline menu-icon"></i>
                                    <span class="menu-title">HHA Exchange</span>
                                    <i class="menu-arrow"></i></a>
                                <div class="submenu">
                                    <ul class="submenu-item">
                                        {{-- @can('hha-medical')
                                        <li class="nav-item ">
                                            <a class="nav-link" href="<?php echo URL::to('/hha/hha-medical'); ?>">
                                                <span class="menu-title">Medicals</span>
                                            </a>
                                        </li>
                                        @endcan --}}
                                        @can('hha-due-medical')

                                            <li class="nav-item ">
                                                <a class="nav-link" href="{{ url('hha/due-medical-report')}}">
                                                    <span class="menu-title">HHA Due Medical Report</span>
                                                </a>
                                            </li>

                                        @endcan
                                        @can('hha-other-compliance')
                                        <li class="nav-item ">
                                            <a class="nav-link" href="<?php echo URL::to('/hha/hha-other-compliances'); ?>">
                                                <span class="menu-title">Other Complience</span>
                                            </a>
                                        </li>
                                        @endcan
                                        @can('hha-patient-appointment')

                                        <li class="nav-item ">
                                            <a class="nav-link" href="<?php echo URL::to('/hha/hha-patient'); ?>">
                                                <span class="menu-title">HHA Patient</span>
                                            </a>
                                        </li>

                                        @endcan
                                        @can('view-hha-caregiver-list')

                                        <li class="nav-item ">
                                            <a class="nav-link" href="<?php echo URL::to('/hha-caregiver-list'); ?>">
                                                <span class="menu-title">HHA Caregiver</span>
                                            </a>
                                        </li>

                                        @endcan

                                        @can('hha-patient-md-order-list')

                                            <li class="nav-item ">
                                                <a class="nav-link" href="<?php echo URL::to('/hha/hha-mdo/hha-mdo-patient-list'); ?>">
                                                    <span class="menu-title">HHA MDO Patient</span>
                                                </a>
                                            </li>

                                        @endcan
                                        @can('hha-medical-service')

                                        <li class="nav-item ">
                                            <a class="nav-link" href="{{ url('hha/hha-caregiver-medicals')}}">
                                                <span class="menu-title">Setup Due Medical</span>
                                            </a>
                                        </li>
                                        @endcan
                                    </ul>
                                </div>
                            </li>
                            @endcan
                            @canany(['role-list','user-list','agency-list','doctor-list','language-list','template-list','location-list','field-master-list','form-setup-list','approve-stamp-list','insurance-master-list','notification-setting-list','ebook-list','event-master-list','disable-date-list','pse-list','site-setting','group-notification','announcements-list','rate-card-list','directory-list','enquiry-list','manage-telehealth-location','hub-company-list','service-master-list','vns-question','vns-procedure','vns-procedure-result','vns-social-history','app-token-generate','reactivate-patient-list','agency-task-health-setting','user-doc-approval-list'])
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="mdi mdi-settings menu-icon"></i>
                                    <span class="menu-title">Setting</span>
                                    <i class="menu-arrow"></i></a>
                                <div class="submenu submenu-menubar">
                                    <ul class="submenu-item">
                                        @can('role-list')
                                        <li class="nav-item {{ request()->is('roles') ? 'active' : '' }}">
                                            <a class="nav-link" href="{{ route('roles.index') }}">
                                                <span class="menu-title">Manage Role</span>
                                            </a>
                                        </li>

                                        @endcan
                                        @can('user-list')
                                        <li class="nav-item {{ request()->is('user/*') ? 'active' : '' }}">
                                            <a class="nav-link" href="<?php echo URL::to('/user'); ?>">
                                                <span class="menu-title">Users</span>
                                            </a>
                                        </li>
                                        @endcan

                                        @can('agency-list')
                                        <li class="nav-item {{ request()->is('agency/*') ? 'active' : '' }}">
                                            <a class="nav-link" href="<?php echo URL::to('/agency'); ?>">
                                                <span class="menu-title">Agencies</span>
                                            </a>
                                        </li>
                                        @endcan
                                        @can('doctor-list')
                                        <li class="nav-item {{ request()->is('doctor/*') ? 'active' : '' }}">
                                            <a class="nav-link" href="<?php echo URL::to('/doctor'); ?>">
                                                <span class="menu-title">Doctor</span>
                                            </a>
                                        </li>
                                        @endcan

                                        @can('language-list')
                                        <li class="nav-item {{ request()->is('language/*') ? 'active' : '' }}">
                                            <a class="nav-link" href="{{ route('language.index') }}">
                                                <span class="menu-title">Language</span>
                                            </a>
                                        </li>
                                        @endcan

                                        @can('template-list')
                                        <li class="nav-item {{ request()->is('template/*') ? 'active' : '' }}"><a class="nav-link" href="<?php echo URL::to('/'); ?>/template"><span class="menu-title">Template</span></a></li>
                                        @endcan
                                        @can('role-list')
                                        <li class="nav-item {{ request()->is('master-type-view') ? 'active' : '' }}">
                                            <a class="nav-link" href="<?php echo URL::to('/master-type-view'); ?>">

                                                <span class="menu-title">Master</span>
                                            </a>
                                        </li>
                                        @endcan

                                        @can('location-list')
                                        <li class="nav-item {{ request()->is('location') ? 'active' : '' }}">
                                            <a class="nav-link" href="<?php echo URL::to('/location'); ?>">
                                                <span class="menu-title">Location Master</span>
                                            </a>
                                        </li>
                                        @endcan

                                        @can('field-master-list')
                                        <li class="nav-item {{ request()->is('field-master') ? 'active' : '' }}">
                                            <a class="nav-link" href="{{ route('field-master.index') }}">
                                                <span class="menu-title">Field Master</span>
                                            </a>
                                        </li>
                                        @endcan

                                        @can('form-setup-list')
                                        <li class="nav-item {{ request()->is('form-setup') ? 'active' : '' }}">
                                            <a class="nav-link" href="{{ route('form-setup.index') }}">
                                                <span class="menu-title">Form Setup</span>
                                            </a>
                                        </li>
                                        @endcan

                                        @can('approve-stamp-list')
                                        <li class="nav-item {{ request()->is('stamp') ? 'active' : '' }}">
                                            <a class="nav-link" href="{{ route('stamp.index') }}">
                                                <span class="menu-title">Stamp</span>
                                            </a>
                                        </li>
                                        @endcan

                                        @can('insurance-master-list')
                                        <li class="nav-item ">
                                            <a class="nav-link" href="{{ url('/insurance-master') }}">

                                                <span class="menu-title">Insurance Master</span>
                                            </a>
                                        </li>
                                        @endcan

                                        @can('notification-setting-list')
                                        <li class="nav-item ">
                                            <a class="nav-link" href="{{ url('/notification-setting') }}">

                                                <span class="menu-title">Notification Email</span>
                                            </a>
                                        </li>
                                        @endcan

                                        @canany('ebook-list')
                                        <li class="nav-item ">
                                            <a class="nav-link" href="{{ url('/ebook') }}">

                                                <span class="menu-title">Manage Ebook </span>
                                            </a>
                                        </li>
                                        @endcan

                                        @can('event-master-list')
                                        <li class="nav-item ">
                                            <a class="nav-link" href="{{ url('event-master')}}">
                                                <span class="menu-title">Manage Popup</span>
                                            </a>
                                        </li>
                                        @endcan
                                        @can('disable-date-list')
                                        <li class="nav-item ">
                                            <a class="nav-link" href="{{ url('disable-date')}}">
                                                <span class="menu-title">Disable Date Setting</span>
                                            </a>
                                        </li>
                                        @endcan
                                        @can('pse-list')
                                        <li class="nav-item ">
                                            <a class="nav-link" href="{{ url('pse-location')}}">
                                                <span class="menu-title">PSE Location</span>
                                            </a>
                                        </li>
                                        @endcan
                                        @can('site-setting')
                                        <li class="nav-item ">
                                            <a class="nav-link" href="{{ url('site-setting')}}">
                                                <span class="menu-title">Site Setting</span>
                                            </a>
                                        </li>
                                        @endcan
                                        @can('group-notification')
                                        <li class="nav-item {{ request()->is('group-notification') ? 'active' : '' }}">
                                            <a class="nav-link" href="{{ route('group-notification.index') }}">
                                                <span class="menu-title">Manage Group Notification</span>
                                            </a>
                                        </li>
                                        @endcan
                                        @can('announcements-list')
                                        <li class="nav-item ">
                                            <a class="nav-link" href="{{ url('announcements')}}">
                                                <span class="menu-title">Manage Announcements</span>
                                            </a>
                                        </li>
                                        @endcan
                                        @can('rate-card-list')
                                        <li class="nav-item ">
                                            <a class="nav-link" href="{{ url('rate-card')}}">
                                                <span class="menu-title">Manage Rate Card</span>
                                            </a>
                                        </li>
                                        @endcan
                                        @can('directory-list')
                                            <li class="nav-item ">
                                                <a class="nav-link"  href="{{ url('directory')}}">
                                                    <span class="menu-title">Directory</span>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('enquiry-list')
                                        <li class="nav-item ">
                                            <a class="nav-link" href="{{ url('enquiry')}}">

                                                <span class="menu-title">Manage Enquiry</span>
                                            </a>
                                        </li>
                                        @endcan
                                        @can('manage-telehealth-location')
                                            <li class="nav-item ">
                                                <a class="nav-link" href="{{ url('telehealth-schedule-manage')}}">
                                                    <span class="menu-title">Telehealth Schedule</span>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('hub-company-list')
                                            <li class="nav-item ">
                                                <a class="nav-link" href="{{ url('hub-company')}}">
                                                    <span class="menu-title">Hub Company</span>
                                                </a>
                                            </li>
                                        @endcan

                                        @can('service-master-list')
                                            <li class="nav-item ">
                                                <a class="nav-link" href="{{ url('service-master')}}">
                                                    <span class="menu-title">Master Services</span>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('vns-procedure')
                                            <li class="nav-item ">
                                                <a class="nav-link" href="{{ url('vns-procedure')}}">
                                                    <span class="menu-title">VNS Procedure</span>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('vns-question')
                                            <li class="nav-item ">
                                                <a class="nav-link" href="{{ url('vns-question')}}">
                                                    <span class="menu-title">VNS Question</span>
                                                </a>
                                            </li>
                                        @endcan

                                        @can('vns-procedure-result')
                                            <li class="nav-item ">
                                                <a class="nav-link" href="{{ url('vns-procedure-result')}}">
                                                    <span class="menu-title">VNS Procedure Result</span>
                                                </a>
                                            </li>
                                        @endcan

                                        @can('vns-social-history')
                                            <li class="nav-item ">
                                                <a class="nav-link" href="{{ url('vns-social-history')}}">
                                                    <span class="menu-title">VNS Social History</span>
                                                </a>
                                            </li>
                                        @endcan

                                        @can('merge-agency')
                                            <li class="nav-item ">
                                                <a class="nav-link" href="{{ url('patient-agency-merge/index')}}">
                                                    <span class="menu-title">Agency Merge</span>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('announcement-master-list')
                                        <li class="nav-item ">
                                            <a class="nav-link" href="{{ url('announcement-master')}}">
                                                <span class="menu-title">Announcement Master</span>
                                            </a>
                                        </li>
                                        @endcan
                                        @can('department-list')
                                            <li class="nav-item ">
                                                <a class="nav-link" href="{{ url('tasks/department-master')}}">
                                                    <span class="menu-title">Department Master</span>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('app-token-generate')
                                            <li class="nav-item ">
                                                <a class="nav-link" href="{{ url('app-tokens')}}">
                                                    <span class="menu-title">App Token</span>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('zipcode-list')
                                            <li class="nav-item ">
                                                <a class="nav-link" href="{{ url('setting/zipcode')}}">
                                                    <span class="menu-title">Zip Code</span>
                                                </a>
                                            </li>
                                        @endcan

                                        @can('reactivate-patient-list')
                                            <li class="nav-item ">
                                                <a class="nav-link" href="{{ url('deleted-patient-management')}}">
                                                    <span class="menu-title">Deleted Patient Management</span>
                                                </a>
                                            </li>
                                        @endcan

                                        @can('branch-list')
                                            <li class="nav-item ">
                                                <a class="nav-link" href="{{ url('branch-master')}}">
                                                    <span class="menu-title">Branch Management</span>
                                                </a>
                                            </li>
                                        @endcan

                                        @can('resolution-sms-template')
                                        <li class="nav-item {{ request()->is('resolution-sms-template') ? 'active' : '' }}">
                                            <a class="nav-link" href="{{ url('resolution-sms-template')}}">
                                                <span class="menu-title">Resolution SMS Templates</span>
                                            </a>
                                        </li>
                                        @endcan

                                        @can('agency-task-health-setting')
                                            <li class="nav-item {{ request()->is('agency-task-health-setting') ? 'active' : '' }}">
                                                <a class="nav-link" href="{{ url('agency-task-health-setting') }}">

                                                    <span class="menu-title">Agency Task Health Settings</span>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('user-doc-approval-list')
                                            <li class="nav-item {{ request()->is('user-doc-approval*') ? 'active' : '' }}">
                                                <a class="nav-link" href="{{ url('user-doc-approval')}}">
                                                    <span class="menu-title">User Doc Approval</span>
                                                </a>
                                            </li>
                                        @endcan
                                    </ul>
                                </div>
                            </li>
                            @endcan
                            @can('task-list')
                            <li class="nav-item {{ request()->is('tasks/task-list') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('tasks.task-list.index') }}">
                                    <i class="mdi mdi-clipboard-text-outline menu-icon"></i>
                                    <span class="menu-title">Task</span>
                                </a>
                            </li>
                            @endcan
                            @can('request-list')
                            <li class="nav-item ">
                                <a class="nav-link" href="{{ route('request-list.index') }}">
                                    <i class="mdi mdi-clipboard-text-outline menu-icon"></i>
                                    <span class="menu-title">Request</span>
                                </a>
                            </li>
                            @endcan

                           @canany(['manage-invoice','payment-log-import'])
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                <i class="mdi mdi-receipt menu-icon"></i>
                                <span class="menu-title">Account</span>
                                <i class="menu-arrow"></i></a>
                                    <div class="submenu">
                                        <ul class="submenu-item">
                                            @can('manage-invoice')
                                                <li class="nav-item {{ request()->is('account/admin/invoices') ? 'active' : '' }}">
                                                    <a class="nav-link" href="{{ url('account/admin/invoices') }}">
                                                        </i><span class="menu-title">Invoices</span>
                                                    </a>
                                                </li>
                                            @endcan

                                            @can('payment-log-import')
                                                <li class="nav-item {{ request()->is('account/payment-log-listing') ? 'active' : '' }}">
                                                    <a class="nav-link" href="{{ url('account/payment-log-listing') }}">
                                                        <span class="menu-title">Payment Log</span>
                                                    </a>
                                                </li>
                                            @endcan
                                        </ul>
                                    </div>
                            </li>
                            @endcan

                            @canany(['dashboard-graph','hamaspik-appointment-report','service-request-list','esign-report-list','form-report-list','agency-user-report-list','agency-summary-list','feedback-form-report-list','md-order-report','payment-log-report','telehealth-booking-report','hub-record-report','referral-source-report','resolution-log-report','bulk-sms-cdpap-caregiver','bulk-view-report-list','rn-pad-report','lead-coordination-report','hha-mdo-report-log','reporting-tool','task-health-log-list','inflowcare-patient-log-report'])


                            <li class="nav-item ">
                                <a href="#" class="nav-link">
                                    <i class="mdi mdi-settings menu-icon"></i>
                                    <span class="menu-title">Report </span>
                                    <i class="menu-arrow"></i></a>
                                <div class="submenu submenu-menubar">
                                    <ul class="submenu-item">


                                        @can('hamaspik-appointment-report')
                                        <li class="nav-item {{ request()->is('service-wise-appointment-report') ? 'active' : '' }}">
                                            <a class="nav-link" href="{{ url('service-wise-appointment-report') }}">
                                                <span class="menu-title">Hamaspik Appointment Report</span>
                                            </a>
                                        </li>
                                        @endcan
                                        @can('document-report')
                                        <li class="nav-item {{ request()->is('document-report') ? 'active' : '' }}">
                                            <a class="nav-link" href="{{ url('document-report') }}">
                                                <span class="menu-title">Document Report</span>
                                            </a>
                                        </li>
                                        @endcan

                                        @can('form-report-list')
                                        <li class="nav-item {{ request()->is('form-report') ? 'active' : '' }}">
                                            <a class="nav-link" href="{{ url('form-report') }}">
                                                <span class="menu-title">Form Report</span>
                                            </a>
                                        </li>
                                        @endcan
                                        @can('agency-user-report-list')
                                        <li class="nav-item {{ request()->is('agency-user-report') ? 'active' : '' }}">
                                            <a class="nav-link" href="{{ url('agency-user-report') }}">
                                                <span class="menu-title">Agency User Report</span>
                                            </a>
                                        </li>
                                        @endcan
                                        @if(Auth()->user()->agency_fk =="")

                                        @else
                                        <li class="nav-item ">
                                            <a class="nav-link" href="{{ url('agency-dashboard')}}">
                                                <i class="mdi mdi-clipboard-text-outline menu-icon"></i>
                                                <span class="menu-title">Agency Dashboard</span>
                                            </a>
                                        </li>
                                        @endif
                                        @can('agency-summary-list')
                                        <li class="nav-item {{ request()->is('agency-summary') ? 'active' : '' }}">
                                            <a class="nav-link" href="{{ url('agency-summary') }}">
                                                <span class="menu-title">Agency Summary Report</span>
                                            </a>
                                        </li>
                                        @endcan

                                        @can('esign-report-list')
                                        <li class="nav-item {{ request()->is('esign-report') ? 'active' : '' }}">
                                            <a class="nav-link" href="{{ url('esign-report') }}">
                                                <span class="menu-title">Esign Report</span>

                                            </a>
                                        </li>
                                        @endcan

                                        @can('third-party-report-list')
                                        <li class="nav-item ">
                                            <a class="nav-link" href="{{ url('third-party-report-list')}}">
                                                <span class="menu-title">Third Party Report List</span>
                                            </a>
                                        </li>
                                        @endcan
                                        @can('dashboard-graph')
                                        <li class="nav-item {{ request()->is('dashboard-graph') ? 'active' : '' }}">
                                            <a class="nav-link" href="{{ url('dashboard-graph') }}">
                                                <span class="menu-title">Dashboard Graph</span>
                                            </a>
                                        </li>
                                        @endcan
                                        @can('service-request-list')
                                        <li class="nav-item {{ request()->is('patient-service-requested') ? 'active' : '' }}">
                                            <a class="nav-link" href="{{ url('patient-service-requested') }}">
                                                <span class="menu-title">Service Request Report</span>
                                            </a>
                                        </li>
                                        @endcan
                                        @can('feedback-form-report-list')
                                        <li class="nav-item {{ request()->is('feedback-form-report') ? 'active' : '' }}">
                                            <a class="nav-link" href="{{ url('feedback-form-report') }}">
                                                <span class="menu-title">Feedback Form Report</span>
                                            </a>
                                        </li>
                                        @endcan
                                        @can('md-order-report')
                                        <li class="nav-item mt-2 {{ request()->is('md-order-report') ? 'active' : '' }}">
                                            <a class="nav-link" href="{{ url('md-order-report') }}">
                                                <span class="menu-title">MD Order Report</span>
                                            </a>
                                        </li>
                                        @endcan

                                        @can('emmacare-referal-report')
                                        <li class="nav-item {{ request()->is('emmacare-referal') ? 'active' : '' }}">
                                            <a class="nav-link" href="{{ url('emmacare-referal') }}">
                                                <span class="menu-title">Emmacare Referral Report</span>
                                            </a>
                                        </li>
                                        @endcan
                                        @can('payment-log-report')
                                        <li class="nav-item {{ request()->is('payment-log-report') ? 'active' : '' }}">
                                            <a class="nav-link" href="{{ url('payment-log-report') }}">
                                                <span class="menu-title">Payment Log Report</span>
                                            </a>
                                        </li>
                                        @endcan
                                        @can('telehealth-booking-report')
                                        <li class="nav-item {{ request()->is('telehealth-book-report') ? 'active' : '' }}">
                                            <a class="nav-link" href="{{ url('telehealth-book-report') }}">
                                                <span class="menu-title">Telehealth Book Report</span>
                                            </a>
                                        </li>
                                        @endcan
                                        @if(Auth()->user()->show_hub == 1)
                                            @can('hub-record-report')
                                            <li class="nav-item {{ request()->is('hub-record-report') ? 'active' : '' }}">
                                                <a class="nav-link" href="{{ url('hub-record-report') }}">
                                                    <span class="menu-title">Hub Record Report</span>
                                                </a>
                                            </li>
                                            @endcan
                                            @can('hub-notes-report')
                                            <li class="nav-item {{ request()->is('hub-notes-report') ? 'active' : '' }}">
                                                <a class="nav-link" href="{{ url('hub-notes-report') }}">
                                                    <span class="menu-title">Hub Notes Report</span>
                                                </a>
                                            </li>
                                            @endcan
                                            @can('hub-doc-report')
                                            <li class="nav-item {{ request()->is('hub-doc-report') ? 'active' : '' }}">
                                                <a class="nav-link" href="{{ url('hub-doc-report') }}">
                                                    <span class="menu-title">Hub Document Report</span>
                                                </a>
                                            </li>
                                            @endcan
                                        @endif

                                            @can('referral-source-report')
                                            <li class="nav-item {{ request()->is('referral-source-report') ? 'active' : '' }}">
                                                <a class="nav-link" href="{{ url('referral-source-report') }}">
                                                    <span class="menu-title">Referral Source Report</span>
                                                </a>
                                            </li>
                                            @endcan
                                            @can('resolution-log-report')
                                            <li class="nav-item {{ request()->is('resolution-log-report') ? 'active' : '' }}">
                                                <a class="nav-link" href="{{ url('resolution-log-report') }}">
                                                    <span class="menu-title">Resolution Log Report</span>
                                                </a>
                                            </li>
                                            @endcan

                                            @can('bulk-sms-cdpap-caregiver')
                                            <li class="nav-item {{ request()->is('bulk-sms-cdpap-caregiver') ? 'active' : '' }}">
                                                <a class="nav-link" href="{{ url('bulk-sms-cdpap-caregiver') }}">
                                                    <span class="menu-title">Bulk SMS Cdpap Caregiver</span>
                                                </a>
                                            </li>
                                            @endcan

                                            @can('bulk-view-report-list')
                                            <li class="nav-item {{ request()->is('book-appointment-report') ? 'active' : '' }}">
                                                <a class="nav-link" href="{{ url('book-appointment-report') }}">
                                                    <span class="menu-title">Book Appointment Report</span>
                                                </a>
                                            </li>
                                            @endcan
                                            @can('service-request-list')
                                            <li
                                                class="nav-item {{ request()->is('hub-patient-service-requested') ? 'active' : '' }}">
                                                <a class="nav-link" href="{{ url('hub-patient-service-requested') }}">
                                                    <span class="menu-title">Hub NyBest Medical Request Report</span>
                                                </a>
                                            </li>
                                            @endcan
                                            @can('referrals-weight-report')
                                            <li
                                                class="nav-item {{ request()->is('referrals-weight') ? 'active' : '' }}">
                                                <a class="nav-link" href="{{ url('report/referrals-weight') }}">
                                                    <span class="menu-title">Referrals Weight Report</span>
                                                </a>
                                            </li>
                                            @endcan
                                              @can('hub-record-task-menu')
                                                <li class="nav-item">
                                                    <a class="nav-link" href="{{ url('hub-record/task-record') }}">

                                                        <span class="menu-title">Hub Record Task</span>
                                                    </a>
                                                </li>
                                            @endcan

                                            @if(auth()->user()->agency_fk == "")
                                            @can('payment-type-report')
                                            <li
                                                class="nav-item {{ request()->is('payment-type-report') ? 'active' : '' }}">
                                                <a class="nav-link" href="{{ url('payment-type-report') }}">
                                                    <span class="menu-title">Payment Type Report</span>
                                                </a>
                                            </li>
                                            @endcan
                                            @endif
                                            @can('rn-pad-report')
                                                <li class="nav-item {{ request()->is('rnpad') ? 'active' : '' }}">
                                                    <a class="nav-link" href="{{ url('rnpad') }}">

                                                        <span class="menu-title">RNPad Documents Report</span>
                                                    </a>
                                                </li>
                                            @endcan
                                            @can('hha-mdo-report-log')
                                                <li class="nav-item {{ request()->is('hha/hha-mdo/mdo-report-log') ? 'active' : '' }}">
                                                    <a class="nav-link" href="{{ url('hha/hha-mdo/mdo-report-log') }}">

                                                        <span class="menu-title">HHA MDO Documents Report</span>
                                                    </a>
                                                </li>
                                            @endcan

                                            @can('lead-coordination-report')
                                                <li class="nav-item {{ request()->is('lead-coordination-report') ? 'active' : '' }}">
                                                    <a class="nav-link" href="{{ url('lead-coordination-report') }}">

                                                        <span class="menu-title">Lead Coordination Report</span>
                                                    </a>
                                                </li>
                                            @endcan

                                            @can('reporting-tool')
                                                <li class="nav-item {{ request()->is('report/custom-reports') ? 'active' : '' }}">
                                                    <a class="nav-link" href="{{ url('report/custom-reports') }}">

                                                        <span class="menu-title">Reporting Tool</span>
                                                    </a>
                                                </li>
                                            @endcan

                                            @can('mdo485-summary-report')
                                                <li class="nav-item {{ request()->is('report/mdo485-summary-report') ? 'active' : '' }}">
                                                    <a class="nav-link" href="{{ url('report/mdo485-summary-report') }}">

                                                        <span class="menu-title">MDO485 Summary Report</span>
                                                    </a>
                                                </li>
                                            @endcan

                                            @can('call-details-list')
                                            <li class="nav-item {{ request()->is('call-details') ? 'active' : '' }}">
                                                <a class="nav-link" href="{{ url('call-details') }}">
                                                    <span class="menu-title">Call Details (CDR) Report</span>
                                                </a>
                                            </li>
                                            @endcan

                                    </ul>
                                </div>
                            </li>
                            @endcan
                            @canany(['hub-utilization-report'])


                            <li class="nav-item ">
                                <a href="#" class="nav-link">
                                    <i class="mdi mdi-settings menu-icon"></i>
                                    <span class="menu-title">Hub Report </span>
                                    <i class="menu-arrow"></i></a>

                                <div class="submenu submenu-menubar">
                                    <ul class="submenu-item">
                                        @can('hub-utilization-report')
                                        <li class="nav-item {{ request()->is('hub-utilization-report') ? 'active' : '' }}">
                                            <a class="nav-link" href="{{ url('hub-utilization-report') }}">
                                                <span class="menu-title">Hub Utilization Report</span>
                                            </a>
                                        </li>
                                        @endcan


                                    </ul>
                                </div>
                            </li>
                            @endcan
                            @canany(['alayacare-client-list','alayacare-employee-list','alayacare-due-skill'])
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="mdi mdi-clipboard-text-outline menu-icon"></i>
                                    <span class="menu-title">AlayaCare</span>
                                    <i class="menu-arrow"></i></a>
                                <div class="submenu">
                                    <ul class="submenu-item">
                                        @can('alayacare-client-list')
                                        <li class="nav-item {{ request()->is('alayacare/alayacare-client/client-list') ? 'active' : '' }}">
                                            <a class="nav-link" href="{{ url('alayacare/alayacare-client/client-list') }}">
                                                <span class="menu-title">Client</span>
                                            </a>
                                        </li>
                                        @endcan
                                        @can('alayacare-employee-list')
                                        <li class="nav-item {{ request()->is('alayacare/alayacare-employee/employee-list') ? 'active' : '' }}">
                                            <a class="nav-link" href="{{ url('alayacare/alayacare-employee/employee-list') }}">
                                                <span class="menu-title">Employee</span>
                                            </a>
                                        </li>
                                        @endcan

                                        @can('alayacare-due-skill')
                                        <li class="nav-item {{ request()->is('alayacare/alayacare-skill/alayacare-due-skill-list') ? 'active' : '' }}">
                                            <a class="nav-link" href="{{ url('alayacare/alayacare-skill/alayacare-due-skill-list') }}">
                                                <span class="menu-title">Due Skill</span>
                                            </a>
                                        </li>
                                        @endcan
                                    </ul>
                                </div>
                            </li>
                            @endcan
                            @can('robort-list')
                                <li class="nav-item ">
                                    <a class="nav-link" href="{{ url('remote/remote-list') }}">
                                        <i class="mdi mdi-clipboard-text-outline menu-icon"></i>
                                        <span class="menu-title">Remote Focus</span>
                                    </a>
                                </li>

                            @endcan

                            @can('api-docs')
                            <li class="nav-item ">
                                <a class="nav-link" href="{{ asset('/api_doc_lead_v2.pdf') }}" download>
                                    <i class="mdi mdi-clipboard-text-outline menu-icon"></i>
                                    <span class="menu-title">API Doc</span>
                                </a>
                            </li>

                            @endcan
                            @canany(['third-party-patient','arla-list','task-health-list','pending-visiting-medical','task-health-visit-list'])
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="mdi mdi-settings menu-icon"></i>
                                    <span class="menu-title">Third Party</span>
                                    <i class="menu-arrow"></i></a>
                                <div class="submenu">
                                    <ul class="submenu-item">
                                        @can('third-party-patient')
                                        <li class="nav-item ">
                                            <a class="nav-link" href="{{ url('third-party-patient')}}">

                                                <span class="menu-title">Visiting Aid @if($pendingCount !=0) ({{ $pendingCount }}) @endif</span>
                                            </a>
                                        </li>

                                        @endcan
                                        @can('arla-list')
                                        <li class="nav-item ">
                                            <a class="nav-link" href="{{ url('arla-appointment')}}">

                                                <span class="menu-title">Arla Aid @if($pendingCount !=0) ({{ $pendingCount }}) @endif</span>
                                            </a>
                                        </li>

                                        @endcan
                                        @can('task-health-list')
                                        <li class="nav-item ">
                                            <a class="nav-link" href="{{ url('task-health')}}">

                                                <span class="menu-title">Task Health</span>
                                            </a>
                                        </li>
                                        @endcan

                                        @can('pending-visiting-medical')
                                        <li class="nav-item ">
                                            <a class="nav-link" href="{{ url('visiting-aid/pending-medicals')}}">

                                                <span class="menu-title">Visiting Pending Medical </span>
                                            </a>
                                        </li>

                                        @endcan
                                    </ul>
                                </div>
                            </li>
                            @endcan


                        <?php } else { ?>


                            <li class="nav-item {{ request()->is('appointment') ? 'active' : '' }}   agency-logo">
                                @if(isset($agencyObj) && $agencyObj != null)
                                @if($agencyObj->agency_logo !="")
                                <img id="agency-logo" src="{{ url('download-agency-images')}}?id={{ $agencyObj->id}}" style="height: 76px;width: 145px;border-radius: 5px;" alt="Logo">
                                @endif
                                @endif
                                <a class="nav-link" href="<?php echo URL::to('/appointment'); ?>">
                                    <i class="mdi mdi-clipboard-text-outline menu-icon"></i>
                                    <span class="menu-title">Appointment</span>
                                </a>
                            </li>
                            @if(Auth()->user()->agency_fk ==106)
                            <li class="nav-item {{ request()->is('service-wise-appointment-report') ? 'active' : '' }}   agency-logo">

                                <a class="nav-link" href="<?php echo URL::to('/service-wise-appointment-report'); ?>">
                                    <i class="mdi mdi-clipboard-text-outline menu-icon"></i>
                                    <span class="menu-title">Report</span>
                                </a>
                            </li>
                            @endif
                            @if(!in_array(auth()->user()->id,Utility::agencyPortalRolePermission()))
                            <li class="nav-item agency-logo {{ request()->is('md-order-report') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ url('md-order-report') }}">
                                    <i class="mdi mdi-clipboard-text-outline menu-icon"></i>
                                    <span class="menu-title">MD Order Report</span>
                                </a>
                            </li>
                            @endif
                        <?php } ?>
                        @canany(['user-dashboard','search-patient-details','employee-dashboard','esign-dashboard','task-dashboard','appointment-dashboard','visiting-aid-dashboard','payment-dashboard','diagnosis','reports-list','hub-analytics','esign-patient-dashboard'])
                        <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="mdi mdi-clipboard-text-outline menu-icon"></i>
                                    <span class="menu-title">New Features</span>
                                    <i class="menu-arrow"></i>
                                </a>
                                <div class="submenu">
                                    <ul class="submenu-item">




                                        @can('user-dashboard')
                                        <li class="nav-item ">
                                            <a class="nav-link" href="{{ url('user-dashboard')}}">

                                                <span class="menu-title">User Dashboard</span>
                                            </a>
                                        </li>
                                        @endcan
                                        @can('employee-dashboard')
                                        <li class="nav-item ">
                                            <a class="nav-link" href="{{ url('employee-dashboard')}}">

                                                <span class="menu-title">Employee Dashboard</span>
                                            </a>
                                        </li>
                                        @endcan

                                        @can('search-patient-details')

                                        <li class="nav-item ">
                                            <a class="nav-link" href="{{ url('patient-details-search')}}">

                                                <span class="menu-title">Search Patient Details</span>
                                            </a>
                                        </li>
                                        @endcan


                                        @can('esign-dashboard')
                                        <li class="nav-item ">
                                            <a class="nav-link" href="{{ url('esign-dashboard')}}">

                                                <span class="menu-title">Esign Dashboard</span>
                                            </a>
                                        </li>
                                        @endcan
                                        @can('task-dashboard')
                                        <li class="nav-item ">
                                            <a class="nav-link" href="{{ url('task-dashboard')}}">

                                                <span class="menu-title">Task Dashboard</span>
                                            </a>
                                        </li>
                                        @endcan

                                        @can('appointment-dashboard')
                                        <li class="nav-item ">
                                            <a class="nav-link" href="{{ url('appointment-dashboard')}}">
                                                <span class="menu-title">Appointment Dashboard</span>
                                            </a>
                                        </li>
                                        @endcan
                                        @can('visiting-aid-dashboard')
                                        <li class="nav-item ">
                                            <a class="nav-link" href="{{ url('visiting-aid-dashboard')}}">
                                                <span class="menu-title">Visiting Aid Dashboard</span>
                                            </a>
                                        </li>
                                        @endcan
                                        @can('payment-dashboard')
                                        <li class="nav-item ">
                                            <a class="nav-link"  href="{{ url('payment-dashboard')}}">
                                                <span class="menu-title">Payment Dashboard</span>
                                            </a>
                                        </li>
                                        @endcan

                                        @can('diagnosis')
                                        <li class="nav-item ">
                                            <a class="nav-link"  href="{{ url('patient/diagnosis')}}">
                                                <span class="menu-title">AI Diagnosis</span>
                                            </a>
                                        </li>

                                        @endcan
                                        @can('reports-list')
                                        <li class="nav-item ">
                                            <a class="nav-link"  href="{{ url('reports')}}">
                                                <span class="menu-title">Reports</span>
                                            </a>
                                        </li>
                                        @endcan
                                         @can('hub-analytics')
                                        <li class="nav-item ">
                                            <a class="nav-link"  href="{{ url('hub-analytics')}}">
                                                <span class="menu-title">Hub Analytics Dashboard</span>
                                            </a>
                                        </li>
                                        @endcan

                                        @can('esign-patient-dashboard')
                                            <li class="nav-item {{ request()->is('esign/esign-patient-dashboard') ? 'active' : '' }}">
                                                <a class="nav-link" href="{{ url('esign/esign-patient-dashboard') }}">

                                                    <span class="menu-title">Esign Patient Dashboard</span>
                                                </a>
                                            </li>
                                        @endcan

                                    </ul>
                                </div>

                            </li>
                        @endcan

                        @if(auth()->user()->user_type_fk ==184)
                            @can('ebook-menu')
                                <li class="nav-item  {{ request()->is('ebook-view') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ url('/ebook-view') }}">
                                        <i class="mdi mdi-clipboard-text-outline menu-icon"></i>
                                        <span class="menu-title">Ebook </span>
                                    </a>
                                </li>
                            @endcan
                        @else
                            @if(Auth()->user()->agency_fk != "")
                                @if(!in_array(auth()->user()->id,Utility::agencyPortalRolePermission()))
                                    <li class="nav-item agency-logo  {{ request()->is('ebook-view') ? 'active' : '' }}">
                                        <a class="nav-link" href="{{ url('/ebook-view') }}">
                                            <i class="mdi mdi-clipboard-text-outline menu-icon"></i>
                                            <span class="menu-title">Ebook </span>
                                        </a>
                                    </li>
                                @endif
                            @endif
                        @endif
                        @if(auth()->user()->user_type_fk ==184)
                            @can('flagged-menu')
                                <li class="nav-item @if(isset($agencyObj) && $agencyObj->id != null) agency-logo @endif">
                                    <a class="nav-link" href="{{ url('flag-list')}}">
                                        <i class="mdi mdi-flag menu-icon"></i>
                                        <span class="menu-title">Flagged</span>
                                    </a>
                                </li>
                            @endcan
                        @else
                            @if(Auth()->user()->agency_fk != "")
                                @if(!in_array(auth()->user()->id,Utility::agencyPortalRolePermission()))
                                    <li class="nav-item @if(isset($agencyObj) && $agencyObj->id != null) agency-logo @endif">
                                        <a class="nav-link" href="{{ url('flag-list')}}">
                                            <i class="mdi mdi-flag menu-icon"></i>
                                            <span class="menu-title">Flagged</span>
                                        </a>
                                    </li>
                                @endif
                            @endif
                        @endif


                        @can('doc-dashboard')
                            <li class="nav-item ">
                                <a class="nav-link"  href="{{ url('document-dashboard')}}">
                                <i class="mdi mdi-file menu-icon"></i>
                                    <span class="menu-title">Document Dashboard</span>
                                </a>
                            </li>
                        @endcan
                        @can('hub-record-flagged-menu')
                                <li class="nav-item @if(isset($agencyObj) && $agencyObj->id != null) agency-logo @endif">
                                    <a class="nav-link" href="{{ url('hub-flag-list')}}">
                                        <i class="mdi mdi-flag menu-icon"></i>
                                        <span class="menu-title">Hub Record Flagged</span>
                                    </a>
                                </li>
                            @endcan
                          @can('kiosk-admin')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('kiosk/admin/appointments')}}">
                                    <i class="mdi mdi-clipboard-text-outline menu-icon"></i>
                                    <span class="menu-title">Kiosk</span>
                                </a>
                            </li>
                        @endcan
                        @can('ai-call-logs')
                         @if(Auth()->user()->agency_fk == "")
                        <li class="nav-item {{ request()->is('ai-call-logs*') ? 'active' : '' }} agency-logo">
                            <a class="nav-link" href="{{ url('ai-call-logs')}}">
                                <i class="mdi mdi-robot menu-icon"></i>
                                <span class="menu-title">AI Call Logs</span>
                            </a>
                        </li>
                        @endif
                        @endcan
                         
                        @canany(['poc-mapping-list','api-log-report-list','audit-log-report','inflowcare-patient-log-report','expiring-medical','hha-audit-log-list','agency-task-health-setting','task-health-cron-log-list','esign-patient-dashboard','task-health-cron-log-list','hha-log'])
                        <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="mdi mdi-clipboard-text-outline menu-icon"></i>
                                    <span class="menu-title">Developer</span>
                                    <i class="menu-arrow"></i>
                                </a>
                                <div class="submenu">
                                    <ul class="submenu-item">

                                        @can('api-log-report-list')
                                        <li class="nav-item {{ request()->is('api-log-report') ? 'active' : '' }}">
                                            <a class="nav-link" href="{{ url('api-log-report') }}">
                                                <span class="menu-title">API call log Report</span>

                                            </a>
                                        </li>
                                        @endcan
                                        @can('audit-log-report')
                                            <li class="nav-item {{ request()->is('audit-log-report') ? 'active' : '' }}">
                                                <a class="nav-link" href="{{ url('audit-log-report') }}">
                                                    <span class="menu-title">Audit Log Report</span>
                                                </a>
                                            </li>
                                        @endcan

                                        @can('poc-mapping-list')
                                            <li class="nav-item {{ request()->is('hha/poc-mapping*') ? 'active' : '' }}">

                                                <a class="nav-link" href="{{ url('hha/poc-mapping')}}">
                                                    <span class="menu-title">POC Mapping</span>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('inflowcare-patient-log-report')
                                            <li class="nav-item {{ request()->is('inflowcare-patient-logs') ? 'active' : '' }}">
                                                <a class="nav-link" href="{{ url('inflowcare-patient-logs') }}">

                                                    <span class="menu-title">Inflowcare Patient Log Report</span>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('expiring-medical')
                                        <li class="nav-item {{ request()->is('expiring-medical') ? 'active' : '' }}">
                                            <a class="nav-link" href="{{ url('expiring-medical') }}">
                                                <span class="menu-title">Expiring Medical Next 10 Days</span>
                                            </a>
                                        </li>
                                        @endcan
                                        @can('task-health-log-list')
                                            <li class="nav-item {{ request()->is('task-health-log') ? 'active' : '' }}">
                                                <a class="nav-link" href="{{ url('task-health-log') }}">

                                                    <span class="menu-title">Task Health Log</span>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('hha-audit-log-list')
                                            <li class="nav-item {{ request()->is('hha-audit-log') ? 'active' : '' }}">
                                                <a class="nav-link" href="{{ url('hha-audit-log') }}">

                                                    <span class="menu-title">HHA Audit Log</span>
                                                </a>
                                            </li>
                                        @endcan
                                        
                                        @can('task-health-cron-log-list')
                                            <li class="nav-item {{ request()->is('task-health-cron-log') ? 'active' : '' }}">
                                                <a class="nav-link" href="{{ url('task-health-cron-log') }}">

                                                    <span class="menu-title">Task Health Cron Log</span>
                                                </a>
                                            </li>
                                        @endcan
                                        
                                        @can('hha-log')
                                            <li class="nav-item {{ request()->is('hha/hha-send-log') ? 'active' : '' }}">
                                                <a class="nav-link" href="{{ url('hha/hha-send-log') }}">

                                                    <span class="menu-title">HHA Log</span>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('alayacare-log')
                                            <li class="nav-item {{ request()->is('alayacare/alayacare-cron-log') ? 'active' : '' }}">
                                                <a class="nav-link" href="{{ url('alayacare/alayacare-cron-log') }}">

                                                    <span class="menu-title">Alayacare Log</span>
                                                </a>
                                            </li>
                                        @endcan
                                    </ul>
                                </div>

                            </li>
                        @endcan
                        @if(Auth()->user()->agency_fk != "" && Auth()->user()->login_type_fk == 2 && Auth()->user()->role_access == 1)
                            @if(!in_array(auth()->user()->id,Utility::agencyPortalRolePermission()))
                                <li class="nav-item agency-logo {{ request()->is('agency-setting') ? 'active' : '' }}">
                                    <a class="nav-link"  href="{{ url('agency-setting')}}"><i class="fa fa-cog menu-icon"></i><span class="menu-title">Setting</span></a>
                                </li>
                            @endif
                        @endif

                        @if(Auth()->user()->agency_fk != "" && Auth()->user()->login_type_fk == 2)
                            @if(!in_array(auth()->user()->id,Utility::agencyPortalRolePermission()))
                                <li class="nav-item  agency-logo {{ request()->is('document-report') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ url('document-report') }}">
                                        <i class="mdi mdi-file-document-box menu-icon"></i><span class="menu-title">Document Report</span>
                                    </a>
                                </li>
                            @endif
                        @endif

                        @if(Auth()->user()->agency_fk != "" && Auth()->user()->login_type_fk == 2)
                            @if(!in_array(auth()->user()->id,Utility::agencyPortalRolePermission()))
                                <li class="nav-item agency-logo {{ request()->is('account/agency/invoices*') ? 'active' : '' }}">
                                    <a href="{{ url('account/agency/invoices') }}" class="nav-link">
                                        <i class="mdi mdi-receipt menu-icon"></i><span class="menu-title">Invoices</span>
                                    </a>
                                </li>
                            @endif
                        @endif
                        @if(auth()->user()->can('agency-file-manager') || (Auth()->user()->agency_fk != "" && isset($agencyObj) && $agencyObj != null && $agencyObj->enable_file_manager == 1))
                            <li class="nav-item agency-logo {{ request()->is('file-manager*') ? 'active' : '' }}">
                                <a href="{{ url('file-manager') }}" class="nav-link">
                                    <i class="mdi mdi-folder menu-icon"></i><span class="menu-title">File Manager</span>
                                </a>
                            </li>
                        @endif

                        @if(auth()->user()->agency_fk != "" && $agencyObj->view_payment_report != 0)
                            <li
                                class="nav-item agency-logo {{ request()->is('payment-type-report') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ url('payment-type-report') }}">
                                    <i class="mdi mdi-file-document-box menu-icon"></i><span class="menu-title">Payment Type Report</span>
                                </a>
                            </li>
                        @endif

                    </ul>
                </div>
                @if(auth()->user()->agency_fk != "")
                <div class="mr-3">
                    <a data-toggle="modal" data-target="#exampleModal-show-images"><img alt="" src="{{ asset('image_log.png')}}" style="height: 64px;margin-top: 5px;margin-bottom: 5px;"></a>
                </div>
                @endif
            </nav>
        </div>

        <!-- partial:partials/_navbar.html -->


        <!-- partial -->
        <script>
            $('a[data-notif-id]').click(function() {

                var notif_id = $(this).data('notifId');
                var targetHref = $(this).data('href');
                $.ajax({
                    // async: false,
                    global: false,
                    type: "GET",
                    url: "<?php echo URL::to('/'); ?>/NotifMarkAsRead123455",
                    data: {
                        'notif_id': notif_id
                    },
                    succes: function(response) {
                        if (response == 'success') {
                            window.location.href = targetHref;
                        } else {
                            alert("Sorry, something went wrong. Please try again.");
                            return false;
                        }
                    }

                })
            });

            function openLocation() {
                $("#location-right-sidebar").toggleClass("open");
            }


            function closelocationSearch() {
                $("#location-right-sidebar").removeClass("open");
                $("#right-panel-ship-address").val("");
                $(".list-wrapper").val("");
                $(".list-wrapper").html("");
            }
            $('.settings-close').on('click', function() {
                closelocationSearch();
            })
            $('.reset-location-search').on('click', function() {
                $("#right-panel-ship-address").val("");
                $(".list-wrapper").html("");
            })
        </script>