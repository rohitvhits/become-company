<table id="" class="table table-bordered">
							<thead>
								<tr>
									<th>#</th>
								 
								  <th>Agency Name</th>
								  <th>Doctor Name</th>
								  <th>Type</th>
								  <th>Patient Name</th>
								  <th>Phone Number</th>
								   <th>Date of Birth</th>
								   <th>Record Form</th>
								    
								  <th>Telehealth Date</th>
								  
								</tr>
							</thead>
							<tbody>
							<tr>
								<td><input type="button" onclick="getAjax()" name="search" class="btn btn-primary btn-sm btn-rounded btn-fw  pull-right" value="search"></td>
								<td>
									<select name="type" class="form-control" id="agency_id">
										<option value="">Select Agency</option>
										@foreach($agency_list as $va)
											<option value="{{$va->id}}" @if($record_id ==$va->id) selected @endif>{{$va->agency_name}}</option>
										@endforeach
										
									</select>
									
								</td>
								<td>
									
								</td>
								<td>
									<select name="type"  id="type_id">
										<option value="">Select Type</option>
										<option value="Patient" @if($type_id == "Patient") selected @endif>Patient</option>
										<option value="Caregiver" @if($type_id == "Caregiver") selected @endif>Caregiver</option>
									</select>
								</td>
								<td>
									<input type="text" class="form-control" id="fullname_id" value="{{$fullname_id}}">
								</td>
								<td></td>
								<td></td>
								<td>
									<select name="type" class="form-control" id="record_id">
										<option value="">Select Record</option>
										<option value="0" @if($record_id == 0) selected @endif>Ny Best Medical Care</option>
										<option value="1" @if($record_id == 1) selected @endif>NY Best Medicalss</option>
									</select>
								
								</td>
								<td>
								
								</td>
							</tr>
							@php 
							$cnt = 1;
							@endphp 
								@if(count($upcomming_telehealth) >0)
									@foreach($upcomming_telehealth as $upcomming)
										<tr>
											<td><a href="{{url('patient/view')}}/{{$upcomming->id}}"> #<?php echo $upcomming->id;?></a></td>
											<td>{{ucwords($upcomming->agency_name)}}</td>
											<td>{{ucwords($upcomming->full_name)}}</td>
											<td>{{ucwords($upcomming->type)}}</td>
											<td>{{ucwords($upcomming->first_name)}} {{ucwords($upcomming->last_name)}}</td>
											<td>{{$upcomming->phone}}</td>
											<td> @if ($upcomming->dob != '0000-00-00') 
													{{Common::convertMDY($upcomming->dob)}}
												@endif
											</td>
											<td>
											@if($upcomming->record_id != '')
												<label class='badge badge-info badge-pill'>NY Best Medicalss</label>
												@else
												<label class='badge badge-primary badge-pill'>Ny Best Medical Care</label>
												@endif
										
											</td>
											<td>
												@if($upcomming->telehealth_date_time != '')
												{{Common::convertMDY($upcomming->telehealth_date_time)}}
												@endif
													
										
											</td>
										
								</tr>
											
									@endforeach
								@endif
								@if(count($upcomming_telehealth) ==0)
									<tr>
										<td>No record available</td>
									</tr>
								@endif
							</tbody>
						</table>
						<script type="text/javascript" src="<?php echo URL::to('/');?>/assets/js/moment.min.js"></script>
<script type="text/javascript" src="<?php echo URL::to('/');?>/assets/js/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo URL::to('/');?>/css/daterangepicker.css" />
						<script>
						$(function () {
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
								}, function (chosen_date, end_date) {

									$('.datepickernn').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format('MM/DD/YYYY'));
								})
								
					});
</script>