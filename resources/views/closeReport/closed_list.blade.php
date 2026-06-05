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
                     <h4 class="card-title">Records Report List (<span id="total_record"></span>)</h4>
                 </div>
                 <div class="col-sm-7 pull-right">
                     <!--<a href="javascript:void(0)" onclick="getArchive()" class="btn btn-info btn-fw btn-sm pull-right"><i class="mdi mdi-reload"></i>Patient Archive</a>-->
                     <a href="javascript:void(0)" class="btn btn-success pull-right btn-fw btn-sm" id="test_record"><i
                             class="mdi mdi-file-export"></i>Export</a>
                     <a href="<?php echo URL::to('/'); ?>/close-report" class="btn btn-danger pull-right btn-fw btn-sm"><i
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

         

         @include('include/footer')
         
         <script>
            

             function ajaxList(page) {
                 var agency_fk = $('#agency_fk').val();
                 var name = $('#name').val();
                 
                 var created_date = $('#closed_date').val();
                 var status = $('#status').val();
                
                 var field = $('#fields').val();
                 var sort = $('#sort').val();

                 $('.order-listing-loader').attr('style', 'display:flex');
                 $.ajax({
                     type: "GET",
                     url: "{{ url('/close-report-ajax') }}?page=" + page,
                     data: {
                         'agency_fk': agency_fk,
                         'name': name,
                         
                         'created_date': created_date,
                         'status':status,
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
                
                 var created_date = $('#closed_date').val();
                 var field = $('#fields').val();
                 var sort = $('#sort').val();
                 var status = $('#status').val();
                 $('.order-listing-loader').attr('style', 'display:flex');
                 $.ajax({
                     type: "GET",
                     url: "{{ url('/close-report-export-csv') }}",
                     xhrFields: {
                         responseType: 'blob'
                     },
                     data: {
                         'agency_fk': agency_fk,
                         'name': name,
                        'status':status,
                         'created_date': created_date,
                         
                         'field': field,
                         'sort': sort


                     },
                     success: function(res) {
                         $('.order-listing-loader').attr('style', 'display:none');
                         var blob = new Blob([res]);
                         var link = document.createElement('a');
                         link.href = window.URL.createObjectURL(blob);

                         link.download = "ClosedRecord.csv";
                         link.click();
                     }
                 })

             })
         </script>

         <script type="text/javascript" src="<?php echo URL::to('/'); ?>/assets/js/moment.min.js"></script>
         <script type="text/javascript" src="<?php echo URL::to('/'); ?>/assets/js/daterangepicker.min.js"></script>
         <link rel="stylesheet" type="text/css" href="<?php echo URL::to('/'); ?>/css/daterangepicker.css" />

         
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
