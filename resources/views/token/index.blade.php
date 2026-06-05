 @include('include/header')
 @include('include/sidebar')

 <style>
 	.label {
 		display: inline;
 		padding: .2em .6em .3em;
 		font-size: 100%;
 		font-weight: 700;
 		line-height: 1;
 		color: #fff;
 		text-align: center;
 		white-space: nowrap;
 		vertical-align: baseline;
 		border-radius: .25em;
 	}

 	.label-success {
 		background-color: #5cb85c;
 	}

 	.label-danger {
 		background-color: #d9534f;
 	}

 	.label-warning {
 		background-color: #f0ad4e;
 	}

 	.label-default {
 		background-color: #777;
 	}
 </style>
 <div class="main-panel">

 	<div class="content-wrapper">
 		<div class="card">
 			<div class="row list-name">
 				<div class="col-sm-6">
 					<h4 class="card-title">Generate Token</h4>
 				</div>
 				<div class="col-sm-6 pull-right">

 					<?php if ($auth['user_type_fk'] == 3) { ?> <a href="<?php echo URL::to('/'); ?>/agency-token/export?agency_id=<?php echo $agency_name; ?>" class="btn btn-success pull-right btn-fw btn-sm" id="test_record" onclick="export_data()"><i class="mdi mdi-file-export"></i>Export</a> <a href="<?php echo URL::to("/"); ?>/agency-token" class="btn btn-danger pull-right btn-fw btn-sm"><i class="mdi mdi-reload"></i> Reset</a>
 						<a href="javascript:void(0)" data-toggle="modal" data-target="#exampleModal-2" class="btn btn-primary pull-right btn-fw btn-sm"><i class="mdi mdi-plus"> </i>Add Token</a>
 					<?php }
						if ($auth['user_type_fk'] == 5) { ?>
 						<a href="javascript:void(0)" onclick="<?php if (count($query) > 0) { ?> return confirm('Already one token generate by this agency')<?php } else {  ?> getSubmits() <?php } ?>" class="btn btn-primary pull-right btn-fw btn-sm"><i class="mdi mdi-plus"> </i>Add Token</a>
 					<?php } ?>
 				</div>
 			</div>

 			<div class="card-body">
 				<div class="row">
 					<div class="col-12">
 						<div class="table-responsive">
 							<table class="table table-bordered">
 								<thead>
 									<th>#</th> <?php if ($auth['user_type_fk'] == 3) { ?>
 										<th>Agency Name</th> <?php } ?>
 									<th>Token</th>
 									<th>Action</th>
 								</thead>
 								<tbody> <?php if ($auth['user_type_fk'] == 3) { ?> <form>
 											<tr>
 												<td></td>
 												<td> <?php if ($auth['user_type_fk'] == 3) { ?> <select name="agency_name" class="form-control" id="agencys_id">
 															<option value="">Select Agency</option> <?php if (!empty($agency_list)) {
																											foreach ($agency_list as $val) { ?> <option value="<?php echo $val->id; ?>" <?php if ($agency_name == $val->id) {
																																																																		echo "selected='selected'";
																																																																	} ?>><?php echo $val->agency_name; ?></option> <?php }
																																																																																										} ?>
 														</select> <?php } ?> </td>
 												<td></td>
 												<td> <input type="submit" name="submit" value="Search" class="btn btn-primary pull-right btn-fw btn-sm"> </td>
 											</tr>
 										</form> <?php } ?>
 									<?php
										if (count($query) > 0) {
											$i = 1 + (($query->currentPage() - 1) * $query->perPage());

											foreach ($query as $row) {  ?>
 											<tr>
 												<th scope="row"><?= $i++ ?></th>
 												<?php if ($auth['user_type_fk'] == 3) { ?>
 													<td><?php echo $row->agency_name; ?></td>
 												<?php } ?>
 												<td><?php echo $row->token; ?></td>
 												<td><a href="<?php echo URL::to('/'); ?>/agency-token/token-delete/<?php echo $row->id; ?>" onclick="return confirm('Are you sure remove this record?')"><i class="fa fa-trash"></i></a></td>
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
 								{{ $query->appends(request()->query())->links("pagination::bootstrap-4")  }}

 							</div>
 						</div>
 					</div>
 				</div>
 			</div>
 		</div>

 	</div>
 	<form id="directId" action="<?php echo URL::to('/'); ?>/agency-token/token-insert" method="post" enctype="multipart/form-data">
 		<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
 		<input type="hidden" name="agency_id" value="<?php echo $auth['agency_fk']; ?>">
 	</form>
 	<div class="modal fade" id="exampleModal-2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel-2" aria-hidden="true">
 		<div class="modal-dialog" role="document">
 			<div class="modal-content">
 				<div class="modal-header">
 					<h5 class="modal-title" id="exampleModalLabel-2">Generate Agency Token</h5>
 					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
 						<span aria-hidden="true">×</span>
 					</button>
 				</div>
 				<form action="<?php echo URL::to('/'); ?>/agency-token/token-insert" id="submitId" method="post" enctype="multipart/form-data">
 					<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
 					<div class="modal-body">
 						<div class="form-group">
 							<label>Agency Name</label>
 							<select name="agency_id" class="form-control" id="agency_id">
 								<option value="">Select Agency</option>
 								<?php if (!empty($agency_list)) {
										foreach ($agency_list as $val) { ?>
 										<option value="<?php echo $val->id; ?>"><?php echo $val->agency_name; ?></option>

 								<?php }
									} ?>

 							</select>
 							<span id="agency_errors" style="color:red"></span>
 						</div>
 					</div>
 					<div class="modal-footer">
 						<button type="submit" class="btn btn-success">Submit</button>
 						<button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
 					</div>
 				</form>
 			</div>
 		</div>
 	</div>
 	@include('include/footer')

 	<script>
 		function getSubmits() {
 			$('#directId').submit();

 		}

 		$('#submitId').submit(function(e) {
 			var agencyId = $('#agency_id').val();
 			var cnt = 0;
 			$('#agency_errors').html("");
 			if (agencyId == '') {
 				$('#agency_errors').html("Required !");
 				cnt = 1;
 			}
 			if (agencyId != '') {
 				$.ajax({
 					async: false,
 					global: false,
 					url: "<?php echo URl::to('/'); ?>/checkGenereteAgencyToken",
 					type: "GET",
 					data: {
 						"agency_id": agencyId
 					},
 					success: function(response) {
 						if (response != 1) {
 							$('#agency_errors').html("Token already generated");
 							cnt = 1;
 						}
 					}

 				})

 			}

 			if (cnt == 1) {
 				return false;
 			} else {
 				return true;
 			}

 		});
 	</script>