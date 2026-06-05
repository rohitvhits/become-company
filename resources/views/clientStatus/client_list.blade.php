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
					<h4 class="card-title">Client Report</h4>
				</div>
				<div class="col-sm-6 pull-right">
					<a href="<?php echo URL::to('/'); ?>/report/client-status/export-csv?agency=<?php echo $agency_fk; ?>&patient_name=<?php echo $patient_name; ?>&medicarItem=<?php echo $medicarItem; ?>&file_date=<?php echo $file_date; ?>&patient_status=<?php echo $patient_status; ?>&cin=<?php echo $cin; ?>&recent_month=<?php echo $recent_month; ?>&emc_rep=<?php echo $emc_rep; ?>&agency_rep=<?php echo $emc_rep; ?>&phone=<?php echo $phone; ?>&undercareItem=<?php echo $undercareItem; ?>&trustName=<?php echo $trustName; ?>" class="btn btn-success pull-right btn-fw btn-sm" id="test_record"><i class="mdi mdi-file-export"></i>Export</a>
					<a href="<?php echo URL::to('/'); ?>/report/client-status" class="btn btn-danger pull-right btn-fw btn-sm"><i class="mdi mdi-reload"></i> Reset</a>
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
										<th>Record ID</th>
										<th>Agency Name</th>
										<th>Record Name</th>
										<th>Medicaid Item</th>
										<th>File Date</th>
										<th>Status</th>
										<th>CIN Number</th>
										<th>Recert Month</th>
										<th>EMC rep</th>
										<th> Agency Rep</th>
										<th>Phone Number</th>

										<th> Undercare Name</th>
										<th>Trust Name</th>
										<th>Action</th>
									</tr>

									<tr>
										<form method="get" action="">
											<td></td>
											<td></td>
											<td>

												<select class="form-control" name="agency" id="agency">
													<option value="">Select agency</option>
													<?php foreach ($agencyList as $rwAgency) { ?>
														<option value="<?php echo $rwAgency->id ?>" <?php echo (($agency_fk) == $rwAgency->id) ? 'selected' : ''; ?>><?php echo $rwAgency->agency_name; ?></option>
													<?php } ?>
												</select>
											</td>
											<td>
												<input type="text" name="patient_name" value="<?php echo $patient_name; ?>" class="form-control" placeHolder="Enter Record Name">

											</td>
											<td>
												<select name="medicarItem" class="form-control">
													<option value="">Select medicare item</option>
													<?php
													if (count($masterData) > 0) {
														foreach ($masterData as $agency) {
															if (in_array($agency->master_type_fk, array("4"))) {
													?>
																<option value="<?php echo $agency->id; ?>" <?php if ($medicarItem == $agency->id) {
																												echo "selected='selected'";
																											} ?>><?php echo $agency->name; ?></option>
													<?php }
														}
													} ?>
												</select>

											</td>
											<td>
												<input type="text" name="file_date" value="<?php echo $file_date; ?>" class="form-control datepicker" placeHolder="Enter File Date">

											</td>
											<td>

												<select class="form-control" name="patient_status" id="patient_status">
													<option value="">Select Status </option>
													<?php
													foreach ($masterData as $rwStatus) {
														if (in_array($rwStatus->id, array("16", "62"))) { ?>
															<option value="<?= $rwStatus->id ?>" <?= ($patient_status == $rwStatus->id) ? "selected" : '' ?>><?= $rwStatus->name ?> </option>
													<?php }
													} ?>
												</select>
											</td>
											<td>
												<input type="text" name="cin" class="form-control" value="<?php echo $cin; ?>" placeHolder="Enter CIN Number">
											</td>
											<td>
												<?php
												$month_names = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
												?>


												<select name="recent_month" class="form-control">
													<option value="">Select Month</option>
													<?php $count = 1;

													foreach ($month_names as $va) {

													?>
														<option value="<?php echo $count; ?>" <?php if ($recent_month == $count) {
																									echo "selected='selected'";
																								} ?>><?php echo $va; ?></option>
													<?php $count++;
													} ?>
												</select>

											</td>
											<td>
												<select name="emc_rep" class="form-control">
													<option value="">Select EMC Rep</option>
													<?php $count = 1;
													if (count($emdarray) > 0) {
														foreach ($emdarray as $val) {

													?>
															<option value="<?php echo $val->id; ?>" <?php if ($emc_rep == $val->id) {
																										echo "selected='selected'";
																									} ?>> <?php echo $val->first_name . ' ' . $val->last_name; ?></option>
													<?php  }
													} ?>
												</select>

											</td>
											<td>
												<select name="agency_rep" class="form-control">
													<option value="">Select Agency Rep</option>
													<?php
													if (count($agencyRap) > 0) {
														foreach ($agencyRap as $agency) {

													?>
															<option value="<?php echo $agency->id; ?>" <?php if ($agency_rep == $agency->id) {
																											echo "selected='selected'";
																										} ?>><?php echo $agency->first_name . ' ' . $agency->last_name; ?></option>
													<?php }
													}  ?>
												</select>
											</td>
											<td>
												<input type="text" name="phone" class="form-control" value="<?php echo $phone; ?>" placeHolder="Enter Phone">
											</td>

											<td>
												<select name="undercareItem" class="form-control">
													<option value="">Select undercare item</option>
													<?php
													if (count($masterData) > 0) {
														foreach ($masterData as $undercare) {
															if (in_array($undercare->master_type_fk, array("5"))) {
													?>
																<option value="<?php echo $undercare->id; ?>" <?php if ($undercareItem == $undercare->id) {
																													echo "selected='selected'";
																												} ?>><?php echo $undercare->name; ?></option>
													<?php }
														}
													} ?>
												</select>

											</td>
											<td>
												<select name="trustName" class="form-control">
													<option value="">Select Trust Name</option>
													<?php
													if (count($masterData) > 0) {
														foreach ($masterData as $trustCare) {
															if (in_array($trustCare->master_type_fk, array("6"))) {
													?>
																<option value="<?php echo $trustCare->id; ?>" <?php if ($trustName == $trustCare->id) {
																													echo "selected='selected'";
																												} ?>><?php echo $trustCare->name; ?></option>
													<?php }
														}
													} ?>
												</select>

											</td>
											<td>
												<input type="submit" name="search" class="btn btn-primary btn-sm btn-rounded btn-fw  pull-right" value="search">
											</td>





										</form>
									</tr>

								</thead>
								<tbody>
									<?php
									if (count($report_list) > 0) {
										$i = 1 + (($report_list->currentPage() - 1) * $report_list->perPage());
										foreach ($report_list as $row) {
									?>
											<tr>
												<td><?php echo $i++; ?></td>
												<td><a href="<?php echo URL::to('/'); ?>/record/<?php echo $row->id; ?>"><?php echo 'Record #' . $row->id; ?></a></td>
												<td><a href="<?php echo URL::to('/'); ?>/agency-view/<?php echo $row->agency_fk; ?>"><?php echo ucfirst($row->agencyName); ?></a></td>
												<td><a href="<?php echo URL::to('/'); ?>/record/<?php echo $row->id; ?>"><?php echo ucfirst($row->first_name . ' ' . $row->last_name); ?></a></td>

												<td><?php echo ucfirst($row->MadicareItem); ?></td>
												<td><?php if ($row->file_date != '') {
														echo date('m-d-Y', strtotime($row->file_date));
													} ?></td>
												<td><?php echo ucfirst($row->statusName); ?></td>
												<td><?php echo ucfirst($row->cin); ?></td>
												<td><?php if ($row->recent_month != '') {
														echo date('M', strtotime($row->recent_month));
													} ?></td>
												<td><?php echo ucfirst($row->EMCUserName); ?></td>
												<td><?php echo ucfirst($row->AgencyUserRep); ?></td>
												<td><?php echo ucfirst($row->phone); ?></td>
												<td><?php echo ucfirst($row->UndercareActionName); ?></td>
												<td><?php echo ucfirst($row->TrustName); ?></td>
												<td></td>
											</tr>

										<?php }
									}
									if (count($report_list) == 0) {  ?>
										<tr>
											<td colspan="13">No record available</td>

										</tr>
									<?php } ?>
								</tbody>
							</table>

							<div class="pull-right pegination-margin">
								{{$report_list->appends(request()->input())->links("pagination::bootstrap-4")}}
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