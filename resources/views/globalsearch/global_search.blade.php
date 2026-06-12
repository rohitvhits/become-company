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
				<div class="row list-name m-3">
					<div class="col-sm-6">
						<h4 class="card-title">Search By <?php echo $search; ?></h4>
					</div>

				</div>

				<div class="card-body">
					<div class="row">
						<div class="col-12">
							<div class="table-responsive">
								<table class="table table-bordered">
									<thead>

										<th>#</th>
										<?php if ($user->agency_fk == "") { ?>
											<th>Agency Name</th>
										<?php } ?>
										<th>Type/Discipline</th>
										<th>Patient Code</th>
										<th>Name/Mobile/Services</th>

										<th>Phone</th>
										<th>Assigned To</th>
										<th>Due Date</th>
										<th>Appointment Date - Location</th>
										<th>Created Date</th>

										<th>Status</th>
										@if($user->agency_fk == "")
										<th>Action</th>
										@endif

									</thead>
									<tbody>
										<?php
										
										
										if (count($appointment_details) > 0) {
											$i = 1 + (($appointment_details->currentPage() - 1) * $appointment_details->perPage());

											foreach ($appointment_details as $rows) {
												$appointmentShow=true;
												if($user->agency_fk !=""){
													if(!in_array($rows->agency_id,$agencyIds)){
														$appointmentShow = false;
													}
												}

												if($appointmentShow){
												?>
												<tr>
													<td><a href="<?php echo URL::to('/'); ?>/patient/view/<?php echo $rows->id; ?>"><?= '#' . ' ' . $rows->id ?></a></td>
													<?php if ($user->agency_fk == "") { ?>
														<td><?= $rows->agency_name ?></td>
													<?php } ?>
													<td>{{ $rows->type}}
														<br />
														<?php echo $rows->diciplin; ?>

													</td>
													<td> {{ $rows->patient_code}}</td>
													<td> <?php echo $rows->first_name . ' ' . $rows->last_name; ?><br />
														<?php echo $rows->mobile; ?><br />
														<?php echo $rows->name; ?><br /></td>

													<td><?php echo $rows->phone; ?></td>
													<td>{{ $rows->assignToUser!=null && isset($rows->assignToUser->users) ? $rows->assignToUser->users->full_name : 'N/A' }}</td>
													<td> @if($rows->due_date!='')

														<?php if ($rows->due_date != '1969-12-31') {
															echo date('m/d/Y h:i A', strtotime($rows->due_date));
														} ?>

														@endif</td>
													<td>
														@if(strtolower($rows->type) == 'caregiver')
															@if(isset($rows->appointment_date))
																<label class="badge badge-success">Schedule Appointment</label> <br/>
																<?php if ($rows->appointment_date != '') {
																		echo date('m/d/Y', strtotime($rows->appointment_date));
																	} ?> <?php if ($rows->start_time != '' && $rows->end_time) {
																				$start_time = date('h:i A', strtotime($rows->start_time));
																				$end_time = date('h:i A', strtotime($rows->end_time));
																			?><br /><?php
																					echo $start_time . ' - ' . $end_time;
																				} ?>
																	<br />
																<?php echo $rows->location_name; ?><br />
															@endif
															@if(isset($rows->telehealth_date_time))
																@if(isset($rows->appointment_date))
																	<hr/>
																@endif
																<label class="badge badge-primary">Telehealth Appointment</label>
																<br/>
																{{date('m/d/Y', strtotime($rows->telehealth_date_time))}}<br />
																{{$rows->telehealth_time_frame ?: $rows->telehealth_time_slot}} <br/>
																Nurse: {{$rows->telehealth_nurse}} <br/>
															@endif
														@endif
														@if(strtolower($rows->type) == 'patient')
															@if ($rows->appointment_date != '')
																<label class="badge badge-success">Schedule Appointment</label> <br/>   
																{{date('m/d/Y h:i A', strtotime($rows->appointment_date))}}
															@endif
															@if(isset($rows->telehealth_date_time))
																@if(isset($rows->appointment_date))
																	<hr/>
																@endif
																<label class="badge badge-primary">Telehealth Appointment</label>
																<br/>
																{{date('m/d/Y', strtotime($rows->telehealth_date_time))}}<br />
																{{$rows->telehealth_time_frame ?: $rows->telehealth_time_slot}} <br/>
																Nurse: {{$rows->telehealth_nurse}} <br/>
															@endif
														@endif
													</td>
													<td><?php echo date('m/d/Y h:i A', strtotime($rows->created_date)); ?><br />
														{{$rows->created_by_username}}
													</td>
													<td>
														<?php
														if ($rows->status == 'Pending' || $rows->status == 'pending') {
														?>
															<label class='badge badge-warning'>Pending</label>

														<?php } ?>
														<?php

														if (strtolower($rows->status) == 'booked') {
														?>
															<label class='badge badge-info'>Booked</label>

														<?php } ?>
														<?php

														if ($rows->status == 'completed') {
														?>
															<label class='badge badge-success'>Completed</label>

														<?php } ?>
														<?php

														if ($rows->status == 'in process') {
														?>
															<label class='badge badge-secondary'>In process</label>

														<?php } ?>
														<?php
														if ($rows->status == 'cancelled' or  $rows->status == 'refuese' or $rows->status == 'no show' or  $rows->status == 'no answer' or $rows->status == 'unable to contact') {
														?>
															<label class='badge badge-danger'>Cancelled</label>

														<?php } ?>
														<?php

														if ($rows->status == 'noshow') {
														?>
															<label class='badge badge-light'>No Show</label>

														<?php } ?>
														<?php

														if ($rows->status == 'arrived') {
														?>
															<label class='badge badge-primary'>Arrived</label>

														<?php } ?>
														<?php

														if ($rows->status == 'processing') {
														?>
															<label class='badge badge-secondary'>Processing</label>

														<?php }
														if ($rows->status == 'refused') { ?>
															<label class='badge badge-danger'>Refused</label>
														<?php }
														if ($rows->status == 'hospitalized/rehab') { ?>
															<label class='badge badge-info'>Hospitalized/Rehab</label>
														<?php }
														if ($rows->status == 'Pending Termination') { ?>
															<label class='badge badge-danger'>Pending Termination</label>
														<?php }
														if ($rows->status == 'On Hold') { ?>
															<label class='badge badge-secondary'>On Hold</label>
														<?php }
														if ($rows->status == 'On Leave') { ?>
															<label class='badge badge-info'>On Leave</label>
														<?php }
														if ($rows->status == 'Terminated') { ?>
															<label class='badge badge-danger'>Terminated</label>
														<?php }
														if ($rows->status == 'unableToContact') { ?>

															<label class='badge badge-danger'>Unable To Contact</label>
														<?php } ?>
														@if ($rows->status == '1st Attempt - Unable to Contact' || $rows->status == '2nd Attempt - Unable to Contact' || $rows->status == '3rd Attempt - Unable to Contact' || $rows->status == 'Patient Asked to Reschedule' || $rows->status == 'New Order Received')
															<label for="" class='badge badge-info'>{{$rows->status}}</label>
														@endif

														@if ($rows->status == 'Telehealth Completed' || $rows->status == 'Telehealth Completed , Pending Forms' || $rows->status == 'Form Completed' || $rows->status == 'Service Provided')
															<label for="" class='badge badge-success'>{{$rows->status}}</label>
														@endif

														@if ($rows->status == 'Patient Deceased' || $rows->status == 'Appointment was missed' || $rows->status == 'Appointment Missed' || $rows->status == 'Closed Temporarily')
															<label for="" class='badge badge-danger'>{{$rows->status}}</label>
														@endif

														@if ($rows->status == 'Signed' || $rows->status == 'Signed & Sent Back to the Agency' || $rows->status == 'New Form Requested')
															<label for="" class='badge badge-primary'>{{$rows->status}}</label>
														@endif	
@if (strtolower($rows->status) == 'inactive')
															<label for="" class='badge badge-danger'>{{ucfirst($rows->status)}}</label>
														@endif
													</td>
													@if($user->agency_fk == "")
													<td>
														@if(empty($rows->archived_at))
															<a title="Archive" href="javascript:void(0)" onclick="getArchiveById({{ $rows->id }})">
																<i class="fa fa-archive"></i>
															</a>
														@else
															<a title="Unarchive" href="javascript:void(0)" onclick="getUnArchiveById({{ $rows->id }})">
																<i class="fa fa-file-archive-o"></i>
															</a>
														@endif
													</td>
													@endif
												</tr>

											<?php } }
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
									<?php if (count($appointment_details) > 0) { ?>
										{{$appointment_details->appends(request()->input())->links()}}
									<?php } ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		
	</div>


<script>
    var _GLOBAL_SEARCH_ARCHIVE_URL   = "{{ url('patient/patient-archive') }}";
    var _GLOBAL_SEARCH_UNARCHIVE_URL = "{{ url('patient/patient-unarchive') }}";
    var _GLOBAL_SEARCH_CSRF          = "{{ csrf_token() }}";
</script>
<script src="{{ asset('assets/modulejs/globalsearch/global_search_module.js') }}?time={{ env('timestamp')}}"></script>
	@include('include/footer')