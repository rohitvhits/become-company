@include('include/header')
@include('include/sidebar')
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2/select2.min.css">
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
<link href="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo URL::to('/'); ?>/assets/sweetalert.min.css" rel="stylesheet" type="text/css" />
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
			<h5 class="mb-0 font-weight-bold">SMS Template List</h5>
			<div class="page-rightbtns">
				<div>
					<a href="<?php echo URL::to('/sms-template/add') ?>" class="btn btn-primary btn-rounded btn-fw btn-sm pull-right"><i class="mdi mdi-plus"> </i>Add SMS Template</a>

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


		<div class="row">
			<div class="col-12">
				<table id="order-listing1" class="table table-bordered table-width1">
				<thead>
											<tr>

												<th>SMS Name</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
											<?php

											if (count($templete_list) > 0) {
												foreach ($templete_list as $val) {
											?>
													<tr>

														<td class="mailbox-subject"><?php echo ucfirst($val->name); ?>

														</td>
														<td>

															<a href="<?php echo URL::to('/'); ?>/sms-template/delete/<?php echo $val->id; ?>" onclick="return confirm('Are you sure remove this sms template?');" title="Delete"><i class="fa fa-trash-o"></i></a>&nbsp;&nbsp;
															<a href="<?php echo URL::to('/'); ?>/sms-template/edit/<?php echo $val->id; ?>" title="Edit"><i class="fa fa-pencil"></i></a>&nbsp;&nbsp;

														</td>
													</tr>
												<?php }
											}
											if (count($templete_list) == 0) {  ?>
												<tr>
													<td colspan="8">No record available</td>
												</tr>
											<?php } ?>

										</tbody>
				</table>

				<div class="pull-right pegination-margin">
					{{$templete_list->appends(request()->input())->links("pagination::bootstrap-4")}}
				</div>

			</div>
		</div>

	</div>
	<?php
	$follow_dates = '';
	?>

	<div class="modal fade" id="exampleModal-4" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="ModalLabel">Change Status</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form class="forms-sample" enctype="multipart/form-data" action="{{url('tasks/task-change-status')}}" name="adduser" method="post" id="form">
						<input type="hidden" name="_token" value="{{csrf_token()}}">
						<input type="hidden" name="id" id="edit_id" value="">
						<div class="form-group">
							<label for="recipient-name" class="col-form-label">Status<span style="color:red">*</span>:</label>
							<select name="status_id" class="form-control" id="status_id">
								<option value="">Select Status</option>
								<option value="Urgent">Urgent</option>
								<option value="Outstanding">Outstanding</option>
								<option value="Pending">Pending</option>
								<option value="Completed">Completed</option>
							</select>

							<span id="location_error" class="error mt-2 text-danger" for="document_type"></span>
						</div>
						<div class="form-group">
							<label for="recipient-name" class="col-form-label">Notes:</label>
							<textarea class="form-control" type="text" class="form-control" name="task_description" placeholder="Enter Task Description" id="task_description" rows="4" cols="50"></textarea>
						</div>
						<div class="modal-footer">
							<button type="button" onclick="getChangeStatus()" class="btn btn-success">Save</button>
							<button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	@include('include/footer')
	<script src="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.js"></script>
	<script src="<?php echo URL::to('/'); ?>/assets/sweetalert.min.js"></script>
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
	</script>