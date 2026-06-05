 @include('include/header')
 @include('include/sidebar')

 <style type="text/css">
     #order-listing_length,
     #order-listing_paginate,
     #order-listing_info {
         display: none;
     }

     #order-listing_filter {
         text-align: right;
     }

     .select2-design+.select2.select2-container.select2-container--default {
         width: 100% !important;
     }

     td {
         table-layout: fixed;
         width: 20px;
         overflow: hidden;
         word-wrap: break-word;
     }

     .table-width1 {
         background-color: #fff;
     }

     .search-inner {
         display: flex;
         justify-content: space-between;
         padding-top: 10px;
         padding-right: 20px;
         padding-left: 20px;
     }

     .search-main1 {
         border-top: 1px solid #eeeeee;
         margin-left: -20px;
         margin-right: -20px;
     }

     .search-btn1,
     .search-btn1:hover,
     .search-btn1:active,
     .search-btn1:focus {
         background: #007bff !important;
         border: #007bff !important;
         border-radius: 20px;
         height: 36px;
     }

     .page-title-main {
         display: flex;
         justify-content: space-between;
         align-items: center;
         margin-bottom: 20px;
     }

     .search-card1 {
         margin-bottom: 20px;
     }

     .search-card1 .form-group {
         margin-bottom: 0.5rem;
     }

     .search-card1 label {
         margin-bottom: 0;
     }

     .search-card1 .card-body {
         padding-bottom: 10px;
     }

     .search-card1 input[type=text] {
         border-radius: 4px;
         border-color: #aaa;
     }

     .srch-icon {
         padding: 0 !important;
         width: 40px;
         height: 40px;
     }
 </style>

 <div class="main-panel">
     <div class="content-wrapper">
         <div class="page-title-main">
             <h5 class="mb-0 font-weight-bold">Hub Company List</h5>
             <div class="page-rightbtns">
                 <div>
                     @can('hub-company-add')
                         <a href="<?php echo URL::to('/hub-company/add'); ?>" class="btn btn-primary btn-rounded btn-fw btn-sm"><i
                                 class="mdi mdi-plus"> </i> Add Hub Company </a>
                     @endcan

                     <a href="<?php echo URL::to('/'); ?>/hub-company" class="btn btn-light btn-rounded btn-fw btn-sm ml-1"><i
                             class="mdi mdi-reload"></i>
                         Reset</a>

                     @can('hub-company-export')
                        {{-- <a href="" class="btn btn-success btn-rounded btn-sm btn-fw ml-1" id="test_agency"
                             onclick="export_data()"><i class="mdi mdi-file-export"></i>Export</a> --}}
                     @endcan

                     <button class="btn btn-dark btn-rounded btn-fw btn-sm ml-1 srch-icon" id="searchbtns"><i
                             class="fa fa-search"></i></button>
                 </div>
             </div>
         </div>

         <div class="row ">
             <div class="col-sm-12">
                 <div class="card search-card1" id="search-div" style="display: none;">
                     <div class="card-body">
                         <form method="get" id="formsubmit_search">
                           
                             <div class="row">
                                 <div class="col-md-3">
                                     <div class="form-group row">
                                         <label class="col-sm-12 ">Company Name</label>
                                         <div class="col-sm-12 ">
                                             <input autocomplete="off" type="text" class="form-control"
                                                 name="agency_name" id="agency_name" value="{{ $agency_name }}"
                                                 autocomplete="off">
                                         </div>
                                         <span class="error ml-2" id="error_all"></span>
                                     </div>
                                 </div>
                                 <div class="col-md-3">
                                     <div class="form-group row">
                                         <label class="col-sm-12 ">Email</label>
                                         <div class="col-sm-12">
                                             <input type="text" autocomplete="off" class="form-control"
                                                 name="email" id="email" value="{{ $email }}"
                                                 autocomplete="off">
                                         </div>
                                     </div>
                                 </div>
                                 <div class="col-md-3">
                                     <div class="form-group row">
                                         <label class="col-sm-12 ">Phone</label>
                                         <div class="col-sm-12">
                                             <input type="text" autocomplete="off" class="form-control"
                                                 name="phone" id="phone" value="<?php echo $phone; ?>"
                                                 autocomplete="off">
                                         </div>
                                     </div>
                                 </div>
                             </div>
                             <div class="search-main1">
                                 <div class="search-inner">
                                     <div>
                                         <input type="submit" name="search"
                                             class="btn btn-primary search-btn1 searchAppoinment" id="search-data"
                                             value="Search">
                                     </div>
                                 </div>
                             </div>
                         </form>
                     </div>
                 </div>
             </div>
         </div>

         <div class="row">
             <div class="col-12">
                 <table id="order-listing1" class="table table-bordered table-width1">
                     <thead>
                         <tr>
                             <th style="width:20px;">#</th>
                             <th>Record#</th>
                             <th>Name</th>
                             <th>Email</th>
                             <th>Phone</th>
                         </tr>
                     </thead>
                     <tbody>

                         <?php if ($query->total() != 0) {
                        $i = 1 + (($query->currentPage() - 1) * $query->perPage());
                        foreach ($query as $row) {  ?>
                         <tr>
                             <th scope="row"><?= $i++ ?></th>
                             <td nowrap><a href="<?php echo URL::asset('/'); ?>hub-company-view/<?= $row->id ?>"><?= '#' . ' ' . $row->id ?></a></td>
                             <td nowrap><?= ucwords($row->agency_name) ?></td>
                             <td nowrap ><?php echo $row->email; ?></td>
                             <td nowrap ><?php echo $row->phone; ?></td>
                         </tr>
                         <?php }
                      } else { ?>
                         <tr>
                             <td colspan="12">
                                 <center><b>Data not found</b></center>
                             </td>
                         </tr>
                         <?php } ?>
                     </tbody>
                 </table>
                 <div class="pull-right pegination-margin">
                     {{ $query->appends(request()->query())->links('pagination::bootstrap-4') }}
                 </div>
             </div>
         </div>
     </div>

     <!-- Rate Start -->

     <!-- Rate End -->
     <script>
         /* ..Start.. For page refresh when search data then show search area */
         $(document).ready(function() {
             var url = window.location.search;
             var arguments = url.split('?')[1];
             var searchText = arguments.split('=')[0];
             if (searchText == 'agency_name') {
                 $("#search-div").show();
             }
         });
         /* ..End.. For page refresh when search data then show search area */
         $("#searchbtns").click(function() {
             $("#search-div").toggle();
         });

         $('#formsubmit_search').submit(function() {
            var agency_name = $('#agency_name').val();
             var email = $('#email').val();
             var phone = $('#phone').val();
             var city = $('#city').val();
             var is_sms = $('#is_sms').val();
             $("#error_all").html('');

             if (agency_name == '' && email == '' && phone == '' && city == '' && is_sms == '') {
                $("#error_all").html('Please enter any one search text');
                 return false;
             } else {
                return true;
             }
        });

        
         $(document).on("click", ".searchAppoinment", function() {           
            
         });
     </script>
     <script>
         function export_data() {

             var agency_name = $('#agency_name').val();
             var email = $('#email').val();
             var phone = $('#phone').val();
             var city = $('#city').val();
             var is_sms = $('#is_sms').val();
             var temp1 = '<?php echo URL::to('/'); ?>/agency-export?agency_name=' + agency_name + '&email=' + email + '&phone=' +
                 phone + '&city=' + city+'&is_sms='+is_sms;
             //  var temp = temp1.replace("http://", "https://");
             $('#test_agency').attr("style", '');
             $('#test_agency').attr("href", temp1);
         }
        
     </script>

     @include('include/footer')
