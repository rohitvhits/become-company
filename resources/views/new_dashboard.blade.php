@include('include/header')
@include('include/sidebar')
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<link href="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo URL::to('/');?>/assets/js/moment.min.js"></script>
<script type="text/javascript" src="<?php echo URL::to('/');?>/assets/js/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo URL::to('/');?>/assets/css/daterangepicker.css" />
<div class="main-panel">
	<div class="content-wrapper">
	

			<div class="row grid-margin-top">  
				<div class="col-6 ">  
										
					<div class="card" style="height:476px;overflow-y:auto;">
				
					
						<div class="card-body" >
								<h4 class="card-title">Completed Record</h4>
							
									<div class="table-responsive ">
								  
										<table id="" class="table table-bordered">
											<thead>
												<tr>
												  <th>Record#</th>
												  <th>Record Name</th>
												  <th>Contact Number</th>
												  <th>File Date </th>
												  
												<!--  <th>Action</th> -->
												</tr>
											</thead>
											<tbody>
												<?php if(count($record_complete_list) >0){
												$cnt = 1;
													foreach($record_complete_list as $kkys){   ?>

														<tr>
															<td><a href="<?php echo URL::to('/');?>/record/<?php echo $kkys->id;?>">Record #<?php echo $kkys->id;?></a></td>
															<td><?php echo ucwords($kkys->first_name.' '.$kkys->last_name);?></td>
															<td><?php echo $kkys->phone;?></td>
															<td><?php if($kkys->file_date !=''){ echo date('m/d/Y',strtotime($kkys->file_date)); } ?></td>
														
														</tr>
												<?php } } if(count($record_complete_list) ==0) { ?>
													<tr><td colspan="6">No record available</td></tr>
												<?php } ?>
											</tbody>
										</table>
										<div class="pull-right pegination-margin">
										</div>
										
									</div>
								</div>
						

					</div> 	
			  
				</div>
				<div  class="col-md-6 grid-margin stretch-card" >
					  <div class="card"  style="height:476px;overflow-y:auto;">
						

						<div class="card-body">
							
	<h4 class="card-title">Recent Notes</h4>
										<?php if(count($NotesList) >0){
							$cnt = 1;
								foreach($NotesList as $kkys){ ?>
									<div class="d-flex align-items-center py-2 border-bottom">
										
										<div class="ml-1">
											<h6 class="mb-1"><a href="<?php echo URL::to('/');?>/record/<?php echo $kkys->record_id;?>">Record #<?php echo $kkys->record_id;?> <?php echo ucwords($kkys->first_name.' '.$kkys->last_name);?></a></h6>
											<p><?php echo $kkys->message;?></p>
											<p class="text-muted mb-0 tx-12"><i class="mdi mdi-map-marker mr-1"></i><?php echo $kkys->agency_name;?>  <?php echo date('m/d/Y h:i A',strtotime($kkys->created_at)); ?></p>
										</div>
										
									</div>
								<?php }
								 }  if(count($NotesList) ==0){?>
								<span>No record available</span>
							<?php } ?>


							
						</div>
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