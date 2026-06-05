 @include('include/header')
 @include('include/sidebar')
 <!--<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
-->
 <style type="text/css">
 	#order-listing_length,
 	#order-listing_paginate,
 	#order-listing_info {
 		display: none;
 	}

 	#order-listing_filter {
 		text-align: right;
 	}
 </style>
 <div class="main-panel">

 	<div class="content-wrapper">
 		<div class="card">
 			<div class="row list-name">
 				<div class="col-sm-6">
 					<h4 class="card-title">Document Type List</h4>
 				</div>
 				<div class="col-sm-6 pull-right">
 					<a href="<?php echo URL::to('/'); ?>/document-item/document-export" class="btn btn-success pull-right btn-fw btn-sm" id="test_record"><i class="mdi mdi-file-export"></i>Export</a>

 					<a href="<?php echo URL::to('/document-item/document-add') ?>" class="btn btn-primary pull-right btn-fw btn-sm"><i class="mdi mdi-plus"> </i>Add Document Type</a>
 				</div>
 			</div>

 			<div class="card-body compact-view">
 				<div class="row">
 					<div class="col-12">
 						<div class="table-responsive">


 							<table id="order-listing1" class="table table-bordered">
 								<thead>
 									<tr>
 										<th>Name</th>
 										<th>Action</th>

 									</tr>
 									<tr>
 								</thead>
 								<tbody>
 									<?php
										if (count($query) > 0) {
											$i = 1 + (($query->currentPage() - 1) * $query->perPage());

											foreach ($query as $row) {  ?>
 											<tr>
 												<td><?php echo $row->name; ?></td>
 												<td>

 													<a href="<?php echo URL::to('/document-item/document-edit/' . $row->id) ?>" data-toggle="tooltip" title="{{ trans('sentence.Edit')}}"><i class="mdi mdi-eyedropper"></i></a>
 													<a href="<?php echo URL::to('/document-item/document-delete/' . $row->id) ?>" data-toggle="tooltip" title="{{ trans('sentence.Delete')}}" onclick="return confirm('Are you sure remove this record?')"><i class="mdi mdi-delete"></i></a>
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
 								{{$query->appends(request()->input())->links("pagination::bootstrap-4")}}
 							</div>
 						</div>
 					</div>
 				</div>
 			</div>
 		</div>
 	</div>

 	<div class="modal fade" id="modal-default" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
 		<div class="modal-dialog modal-lg" role="document">
 			<div id="messages_id"></div>
 		</div>
 	</div>

 	@include('include/footer')