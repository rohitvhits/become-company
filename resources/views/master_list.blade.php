 @include('include/header')
 @include('include/sidebar')
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
	.grid-margin{
		margin-top: 12px;
	}
 </style>
 <div class="main-panel">
 	<div class="content-wrapper">
 		<div class="page-title-main">
 			<h5 class="mb-0 font-weight-bold mb-5">Master List</h5>
 		</div>

 		<div class="card">

 			<div class="card-body">


 				<?php if (isset($masterDetail)) {
						$action = '/update_master';
						$btnName = 'Update';
					} else {
						$action = '/add_master';
						$btnName = 'Add New';
					} ?>
 				<form class="form-sample" action='<?php echo URL::to($action) ?>' name="add_master" method="post" onsubmit="return validation();">
 					<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
 					<input type="hidden" name="id" value="<?php if (isset($masterDetail)) {
																echo $masterDetail[0]['id'];
															} else {
															} ?>">
 					<div class="row">

 						<div class="col-md-4">
 							<div class="form-group row">
 								<label class="col-sm-3 col-form-label">View Type</label>
 								<div class="col-sm-9">
 									<?php if (isset($masterTypeAll)) { ?>
 										<select class="form-control" name="master_type_fk" id="master_type_fk" onclick="searchType()">

 											<?php foreach ($masterTypeAll as $typeAll) {  ?>
 												<option value="<?php echo $typeAll['id']; ?>" <?php echo ($typeAll['id'] == $masterDetail[0]['master_type_fk']) ? 'selected' : '';  ?>><?php echo  $typeAll['name']; ?></option>
 											<?php } ?>
 										</select>
 									<?php } else { ?>
 										<select class="form-control" name="master_type_fk" id="master_type_fk" onclick="searchType()">
 											<option value="<?php echo $masterType[0]['id']; ?>"><?php echo ucwords(str_replace('_', ' ', $masterType[0]['name'])); ?></option>
 										</select>
 									<?php } ?>
 									<span class="error mt-2 text-danger" id="type_error"><?php echo $errors->add_master->first('master_type_fk'); ?></span>
 								</div>
 							</div>
 						</div>

 						<div class="col-md-3">
 							<div class="form-group">
								<input type="text" class="form-control" placeholder="Enter Name" id="name" name="name" value="<?php echo old('name'); ?><?php if (isset($masterDetail)) {
																																								echo $masterDetail[0]['name'];
																																							}  ?>">
								<span class="error mt-2 text-danger" id="name_error"><?php echo $errors->add_master->first('name'); ?></span>
 							</div>
 						</div>
 						@if($master_type_fk ==11)
 						<div class="col-md-3">
 							<div class="form-group row">

 								<div class="col-sm-9">
 									<select name="service_type" class="form-control">
 										<option value="">Select Service Type</option>
 										<option value="Patient" @if(isset($masterDetail[0]->types) && $masterDetail[0]->types =='Patient') selected @endif>Patient</option>
 										<option value="Caregiver" @if(isset($masterDetail[0]->types) && $masterDetail[0]->types =='Caregiver') selected @endif>Caregiver</option>

 									</select>
 								</div>
 							</div>
 						</div>
 						@endif
 						<div class="col-md-2">
 							<div class="form-check form-check-flat form-check-primary">

 								<div class="col-sm-9">
 									<label class="form-check-label">
 										<input type="checkbox" class="form-check-input" name="cused" value="1" <?php if (isset($masterDetail[0]['public_id']) && $masterDetail[0]['public_id'] == 1) {
																													echo "checked='checked'";
																												} ?>>
 										Private Used
 										<i class="input-helper"></i></label>
 								</div>
 							</div>
 						</div>

 						<div class="col-md-2">
 							<button type="submit" class="btn btn-success btn-sm"><?php echo $btnName ?></button>&nbsp;
 							<button id="deleteRecord" class="btn btn-danger btn-sm" data-url="{{ route('deleteMultipleRecord') }}" data-mty="{{isset($_GET['master_type_fk']) ? $_GET['master_type_fk'] : ''}}">Delete</button>
 						</div>
 					</div>
 				</form>
 				<!--   <a href="<?php echo URL::to('/adduser') ?>" class="btn btn-primary btn-rounded btn-fw pull-right">Add User</a> -->
 				<div class="row grid-margin">
 					<div class="col-12">
 						<div class="table-responsive">
 							<table id="order-listing1" class="table table-bordered">
 								<thead>
 									<tr>
 										@if(count($masterData)>0)
 										<th width="50px"><input type="checkbox" id="master"></th>
 										@endif
 										<th>No.</th>
 										<th>Type</th>
 										<th>Name</th>
 										@if($master_type_fk ==11)
 										<th>Service Type</th>
 										@endif
 										<th>Action</th>
 									</tr>
 								</thead>
 								<tbody>
 									<?php

										$i = 1 + (($masterData->currentPage() - 1) * $masterData->perPage());
										foreach ($masterData as $row) {  ?>
 										<tr id="tr_{{$row->id}}">

 											<td><input type="checkbox" class="sub_chk" data-id="{{$row->id}}"></td>

 											<th><?= $i++ ?></th>
 											<td><?= ucwords(str_replace('_', ' ', $row->typeName)); ?></td>
 											<td><?= ucfirst($row->name) ?></td>
 											@if($master_type_fk ==11)
 											<td><?= ucfirst($row->types) ?></td>
 											@endif
 											<td>
											 @can('master-edit')	
											<a href="<?php echo URL::asset("/"); ?>edit_master?id=<?= $row->id ?>" data-toggle="tooltip" title="{{ trans('sentence.Edit')}}"><i class="mdi mdi-eyedropper"></i></a> 
											@endcan
											@can('master-edit')
											<a href="<?php echo URL::asset("/"); ?>delete_master?id=<?= $row->id ?>" data-toggle="tooltip" title="{{ trans('sentence.Delete')}}" onclick="return confirm('Are you sure remove this record?')"><i class="mdi mdi-delete"></i></a>
											@endcan
 												@if($master_type_fk ==11)
													@can('service-on-off')
													<label class="toggle-switch toggle-switch-success">
														<input type="checkbox" data-id="{{ $row->id}}" name="is_disabled" value="1" onChange="changeStatus('{{$row->id}}')" id="is_disable_{{$row->id}}" @if($row->is_disable ==1) checked @endif>
														<span class="toggle-slider round"></span>
													</label>
													@endcan
 												@endif
 											</td>
 										</tr>
 									<?php } ?>
 								</tbody>

 							</table>
 							<div class="pull-right pegination-margin">
 								{{$masterData->appends(request()->input())->links("pagination::bootstrap-4")}}
 							</div>


 						</div>
 					</div>
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
 	<script>
 		$(document).on("click", "#master", function() {
 			if ($(this).is(':checked', true)) {
 				$(".sub_chk").prop('checked', true);
 			} else {
 				$(".sub_chk").prop('checked', false);
 			}
 		});


 		$(document).on("click", "#deleteRecord", function(e) {
 			e.preventDefault();
 			var allVals = [];
 			$(".sub_chk:checked").each(function() {
 				allVals.push($(this).attr('data-id'));
 			});
 			if (allVals.length <= 0) {
 				swal({
 					title: "Please Select Checkbox",
 					text: "",
 					type: "warning",
 				});
 			} else {
 				swal({
 						title: "Are you sure you want to delete this records ?",
 						text: "",
 						type: "warning",
 						showCancelButton: true,
 						confirmButtonColor: '#DD6B55',
 						confirmButtonText: 'Yes',
 						cancelButtonText: "No",
 						closeOnConfirm: false,
 						closeOnCancel: false
 					},
 					function(isConfirm) {

 						if (isConfirm) {
 							$.ajax({
 								url: $("#deleteRecord").data('url'),
 								type: 'DELETE',
 								data: {
 									master_type_fk: $("#deleteRecord").data('mty'),
 									ids: allVals
 								},
 								headers: {
 									'X-CSRF-TOKEN': '{{csrf_token()}}'
 								},
 								success: function(res) {
 									if (res.status == true) {
 										$.each(allVals, function(index, val) {
 											$("#tr_" + val).slideUp("slow");
 										});
 										toastr.success(res.message);
 										swal.close();
 									}
 								}

 							});
 						} else {
 							swal.close();
 						}
 					});
 			}


 		});


 		function validation() {

 			var temp = 0;

 			var name = $('#name').val();

 			var type = $('#master_type_fk').val();



 			if (name == "") {
 				$('#name_error').html("Required");
 				temp++;
 			} else {
 				$('#name_error').html("");
 				/*var filter2 = /^[a-zA-Z\s]+$/;			

 				if (filter2.test(name)) {			
 					$("#name_error").html("");
 				} else {
 					$("#name_error").html("Only Character allow");
 					temp++;

 				}*/

 			}

 			if (type == "") {
 				$('#type_error').html("Required");
 				temp++;
 			} else {
 				$('#type_error').html("");
 			}




 			if (temp == 0) {

 				return true;

 			} else {

 				return false;

 			}

 		}

 		function changeStatus(id) {
 			if ($('#is_disable_' + id).is(':checked')) {
 				checked = false;
 				status = 'enable';
 			} else {
 				checked = true;
 				status = 'disable';
 			}
 			$.confirm({
 				title: 'Are you sure?',
 				content: 'you want to ' + status + ' this record.',
 				type: 'blue',
 				buttons: {
 					confirm: {
 						text: 'Confirm',
 						btnClass: 'btn-primary',
 						action: function() {
 							$.ajax({
 								global: false,
 								url: "{{url('change-status-master-services')}}",
 								type: "GET",
 								data: {
 									'id': id
 								},
 								success: function(response) {
 									toastr.success(response.message);
 								},
 								error: function(xhr, status, error) {
 									toastr.error(xhr.responseJSON.message);
 								}
 							});
 						}
 					},
 					cancel: function() {
 						$('#is_disable_' + id).prop("checked", checked);
 					}
 				}
 			})

 		}
 	</script>