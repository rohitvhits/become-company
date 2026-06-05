@include('include/header')
@include('include/sidebar')
<?php

?>
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css">
<link href="<?php echo URL::to('/'); ?>/assetsd/css/vertical-layout-light/jquery.timepicker.css" rel="stylesheet" type="text/css">
<link href="<?php echo URL::to('/');?>/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
<style>
	.mini-card .form-control {
		height: 20px;
		padding: 2px;
	}

	dl {
		margin-top: 0;
		margin-bottom: 20px;
	}

	ul,
	ol,
	dl {
		padding-left: 0px !important;
	}

	.dl-horizontal dt {
		float: left;
		width: 72px;
		clear: left;
		text-align: right;
		/* overflow: hidden; */
		text-overflow: ellipsis;
		white-space: nowrap;
	}

	.dl-horizontal dt {
		float: left;
		width: 85px;
		clear: left;
		text-align: right;
		text-overflow: ellipsis;
		white-space: nowrap;
	}

	#otherupdated_id {
		width: 750px;
	}

	#other_id {
		width: 750px;
	}

	h6.fm_1 {
		/* text-align: end;*/
		font-size: 14px;
	}

	dt {
		font-weight: 700;
	}

	.dl-horizontal dd {
		margin-left: 90px;
		margin-bottom: 0px;
	}

	.ml-3,
	.rtl .settings-panel .sidebar-bg-options .rounded-circle,
	.rtl .settings-panel .sidebar-bg-options .color-tiles .tiles,
	.rtl .settings-panel .color-tiles .sidebar-bg-options .tiles,
	.mx-3 {
		margin-left: 1rem !important;
		width: 100%;
	}

	#hr2 .dl-horizontal dd {
		margin-left: 110px;
	}

	#hr2 .dl-horizontal dt {
		width: 101px;
	}

	.profile-feed-item.abc {
		padding: 0;
		border: none;
	}

	.profile-feed-item.border {
		border: none;
	}

	.htv {
		height: 50%;
	}

	.removeSpace {
		margin-top: 0px !important;
		margin-bottom: 0px !important
	}

	#loadersId {
		float: left
	}

	.tab-content {
		padding: 0.5rem;
	}

	.alert-warning {
		color: #856404;
		background-color: #fff3cd;
		border-color: #ffeeba;
	}
