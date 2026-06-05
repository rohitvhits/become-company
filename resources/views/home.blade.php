@include('include/header')
@include('include/sidebar')
<style>
	#reason_id+.select2 .select2-selection {
		padding: 0;
	}

	#status_list+.select2 .select2-selection {
		padding: 0;
	}

	#emcuser_list+.select2 .select2-selection {
		padding: 0;
	}

	.order-listing-loader1 {
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

	.uorder-listing-loader1 {
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
</style>

<link href="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo URL::to('/'); ?>/assets/sweetalert.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2/select2.min.css">
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
<link href="{{ asset('css/custom.css')}}" rel="stylesheet">
<div class="main-panel">
	<div class="content-wrapper">
		<div class="row grid-margin-top">
			<div class="col-8 ">

				<div class="card" style="height:376px;overflow-y:auto;">


					<div class="card-body">
						<div class="row list-name">
							<div class="col-md-10 card-title">
								<h4 class="card-title">New Patients</h4>
							</div>
						</div>
						<div class="row">
							<div class="col-12">
								<div class="table-responsive ">

									<table id="" class="table table-bordered">
										<thead>
											<tr>
												<th>#</th>
												<th>Agency Name</th>
												<th>Record Name</th>
												<th>Contact Number</th>


											</tr>
										</thead>
										<tbody>
											<?php if (count($open_record_list) > 0) {
												$cnt = 1;
												foreach ($open_record_list as $kkys) {   ?>

													<tr>
														<td><a href="<?php echo URL::to('/'); ?>/record/<?php echo $kkys->id; ?>"> #<?php echo $kkys->id; ?></a></td>
														<td><?php echo $kkys->agency_name; ?></td>

														<td><?php echo ucwords($kkys->first_name . ' ' . $kkys->last_name); ?></td>
														<td><?php echo $kkys->phone; ?></td>


													</tr>
												<?php }
											}
											if (count($open_record_list) == 0) { ?>
												<tr>
													<td colspan="6">No record available</td>
												</tr>
											<?php } ?>
										</tbody>
									</table>
									<div class="pull-right pegination-margin">

									</div>

								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="card" style="height:476px;overflow-y:auto;    margin-top: 22px;">


					<div class="card-body">
						<div class="row list-name">
							<div class="col-md-4 card-title">
								<h4 class="card-title">Assign EMC Record

								</h4>
							</div>
							<div class="col-md-8">
								<form action="<?php echo URL::to('/'); ?>/home" method="get">
									<div class="row">
										<div class="col-md-12">
											<form action="" method="get">
												<div class="row">
													<div class="col-6">
														<!-- <input type="text" name="date" id="reportrange" class="form-control"> -->
														<select name="status_id" class="form-control">
															<option value="">Select Status</option>

															<option value="Pending" <?php if ($status_id == 'Pending') {
																						echo "selected='selected'";
																					} ?>>Pending</option>
															<option value="Completed" <?php if ($status_id == 'Completed') {
																							echo "selected='selected'";
																						} ?>>Complete</option>

														</select>
													</div>
													<div class="col-md-6">
														<input type="submit" class="btn btn-primary btn-sm btn-fw">
														<a href="<?php echo URL::to('/'); ?>/assign-emc/AssignEMCExportCsv?status_id=<?php echo $status_id; ?>" class="btn btn-info btn-sm  btn-fw" id="test_record1"><i class="mdi mdi-file-export"></i>Export</a>
													</div>
												</div>
											</form>
										</div>

									</div>
								</form>
							</div>



						</div>
						<div class="row">
							<div class="col-12">
								<div class="table-responsive">

									<table id="" class="table table-bordered">
										<thead>
											<tr>
												<th># Record Id</th>
												<th>Name</th>
												<th>Agency Name</th>
												<th>Status</th>

												<th>Progress Notes</th>

												<th>Action</th>
											</tr>
										</thead>
										<tbody>
											<?php


											if (count($assignUserList) > 0) {
												$cnt = 1;
												foreach ($assignUserList as $assignEMC) {


											?>

													<tr>
														<td><a href="<?php echo URL::to('/'); ?>/record/<?php echo $assignEMC->record_id; ?>">#<?php echo $assignEMC->record_id; ?></a></td>

														<td><?php echo ucwords($assignEMC->first_name . ' ' . $assignEMC->last_name); ?></td>
														<td><?php echo $assignEMC->agency_name; ?></td>

														<td>
															<?php if ($assignEMC->status == "Pending") {
															?>
																<label class="badge badge-warning badge-pill">Pending</label>

															<?php } else if ($assignEMC->status == "Completed") { ?>
																<label class="badge badge-success badge-pill">Completed</label>
															<?php } else { ?>
																<label class="badge badge-danger badge-pill">Rejected</label>
															<?php } ?>
														</td>

														<td><?php echo $assignEMC->progress_notes; ?></td>
														<td>
															<?php if ($assignEMC->status == "Pending") { ?>

																<a href="javascript:void(0)" onclick="getStatus(<?php echo $assignEMC->id; ?>,'Completed')" title="Mark as Complete"><i class="fa fa-thumbs-up"></i></a>

															<?php } ?>
															<a onclick="getData(<?php echo $assignEMC->id; ?>)" data-toggle="modal" data-target="#send_template_model" data-whatever="@mdo"><i class="fa fa-comments"></i></a>
														</td>
													</tr>
												<?php }
											}
											if (count($assignUserList) == 0) { ?>
												<tr>
													<td colspan="6">No record available</td>
												</tr>
											<?php } ?>
										</tbody>
									</table>
									<div class="pull-right pegination-margin">

									</div>

								</div>
							</div>
						</div>
					</div>


				</div>
			</div>
			<div class="col-md-4 grid-margin stretch-card">
				<div class="card" style="height:873px;overflow-y:auto;">


					<div class="card-body">

						<h4 class="card-title">Recent Notes
							<?php if (in_array($user['user_type_fk'], array(3, 4))) { ?>
								<div class="pull-right">
									<input type="radio" name="radioid" value="emc" <?php if (isset($notes_id) && $notes_id == 'EMC') {
																						echo "checked='checked'";
																					} ?> onclick="getsearchRecentNotes('EMC');">EMC
									<input type="radio" name="radioid" value="agency" <?php if (isset($notes_id) && $notes_id == 'Agency') {
																							echo "checked='checked'";
																						} ?> onclick="getsearchRecentNotes('Agency');">Agency
								</div>
							<?php } ?>
						</h4>

						<?php
						//if(isset($_GET['debug']) && $_GET['debug'] ==1) { echo "<pre>";print_r($NotesList); }

						if (count($NotesList) > 0) {
							$cnt = 1;
							foreach ($NotesList as $kkys) {
						?>
								<div class="d-flex align-items-center py-2 border-bottom">

									<div class="ml-1">
										<h6 class="mb-1"><a href="<?php echo URL::to('/'); ?>/record/<?php echo $kkys->record_id; ?>">Record #<?php echo $kkys->record_id; ?> <?php echo ucwords($kkys->first_name . ' ' . $kkys->last_name); ?></a></h6>
										<p style="white-space: pre-wrap;"><?php echo $kkys->message; ?></p>
										<p class="text-muted mb-0 tx-12"><i class="mdi mdi-map-marker mr-1"></i><?php echo $kkys->agency_name; ?> <?php echo date('m/d/Y h:i A', strtotime($kkys->created_at)); ?></p>
										<p class="text-muted mb-0 tx-12"><?php echo ucwords($kkys->fnames . ' ' . $kkys->lnames); ?></p>
									</div>

								</div>
							<?php  }
						}
						if (count($NotesList) == 0) { ?>
							<span>No record available</span>
						<?php } ?>



					</div>
				</div>
			</div>
		</div>
		<div class="row grid-margin-top">
			<div class="col-md-8 ">



			</div>

		</div>
		<div class="row grid-margin-top">
			<div class="col-8 ">

				<div class="card" style="height:476px;overflow-y:auto;">


					<div class="card-body">
						<div class="row list-name">
							<div class="col-md-3 card-title">
								<h4 class="card-title">Follow Up</h4>
							</div>
							<div class="col-md-9">
								<form action="<?php echo URL::to('/'); ?>/home" method="get">
									<div class="row">
										<div class="col-9">
											<!-- <input type="text" name="date" id="reportrange" class="form-control"> -->
											<select name="agent_name" class="form-control">
												<option value="">Select EMC Rep</option>

												<?php if (!empty($agent_list)) {
													foreach ($agent_list as $val) { ?>
														<option value="<?php echo $val->id; ?>" <?php if ($agent_name == $val->id) {
																									echo "selected='selected'";
																								} ?>><?php echo $val->first_name . ' ' . $val->last_name; ?></option>
												<?php }
												} ?>
											</select>
										</div>
										<div class="col-3">
											<input type="submit" class="btn btn-primary btn-sm btn-rounded btn-fw  pull-right">
										</div>
									</div>
								</form>
							</div>

						</div>
						<div class="row">
							<div class="col-12">
								<div class="table-responsive">

									<table id="" class="table table-bordered">
										<thead>
											<tr>
												<th>ID</th>
												<th>Record Name</th>
												<th>Contact Number</th>
												<th>Agency Name</th>
												<th>EMC Rep</th>
												<th>Follow Up </th>

												<!--  <th>Action</th> -->
											</tr>
										</thead>
										<tbody>
											<?php if (count($record_list) > 0) {
												$cnt = 1;
												foreach ($record_list as $kkys) {   ?>

													<tr>
														<td><a href="<?php echo URL::to('/'); ?>/record/<?php echo $kkys->id; ?>"> #<?php echo $kkys->id; ?></a></td>
														<td><a href="<?php echo URL::to('/'); ?>/record/<?php echo $kkys->id; ?>"><?php echo ucwords($kkys->first_name . ' ' . $kkys->last_name); ?> </a></td>
														<td><?php echo $kkys->phone; ?></td>
														<td><?php echo $kkys->agency_name; ?></td>
														<td><?php echo ucwords($kkys->uname . ' ' . $kkys->lname); ?></td>
														<td><?php echo $kkys->follow_date ?></td>

													</tr>
												<?php }
											}
											if (count($record_list) == 0) { ?>
												<tr>
													<td colspan="6">No record available</td>
												</tr>
											<?php } ?>
										</tbody>
									</table>
									<div class="pull-right pegination-margin">

									</div>

								</div>
							</div>
						</div>
					</div>


				</div>

			</div>
			<div class="col-md-4 grid-margin stretch-card">
				<div class="card" style="height:476px;overflow-y:auto;">
					<div class="card-body">
						<h4 class="card-title">Text SMS</h4>

						<?php if (count($message_list) > 0) {
							$cnt = 1;
							foreach ($message_list as $ks) {
								if ($cnt < 100)

						?>
								<div class="d-flex align-items-center py-2 border-bottom">

									<div class="ml-1">
										<h6 class="mb-1"><a href="<?php echo URL::to('/'); ?>/record/<?php echo $ks->record_id; ?>"> <?php echo $ks->phone; ?> - Record #<?php echo $ks->record_id; ?> <?php echo $ks->first_name . ' ' . $ks->last_name; ?> </a>(<?php echo $ks->type; ?>)</h6>
										<p><?php echo $ks->message; ?>
											<?php if ($ks->media != "") {
												$images = json_decode($ks->media);
												foreach ($images as  $img) {
													$imgs = str_replace('/var/www/html/public', URL::to('/'), $img);
											?>
													<img class="" onclick="window.open(this.src)" src="<?= $imgs ?>" style="width: 30px;">
											<?php
												}
											} ?>

										</p>
										<p class="text-muted mb-0 tx-12"><i class="mdi mdi-map-marker mr-1"></i><?php echo $ks->created_at; ?></p>
									</div>
									<i class="mdi mdi-check-circle-outline font-weight-bold ml-auto px-1 py-1 text-info mdi-24px"></i>
								</div>
						<?php
								$cnt++;
							}
						} ?>
					</div>
				</div>
			</div>

		</div>
		<div class="row grid-margin-top">
			<div class="col-6 ">

				<div class="card" style="height:476px;overflow-y:auto;">


					<div class="card-body">
						<div class="row list-name">
							<div class="col-md-12 card-title">
								<h4 class="card-title">File Date <a href=""><a href="" class="btn btn-primary  btn-fw btn-sm pull-right" id="test_record1" onclick="export_data()"><i class="mdi mdi-file-export"></i>Export</a></a></h4>
							</div>

						</div>
						<div class="row">
							<div class="col-12">
								<div class="table-responsive">

									<table id="" class="table table-bordered">
										<thead>
											<tr>
												<th>ID</th>
												<th>Record Name</th>

												<th>File Date </th>
												<th>Agency Name</th>
												<th>EMC Rep</th>

												<!--  <th>Action</th> -->
											</tr>
										</thead>
										<tbody>
											<?php if (count($filedlist) > 0) {
												$cnt = 1;
												foreach ($filedlist as $kkys) {   ?>

													<tr>
														<td><a href="<?php echo URL::to('/'); ?>/record/<?php echo $kkys->id; ?>">#<?php echo $kkys->id; ?></a></td>

														<td><?php echo ucwords($kkys->first_name . ' ' . $kkys->last_name); ?></td>

														<td><?php echo date('m/d/Y', strtotime($kkys->file_date)); ?></td>
														<td><?php echo $kkys->agency_name; ?></td>
														<td><?php echo ucwords($kkys->uname . ' ' . $kkys->lname); ?></td>

													</tr>
												<?php }
											}
											if (count($filedlist) == 0) { ?>
												<tr>
													<td colspan="6">No record available</td>
												</tr>
											<?php } ?>
										</tbody>
									</table>
									<div class="pull-right pegination-margin">

									</div>

								</div>
							</div>
						</div>
					</div>


				</div>

			</div>
			<div class="col-6 ">

				<div class="card" style="height:476px;overflow-y:auto;">


					<div class="card-body">
						<div class="row list-name">
							<div class="col-md-12 card-title">
								<h4 class="card-title">Recent Month<a href="" class="btn btn-primary btn-fw btn-sm pull-right" id="test_record2" onclick="export_data1()"><i class="mdi mdi-file-export"></i>Export</a></h4>
							</div>

						</div>
						<div class="row">
							<div class="col-12">
								<div class="table-responsive">

									<table id="" class="table table-bordered">
										<thead>
											<tr>
												<th>ID</th>
												<th>Record Name</th>

												<th>Recent Month </th>
												<th>Agency Name</th>
												<th>EMC Rep</th>

												<!--  <th>Action</th> -->
											</tr>
										</thead>
										<tbody>
											<?php if (count($recentMonthList) > 0) {
												$cnt = 1;
												foreach ($recentMonthList as $kkys) {   ?>

													<tr>
														<td><a href="<?php echo URL::to('/'); ?>/record/<?php echo $kkys->id; ?>">#<?php echo $kkys->id; ?></a></td>
														<td><?php echo ucwords($kkys->first_name . ' ' . $kkys->last_name); ?></td>

														<td><?php echo date('M-Y', strtotime($kkys->recent_month)); ?></td>
														<td><?php echo $kkys->agency_name; ?></td>
														<td><?php echo ucwords($kkys->uname . ' ' . $kkys->lname); ?></td>

													</tr>
												<?php }
											}
											if (count($recentMonthList) == 0) { ?>
												<tr>
													<td colspan="6">No record available</td>
												</tr>
											<?php } ?>
										</tbody>
									</table>
									<div class="pull-right pegination-margin">
									</div>

								</div>
							</div>
						</div>
					</div>


				</div>

			</div>
		</div>
		<div class="row grid-margin-top">
			<div class="col-12">

				<div class="card" style="height:476px;overflow-y:auto;">
					<div class="card-body">
						<div class="row list-name">
							<div class="col-md-12 card-title">
								<h4 class="card-title">Doctor Paper Work</h4>
							</div>

						</div>
						<div class="row">
							<div class="col-12">
								<span id="doc_paper_work_id"></span>
							</div>
						</div>
					</div>


				</div>

			</div>

		</div>
		<div class="row grid-margin-top">


		</div>
		<div class="row grid-margin-top">
			<div class="col-12">

				<div class="card">


					<div class="card-body">
						<div class="row" style="margin:-10px;">
							<div class="col-md-10 card-title" style="margin-top:5px;">
								<h4 class="card-title">Agency Graph</h4>
							</div>
							<div class="col-md-2">
								<div class="text-center">
									<input type="text" id="datepicker_id" class="form-control datepicker_date" value="<?php echo $current_date; ?>">
								</div>

							</div>
						</div>
						<hr>
						<div class="row">
							<div class="col-12">
								<div id="chart_all_div" style="height: 700px;"></div>
							</div>
						</div>
					</div>

				</div>

			</div>

		</div>
		<div class="row grid-margin-top">
			<div class="col-12">

				<div class="card">


					<div class="card-body">
						<div class="row" style="margin:-10px;">
							<div class="col-md-10 card-title" style="margin-top:5px;">
								<h4 class="card-title">EMC Graph</h4>
							</div>

						</div>
						<hr>
						<div class="row">
							<div class="col-12">
								<div id="chart_all_div1" style="height: 700px;"></div>
							</div>
						</div>
					</div>

				</div>

			</div>

		</div>
		<div class="row grid-margin-top">
			<div class="col-12">

				<div class="card">


					<div class="card-body">
						<div class="row" style="margin:-10px;">
							<div class="col-md-8 card-title" style="margin-top:5px;">
								<h4 class="card-title">EMC User with Status Graph</h4>


							</div>
							<div class="col-md-2">
								<div class="text-center">
									<select name="status_list" id="status_list" class="form-control select2" onchange="getChange()">
										<option value="">Select Status</option>
										<?php if (count($status_list) > 0) {
											foreach ($status_list as $ld) {
										?>
												<option value="<?php echo $ld->id; ?>" <?php if ($status == $ld->id) {
																							echo "selected='selected'";
																						} ?>><?php echo $ld->name; ?></option>
										<?php }
										} ?>
									</select>
								</div>

							</div>
							<div class="col-md-2">
								<div class="text-center">
									<select name="emcuser_list" id="emcuser_list" class="form-control select2" onchange="getChange()">
										<option value="">Select EMC User</option>
										<?php if (count($emcuser_list) > 0) {
											foreach ($emcuser_list as $lds) {
										?>
												<option value="<?php echo $lds->id; ?>" <?php if ($status == $lds->id) {
																							echo "selected='selected'";
																						} ?>><?php echo $lds->name; ?></option>
										<?php }
										} ?>
									</select>
								</div>

							</div>
						</div>
						<hr>
						<div class="row">
							<div class="col-12">
								<div id="chart_all_divd" style="height: 700px;"></div>
							</div>
						</div>
					</div>

				</div>

			</div>

		</div>
		<div class="row grid-margin-top">
			<div class="col-12">

				<div class="card">


					<div class="card-body">
						<div class="row" style="margin:-10px;">
							<div class="col-md-8 card-title" style="margin-top:5px;">
								<h4 class="card-title">Close Status Graph</h4>


							</div>
							<div class="col-md-2">
								<div class="text-center">
									<select name="reason_id" id="reason_id" class="form-control select2" onchange="getReason()">
										<option value="">Select Reason</option>
										<?php if (count($reason_list_details) > 0) {
											foreach ($reason_list_details as $ld) {
										?>
												<option value="<?php echo $ld->id; ?>"><?php echo $ld->name; ?></option>
										<?php }
										} ?>
									</select>
								</div>

							</div>

						</div>
						<hr>
						<div class="row">
							<div class="col-12">
								<div id="chart_all_divd_closed" style="height: 700px;"></div>
							</div>
						</div>
					</div>

				</div>

			</div>

		</div>


		<div class="row grid-margin-top">
			<div class="col-12">

				<div class="card">


					<div class="card-body">
						<div class="row" style="margin:-10px;">
							<div class="col-md-10 card-title" style="margin-top:5px;">
								<h4 class="card-title">Trust Report</h4>
							</div>

						</div>
						<hr>
						<div class="row">
							<div class="col-12">
								<div class="order-listing-loader">
									<i class="fa fa-spinner fa-spin"></i>
								</div>
								<span id="ajax_trust_id"></span>
							</div>
						</div>
					</div>

				</div>
			</div>

		</div>
		<div class="row grid-margin-top">
			<div class="col-12">

				<div class="card">


					<div class="card-body">

						<div class="row" style="margin:-10px;">
							<div class="col-md-10 card-title" style="margin-top:5px;">
								<h4 class="card-title">Day wise Agency Report</h4>
							</div>
							<div class="col-md-2">
								<div class="text-center">
									<input type="text" id="datepicker_id" class="form-control datepicker_date_new">
								</div>
							</div>
						</div>
						<hr>
						<div class="row">
							<div class="col-12">
								<div class="order-listing-loader1">
									<i class="fa fa-spinner fa-spin"></i>
								</div>
								<span id="ajax_days_wise_record"></span>
							</div>
						</div>
					</div>

				</div>
			</div>

		</div>
		<div class="row grid-margin-top">
			<div class="col-12">

				<div class="card">


					<div class="card-body">

						<div class="row" style="margin:-10px;">
							<div class="col-md-10 card-title" style="margin-top:5px;">
								<h4 class="card-title">30-45 days Filed Records</h4>
							</div>

						</div>
						<hr>
						<div class="row">
							<div class="col-12">
								<div class="uorder-listing-loader1">
									<i class="fa fa-spinner fa-spin"></i>
								</div>
								<span id="30_40_days_wise_record"></span>
							</div>
						</div>
					</div>

				</div>
			</div>

		</div>

	</div>

</div>

</div>




<div class="modal fade" id="send_template_model" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="ModalLabel">Notes</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">

				<div class="col-12">
					<input type="hidden" name="" id="hidden_id">
					<div class="chat-messages" id="sms-messages">
						<div id="chat-messages-inner" class="sms-container">

						</div>


					</div>
				</div>
				<div class="modal-footer" style="border-top:0;">

					<div class="chat-message  custom-chat" style="width:100%">
						<form id="attachsubmit" method="post" onsubmit="return false;">
							<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
							<button class="btn btn-success btn-sm" id="text-sms-send-btn" onclick="sendMessagefile()">Send</button>
							<span class="input-box">
								<!--   <input type="text" name="msg-box" id="text-msg-box" /> -->
								<textarea style="margin-bottom: 0 !important; width: 100%;" name="msg-box" id="text-sms-box"></textarea>

							</span>
						</form>


					</div>
				</div>

			</div>

		</div>
	</div>
</div>
<div class="modal fade" id="exampleModal-doc-paper" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Notes </h5>
				<button type="button" class="close" id="doc_paperwork_closed" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</div>

			<div class="modal-body">
				<form class="form-sample" action='{{url("doctor-paper-work")}}' id="doctor_paper_work" name="adduser" method="post">
					<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
					<input type="hidden" name="record_id" id="record_id_new" value="">



					<div class="form-group">
						<label> NOTES</label>

						<input type="text" class="form-control" placeholder="Enter Notes" id="notes_name" name="notes_name">
						<span class="notes_name_error" style="color:red"></span>
					</div>

				</form>

			</div>

			<div class="modal-footer">
				<button type="button" id="notes_submit" class="btn btn-success">Submit</button>
				<button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
			</div>
		</div>
	</div>

</div>
<!--Chnage-password-box-->
<div class="modal fade" id="change-password-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel-2" style="display: none;" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel-2">Change Password</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</div>

			<form id="submitId" class="widget-form" method="POST" action="{{ url('update-expired-password') }}">
				@csrf
				<div class="modal-body">
					<h6 class="font-weight-light">Password must contain at least 8 characters including 1 uppercase, 1 lowercase, a number and symbol</h6>
					<div class="form-group">
						<input type="hidden" name="id" value="{{sha1(auth()->user()->id)}}">
						<input type="hidden" name="type" value="home">
						<label>New Password</label>
						<input id="password" type="password" class="md-input form-control" name="password" autocomplete="new-password">
						<span class="password_error" style="color:red">{{ $errors->edit_user->first('password') }}</span>
					</div>
					<div class="form-group">
						<label>Confirm Password</label>
						<input type="password" class="md-input form-control" name="password_confirmation" autocomplete="new-password" id="cpassword">
						<span class="cpassword_error" style="color:red"></span>
					</div>


				</div>

				<div class="modal-footer">

					<button class="btn btn-primary" id="changePasswordBtn" type="button">Update Password</button>
					<button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!--End-chnage-password-box-->
<script>
	$('#reportrange').daterangepicker({
		ranges: {
			'Today': [moment(), moment()],
			'Tomorrow': [moment().add(1, 'days'), moment().add(1, 'days')],
			'Next 7 Days': [moment(), moment().add(6, 'days')],
			'Next 30 Days': [moment(), moment().add(29, 'days')],
			'This Month': [moment().add('month'), moment().endOf('month')],
			'Next Month': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month').endOf('month')]
		}
	});

	function export_data() {
		$('#test_record1').attr('href', '<?php echo URL::to('/'); ?>/file-date-export');

	}

	function export_data1() {
		$('#test_record2').attr('href', '<?php echo URL::to('/'); ?>/recent-date-export');

	}

	function getsearchRecentNotes(id) {
		window.location.href = "<?php echo URL::to('/'); ?>/home?notes_type=" + id;
	}
</script>
@include('include/footer')


<script type="text/javascript" src="<?php echo URL::to('/'); ?>/assets/js/moment.min.js"></script>
<script type="text/javascript" src="<?php echo URL::to('/'); ?>/assets/js/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo URL::to('/'); ?>/css/daterangepicker.css" />

<script src="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.js"></script>
<script src="<?php echo URL::to('/'); ?>/assets/sweetalert.min.js"></script>
<script type="text/javascript" src="<?php echo URL::to('/'); ?>/assets/vendors/select2/select2.min.js"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script>
	$('.select2').select2();

	function getStatus(id, status) {

		swal({
				title: "Are you sure?",
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
						async: false,
						global: false,
						type: "GET",
						url: "<?php echo URL::to('/'); ?>/assign-emc/status-change/" + id,
						data: {
							"status": status
						},
						success: function(res) {
							if (res == 1) {
								toastr.success("Status successfully change.");

								location.reload();
							} else {
								toastr.error("Sorry, something went wrong. Please try again.");
							}
						}

					})

				} else {
					swal.close();
				}
			});

	}
	var i = 0;

	function getData(id) {
		$('#hidden_id').val(id);
		$('.notes-messages').html("");
		$('#loadersId').attr('style', 'display:block');
		var mess = $("input[name='radio']:checked").val();

		$.ajax({
			url: "<?php echo URL::to('/'); ?>/assign-emc/get-notes/" + id,
			type: "post",
			data: {
				_token: '<?php echo csrf_token(); ?>',
				'readMessage': id
			},
			success: function(response) {
				var jsons = JSON.parse(response);
				$('#chat-messages-inner').html("");

				if (jsons.length == 0) {
					$('#chat-messages-inner').html("<span>No record available</span>");
				}
				jsons.forEach(element => {
					add_sms_obj(element.first_name, element.message, element.created_date, false);

				});
				$('#loadersId').attr('style', 'display:none;');
				// add_message('You', 'img/demo/av1.jpg', input.val(), true);
				// You will get response from your PHP page (what you echo or print)

				var inner = $('.sms-container');
				$('#sms-messages').animate({
					scrollTop: inner.height()
				}, 20);
			},
			error: function(jqXHR, textStatus, errorThrown) {
				console.log(textStatus, errorThrown);
			}
		});
	}

	var smsCounter = 0

	function add_sms_obj(name, msg, date, clear) {
		//alert(sender_id);
		i = i + 1;
		if (!name) {
			name = "";
		}

		var inner = $('.sms-container');
		var time = new Date(date);
		var date = time.getMonth() + '/' + time.getDate() + '/' + time.getFullYear();
		var hours = time.getHours();
		var minutes = time.getMinutes();
		if (hours < 10) hours = '0' + hours;
		if (minutes < 10) minutes = '0' + minutes;
		var id = 'msg-' + i;
		//  var type="Receive";

		var idname = name.replace(' ', '-').toLowerCase();
		inner.append('<p id="' + id + '" class="user-' + idname + '">' +
			'<span class="msg-block"><strong>' + name + ' </strong><span class="time"> ' + date + ' ' + hours + ':' + minutes + '</span>' +
			'<span class="msg">' + msg + '</span></span></p>');
		$('#' + id).hide().fadeIn(800);
		if (clear) {
			$('.chat-message textarea').val('').focus();
		}
		$('#sms-messages').animate({
			scrollTop: inner.height()
		}, 20);
	}

	function sendMessagefile() {
		var alldata = new FormData($('#attachsubmit')[0]);
		var id = $('#hidden_id').val();
		var name = "you";
		var message = $('#text-sms-box').val();
		if (id != 0 && message != "") {
			$.ajax({
				type: 'POST',
				data: alldata,
				url: "<?php echo URL::to('/'); ?>/assign-emc/add-notes/" + id,
				dataType: "json",
				mimeType: "multipart/form-data",
				contentType: false,
				processData: false,
				success: function(response) {
					addSMSmessage('You', message, true);
					// You will get response from your PHP page (what you echo or print)
				},
				error: function(jqXHR, textStatus, errorThrown) {
					console.log(textStatus, errorThrown);
				}
			});
		}
	}

	function addSMSmessage(name, msg, clear) {
		console.log(name);
		smsCounter = smsCounter + 1;
		var inner = $('.sms-container');
		var time = new Date();
		var hours = time.getHours();
		var minutes = time.getMinutes();
		if (hours < 10) hours = '0' + hours;
		if (minutes < 10) minutes = '0' + minutes;
		var id = 'sms-msg-' + smsCounter;
		var idname = name.replace(' ', '-').toLowerCase();
		inner.append('<p id="' + id + '" class="user-' + idname + '">' +
			'<span class="msg-block"> <strong>' + name + ' </strong> <span class="time"> ' + hours + ':' + minutes + '</span>' +
			'<span class="msg">' + msg + '</span></span></p>');
		$('#' + id).hide().fadeIn(800);
		if (clear) {
			$('#text-sms-box').val('').focus();
		}
		$('#sms-messages').animate({
			scrollTop: $('.sms-container').height()
		}, 1000);
	}
