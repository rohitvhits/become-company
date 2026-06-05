@include('include/header')
@include('include/sidebar')
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<link href="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo URL::to('/');?>/js/moment.min.js"></script>
<script type="text/javascript" src="<?php echo URL::to('/');?>/js/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo URL::to('/');?>/css/daterangepicker.css" />
<div class="main-panel">
        <div class="content-wrapper">
		<?php if(count($open_record_list) >0){ ?>
        	<div class="row grid-margin-top">  
          <div class="col-12 ">  
                                    
			<div class="card" style="height:376px;overflow-y:auto;">
			
				
            <div class="card-body" >
				<div class="row list-name">
					<div class="col-md-10 card-title">  <h4 class="card-title">Pending Appointment</h4></div>
				</div>
				<div class="row">
					<div class="col-12">
					  <div class="table-responsive ">
					  
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
								    <th>Location</th>
								  <th>Appointment Date</th>
								  <th>Appointment Time	</th>
								  <th>Service</th>
								 
								  <th>Status</th> 
								  <th>Created Date</th>
								  
								</tr>
							</thead>
							<tbody>
								<?php if(count($open_record_list) >0){
								$cnt = 1;
									foreach($open_record_list as $kkys){   ?>

										<tr>
											<td><a href="<?php echo URL::to('/');?>/patient/view/<?php echo $kkys->id;?>"> #<?php echo $kkys->id;?></a></td>
											<td><?php echo ucwords($kkys->agency_name);?></td>
											<td><?php echo ucwords($kkys->full_name);?></td>
											<td><?php echo ucwords($kkys->type);?></td>
											<td><?php echo ucwords($kkys->first_name.' '.$kkys->last_name);?></td>
											<td><?php echo $kkys->phone;?></td>
											<td><?php if ($kkys->dob != '0000-00-00') {
                                  echo Common::convertMDY($kkys->dob);
                                } ?></td>
											<td><?php echo $kkys->address1.' '.$kkys->city; ?></td>
											<td><?php if ($kkys->appointment_date != '') {
                                  echo Common::convertMDY($kkys->appointment_date);
                                } ?></td>
											<td><?php if ($kkys->start_time != '' && $kkys->end_time) {
								$start_time = date('h:i A', strtotime($kkys->start_time));
								$end_time = date('h:i A', strtotime($kkys->end_time));
								
                                  echo $start_time.' - '.$end_time;
                                } ?></td>
								<td><?php echo $kkys->name;?></td>
                           <td>
                             <?php

                              if ($kkys->status == 'Pending') {
                              ?>
                               <label class='badge badge-warning badge-pill'>Pending</label>

                             <?php } ?>
                             <?php

                              if ($kkys->status == 'booked') {
                              ?>
                               <label class='badge badge-info badge-pill'>Booked</label>

                             <?php } ?>
                             <?php

                              if ($kkys->status == 'completed') {
                              ?>
                               <label class='badge badge-success badge-pill'>Completed</label>

                             <?php } ?>
                             <?php

                              if ($kkys->status == 'cancelled') {
                              ?>
                               <label class='badge badge-danger badge-pill'>Cancelled</label>

                             <?php } ?>

                           </td>
						   <td><?php echo Common::convertMDY($kkys->created_date);?> </td>
											
										
										</tr>
								<?php } } if(count($open_record_list) ==0) { ?>
									<tr><td colspan="8">No record available</td></tr>
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
	<?php } ?>
	<div class="row grid-margin stretch-card">
		
		<div class="col-12">
			<div class="card">
			  
			 
			  <div class="card-body">
				  <div class="d-flex align-items-center justify-content-between mb-3">
					<p class="card-title mb-0">Upcoming appointment</p>
					
				</div>
				<div class="row">
					<div class="col-12">
					  <div class="table-responsive ">
					  
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
								    <th>Location</th>
								  <th>Appointment Date</th>
								  <th>Appointment Time	</th>
								  <th>Service</th>
								 
								  <th>Status</th> 
								  <th>Created Date</th>
								  
								</tr>
							</thead>
							<tbody>
								<?php if(count($upcomming_record_list) >0){
								$cnt = 1;
									foreach($upcomming_record_list as $upcomming){    ?>

										<tr>
											<td><a href="<?php echo URL::to('/');?>/patient/view/<?php echo $upcomming->id;?>"> #<?php echo $upcomming->id;?></a></td>
											<td><?php echo ucwords($upcomming->agency_name);?></td>
											<td><?php echo ucwords($upcomming->full_name);?></td>
											<td><?php echo ucwords($upcomming->type);?></td>
											<td><?php echo ucwords($upcomming->first_name.' '.$upcomming->last_name);?></td>
											<td><?php echo $upcomming->phone;?></td>
											<td><?php if ($upcomming->dob != '0000-00-00') {
                                  echo Common::convertMDY($upcomming->dob);
                                } ?></td>
											<td><?php echo $upcomming->address1.' '.$upcomming->city; ?></td>
											<td><?php if ($upcomming->appointment_date != '') {
                                  echo Common::convertMDY($upcomming->appointment_date);
                                } ?></td>
											<td><?php if ($upcomming->start_time != '' && $upcomming->end_time) {
								$start_time = date('h:i A', strtotime($upcomming->start_time));
								$end_time = date('h:i A', strtotime($upcomming->end_time));
								
                                  echo $start_time.' - '.$end_time;
                                } ?></td>
								<td><?php echo $upcomming->name;?></td>
                           <td>
                             <?php

                              if ($upcomming->status == 'Pending') {
                              ?>
                               <label class='badge badge-warning badge-pill'>Pending</label>

                             <?php } ?>
                             <?php

                              if ($upcomming->status == 'booked') {
                              ?>
                               <label class='badge badge-info badge-pill'>Booked</label>

                             <?php } ?>
                             <?php

                              if ($upcomming->status == 'completed') {
                              ?>
                               <label class='badge badge-success badge-pill'>Completed</label>

                             <?php } ?>
                             <?php

                              if ($upcomming->status == 'cancelled') {
                              ?>
                               <label class='badge badge-danger badge-pill'>Cancelled</label>

                             <?php } ?>

                           </td>
						   <td><?php echo Common::convertMDY($upcomming->created_date);?> </td>
											
										
										</tr>
								<?php } } if(count($upcomming_record_list) ==0) { ?>
									<tr><td colspan="8">No record available</td></tr>
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
	@if(in_array($user->user_type_fk,array(184)))
	<div class="row grid-margin stretch-card">
		
		<div class="col-12">
			<div class="card">
			  
			 
			  <div class="card-body">
				  <div class="d-flex align-items-center justify-content-between mb-3">
					<p class="card-title mb-0">Upcoming Telehealth appointment</p>
					
				</div>
				<div class="row">
					<div class="col-12">
					  <div class="table-responsive ">
					  
						<span id="responsive_telehealth"></span>
						<div class="pull-right pegination-margin">
				
						</div>
						
					  </div>
					</div>
				  </div>
			  </div>
			</div>
		</div>
	</div>	
	@endif
</div>
<script>
function getAjax(){
		var agency_id = $('#agency_id').val();
		var type_id = $('#type_id').val();
		var fullname_id = $('#fullname_id').val();
		var record_id = $('#record_id').val();
		var datepicker_id = $('#datepicker_id').val();
		$.ajax({
			type:"GET",
			url:"{{url('dashboard-telehealth')}}",
			data:{
				'agency_id':agency_id,
				'type_id':type_id,
				'fullname_id':fullname_id,
				'record_id':record_id,
				'datepicker_id':datepicker_id,
				
			},
			success:function(res){
				console.log(res);
				$('#responsive_telehealth').html("");
				$('#responsive_telehealth').html(res);
			}
			
		});
		return false;
	}
	getAjax();
$('#reportrange').daterangepicker({
    ranges: {
        'Today': [moment(), moment()],		
        'Tomorrow': [moment().add(1, 'days'), moment().add(1, 'days')],
        'Next 7 Days': [ moment(),moment().add(6, 'days')],
        'Next 30 Days': [moment(),moment().add(29, 'days') ],
        'This Month': [moment().add('month'), moment().endOf('month')],
        'Next Month': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month').endOf('month')]
    }});
	function export_data(){
		$('#test_record1').attr('href','<?php echo URL::to('/');?>/file-date-export');
		
	}
	function export_data1(){
	$('#test_record2').attr('href','<?php echo URL::to('/');?>/recent-date-export');
		
	}
	
	function getsearchRecentNotes(id){
		window.location.href="<?php echo URL::to('/');?>/home?notes_type="+id;
	}
	
</script>
@include('include/footer')

<script src="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.js"></script>

<script>
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