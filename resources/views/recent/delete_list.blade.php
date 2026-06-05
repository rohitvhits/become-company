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
 </style>
 <div class="main-panel">

 	<div class="content-wrapper">
 		<div class="card">
 			<div class="row list-name">
 				<div class="col-sm-6">
 					<h4 class="card-title">Deleted Notes</h4>
 				</div>

 			</div>

 			<div class="card-body compact-view">
 				<div class="row">
 					<div class="col-12">
 						<div class="table-responsive">


 							<table id="order-listing1" class="table table-bordered">
 								<thead>
 									<tr>
 										<th>#</th>
 										<th>Record Id</th>
 										<th>Type</th>
 										<th>Message</th>
 										<th>Deleted Date</th>
 										<th>Deleted By</th>
 										<th>Action</th>

 									</tr>
 									<tr>
 										<form method="get" action="">
 										</form>
 									</tr>
 								</thead>
 								<tbody>
 									<?php
										if (count($deleteList) > 0) {
											$i = 1 + (($deleteList->currentPage() - 1) * $deleteList->perPage());

											foreach ($deleteList as $row) {  ?>
 											<tr>
 												<td><?php echo $i; ?></td>
 												<td><a href="<?php echo URL::asset("/"); ?>record/<?= $row->id ?>"><?= $row->id ?> </a></td>
 												<td><?= $row->type ?> </a></td>
 												<td><?= $row->message ?> </a></td>
 												<td><?php if ($row->deleted_date != '') {
															echo date('m/d/Y', strtotime($row->deleted_date));
														} ?> </a></td>
 												<td><?= $row->first_name . ' ' . $row->last_name ?> </a></td>
 												<td> <a href="<?php echo URL::to('/'); ?>/notes-restore/<?php echo $row->id; ?>" title="Restore" onclick="return confirm('Are you sure restore this notes?')"><i class="fa fa-undo"></i></a>
 												</td>
 											</tr>
 										<?php $i++;
											}
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
 								{{$deleteList->appends(request()->input())->links("pagination::bootstrap-4")}}
 							</div>
 						</div>
 					</div>
 				</div>
 			</div>
 		</div>
 	</div>

 	@include('include/footer')