@include('include/header')
 @include('include/sidebar')

 <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2/select2.min.css">
 <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
 <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
 <link href="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
 <style>
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
         width: 1400px;
     }

     .table-head-fix tbody {
         display: block;
         max-height: calc(100vh - 350px);
         overflow-y: scroll;
     }

     .table-head-fix tbody::-webkit-scrollbar {
         width: 0;
         height: 0;
     }

     .table-head-fix thead,
     .table-head-fix tbody tr {
         display: table;
         width: 100%;
     }

     
     .recordtabletdwidth th:nth-child(1),
     .recordtabletdwidth td:nth-child(1) {
         min-width: 40px;
         max-width: 40px;
         width: 40px;
     }

     .recordtabletdwidth th:nth-child(2),
     .recordtabletdwidth td:nth-child(2) {
         min-width: 80px;
         max-width: 80px;
         width: 80px;
     }

     .recordtabletdwidth th:nth-child(3),
     .recordtabletdwidth td:nth-child(3) {
         min-width: 100px;
         max-width: 100px;
         width: 100px;
     }

     .recordtabletdwidth th:nth-child(4),
     .recordtabletdwidth td:nth-child(4) {
         min-width: 180px;
         max-width: 180px;
         width: 180px;
     }

     .recordtabletdwidth th:nth-child(5),
     .recordtabletdwidth td:nth-child(5) {
         min-width: 160px;
         max-width: 160px;
         width: 160px;
     }

     .recordtabletdwidth th:nth-child(6),
     .recordtabletdwidth td:nth-child(6) {
         min-width: 140px;
         max-width: 140px;
         width: 140px;
     }

     .recordtabletdwidth th:nth-child(7),
     .recordtabletdwidth td:nth-child(7) {
         min-width: 150px;
         max-width: 150px;
         width: 150px;
         overflow: hidden;
     }

     .recordtabletdwidth th:nth-child(8),
     .recordtabletdwidth td:nth-child(8) {
         min-width: 160px;
         max-width: 160px;
         width: 160px;
     }

     .recordtabletdwidth th:nth-child(9),
     .recordtabletdwidth td:nth-child(9) {
         min-width: 110px;
         max-width: 110px;
         width: 110px;
     }

     .recordtabletdwidth th:nth-child(10),
     .recordtabletdwidth td:nth-child(10) {
         min-width: 150px;
         max-width: 150px;  
         width: 150px;
     }

     .recordtabletdwidth th:nth-child(11),
     .recordtabletdwidth td:nth-child(11) {
         min-width: 100px;
         max-width: 100px;  
         width: 100px;
     }
     .recordtabletdwidth th:nth-child(12),
     .recordtabletdwidth td:nth-child(12) {
         min-width: 100px;
         max-width: 100px;  
         width: 100px;
     }
     .sorting-btn {
         display: flex;
         flex-direction: column;
         margin-left: auto;
     }

     .sorting-div {
         display: flex;
         align-items: center;
     }

     .sorting-btn button {
         padding: 0;
         margin: 0;
         border: 0;
         background: transparent;
         line-height: 0.5;
     }

     .sorting-btn button i {
         line-height: 0.3;
     }

     .order-listing-loader {
         position: absolute;
         left: 0;
         top: 0;
         background: #ffffff94;
         bottom: 0;
         right: 0;
         width: 100%;
         font-size: 30px;
         display: none;
         align-items: center;
         justify-content: center;

     }

     .page-title-main {
         display: flex;
         justify-content: space-between;
         align-items: center;
         margin-bottom: 20px;
     }
 </style>
 <div class="main-panel">
     <?php
     $auth = auth()->user();
     ?>
     <div class="content-wrapper">
         <div class="page-title-main">
             <h5 class="mb-0 font-weight-bold">Expiring Medical Next 10 Days (<span id="appointment_id"></span>)</h5>
             <div class="page-rightbtns">
                 <div class="row">
                    <div class="col-md-4">
                        <select class="form-control" id="selectedAgencyId">
                            <option value="">Select Agency</option>
                            @foreach($agency_list as $agency)
                            <option value="{{ $agency->id}}">{{ $agency->agency_name}}</option>
                            @endforeach
                        </select>   
                    </div>
                    <div class="col-md-4">
                        <select class="form-control" id="selectedStatusId">
                            <option value="">Select Status</option>
                            <option value="Pending">Pending</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="booked">Booked</option>
                            <option value="completed">Completed</option>
                            <option value="noshow">No Show</option>
                            <option value="arrived">Arrived</option>

                            <option value="processing">Processing</option>
                            <option value="Not interested">Not Interested</option>
                            <option value="hospitalized/rehab">Hospitalized/Rehab</option>
                            <option value="unableToContact">Unable To Contact</option>
                            <option value="refused">Refused</option>
                            <option value="checkin">Mark as CheckIn</option>

                        </select>   
                    </div>
                    <div class="col-md-4" style="display:none" id="show_export">
                    <a  class="btn btn-success btn-rounded btn-sm btn-fw ml-1" id="test_agency" onclick="export_data()"><i class="mdi mdi-file-export"></i>Export</a>
                    </div>
                 
                     
                     
                 </div>
             </div>
         </div>
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
             <div class="card-body compact-view">
                 <div class="row">
                     <div class="col-12">
                         <div class="wmd-view-topscroll">
                             <div class="scroll-div1">
                             </div>
                         </div>
                         <div class="wmd-view">
                             <div class="scroll-div2">
                                 <span id="resp"></span>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
     </div>
     <div class="row" style='margin-top: 25px;'>
         <pre id='toastrOptions'></pre>
     </div>
     @include('include/footer')

     <script src="<?= URL::to('/js/jquery.min.js') ?>"></script>
     <link rel="stylesheet" href="<?= URL::to('/css/jquery-ui.css') ?>">
     <script src="<?= URL::to('assets/js/jquery-ui.min.js') ?>"></script>
     <script type="text/javascript" src="<?php echo URL::to('/'); ?>/assets/js/moment.min.js"></script>
     <script type="text/javascript" src="<?php echo URL::to('/'); ?>/assets/js/daterangepicker.min.js"></script>
     <link rel="stylesheet" type="text/css" href="<?php echo URL::to('/'); ?>/css/daterangepicker.css" />
     <script src="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.js"></script>

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

         function hhaAppoitnemtList(page) {
             var agency_id = $('#selectedAgencyId').val();
             var status = $('#selectedStatusId').val();
            if(agency_id =='' && status ==''){
                agency_id =0;
            }
             $.ajax({
                 url: "{{ url('expiring-appointment-ajax') }}?page=" + page,
                 type: "GET",
                 data: {
                     'agency_fk': agency_id,
                     'status': status,
                   
                 },
                 success: function(res) {
                 
                     $('#resp').html("");
                     $('#resp').html(res)
                 }
             })
             return false;
         }

         $('#selectedAgencyId').change(function(e){
            $('#show_export').attr('style','display:none');
            if($('#selectedAgencyId').val() !=''){
                $('#show_export').attr('style','');
            }
            hhaAppoitnemtList(1);
         })
         hhaAppoitnemtList(1);

         $('#selectedStatusId').change(function(e){
            $('#show_export').attr('style','display:none');
            if($('#selectedStatusId').val() !='' || $('#selectedAgencyId').val() !='' ){
                $('#show_export').attr('style','');
            }
            hhaAppoitnemtList(1);
         });
         
         $('body').on('click', '.pagination a', function(event) {
             $('li').removeClass('active');
             $(this).parent('li').addClass('active');
             event.preventDefault();
             var myurl = $(this).attr('href');
             var page = $(this).attr('href').split('page=')[1];
             hhaAppoitnemtList(page);
         });

         function export_data(){
            var agency_id = $('#selectedAgencyId').val();
            var status = $('#selectedStatusId').val();
            var url = '{{ url("expiring-appointment-export") }}?agency_fk='+agency_id+'&status='+status;
            $('#test_agency').attr("href", url);
             
         }
     </script>