</style>
<!--main-container-part-->
<div class="main-panel">
	<div class="content-wrapper">

		<div class="dashboard-header d-flex flex-column grid-margin">
			<div class="d-flex align-items-center justify-content-between flex-wrap border-bottom pb-3 mb-3">
				<div class="d-flex align-items-center">
				<h4 class="mb-0 font-weight-bold">ID # <?= $record->id . " - " . ucwords($record->first_name) . ' ' . ucwords($record->last_name) . " " ?> </h4> &nbsp;&nbsp;<?php echo $record->phone; ?> ( <?php echo $record->agency_name; ?> )
				</div>
				<?php if ($user['user_type_fk'] == 184) { ?>
					<div class="button-wrapper d-flex align-items-center mt-md-3 mt-xl-0">
						<?php if ($record->status == 'Pending') { ?>
							<button class="btn btn-info btn-sm  d-none d-md-block" onclick="getStatus('scheduled');">Scheduled </button> &nbsp;&nbsp;
						<?php } ?>

						<?php if ($record->status == 'booked') { ?>
							<button class="btn btn-success btn-sm  d-none d-md-block" onclick="getStatus('completed');">Mark as Completed </button> &nbsp;&nbsp;
						<?php } ?>

						<button class="btn btn-danger btn-sm  d-none d-md-block" data-toggle="modal" class="pull-right" data-target="#exampleModal-cancel" data-whatever="@mdo">Mark as Cancel </button> &nbsp;&nbsp;
						
					</div>
				<?php } ?>
			</div>
		</div>
		
		
		<div class="row">

			<div class="col-12 grid-margin stretch-card">
				<div class="card">
					<div class="card-body mini-card">

						<div class="row">

							<div class="profile-feed col-12 pull-right" id="edit_medical">
								<h6 class="card-title">Appointment Details
									<?php if ($user['user_type_fk'] == 184) { ?>
									<?php
				
												if ($record->status != 'completed' && $record->status != 'cancelled') {
												?>
										<a href="javascript:void(0)" class="pull-right btn btn-info btn-sm  d-none d-md-block" data-toggle="modal" class="pull-right" data-target="#exampleModal-4" data-whatever="@mdo"> Schedule Appoinment</a>
									<?php } } ?>
									
								</h6>

								<div class="d-flex align-items-start profile-feed-item abc" id="hr2">
									<div class="ml-1 col-4">

										<dl class="dl-horizontal">
											<dt> First Name</dt>
											<dd> <?php echo $record->first_name.'<br>'; ?></dd>
											<dt> Mobile</dt>
											<dd> <?php echo $record->mobile.'<br>'; ?></dd>

											<dt> Created Date</dt>
											<dd> <?php echo Common::convertMDY($record->created_date).'<br>'; ?></dd>
											<?php if ($record->appointment_date  != '0000-00-00 00:00:00' && $record->appointment_date != '') { ?>
												<dt> Appointment </dt>
												<dd>&nbsp;</dd>
												<dt> Date</dt>

												<dd> <?php echo Common::convertMDY($record->appointment_date).'<br>'; ?></dd>
											<?php } ?>
											<?php if ($record->appointment_date  != '0000-00-00 00:00:00' && $record->appointment_date != '' ) { ?>

												<dt>  Time</dt>
												<?php if($record->type =='Caregiver'){?>
												<dd> <?php echo date('h:i A', strtotime($record->start_time)).' - '.date('h:i A', strtotime($record->edate)).'<br>'; ?></dd>
												<?php }else{ ?>
												<dd> <?php echo date('h:i A', strtotime($record->appointment_date)).'<br>'; ?></dd>
												
												<?php } ?>
											<?php } ?>
											<dt> Patient Code</dt>
											<dd> <?php echo $record->patient_code.'<br>'; ?></dd>
										</dl>
									</div>

									<div class="ml-2 col-4">

										<dl class="dl-horizontal">
											<dt> Middle Name </dt>
											<dd> <?php if(isset($record->middle_name) && $record->middle_name !=''){ echo $record->middle_name.'<br>'; }else{ echo "N/A";}?>&nbsp; </dd>
											<dt> Gender</dt>
											<dd> <?php if($record->gender !=''){ echo $record->gender.'<br>'; }else{ echo "N/A";}?></dd>
											<?php if ($record->full_name != '') { ?>
												<dt> Doctor Name</dt>
												<dd> <?php echo $record->full_name.'<br>'; ?></dd>
											<?php } else { ?>
										
											<?php } ?>
											
											<dt> Type</dt>
											<dd> <?php echo $record->type.'<br>'; ?></dd>
											
											<?php if($record->type =='Caregiver'){?>
												<dt>Location</dt>
												<dd><?php echo $record->location.'<br>';?></dd>
											<?php }
											?>
											<dt> Discipline</dt>
											<dd> <?php echo $record->diciplin.'<br>'; ?></dd>

										</dl>
									</div>
									<div class="ml-2 col-4">

										<dl class="dl-horizontal">


											<dt> Last Name</dt>
											<dd> <?php if(isset($record->last_name) && $record->last_name !=''){ echo $record->last_name.'<br>';}else{ echo "N/A";} ?></dd>
											<?php if ($record->dob != '0000-00-00') { ?>
												<dt> Date of Birth</dt>
												<dd> <?php echo Common::convertMDY($record->dob).'<br>'; ?></dd>

											<?php } ?>
											
												<dt> Service</dt>
											<dd> <?php if(isset($record->service) && $record->service !=''){ echo $record->service.'<br>';} else{ echo "N/A";}?></dd>
											
											<dt> Status</dt>
											<dd> <?php

													if ($record->status == 'Pending') {
													?>
													<label class='badge badge-warning badge-pill'>Pending</label>

												<?php } ?>
												<?php

												if ($record->status == 'booked') {
												?>
													<label class='badge badge-info badge-pill'>Booked</label>

												<?php } ?>
												<?php

												if ($record->status == 'completed') {
												?>
													<label class='badge badge-success badge-pill'>Completed</label>

												<?php } ?>
												<?php

												if ($record->status == 'cancelled') {
												?>
													<label class='badge badge-danger badge-pill'>Cancelled</label>

												<?php } ?>
											</dd>
											
												<dt>Language</dt>
												<dd><?php echo $record->language;?></dd>
											

										</dl>	

									</div>
									<div class="ml-2 col-4">
										<dl class="dl-horizontal">
											
										</dl>
									</div>

								</div>

							</div>

							<hr>
						</div>
					</div>
				</div>
			</div>


			<div class="col-12 grid-margin stretch-card">
				<div class="card">


					<div class="card-body">
						<div class="d-flex align-items-center justify-content-between mb-3">
							<p class="card-title mb-0">Document Section</p>
							<?php if ($user['user_type_fk'] == 184) { ?>
								<p class="mb-0 tx-13">
									<a data-toggle="modal" class="pull-right" data-target="#exampleModal-5" data-whatever="@mdo"><i class="mdi mdi-plus"></i> Add</a>
								</p>
							<?php } ?>

						</div>
						<div class="row">
							<div class="col-12">
								<div class="table-responsive ">

									<table id="" class="table table-bordered">
										<thead>
											<tr>
												<th>#</th>

												<th>Document Name</th>
												<th>Attachment</th>

												<th>Created Date</th>

											</tr>
										</thead>
										<tbody>
											<?php
											if (count($document_list) > 0) {
												$cnt = 1;
												foreach ($document_list as $va) {
											?>

													<tr>
														<td><?php echo $cnt; ?></td>
														<td><?php echo $va->document_name; ?></td>
														<td><a target="_blank" href="<?php echo URL::to('/'); ?>/documentPatient/<?php echo $va->attachment; ?>"><i class="fa fa-download"></i></a></td>
														<td><?php echo Common::convertMDY($va->created_date); ?></td>
													</tr>
												<?php $cnt++;
												}
											}
											if (count($document_list) == 0) { ?>
												<tr>
													<td colspan="6"> Data not found</td>
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
			<div class="col-12 grid-margin stretch-card">
				<div class="card">


					<div class="card-body">
						<div class="d-flex align-items-center justify-content-between mb-3">
							<p class="card-title mb-0">Notes Section</p>
							<div class="pull-right">
								<input type="radio" class="" value="1" name="radio1" onclick="getClickAble('Agency');">Agency
								<input type="radio" class="" value="0" checked='checked' name="radio1" onclick="getClickAble('Self');">Self
							</div>

						</div>
						<div class="row">
							<div class="col-12">
								
													
								<div class="chat-messages" id="sms-messages">
									<div id="chat-messages-inner" class="notes-messages"></div>
								</div>
								<div class="chat-message  custom-chat">
														<form id="attachsubmits" method="post" onsubmit="return false;">
															<input type="hidden" name="_token" value="<?php echo csrf_token();?>">
																<button class="btn btn-success btn-sm" id="text-sms-send-btn" onclick="sendMessagefile()">Send</button>
																	<span class="input-box">
																  <!--   <input type="text" name="msg-box" id="text-msg-box" /> -->
																		<textarea style="margin-bottom: 0 !important; width: 100%;" name="msg-box" id="text-sms-box"></textarea>
																		<input type="hidden" name="agency_id" value="">
																		<input type="hidden" name="agency_id_main" value="<?php echo $record->agency_id;?>">
																	</span>
														</form>
														

													</div>
							</div>
						</div>
					</div>
				</div>
			</div>


		</div>

	</div>
	<div class="modal fade" id="exampleModal-4" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="ModalLabel">Add Appointment</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form class="forms-sample" enctype="multipart/form-data" action="<?php echo URL::to('/patient/appointment-add') ?>" name="adduser" method="post" id="form">
						<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
						<input type="hidden" name="id" value="<?php echo $record->id; ?>">
						<?php if($record->type =='Caregiver'){?>
						<div class="form-group">
							<label for="recipient-name" class="col-form-label">Location<span style="color:red">*</span>:</label>
							<select name="location_id" class="form-control" id="location_id" onchange="getTimeSearch()">
								<option value="">Select Location</option>
								<?php foreach ($location_list as $ks) { ?>
									<option value="<?php echo $ks->id; ?>" <?php if($record->location_id  == $ks->id){ echo "selected='selected'";}?>><?php echo $ks->address1; ?></option>
								<?php } ?>
							</select>

							<span id="location_error" class="error mt-2 text-danger" for="document_type"></span>
						</div>
						<?php } ?>
						<?php 
							$dates = '';
							$time = '';
						if($record->appointment_date !=''){ 
								$dates = date('m/d/Y',strtotime($record->appointment_date)); 
								$time = date('H:i:s',strtotime($record->appointment_date));
							
							}?>
						<div class="form-group">
							<label for="recipient-name" class="col-form-label">Appointment Date <span style="color:red">*</span>:</label>
							<input type="text" name="date" class="form-control" autocomplete="off" id="date_id" onchange="getTimeSearch()" value="<?php echo $dates;?>">
							<span id="date_error" class="error mt-2 text-danger" for="document_type"></span>
						</div>
						
						<div class="form-group">
							<label for="message-text" class="col-form-label">Appointment Time<span style="color:red">*</span>:</label>
							<?php if($record->type =='Caregiver'){?>
							<select name="time" class="form-control" id="timeid">
								<option value="">Select Appointment Time</option>
								<?php /*if (count($times) > 0) {
									foreach ($times as $vs) { ?>
										<option value="<?php echo $vs; ?>"><?php echo $vs; ?></option>
								<?php }
								} */?>
							</select>

							<?php }else{ ?>
						<input type="time" name="time" class="form-control" id="times_id" value="<?php echo $time;?>">
							
							<?php } ?>
							<span id="time_error" class="error mt-2 text-danger" for="document_type"></span>
						</div>
						
						
						<div class="form-group">
							<label for="message-text" class="col-form-label">Doctor<span style="color:red">*</span>:</label>
							<select name="doctor_id" class="form-control" id="doctor_id">
								<option value="">Select Doctor</option>
								<?php if (count($doctor_list) > 0) {
									foreach ($doctor_list as $vs) { ?>
										<option value="<?php echo $vs->id; ?>" <?php if($record->doctor_id  == $vs->id){ echo "selected='selected'";}?>><?php echo $vs->full_name; ?></option>
								<?php }
								} ?>
							</select>

							<span class="error mt-2 text-danger" id="doctor_error" for="file_name"></span>
						</div>

						<div class="modal-footer">
							<button type="submit" class="btn btn-success">Save</button>
							<button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="exampleModal-5" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="ModalLabel">Add Document</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form class="forms-sample" enctype="multipart/form-data" action="<?php echo URL::to('/patient/document-send-patientId') ?>" name="adduser" method="post" id="formnew">
						<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
						<input type="hidden" name="id" value="<?php echo $record->id; ?>">

						<div class="form-group">
							<label for="recipient-name" class="col-form-label">Document Name<span style="color:red">*</span>:</label>
							<input type="text" name="document_id" class="form-control" id="datenew_id">
							<span id="document_id_error" class="error mt-2 text-danger" for="document_type"></span>
						</div>
						<div class="form-group">
							<label for="message-text" class="col-form-label">Attachment<span style="color:red">*</span>:</label>
							<input type="file" class="form-control" id="timeidnew" name="images">
							<span class="error mt-2 text-danger" id="images_error" for="file_name"></span>
						</div>
						<div class="modal-footer">
							<button type="submit" class="btn btn-success">Save</button>
							<button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	
	<div class="modal fade" id="exampleModal-cancel" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="ModalLabel">Cancelled Notes</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					

						<div class="form-group">
							<label for="recipient-name" class="col-form-label">Notes:</label>
							<input type="textarea" name="document_id" class="form-control" id="notes_id">
						</div>
						
						<div class="modal-footer">
							<button type="button" class="btn btn-success" onclick="getCancel()">Save</button>
							<button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
						</div>
					
				</div>
			</div>
		</div>
	</div>


	@include('include/footer')
	<script src="<?= URL::to('assets/js/jquery.min.js') ?>"></script>
	<script src="<?= URL::to('assets/js/jquery-ui.min.js') ?>"></script>
	<script src="<?php echo URL::to('/');?>/assets/css/toastr/toastr.min.js"></script>

	<script>
	function getClickAble(val){
		$('input[name="agency_id"]').val(val);
		loadAllNotes();
	}
		$('#form').submit(function(e) {
			var date = $('#date_id').val();
			var time = $('#timeid').val();
			var doctor_id = $('#doctor_id').val();
			var location_id = $('#location_id').val();
			var times_id = $('#times_id').val();
			$('#date_error').html("");
			$('#time_error').html("");
			$('#doctor_error').html("");
			var cnt = 0;
			if (date.trim() == '') {
				$('#date_error').html("Required");
				cnt = 1;
			}
			<?php if($record->type =='Caregiver'){?>
			if (time.trim() == '') {
				$('#time_error').html("Required");
				cnt = 1;
			}
			<?php } else{?>
			if (times_id.trim() == '') {
				$('#time_error').html("Required");
				cnt = 1;
			}
			<?php } ?>
			if (doctor_id == '') {
				$('#doctor_error').html("Required");
				cnt = 1;
			}
			if (location_id == '') {
				$('#location_error').html("Required");
				cnt = 1;
			}

<?php if($record->type =='Caregiver'){?>
	if(time.trim() !=''){
		$.ajax({
			async:false,
			global:false,
			url:"<?php echo URL::to('/');?>/location/remaining-time-slot",
			type:"GET",
			data:{"time":time},
			success:function(res){
				if(res ==1){
				}else{
					$('#time_error').html("Slot limit over");
				cnt = 1;
				}
			}
		})
		
	}
<?php }?>

			if (cnt == 1) {
				return false
			} else {
				return true;
			}

		});
		$('#formnew').submit(function(e) {
			var datenew_id = $('#datenew_id').val();
			var timemew = $('#timeidnew').val();
			$('#document_id_error').html("");
			$('#time_error').html("");
			var cnt = 0;
			if (datenew_id.trim() == '') {
				$('#document_id_error').html("Required");
				cnt = 1;
			}
			if (timemew.trim() == '') {
				$('#images_error').html("Required");
				cnt = 1;
			}

			if (cnt == 1) {
				return false
			} else {
				return true;
			}

		});

		function getStatus(status) {
			var confi = confirm("Are you sure change status?");
			if (confi == true) {
				$.ajax({
					async: false,
					global: false,
					url: "<?php echo URL::to('/'); ?>/patient/statusUpdate/<?php echo $record->id; ?>",
					type: "GET",
					data: {
						"status": status
					},
					success: function(resp) {
						if (resp == 1) {
							var msg = status + ' successfully changed';
							alert(msg);
							location.reload();

						} else {
							alert("Sorry, something went wrong. Please try again.");
						}
					}

				})

			}
		}
		
		function getTimeSearch(){
			var location_id = $('#location_id').val();
			var date_id = $('#date_id').val();
			var existId = <?php if($record->appoinment_time_id !=''){echo $record->appoinment_time_id;}else{ echo "0";}?>;
			if(location_id !='' && date_id !=''){
				$.ajax({
					
					url: "<?php echo URL::to('/'); ?>/location-schedule-search1",
					type: "GET",
					data: {
						"location_id": location_id,
						'start_time':date_id
					},
					success: function(resp) {
						var json = JSON.parse(resp);
						var htmls = '';
						$('#timeid').html("");
						if (json.length !=0) {
							htmls = '<option value="">Select Appointment Time</option>';
							$.each(json,function(i,v){
								var selected = '';
								if(existId == v.id){
									selected = 'selected="selected"';
								}
								htmls +='<option value="'+v.id+'" '+selected+'>'+v.start_time+'-'+v.end_time+ '(' + v.slots+')'+'</option>' 
							});
							
						} else {
							htmls = '<option value="">No appointment schedule</option>'
						}
						
						$('#timeid').html(htmls);
					}

				})
				
			}
			
		}
		
		$('#date_id').datepicker({
			minDate:1,
			dateFormat:"mm/dd/yy"
		})
		<?php if($record->type =='Caregiver' && $dates !=''){?>
			getTimeSearch();
		<?php }?>
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
		function getCancel(){
			$.ajax({
					async: false,
					global: false,
					url: "<?php echo URL::to('/'); ?>/patient/statusUpdate/<?php echo $record->id; ?>",
					type: "GET",
					data: {
						"status": "cancelled",
						'notes_id':$('#notes_id').val()
					},
					success: function(resp) {
						if (resp == 1) {
							var msg = 'Appointment successfully cancelled';
							toastr.success(msg);
							location.reload();

						} else {
							
							toastr.error("Sorry, something went wrong. Please try again.");
						}
					}

				})
		}	
function sendMessagefile(){
	var alldata  = new FormData($('#attachsubmits')[0]);
	var id = <?php echo $record->id;?>;
	var name = "you";
	var message = $('#text-sms-box').val();
	var radio1 = $('input[name="radio1"]:checked').val();
	
	if(id!= 0 && message != "") {
		$.ajax({
			type: 'POST',
			data: alldata,
			url: "<?php echo URL::to('/');?>/patient/patient-notes/" +id,
			dataType: "json",
			mimeType: "multipart/form-data",
			contentType: false,
			processData: false,
		
			success: function(response) {
			
            addSMSmessage('You', 'Send',message,"", true);
            // You will get response from your PHP page (what you echo or print)
          },
          error: function(jqXHR, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
          }
		});
	}
 }
 function addSMSmessage(name,ctype, msg,file, clear) {

    smsCounter = smsCounter + 1;
    var inner = $('.notes-messages');
    var time = new Date();
    var hours = time.getHours();
    var minutes = time.getMinutes();
    if (hours < 10) hours = '0' + hours;
    if (minutes < 10) minutes = '0' + minutes;
    var id = 'sms-msg-' + smsCounter;
    var idname = name.replace(' ', '-').toLowerCase();
    inner.append('<p id="' + id + '" class="user-' + idname + '">' +
      '<span class="msg-block"> <strong>' + name + ' ('+ ctype +')</strong> <span class="time"> ' + hours + ':' + minutes + '</span>' +
      '<span class="msg">' + msg +' '+ file +'</span></span></p>');
	 
    $('#' + id).hide().fadeIn(800);
    if (clear) {
      $('#text-sms-box').val('').focus();
    }
    $('#sms-messages').animate({
      scrollTop: inner.height()
    }, 1000);
  }
  function loadAllNotes() {
	$('.notes-messages').html("");
	$('#loadersId').attr('style','display:block');
	var mess = $("input[name='agency_id']").val(); 

    $.ajax({
      url: "<?php echo URL::to('/');?>/patient/get-notes/<?php echo $record->id;?>",
      type: "post",
      data: {
        _token: '<?php echo csrf_token();?>',
		'readMessage':mess
      },
      success: function(response) {
            response.forEach(element => {
          add_message_obj(element.id,element.first_name, 'https://web.exmedc.com/img/demo/av1.jpg', element.message, element.created_date, element.type,element.sender_id);

        });
		$('#loadersId').attr('style','display:none;');
        // add_message('You', 'img/demo/av1.jpg', input.val(), true);
        // You will get response from your PHP page (what you echo or print)
      },
      error: function(jqXHR, textStatus, errorThrown) {
        console.log(textStatus, errorThrown);
      }
    });
	return false;
  }
  loadAllNotes();
 var i = 0;
 var smsCounter = 0;
  function add_message_obj(mid,name, img, msg, date, type,sender_id, clear) {
    //alert(sender_id);
    i = i + 1;

    var inner = $('.notes-messages');
    var time = new Date(date);
    var date=(time.getMonth()+1)+'/'+time.getDate()+'/'+time.getFullYear();
    
    var hours = time.getHours();
    var minutes = time.getMinutes();
    if (hours < 10) hours = '0' + hours;
    if (minutes < 10) minutes = '0' + minutes;
    var id = 'msg-' + i;
  //  var type="Receive";
	var ondelete='';
	

    var idname ="";
    inner.append('<p id="' + id + '" class="user-' + idname + '">' +
      '<span class="msg-block"><strong>' + name + '  ('+type+') </strong><span class="time"> '+date+' ' + hours + ':' + minutes + '</span>' +
      '<span class="msg">' + msg + '<span class="pull-right">'+ondelete+'</span></span></span></p>');
    $('#' + id).hide().fadeIn(800);
    if (clear) {
      $('.chat-message textarea').val('').focus();
    }
    $('#sms-messages').animate({
      scrollTop: inner.height()
    }, 20);
  }
	</script>