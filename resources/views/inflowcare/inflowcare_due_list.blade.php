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
        
        <div class="card">
            <div class="row list-name">
                <div class="col-sm-5">
                    <h4 class="card-title">Inflowcare Due Document List (<span id="total_record"></span>)</h4>
                </div>
                
            </div>

            <div class="card-body compact-view">
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <span id="record_list_id"></span>
                        </div>
                        <div id="pagination-container"></div>
                        
                        <input type="hidden" name="" id="fields" value="id">
                        <input type="hidden" name="" id="sort" value="desc">
                    </div>
                </div>
            </div>
        </div>

    </div>


       

         @include('include/footer')
         <script src="{{ asset('js/paging.js')}}"></script>
         <script>
             $(document).ready(function() {
                
            });
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
                 var surplus1 = $('#surplus1').val();
                 var dob = $('#dob_id').val();
                 var closed_date = $('#closed_date').val();
                 var field = $('#fields').val();
                 var sort = $('#sort').val();

                 $('.order-listing-loader').attr('style', 'display:flex');
                 $.ajax({
                     type: "GET",
                     url: "{{ url('/api/v1/get-due-contact-document') }}?flag=exmedc",
                     
                     success: function(res) {
                         var jsons = JSON.parse(res);
                        var html= '';
                            html = '<table id="order-listing1" class="table table-bordered table-head-fix recordtabletdwidth">'
                                    +'<thead><tr><th style="white-space:nowrap"><span>Record</span> </th>'
                                    +'<th style="white-space:nowrap"><span>Caregiver Name</span> </th>'
                                    +'<th style="white-space:nowrap"><span>Caregiver Status</span> </th>'
                                    +'<th style="white-space:nowrap"><span>Medical Name</span> </th>'
                                    +'<th style="white-space:nowrap"><span>Document Name</span> </th>'
                                    +'<th style="white-space:nowrap"><span>Due Date</span> </th>'
                                    +'<th style="white-space:nowrap"><span>Date Performed</span> </th>'
                                    +'<th style="white-space:nowrap"><span>Result</span> </th>'
                                    +'<th style="white-space:nowrap"><span>Note</span> </th>'
                                    +'<th style="white-space:nowrap"><span>Document</span> </th>'
                                    +'<th style="white-space:nowrap"><span>Medical Status	</span> </th>'
                                    +'<th style="white-space:nowrap"><span>Sent To NY Best</span> </th></tr></thead><tbody>'
                                   
                        if(jsons.data.length !=0){
                            var jsonss = jsons.data;
                            $.each(jsonss,function(i,v){
                                html +='<tr class="list-item"><td>'+v.id+'</td><td>'+v.fname+' '+v.lname+'</td><td>'+v.caregiver_status+'</td><td>'+v.medical_name+'</td><td>'+v.document_name+'</td><td>'+v.due_date+'</td><td>'+v.date_performed+'</td><td>'+v.results+'</td><td>'+v.note+'</td><td>'+v.link+'</td><td>'+v.medical_status+'</td><td>'+v.sent_to_ny_best+'</td></tr>'
                            })
                        }else{
                            html ='<tr><td>No record available</td></tr>';
                        }
                         html +='</tbody></table>';
                         $('.order-listing-loader').attr('style', 'display:none');
                         $('#record_list_id').html("");
                        
                        $('#total_record').html(jsons.data.length);
                         $('#record_list_id').html(html);
                        
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
              
         </script>
