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
					<h4 class="card-title">Trust Records List ({{$query->total()}})</h4>
				</div>
				<div class="col-sm-7 pull-right">
					<!--<a href="javascript:void(0)" onclick="getArchive()" class="btn btn-info btn-fw btn-sm pull-right"><i class="mdi mdi-reload"></i>Patient Archive</a>-->
					<a href="<?php echo URL::to('/'); ?>/trust-report-export?name=<?php echo $name; ?>&agency_fk=<?php echo $agency_fk; ?>&email=<?php echo $email; ?>&phone=<?php echo $phone; ?>&emcuser=<?php echo $emcuser; ?>&medicaid_issue=<?php echo $medicaid_issue; ?>&record_form=<?php echo $record_form; ?>&month=<?php echo $month; ?>&five_month=<?php echo $five_month; ?>&patient_status=<?php echo $patient_status; ?>&created_date=<?php echo $created_date; ?>&disability={{ $disability }}&trust_approved={{ $trust_approved }}" class="btn btn-success pull-right btn-fw btn-sm" id="test_record"><i class="mdi mdi-file-export"></i>Export</a>
					<a href="<?php echo URL::to("/"); ?>/trust-report" class="btn btn-danger pull-right btn-fw btn-sm"><i class="mdi mdi-reload"></i> Reset</a>

				</div>
			</div>

			<div class="card-body compact-view">
				<div class="row">
					<div class="col-12">
						<div class="wmd-view-topscroll">
							<div class="scroll-div1">
							</div>
						</div>
						<div class="wmd-view">
							<div class="scroll-div2">

								<table id="order-listing1" class="table table-bordered">
									<thead>
										<tr>
											<!--<th></th>-->
											<th>Record</th>
											@if(in_array($user->user_type_fk,array(3,4)))
											<th>Agency Name</th>
											@endif
											<th>Name</th>
											<th>Email</th>
											<th>Phone</th>
											<th>EMC User</th>
											<th>Medicaid Issue</th>
											<th>Record Form</th>
											<th>3 Month Date</th>
											<th>5 Month Date</th>
											<th>Trust Approved</th>
											<th>Disability</th>
											<th>Status</th>


											<th>Created Date</th>
											<th>Created By</th>

										</tr>
										<tr>
											<form method="get" action="">
												<!--<td><input type="checkbox" name="" id="main_checkBox1"><br>
										<span class="main_checkBox1_error" style="color:red"></span>
									</td>-->
												<td><input type="submit" name="search" class="btn btn-primary btn-fw pull-right btn-sm" value="search"></td>
												@if(in_array($user->user_type_fk,array(3,4)))

												<td>

													<select class="form-control" name="agency_fk1" id="agency_fk" onchange="getUserList(this.value)">
														<option value="">Select agency</option>
														<?php foreach ($agencyList as $rwAgency) { ?>
															<option value="<?php echo $rwAgency->id ?>" <?php echo (($agency_fk) == $rwAgency->id) ? 'selected' : ''; ?>><?php echo $rwAgency->agency_name; ?></option>
														<?php } ?>
													</select>
												</td>
												@endif

												<td><input class="form-control" type="text" name="name" id="name" value="<?php echo $name ?>"></td>
												<td><input class="form-control" type="text" name="email" id="email" value="<?php echo $email ?>"></td>
												<td><input class="form-control" type="text" name="phone" id="phone" value="<?php echo  $phone ?>"></td>
												<td>@if(in_array($user->user_type_fk,array(3,4)))
													<select name="emcuser" class="form-control">
														<option value="">Select Emc User</option>
														<?php if (!empty($userList)) {
															foreach ($userList as $ke) { ?>
																<option value="<?php echo $ke->id; ?>" <?php if ($emcuser == $ke->id) {
																											echo "selected='selected'";
																										} ?>><?php echo $ke->first_name . ' ' . $ke->last_name; ?></option>
														<?php }
														} ?>
													</select>
													@endif
												</td>
												<td>

													<select class="form-control" name="medicaid_issue" id="medicaid_issue">
														<option value="">Medicaid Issue </option>
														<?php
														foreach ($masterData as $rwStatusd) {
															if (in_array($rwStatusd->master_type_fk, array("4"))) { ?>
																<option value="<?= $rwStatusd->id ?>" <?= ($medicaid_issue == $rwStatusd->id) ? "selected" : '' ?>><?= $rwStatusd->name ?> </option>
														<?php }
														} ?>
													</select>
												</td>
												<td>
													<select name="record_form" class="form-control">
														<option value="">Select</option>
														<option value="1" <?php if (isset($record_form) && $record_form == 1) {
																				echo "selected='selected'";
																			} ?>>Ny Best Medical Care</option>
														<option value="0" <?php if (isset($record_form) && $record_form == 0) {
																				echo "selected='selected'";
																			} ?>>NY Best Medicalss</option>

													</select>
												</td>
												<th>
													<input type="text" name="month" class="form-control datepickernn" value="@if(isset($month) && $month !='') {{$month}} @endif">
												</th>
												<td><input autocomplete="off" type="text" name="five_month" class="form-control datepicker1" value="<?php if ($five_month != '') {
																																						echo $five_month;
																																					} ?>"></td>
												<td>
													<select class="form-control" name="trust_approved" id="trust_approved">
														<option value="">Select Trust Approved </option>
														<option value="1" @if($trust_approved==1) selected @endif>Yes</option>
														<option value="0" @if($trust_approved==0) selected @endif>No</option>

													</select>
												</td>
												<td>
													<select class="form-control" name="disability" id="disability">
														<option value="">Select Disability </option>
														<option value="Yes" @if($disability=="Yes" ) selected @endif>Yes</option>
														<option value="No" @if($disability=="No" ) selected @endif>No</option>

													</select>
												</td>
												<td>

													<select class="form-control" name="patient_status" id="patient_status">
														<option value="">Select Status </option>
														<?php
														foreach ($masterData as $rwStatus) {
															if (in_array($rwStatus->master_type_fk, array("3"))) { ?>
																<option value="<?= $rwStatus->id ?>" <?= ($patient_status == $rwStatus->id) ? "selected" : '' ?>><?= $rwStatus->name ?> </option>
														<?php }
														} ?>
													</select>
												</td>
												<td><input autocomplete="off" type="text" name="created_date" class="form-control datepicker_date" value="<?php if ($created_date != '') {
																																								echo $created_date;
																																							} ?>"></td>
												<td>

												</td>

											</form>
										</tr>
									</thead>
									<tbody>
										<?php

										if (count($query) > 0) {
											$i = 1 + (($query->currentPage() - 1) * $query->perPage());

											foreach ($query as $row) { ?>
												<tr>
													<!--<td><input type="checkbox" class="cbox_id" value="<?php echo $row->id; ?>" id="cbox_id<?php echo $row->id; ?>"></td>-->
													<td><a href="<?php echo URL::asset("/"); ?>record/<?= $row->id ?>"><?= $row->id ?> </a></td>
													@if(in_array($user->user_type_fk,array(3,4)))

													<td>
														<span id="changeAgencyList<?= $row->id ?>">
															<?php /* onclick="changeAgency(<?= $row->id?>)" */ ?>
															<span id="<?php echo $row->id; ?>"><?= $row->agency_name ?></span>
															<?php  /*
															<span id="dropid<?= $row->id?>" style="display:none">
																<select onchange="saveToDatabase(this.value,'agency_fk',<?php echo $row->id;?>)" class="form-control" name="agency_fk" id="agency_fk">
																	<option value="">Select agency</option>
																	<?php foreach ($agencyList as $rwAgency) { ?>
																		<option value="<?php echo $rwAgency->id ?>" <?php echo (($row->agency_fk)==$rwAgency->id)? 'selected' : ''; ?> ><?php echo $rwAgency->agency_name; ?></option>
																		<?php } ?>
																</select>
															</span> */
															?>
														</span>

													</td>
													@endif

													<td>
														@if(in_array($user->user_type_fk,array(3,4)))

														<span contenteditable="true" onBlur="saveToDatabase(this,'first_name','<?php echo $row->id; ?>')" onClick="editRow(this);">
															<?= ($row->first_name != '') ? $row->first_name : '  ' ?>

														</span>
														<span contenteditable="true" onBlur="saveToDatabase(this,'middle_name','<?php echo $row->id; ?>')" onClick="editRow(this);"><?= ($row->middle_name != '') ? $row->middle_name : '  '; ?> </span>
														<span contenteditable="true" onBlur="saveToDatabase(this,'last_name','<?php echo $row->id; ?>')" onClick="editRow(this);"><?= ($row->last_name != '') ? $row->last_name : ' '; ?></span>
														@endif

														@if(!in_array($user->user_type_fk,array(3,4)))
														{{$row->first_name}} {{$row->middle_name}} {{$row->last_name}}


														@endif

													</td>


													<!-- 	<td><?= $row->first_name . ' ' . $row->middle_name . ' ' . $row->last_name ?></td> -->
													<td contenteditable="{{in_array($user->user_type_fk,array(3,4))?'true':'false'}}" onBlur="saveToDatabase(this,'email','<?php echo $row->id; ?>')" onClick="editRow(this);">
														<?= $row->email ?> </td>
													<td contenteditable="{{in_array($user->user_type_fk,array(3,4))?'true':'false'}}" onBlur="saveToDatabase(this,'phone','<?php echo $row->id; ?>')" onClick="editRow(this);"><?= $row->phone ?>

													</td>
													<td><?php if (isset($userArray[$row->emc_rep]) && $userArray[$row->emc_rep] != '') {
															echo $userArray[$row->emc_rep];
														} ?>

													</td>
													<td>
														<?php if (isset($masterDataArray[$row->medicaid_issue])) {
															echo $masterDataArray[$row->medicaid_issue];
														} else {
															echo '-';
														} ?>
													</td>
													<td>
														<?php

														if ($row->ny_medicare_id != '') { ?>
															<label class='badge badge-primary badge-pill'>Ny Best Medical Care</label>

														<?php } else { ?>
															<label class='badge badge-info badge-pill'>NY Best Medicalss</label>
														<?php } ?>



													</td>
													<td><?php if ($row->month != '') {
															echo date('m/d/Y', strtotime($row->month));
														} ?></td>
													<td>
														<?php if ($row->five_month != '') {
															echo date('m/d/Y', strtotime($row->five_month));
														} ?>
													</td>
													<td> @if($row->trust_approved ==1) Yes @else No @endif</td>
													<td> @if($row->disability =="Yes") Yes @else No @endif</td>
													<td>
														<span id="change_patient_status<?= $row->id ?>"><span onclick="changeStatus(<?= $row->id ?>)"><?php if (isset($masterDataArray[$row->patient_status])) {
																																						echo $masterDataArray[$row->patient_status];
																																					} else {
																																						echo '-';
																																					} ?></span></span>
													</td>
													<td><?php if (isset($row->created_at) && $row->created_at != '') {
															echo date('m/d/Y', strtotime($row->created_at));
														} ?></td>

													<td>{{ $row->username}}</td>

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
	</div>

	<div class="modal fade" id="modal-default" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div id="messages_id"></div>
		</div>
	</div>

	<div class="modal fade" id="modal-default-patient" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Patient </h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">×</span>
					</button>
				</div>

				<div class="modal-body">
					<form action="" method="post" enctype="multipart/form-data" id="patient_record_submit">
						<input type="hidden" name="patient_record_id" id="patient_record_id">
						<div class="form-group">
							<label for="recipient-name" class="col-form-label">Type<span style="color:red">*</span>:</label>
							<div class="col-sm-8">
								<input type="radio" name="radios" value="Caregiver" onclick="getResponse('Caregiver')">Caregiver
								<input type="radio" name="radios" value="Patient" onclick="getResponse('Patient')">Patient
							</div>
							<span id="radios_error" style="color:red"></span>
						</div>
						<div class="form-group">
							<label for="recipient-name" class="col-form-label">Services<span style="color:red">*</span>:</label>
							<div class="col-sm-8">
								<select name="service_id[]" id="service_id" class="js-example-basic-multiple w-100" multiple="multiple">
									<option value="">Select</option>
								</select>
								<span id="service_id_error" style="color:red"></span>
							</div>
						</div>
					</form>
				</div>

				<div class="modal-footer">
					<button type="button" class="btn btn-success" onclick="getPatientSRecord()">Submit</button>
					<button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
				</div>
			</div>
		</div>
	</div>

	@include('include/footer')
	<script src="<?php echo URL::to('/'); ?>/assets/vendors/select2/select2.min.js"></script>
	<script src="<?php echo URL::to('/'); ?>/assets/js/select2.js"></script>
	<script src="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.js"></script>
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

		function validation() {

		}

		function validation1() {

			var agency_fk = $('#agency_fk').val();
			var name = $('#name').val();
			var email = $('#email').val();
			var phone = $('#phone').val();
			var patient_status = $('#patient_status').val();
			if (agency_fk == '' && name == '' && email == '' && phone == '' && patient_status == '') {
				alert('please select any one');
				return false;
			} else {
				return true;
			}
		}




		/*vishal d patel code end chat message listing*/
	</script>

	<script type="text/javascript" src="<?php echo URL::to('/'); ?>/assets/js/moment.min.js"></script>
	<script type="text/javascript" src="<?php echo URL::to('/'); ?>/assets/js/daterangepicker.min.js"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo URL::to('/'); ?>/css/daterangepicker.css" />

	<script>
		$('.datepicker').datepicker();
		$(function() {
			var start = moment().subtract(0, 'days');
			var end = moment();
			$('.datepickernn').daterangepicker({
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

				$('.datepickernn').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format('MM/DD/YYYY'));
			})

			$('.datepicker1').daterangepicker({
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

				$('.datepicker1').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format('MM/DD/YYYY'));
			})
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
			})

		});
		// Binds the hidden input to be used as datepicker.
		$('.datepicker-input').datepicker({
			dateFormat: 'mm/dd/yy',
			onClose: function(dateText, inst) {
				// When the date is selected, copy the value in the content editable div.
				// If you don't need to do anything on the blur or focus event of the content editable div, you don't need to trigger them as I do in the line below.
				if (dateText != '') {
					$(this).parent().find('.date').focus().html(dateText).blur();
				}
			}

		});
		// Shows the datepicker when clicking on the content editable div
		$('.date').click(function() {
			// Triggering the focus event of the hidden input, the datepicker will come up.
			$(this).parent().find('.datepicker-input').focus();
		});


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