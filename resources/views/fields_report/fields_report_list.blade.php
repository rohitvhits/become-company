@include('include/header')
 @include('include/sidebar')
 <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2/select2.min.css">
 <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
 <link href="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
 <style type="text/css">
     #order-listing_length,
     #order-listing_paginate,
     #order-listing_info {
         display: none;
     }

     #order-listing_filter {
         text-align: right;
     }

     ..select2-container {
         width: 200px !important;
     }

     .wmd-view-topscroll,
     .wmd-view {
         overflow-x: scroll;
         overflow-y: hidden;
         border: none 0px red;
     }

     .wmd-view-topscroll {
         height: 20px;
     }

     .scroll-div1 {

         overflow-x: scroll;
         overflow-y: hidden;
         height: 20px;
     }

     .scroll-div2 {
         height: 20px;
     }

     .scroll-div1,
     .scroll-div2 {
         width: 2000px;
     }

 </style>
 <div class="main-panel">

     <div class="content-wrapper">
         <div class="col-12 grid-margin-top">
             @if (Session::has('success'))
                 <div class="alert alert-success alert-dismissible fade show" role="alert">
                     <strong>{{ Session::get('success') }}</strong>
                     <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                         <span aria-hidden="true">×</span>
                     </button>
                 </div>
             @endif
             @if (Session::has('error'))
                 <div class="alert alert-warning alert-dismissible fade show" role="alert">
                     <strong>{{ Session::get('error') }}</strong>
                     <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                         <span aria-hidden="true">×</span>
                     </button>
                 </div>
             @endif
         </div>
         <div class="card">
             <div class="row list-name">
                 <div class="col-sm-5">
                     <h4 class="card-title">Fields Records Report List (<span id="total_record"></span>)</h4>
                 </div>
                 <div class="col-sm-7 pull-right">
                     <!--<a href="javascript:void(0)" onclick="getArchive()" class="btn btn-info btn-fw btn-sm pull-right"><i class="mdi mdi-reload"></i>Patient Archive</a>-->
                     <a href="javascript:void(0)" class="btn btn-success pull-right btn-fw btn-sm" id="test_record"><i
                             class="mdi mdi-file-export"></i>Export</a>
                     <a href="<?php echo URL::to('/'); ?>/fileds-report" class="btn btn-danger pull-right btn-fw btn-sm"><i
                             class="mdi mdi-reload"></i> Reset</a>
                    
                 </div>
             </div>

             <div class="card-body compact-view">
                 <div class="row">
                     <div class="col-12">
                         <span id="record_list_id"></span>
                         <input type="hidden" name="" id="fields" value="id">
                         <input type="hidden" name="" id="sort" value="desc">
                     </div>
                 </div>
             </div>
         </div>

         <div class="modal fade" id="modal-default" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
             aria-hidden="true">
             <div class="modal-dialog modal-lg" role="document">
                 <div id="messages_id"></div>
             </div>
         </div>

         <div class="modal fade" id="modal-default-patient" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
             aria-hidden="true">
             <div class="modal-dialog" role="document">
                 <div class="modal-content">
                     <div class="modal-header">
                         <h5 class="modal-title" id="exampleModalLabel">Patient </h5>
                         <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                             <span aria-hidden="true">×</span>
                         </button>
                     </div>

                     <div class="modal-body">
                         <form action="" method="post" enctype="multipart/form-data" id="patient_record_submit">
                             <input type="hidden" name="patient_record_id" id="patient_record_id">
                             <div class="form-group">
                                 <label for="recipient-name" class="col-form-label">Type<span
                                         style="color:red">*</span>:</label>
                                 <div class="col-sm-8">
                                     <input type="radio" name="radios" value="Caregiver"
                                         onclick="getResponse('Caregiver')">Caregiver
                                     <input type="radio" name="radios" value="Patient"
                                         onclick="getResponse('Patient')">Patient
                                 </div>
                                 <span id="radios_error" style="color:red"></span>
                             </div>
                             <div class="form-group">
                                 <label for="recipient-name" class="col-form-label">Services<span
                                         style="color:red">*</span>:</label>
                                 <div class="col-sm-8">
                                     <select name="service_id[]" id="service_id" class="js-example-basic-multiple w-100"
                                         multiple="multiple">
                                         <option value="">Select</option>
                                     </select>
                                     <span id="service_id_error" style="color:red"></span>
                                 </div>
                             </div>
                         </form>
                     </div>

                     <div class="modal-footer">
                         <button type="button" class="btn btn-success" onclick="getPatientSRecord()">Submit</button>
                         <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                     </div>
                 </div>
             </div>
         </div>

         @include('include/footer')
         <script src="<?php echo URL::to('/'); ?>/assets/vendors/select2/select2.min.js"></script>
         <script src="<?php echo URL::to('/'); ?>/assets/js/select2.js"></script>
         <script src="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.js"></script>
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
                 "showDuration": "300",
                 "hideDuration": "500",
                 "timeOut": "3000",
                 "extendedTimeOut": 0,
                 "showEasing": "swing",
                 "hideEasing": "linear",
                 "showMethod": "fadeIn",
                 "hideMethod": "fadeOut",
                 "tapToDismiss": false
             };

             function validation() {

             }

             function ajaxList(page) {
                 var agency_fk = $('#agency_fk').val();
                 var name = $('#name').val();
                 var email = $('#email').val();
                 var phone = $('#phone').val();
                 var emc_user_id = $('#emc_user_id').val();
                 var medicaid_issue = $('#medicaid_issue').val();
                 var record_form = $('#record_form').val();
                 var cin_id = $('#cin_id').val();
                 var follow_date = $('#follow_date').val();
                 var filed_date = $('#filed_date').val();
                 var patient_status = $('#patient_status').val();
                 var created_by_id = $('#created_by_id').val();
                 var created_date = $('#created_date').val();
                 var field = $('#fields').val();
                 var sort = $('#sort').val();

                 $('.order-listing-loader').attr('style', 'display:flex');
                 $.ajax({
                     type: "GET",
                     url: "{{ url('/fileds-report/fileds-report-ajax') }}?page=" + page,
                     data: {
                         'agency_fk': agency_fk,
                         'name': name,
                         'email': email,
                         'phone': phone,
                         'emcuser': emc_user_id,
                         'medicaid_issue': medicaid_issue,
                         'record_form': record_form,
                         'cin_id': cin_id,
                         'follow_date': follow_date,
                         'filed_date': filed_date,
                         'patient_status': patient_status,
                         'created_date': created_date,
                         'created_by': created_by_id,
                         'field': field,
                         'sort': sort

                     },
                     success: function(res) {
                         $('.order-listing-loader').attr('style', 'display:none');
                         $('#record_list_id').html("");
                         $('#record_list_id').html(res);
                     }
                 })
                 return false;
             }
             ajaxList(1);
             $('body').on('click', '#searchid', function(e) {
                 ajaxList(1);
             })
             $('body').on('click', '.record_id', function(e) {
                 var fields = $(this).attr('data-field');
                 var sort = $(this).attr('data-sort');

                 $('#fields').val(fields);
                 $('#sort').val(sort);
                 ajaxList(1, fields, sort);
             })
             $(document).on('click', '.pagination a', function(event) {
                 $('li').removeClass('active');
                 $(this).parent('li').addClass('active');
                 event.preventDefault();
                 var myurl = $(this).attr('href');
                 var page = $(this).attr('href').split('page=')[1];
                 ajaxList(page);
             });

             /*vishal d patel code end chat message listing*/
             $('#test_record').click(function(e) {
                 var agency_fk = $('#agency_fk').val();
                 var name = $('#name').val();
                 var email = $('#email').val();
                 var phone = $('#phone').val();
                 var emc_user_id = $('#emc_user_id').val();
                 var medicaid_issue = $('#medicaid_issue').val();
                 var record_form = $('#record_form').val();
                 var cin_id = $('#cin_id').val();
                 var follow_date = $('#follow_date').val();
                 var filed_date = $('#filed_date').val();
                 var patient_status = $('#patient_status').val();
                 var created_by_id = $('#created_by_id').val();
                 var created_date = $('#created_date').val();
                 var field = $('#fields').val();
                 var sort = $('#sort').val();

                 $('.order-listing-loader').attr('style', 'display:flex');
                 $.ajax({
                     type: "GET",
                     url: "{{ url('/fileds-report/export') }}",
                     xhrFields: {
                         responseType: 'blob'
                     },
                     data: {
                         'agency_fk': agency_fk,
                         'name': name,
                         'email': email,
                         'phone': phone,
                         'emcuser': emc_user_id,
                         'medicaid_issue': medicaid_issue,
                         'record_form': record_form,
                         'cin_id': cin_id,
                         'follow_date': follow_date,
                         'filed_date': filed_date,
                         'patient_status': patient_status,
                         'created_date': created_date,
                         'created_by': created_by_id,
                         'field': field,
                         'sort': sort


                     },
                     success: function(res) {
                         $('.order-listing-loader').attr('style', 'display:none');
                         var blob = new Blob([res]);
                         var link = document.createElement('a');
                         link.href = window.URL.createObjectURL(blob);

                         link.download = "FieldsReport.csv";
                         link.click();
                     }
                 })

             })
         </script>

         <script type="text/javascript" src="<?php echo URL::to('/'); ?>/assets/js/moment.min.js"></script>
         <script type="text/javascript" src="<?php echo URL::to('/'); ?>/assets/js/daterangepicker.min.js"></script>
         <link rel="stylesheet" type="text/css" href="<?php echo URL::to('/'); ?>/css/daterangepicker.css" />

         <script>
             $('.datepicker').datepicker();
             $(function() {
                 var start = moment().subtract(0, 'days');
                 var end = moment();
                 $('.datepickernn').daterangepicker({
                     startDate: start,
                     endDate: end,
                     autoUpdateInput: false,
                     startOfWeek: 'sunday',
                     ranges: {
                         'Today': [moment(), moment()],
                         'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                         'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                         'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                         'This Month': [moment().startOf('month'), moment().endOf('month')],
                         'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                             'month').endOf('month')],
                         'Next Month': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month')
                             .endOf('month')
                         ],
                         'Next Week': [moment().add(1, 'weeks').startOf('isoWeek'), moment().add(1, 'weeks')
                             .endOf('isoWeek')
                         ],
                         'Last Week': [moment().subtract(1, 'weeks').startOf('isoWeek'), moment().subtract(1,
                             'weeks').endOf('isoWeek')],
                     }
                 }, function(chosen_date, end_date) {

                     $('.datepickernn').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
                         'MM/DD/YYYY'));
                 })

                 $('.datepicker1').daterangepicker({
                     startDate: start,
                     endDate: end,
                     autoUpdateInput: false,
                     startOfWeek: 'sunday',
                     ranges: {
                         'Today': [moment(), moment()],
                         'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                         'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                         'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                         'This Month': [moment().startOf('month'), moment().endOf('month')],
                         'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                             'month').endOf('month')],
                         'Next Month': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month')
                             .endOf('month')
                         ],
                         'Next Week': [moment().add(1, 'weeks').startOf('isoWeek'), moment().add(1, 'weeks')
                             .endOf('isoWeek')
                         ],
                         'Last Week': [moment().subtract(1, 'weeks').startOf('isoWeek'), moment().subtract(1,
                             'weeks').endOf('isoWeek')],

                     }
                 }, function(chosen_date, end_date) {

                     $('.datepicker1').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
                         'MM/DD/YYYY'));
                 })
                 $('.datepicker_date').daterangepicker({
                     startDate: start,
                     endDate: end,
                     autoUpdateInput: false,
                     startOfWeek: 'sunday',
                     ranges: {
                         'Today': [moment(), moment()],
                         'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                         'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                         'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                         'This Month': [moment().startOf('month'), moment().endOf('month')],
                         'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                             'month').endOf('month')],
                         'Next Month': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month')
                             .endOf('month')
                         ],
                         'Next Week': [moment().add(1, 'weeks').startOf('isoWeek'), moment().add(1, 'weeks')
                             .endOf('isoWeek')
                         ],
                         'Last Week': [moment().subtract(1, 'weeks').startOf('isoWeek'), moment().subtract(1,
                             'weeks').endOf('isoWeek')],

                     }
                 }, function(chosen_date, end_date) {

                     $('.datepicker_date').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
                         'MM/DD/YYYY'));
                 })

             });
             // Binds the hidden input to be used as datepicker.
             $('.datepicker-input').datepicker({
                 dateFormat: 'mm/dd/yy',
                 onClose: function(dateText, inst) {
                     // When the date is selected, copy the value in the content editable div.
                     // If you don't need to do anything on the blur or focus event of the content editable div, you don't need to trigger them as I do in the line below.
                     if (dateText != '') {
                         $(this).parent().find('.date').focus().html(dateText).blur();
                     }
                 }

             });
            

         </script>
         <script type="text/javascript">
             $(function() {
                 $(".wmd-view-topscroll").scroll(function() {
                     $(".wmd-view")
                         .scrollLeft($(".wmd-view-topscroll").scrollLeft());
                 });
                 $(".wmd-view").scroll(function() {
                     $(".wmd-view-topscroll")
                         .scrollLeft($(".wmd-view").scrollLeft());
                 });
             });
             $("#main_checkBox1").click(function() {
                 var names = $("#main_checkBox1").is(":checked");

                 if (names == true) {
                     $('.cbox_id').prop('checked', true);
                 } else {
                     $('.cbox_id').prop('checked', false);
                 }
             });

             

             
         </script>
