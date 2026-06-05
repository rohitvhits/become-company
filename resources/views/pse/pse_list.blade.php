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

     .table-width1 tr th:first-child {
         width: 3%;
     }
     .table-width1 tr th:nth-child(5) {
         width: 10%;
     }
     .table-width1 tr th:nth-child(6) {
         width: 10%;
     }
     .table-width1 tr th:nth-child(7) {
         width: 10%;
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

     .page-title-main {
         display: flex;
         justify-content: space-between;
         align-items: center;
         margin-bottom: 20px;
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
             <h5 class="mb-0 font-weight-bold">PSE List</h5>
             <div class="page-rightbtns">
                 <div>
                     @can('pse-add')
                         <a href="<?php echo URL::to('/pse-location/add'); ?>" class="btn btn-primary btn-rounded btn-fw btn-sm"><i
                                 class="mdi mdi-plus"></i>Add PSE </a>
                     @endcan
                 </div>
             </div>
         </div>

         <div class="col-12 grid-margin-top">
            
         </div>

         <div class="row">
             <div class="col-12">
                 <table id="order-listing1" class="table table-bordered table-width1">
                     <thead>
                         <tr>
                             <th>#</th>
                             <th>Address</th>
                             <th>Address2</th>
                             <th>Short Name</th>
                             <th>City</th>
                             <th>State</th>
                             <th>Zipcode</th>
                             <th>Action</th>
                         </tr>
                     </thead>
                     <tbody>

                         <?php if ($query->total() != 0) {
                        $i = 1 + (($query->currentPage() - 1) * $query->perPage());
                        foreach ($query as $row) {  ?>
                         <tr>
                             <th scope="row"><?= $i++ ?></th>
                             <td><?= $row->address1 ?></td>
                             <td><?= $row->address2 ?></td>
                             <td><?= $row->location_name ?></td>
                             <td><?= $row->city ?></td>
                             <td><?= $row->state ?></td>
                             <td><?= $row->zip_code ?></td>
                             <td>
                                 @can('pse-edit')
                                     <a href="<?php echo URL::asset('/'); ?>pse-location/edit/<?= $row->id ?>" data-toggle="tooltip"
                                         title="PSE Location Edit"><i class="fa fa-edit"></i></a>
                                 @endcan

                                

                                 @can('pse-delete')
                                     <a href="<?php echo URL::asset('/'); ?>pse-location/delete/<?= $row->id ?>" data-toggle="tooltip"
                                         title="PSE Location Delete"
                                         onclick="return confirm('Are you sure remove this record?')"><i
                                             class="mdi mdi-delete"></i></a>
                                 @endcan

                                 
                             </td>
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
                     {{ $query->links('pagination::bootstrap-4') }}
                 </div>
             </div>
         </div>
     </div>
     <div class="row" style='margin-top: 25px;'>
        <pre id='toastrOptions'></pre>
    </div>

     @include('include/footer')