</script>
<script>
	google.charts.load('current', {
		packages: ['corechart', 'bar', 'line']
	});
	google.charts.setOnLoadCallback(drawMultCases);
	google.charts.setOnLoadCallback(drawMultEMC);
	google.charts.setOnLoadCallback(drawMultCasesNew);
	google.charts.setOnLoadCallback(drawMultCasesNewClosed);
	var chart, data, options;
	$(document).ready(function() {

	});

	function drawMultCases() {

		var created_date = $('#datepicker_id').val();

		var data = new google.visualization.DataTable();
		data.addColumn('string', '');
		data.addColumn('number', 'Completed');
		data.addColumn('number', 'Filed');
		data.addColumn('number', 'Closed');

		var options = {
			chartArea: {
				width: '50%'
			},
			hAxis: {
				title: '',
				minValue: 0,
				textStyle: {
					bold: true,
					fontSize: 12,
					color: '#4d4d4d'
				},
				titleTextStyle: {
					bold: true,
					fontSize: 18,
					color: '#4d4d4d'
				}
			},
			vAxis: {
				title: 'Agency',
				textStyle: {
					fontSize: 14,
					bold: true,
					color: '#848484'
				},
				titleTextStyle: {
					fontSize: 14,
					bold: true,
					color: '#848484'
				}
			}
		};

		var chart = new google.visualization.BarChart(document.getElementById('chart_all_div'));

		$.ajax({
			url: "<?php echo URL::to('/'); ?>/graph-ajax?created_date=" + created_date,
			dataType: "json",
			type: "GET",
			success: function(datalist) {

				data.addRows(datalist.result);
				chart.draw(data, options);
				google.visualization.events.addListener(chart, 'select', selectHandler);

				function selectHandler(e) {
					console.log(datalist);
					var c = chart.getSelection()[0].row;
					if (chart.getSelection()[0].column == 1) {
						var patienr = 16;
					} else if (chart.getSelection()[0].column == 2) {
						var patienr = 62;
					} else {
						var patienr = 14;
					}
					window.open("<?php echo URL::to('/'); ?>/record?agency_fk1=" + datalist.Data[c].USERID + '&patient_status=' + patienr + "&created_date1=" + created_date, '_blank');

				}


			}

		});


	}

	function drawMultEMC() {

		var created_date = $('#datepicker_id').val();

		var data = new google.visualization.DataTable();
		data.addColumn('string', '');
		data.addColumn('number', 'Completed');
		data.addColumn('number', 'Filed');
		data.addColumn('number', 'Closed');
		var options = {
			chartArea: {
				width: '50%'
			},
			hAxis: {
				title: '',
				minValue: 0,
				textStyle: {
					bold: true,
					fontSize: 12,
					color: '#4d4d4d'
				},
				titleTextStyle: {
					bold: true,
					fontSize: 18,
					color: '#4d4d4d'
				}
			},
			vAxis: {
				title: 'EMC User',
				textStyle: {
					fontSize: 14,
					bold: true,
					color: '#848484'
				},
				titleTextStyle: {
					fontSize: 14,
					bold: true,
					color: '#848484'
				}
			}
		};

		var chart = new google.visualization.BarChart(document.getElementById('chart_all_div1'));

		$.ajax({
			url: "<?php echo URL::to('/'); ?>/graph-ajax-emc?created_date=" + created_date,
			dataType: "json",
			type: "GET",
			success: function(datalist) {

				data.addRows(datalist.result);
				chart.draw(data, options);
				google.visualization.events.addListener(chart, 'select', selectHandler);

				function selectHandler(e) {
					console.log(datalist);
					var c = chart.getSelection()[0].row;
					if (chart.getSelection()[0].column == 1) {
						var patienr = 16;
					} else if (chart.getSelection()[0].column == 2) {
						var patienr = 62;
					} else {
						var patienr = 14;
					}
					window.open("<?php echo URL::to('/'); ?>/record?emcuser=" + datalist.Data[c].USERID + '&patient_status=' + patienr + "&created_date1=" + created_date, '_blank');

				}



			}
		});

	}

	function drawMultCasesNew() {
		var status = $('#status_list').val();
		var emcuser_list = $('#emcuser_list').val();

		var data = new google.visualization.DataTable();
		data.addColumn('string', 'UserName');
		data.addColumn('number', 'Total');


		var options = {

			hAxis: {
				title: 'User name'

			},
			vAxis: {
				title: 'Total Case Count'
			},
			legend: {
				position: "none"
			}
		};

		var chart = new google.visualization.ColumnChart(document.getElementById('chart_all_divd'));

		$.ajax({
			url: "<?php echo URL::to('/'); ?>/emc-graph-ajax?status=" + status + '&emcuser=' + emcuser_list,
			dataType: "json",
			type: "GET",
			success: function(datalist) {

				data.addRows(datalist.result);
				chart.draw(data, options);
				google.visualization.events.addListener(chart, 'select', selectHandler);

				function selectHandler(e) {
					console.log(datalist);
					var c = chart.getSelection()[0].row;
					console.log(c);
					if (emcuser_list != '') {
						emcs = emcuser_list;
					} else {
						emcs = datalist.Data[c].id;
					}
					var url = "<?php echo URL::to('/'); ?>/record?emcuser=" + emcs + '&patient_status=' + status;
					window.open(url, '_blank');


				}


			}
		});

	}

	function getChange() {
		drawMultCasesNew();
	}

	function drawMultCasesNewClosed() {
		var reason_id = $('#reason_id').val();

		var data = new google.visualization.DataTable();
		data.addColumn('string', 'UserName');
		data.addColumn('number', 'Total');


		var options = {

			hAxis: {
				title: 'User name'

			},
			vAxis: {
				title: 'Total Case Count'
			},
			legend: {
				position: "none"
			}
		};

		var chart = new google.visualization.ColumnChart(document.getElementById('chart_all_divd_closed'));

		$.ajax({
			url: "<?php echo URL::to('/'); ?>/closed-graph-ajax?reason_id=" + reason_id,
			dataType: "json",
			type: "GET",
			success: function(datalist) {

				data.addRows(datalist.result);
				chart.draw(data, options);
				google.visualization.events.addListener(chart, 'select', selectHandler);

				function selectHandler(e) {
					console.log(datalist);
					var c = chart.getSelection()[0].row;
					console.log(datalist.Data[c].id);
					emcs = datalist.Data[c].id;
					var url = "<?php echo URL::to('/'); ?>/record?emcuser=" + emcs + '&patient_status=14&reason_id=' + reason_id;
					window.open(url, '_blank');


				}


			}
		});

	}

	function getReason() {
		drawMultCasesNewClosed();
	}
	$(function() {
		var start = moment().subtract(0, 'days');
		var end = moment();
		$('.datepicker_date').daterangepicker({
			startDate: start,
			endDate: end,
			autoUpdateInput: false,
			startOfWeek: 'sunday',
			ranges: {
				'Today': [moment(), moment()],
				'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
				'Last 7 Days': [moment().subtract(6, 'days'), moment()],
				'Last 30 Days': [moment().subtract(29, 'days'), moment()],
				'This Month': [moment().startOf('month'), moment().endOf('month')],
				'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
				'Next Month': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month').endOf('month')],
				'Next Week': [moment().add(1, 'weeks').startOf('isoWeek'), moment().add(1, 'weeks').endOf('isoWeek')],
				'Last Week': [moment().subtract(1, 'weeks').startOf('isoWeek'), moment().subtract(1, 'weeks').endOf('isoWeek')],
			}
		}, function(chosen_date, end_date) {

			$('.datepicker_date').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format('MM/DD/YYYY'));
			drawMultCases();
			drawMultEMC();
		})



	});


	function getDoctorPaperWork(page) {
		$.ajax({
			url: "<?php echo URL::to('/'); ?>/doctor-paper-work-response?page=" + page,
			type: "GET",
			success: function(datalist) {
				$('#doc_paper_work_id').html("");
				$('#doc_paper_work_id').html(datalist);
				console.log(datalist);

			}
		});

		return false;

	}
	getDoctorPaperWork(0);

	function getRemoveNew(id) {
		$('#' + id).remove();
		var lengts = $('.copy_ids').length;
		if (lengts == 1) {
			$('#lastIdNew').attr('style', 'display:none');
		}
	}

	function addNotesDocPaperWork(id) {
		$('#record_id_new').val(id);
	}
	$('#notes_submit').click(function(e) {
		var names = $('#notes_name').val();
		$('#notes_name_error').html("");
		var cnt = 0;
		console.log(names);
		if (names.trim() == '') {
			console.log("RERR");
			$('.notes_name_error').html("Required");
			cnt = 1;
		}
		if (cnt == 1) {
			return false;
		} else {
			var doctor_paper_work = $('#doctor_paper_work')[0];
			var forms = new FormData(doctor_paper_work);
			$.ajax({
				type: "POST",
				url: '{{url("doctor-paper-work-notes")}}',
				data: $('#doctor_paper_work').serialize(),
				success: function(res) {
					if (res == 1) {
						toastr.success("Notes successfully added.");
						$('.close').click();
						$('#doctor_paper_work')[0].reset();
						getDoctorPaperWork();

					} else {
						toastr.error('Sorry, something went wrong. Please try again.');
					}
				}
			})

		}
	});

	function Approved(id, status) {
		var confirms = confirm("Are you sure change status?");
		if (confirms == true) {
			$.ajax({
				type: "GET",
				url: '{{url("change-status-paper-work")}}',
				data: {
					'id': id,
					'status': status
				},
				success: function(res) {
					if (res == 1) {
						toastr.success("Status successfully changed.");
						$('#doc_paperwork_closed').click();
						getDoctorPaperWork();

					} else {
						toastr.error('Sorry, something went wrong. Please try again.');
					}
				}
			})
		}

	}


	function ajaxTrustRecord(page) {
		var agency_fk = $('#agency_fk').val();
		var name = $('#name').val();
		var email = $('#email').val();
		var phone = $('#phone').val();
		var emc_user_id = $('#emc_user_id').val();
		var medicaid_issue = $('#medicaid_issue').val();
		var record_form = $('#record_form').val();
		var datepickernn = $('#datepickernn').val();
		var five_month = $('#five_month').val();
		var trust_approved = "";
		var disability = $('#disability').val();
		var patient_status = $('#patient_status').val();
		var datepicker_date = $('.datepicker_date_new').val();
		var fields = $('#fields').val();
		var sort = $('#sorting').val();
		$('.order-listing-loader').attr('style', 'display:flex');
		$.ajax({
			url: "{{ url('trust/trust-ajax-list')}}?type=trust",
			type: "GET",
			data: {

				'page': page,
				'agency_fk': agency_fk,
				'name': name,
				'email': email,
				'phone': phone,
				'emcuser': emc_user_id,
				'medicaid_issue': medicaid_issue,
				'record_form': record_form,
				'month': datepickernn,
				'five_month': five_month,
				'trust_approved': trust_approved,
				'disability': disability,
				'patient_status': patient_status,
				'created_date': datepicker_date,
				'fields': fields,
				'orderBy': sort
			},
			success: function(res) {
				$('.order-listing-loader').attr('style', 'display:none');

				$('#ajax_trust_id').html(res);
			}
		})

		return false;
	}
	setTimeout(() => {

		ajaxTrustRecord(1);
	}, 2000);

	$('.js-example-basic-multiple').select2();

	$('body').on('click', '#search_id_new', function() {
		ajaxTrustRecord(1);
	})
	$('body').on('click', '.record_id', function() {
		var fields = $(this).attr('data-field');
		var sorting = $(this).attr('data-sort');
		$('#fields').val(fields);
		$('#sorting').val(sorting);
		ajaxTrustRecord(1);
	})
	$('body').on('click', '.record_id_new', function() {
		var fields = $(this).attr('data-field');
		var sorting = $(this).attr('data-sort');
		$('#fields_new').val(fields);
		$('#sorting_new').val(sorting);
		ajaxAgencyRecord(1);
	})
	$(document).on('click', '.pagination a', function(event) {
		$('li').removeClass('active');
		$(this).parent('li').addClass('active');
		event.preventDefault();
		var myurl = $(this).attr('href');

		var page = $(this).attr('href').split('page=')[1];
		var explode = myurl.split('?');
		var explode1 = explode[1].split('&')[0];
		var news = explode1.split('=');
		console.log(news);
		if (news[1] == 'ajaxAgency') {
			ajaxAgencyRecord(page);
		} else if (news[1] == 'ajax30Days') {

			ajax3045Record(page);
		} else {
			ajaxTrustRecord(page);
		}
		//


	});

	function ajaxAgencyRecord(page) {

		var fields = $('#fields_new').val();
		var sort = $('#sort_new').val();
		var created_date = $('.datepicker_date_new').val();
		$('.order-listing-loader1').attr('style', 'display:flex');
		$.ajax({
			type: "GET",
			url: "{{ url('dashboard-agency-record')}}?page=" + page,

			data: {
				'type': 'ajaxAgency',
				'fields': fields,
				'orderBy': sort,
				'created_date': created_date
			},
			success: function(res) {
				$('.order-listing-loader1').attr('style', 'display:none');
				$('#ajax_days_wise_record').html("");
				$('#ajax_days_wise_record').html(res);
			}
		})

		return false;
	}
	ajaxAgencyRecord(1);

	function ajax3045Record(page) {

		var fields = $('#fields_new').val();
		var sort = $('#sort_new').val();
		var agency_fk_thirty = $('#agency_fk_thirty').val();
		var name_thirty = $('#name_thirty').val();
		var phone_thirty = $('#phone_thirty').val();
		var emcuser_thirty = $('#emcuser_thirty').val();
		var medicaid_issue_thirty = $('#medicaid_issue_thirty').val();



		var filed_date_thirty = $('#filed_date_thirty').val();
		var cin = $('#cin').val();

		$('.uorder-listing-loader1').attr('style', 'display:flex');
		$.ajax({
			type: "GET",
			url: "{{ url('/thirty-fouty-five-filed-report')}}?page=" + page,

			data: {
				'type': 'ajax30Days',
				'flag': 'dashboard',
				'fields': fields,
				'orderBy': sort,
				'agency_fk': agency_fk_thirty,
				'name': name_thirty,

				'phone': phone_thirty,
				'emcuser': emcuser_thirty,
				'medicaid_issue': medicaid_issue_thirty,
				'cin': cin,
				'filed_date': filed_date_thirty,


			},
			success: function(res) {
				$('.uorder-listing-loader1').attr('style', 'display:none');
				$('#30_40_days_wise_record').html("");
				$('#30_40_days_wise_record').html(res);
			}
		})

		return false;
	}
	ajax3045Record(1);
	$(function() {
		var start = moment().subtract(0, 'days');
		var end = moment();
		$('.datepicker_date_new').daterangepicker({
			startDate: start,
			endDate: end,
			autoUpdateInput: false,
			startOfWeek: 'sunday',
			ranges: {
				'Today': [moment(), moment()],
				'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
				'Last 7 Days': [moment().subtract(6, 'days'), moment()],
				'Last 30 Days': [moment().subtract(29, 'days'), moment()],
				'This Month': [moment().startOf('month'), moment().endOf('month')],
				'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
				'Next Month': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month').endOf('month')],
				'Next Week': [moment().add(1, 'weeks').startOf('isoWeek'), moment().add(1, 'weeks').endOf('isoWeek')],
				'Last Week': [moment().subtract(1, 'weeks').startOf('isoWeek'), moment().subtract(1, 'weeks').endOf('isoWeek')],
			}
		}, function(chosen_date, end_date) {

			$('.datepicker_date_new').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format('MM/DD/YYYY'));
			ajaxAgencyRecord(1);
		})





	});
	$(document).ready(function() {

		var expPassword = "{{$exp}}";
		var userExpDate = "{{auth()->user()->password_expired_at }}";
		var currentDate = "{{date('Y-m-d H:i:s')}}";
		if (expPassword == "exp" && userExpDate < currentDate) {
			$('#change-password-modal').modal('show');
		}

	});
	$('#changePasswordBtn').click(function(e) {

		$("#changePasswordBtn").attr('disabled', 'disabled');
		var password = $('#password').val();
		var cpassword = $('#cpassword').val();
		var passwordExpression = /^(?=[^a-z]*[a-z])(?=[^A-Z]*[A-Z])(?=\D*\d)[a-zA-Z\d!@#$%&*]+$/;
		var cnt = 0;
		if (password.trim() == '') {
			$('.password_error').html(" Please enter password");
			cnt = 1;
		} else {
			$.ajax({
				async: false,
				global: false,
				url: "{{ URL::to('/')}}/check-passwords",
				type: "POST",
				data: {
					password: password,
					id: "{{sha1(auth()->user()->id)}}",
					_token: "{{ csrf_token()}}"
				},
				success: function(response) {
					if (response == 1) {
						$('.password_error').html("Password Already Used Please Try Another Password");
						cnt = 1;
					}

				}
			});
		}
		if (cpassword.trim() == '') {
			$('.cpassword_error').html(" Please enter confirm password");
			cnt = 1;
		}

		if (password.trim() != '') {
			if (password.length < 8) {
				$('.password_error').html('Password atleast eight digit allowed');
				cnt = 1;
			} else {
				if (passwordExpression.test(password)) {

				} else {
					$('.password_error').html(
						'Password must contain at least 8 characters including 1 uppercase, 1 lowercase, a number and symbol'
					);
					cnt = 1;
				}
			}

		}

		if (password.trim() != '' && cpassword.trim() != '') {
			if (password.trim() != cpassword.trim()) {
				$('.cpassword_error').html(" Password and confirm password does not match.");
				cnt = 1;
			}

		}

		if (cnt == 1) {
			$("#changePasswordBtn").attr('disabled', false);
			return false;
		} else {
			$.ajax({
				async: false,
				global: false,
				url: "{{ URL::to('/')}}/update-expired-password",
				type: "POST",
				data: {
					password: password,
					password_confirmation: cpassword,
					id: "{{auth()->user()->id}}",
					_token: "{{ csrf_token()}}"
				},
				success: function(response) {
					if (response.status == 'success') {
						toastr.success(response.message);
						$('#change-password-modal').modal('hide');
					} else {
						toastr.error(response.message);
						$("#changePasswordBtn").attr('disabled', false);
					}

				}
			});
		}
	});
</script>