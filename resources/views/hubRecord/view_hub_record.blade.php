
@include('include/sidebar')
<head>
    <title>NY BEST MEDICAL</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.7.95/css/materialdesignicons.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.7.95/css/materialdesignicons.css.map">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.7.95/css/materialdesignicons.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.7.95/css/materialdesignicons.min.css.map">

<link rel="stylesheet" href="<?= URL::to('assets/css/horizontal-default-light/style.css') ?>">
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/fullcalendar/fullcalendar.min.css">
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css">
<link href="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2/select2.min.css">
<link rel="stylesheet"
    href="<?php echo URL::to('/'); ?>/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
<link href="{{ asset('assets/css/tribute.css') }}" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/modulejs/css/patient-new-design.css?time={{ env('timestamp')}}">
<link href="<?php echo URL::to('/'); ?>/assets/bootstrap-datetimepicker.min.css" type="text/css" media="all"
    rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('css/jquery.fancybox.min.css')}}" />  
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/jquery-confirmation/css/jquery-confirm.min.css">  
<link rel="shortcut icon" href="<?= URL::to('img/favicon.png') ?>" />
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<style>
.created-label-margin{
    margin-bottom:-10px !important;
}
.div-top-margin{
    /* margin-top:-6px !important; */
}

.ds-card {
  background: #d7ffd8;
  padding: 20px;
  border-radius: 16px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  display: flex;
  align-items: center;
  transition: 0.3s ease;
}

.ds-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
}

.icon {
  font-size: 32px;
  width: 60px;
  height: 60px;
  border-radius: 50%;
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 15px;
}

.details h3 {
  font-size: 16px;
  margin: 0;
  color: #555;
}

.details p {
  font-size: 20px;
  font-weight: bold;
  margin: 5px 0 0;
  color: #111;
}
.bg-orange { background-color: #f39c12; }
.left-section-tab-content {
    max-width: 100%;
}
    </style>

   
<!--main-container-part-->
<div class="main-panel view-appointmenr-main">
    <div class="content-wrapper px-3 pb-0">
        <div class="dashboard-header d-flex flex-column ">
            <div class="basic-detail-sec border-bottom  mb-3 card">
                <div class="d-flex align-items-center justify-content-between flex-wrap   mb-2">
                    <div class="d-flex align-items-center">
                        <h4 class="mb-0 font-weight-bold mr-1">ID #
                            <?= $record->id . ' - ' . ucwords($record->first_name) . ' ' . ucwords($record->last_name) . ' ' ?>
                        </h4>
                        
                    </div>
                    <div class="appoin-btn-wrapper">
                     
                    </div>
                </div>
                <div class="top-detail-sec">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="top-basic-detail-sec">
                                <div class="">
                                    <div class="">
                                        <div class="col-md-12">
                                            <dt class="detail-title mb-2">Gender</dt>
                                        </div>
                                        <div class="col-md-12">
                                            <dl>
                                                <?php if (isset($record->gender) && $record->gender != '') {
                                                    $otherName = "";
                                                    if ($record->gender == 'other') {
                                                        $otherName = " (" . $record->other_gender . ")";
                                                    }
                                                    echo ucfirst($record->gender) . $otherName . '<br>';
                                                } else {
                                                    echo 'N/A';
                                                } ?>
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                                <div class="">
                                    <div class="">
                                        <div class="">
                                            <div class="">
                                                <div class="col-md-12">
                                                    <dt class="detail-title  mb-2">Mobile</dt>
                                                </div>
                                                <div class="col-md-12">
                                                    <dl>
                                                        <span id="hub_mobile_id"><?php echo preg_replace('/(\d{3})(\d{3})(\d{4})/', '($1) $2-$3', $record->mobile); ?></span>
                                                     
                                                    </dl>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="">
                                    <div class="">
                                        <div class="col-md-12">
                                            <dt class="detail-title mb-2">Phone</dt>
                                        </div>
                                        <div class="col-md-12">
                                            <dl>
                                                <span id="hub_phone_id"><?php echo preg_replace('/(\d{3})(\d{3})(\d{4})/', '($1) $2-$3', $record->phone); ?></span>
                                                <input type="hidden" name="hub_agency_id" id="hub_agency_id" value="{{ $record->agency_id }}">
                                                
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                                <div class="">
                                   
                                </div>
                               
                            </div>
                           
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-sm-12 grid-margin stretch-card mb-4" style="margin-bottom:10% !important">
                        <div class="card">
                            <div class="left-section-main info-tab-sec">
                                
                                <!-- Tab panes -->
                                <div class="tab-content left-section-tab-content">
                                    <div class="tab-pane active" id="personal-info-section">
                                        <div class="row">
                                            
                                            <div class="col-lg-12">
                                             
                                                <div class="">
                                                    <div class="title ">
                                                        <h5><i class="fa fa-list-alt mr-1"></i> Dependent records <a class="show pull-right" onclick="loadDependentData(1)"><i class="fa fa-refresh mr-1" title="Refresh"></i></a> <a class="show pull-right" style="margin-right: 10px;" onclick="openAddChildForm()"><i class="fa fa-plus mr-1" title="Add"></i>Add New</a></h5>
                                                    </div>
                                                    <div class="">
                                                        <div id="child_table">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
    </div>
</div>
@include('hubRecord/modal/hub_record_dependents_add')
@include('hubRecord/modal/hub_record_dependents_edit')
<!-- base:js -->
<script src="<?= URL::to('assets/vendors/js/vendor.bundle.base.js') ?>"></script>
<!-- endinject -->
<!-- Plugin js for this page-->
<script src="<?= URL::to('assets/vendors/jquery.flot/jquery.flot.js') ?>"></script>
<script src="<?= URL::to('assets/vendors/jquery.flot/jquery.flot.pie.js') ?>"></script>
<script src="<?= URL::to('assets/vendors/jquery.flot/jquery.flot.resize.js') ?>"></script>
<script src="<?= URL::to('assets/vendors/jqvmap/jquery.vmap.min.js') ?>"></script>
<script src="<?= URL::to('assets/vendors/jqvmap/maps/jquery.vmap.world.js') ?>"></script>
<script src="<?= URL::to('assets/vendors/jqvmap/maps/jquery.vmap.usa.js') ?>"></script>
<script src="<?= URL::to('assets/vendors/peity/jquery.peity.min.js') ?>"></script>
<script src="<?= URL::to('assets/js/jquery.flot.dashes.js') ?>"></script>
<script src="<?= URL::to('assets/js/jquery-ui.min.js') ?>"></script>
<!-- End plugin js for this page-->
<script>
    
    var _RECORD_ID = "{{$record->id}}";
    var _CSRF_TOKEN = '{{ csrf_token() }}';
    var _MOBILE = "{{ $record->mobile }}";
    var CREATED_USER_NAME = "";
    var GET_BASIC_DETAILS = "{{ url('hub-get-basic-details') }}";
    var GET_DEPENDENT_DATA = "{{ url('hub-dependent-records') }}/{{ $record->id }}";
    var _SAVE_HUB_DEPENDENT_DETAILS = "{{ url('hub-dependent-save')}}";
    var _UPDATE_HUB_DEPENDENT_DETAILS = "{{ url('hub-dependent-update')}}";
    
</script>
<script type="text/javascript" src="{{ asset('assets/js/jquery.tokeninput.js')}}"></script>
<link rel="stylesheet" href="{{ asset('assets/css/token-input.css')}}" type="text/css" />
<script src="{{ asset('assets/vendors/moment/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery-ui.min.js')}}"></script>
<script src="{{ asset('assets/css/toastr/toastr.min.js')}}"></script>
<script src="{{ asset('assets/vendors/inputmask/jquery.inputmask.bundle.js')}}"></script>
<script src="{{ asset('assets/vendors/select2/select2.min.js')}}"></script>
<script src="{{ asset('assets/js/select2.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/daterangepicker.min.js')}}"></script>
<script src="{{ asset('assets/js/tribute.js')}}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('css/daterangepicker.css')}}" />
<script src="<?php echo URL::to('/'); ?>/assets/jquery-confirmation/js/jquery-confirm.min.js"></script>
<script type="text/javascript" src="{{ asset('assets/modulejs/hub_record/hub_record_dependent.js')}}?time={{ time()}}"></script>
<script>
    $(":input").inputmask();

    $(document).ready(function() {
        $('ul.left-section-ul li').click(function() {
            $('ul.left-section-ul li').removeClass('active');
            $(this).addClass('active');
        })

        $('ul.right-section-ul li').click(function() {
            $('ul.right-section-ul li').removeClass('active');
            $(this).addClass('active');

        })

        $('.fancybox').fancybox({
            toolbar: false,
            smallBtn: true,
            iframe: {
                preload: false
            }
        })
    })
</script>
<script src="{{ asset('assets/js/jquery.fancybox.min.js')}}"></script>