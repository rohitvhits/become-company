@include('include/header')
@include('include/sidebar')
<?php

?>
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css">
<link href="<?php echo URL::to('/'); ?>/assetsd/css/vertical-layout-light/jquery.timepicker.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2/select2.min.css">
  <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
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
	.error{
		color:red;
	}
	#Commsas::first-letter {
    text-transform: uppercase;
}
</style>
<!--main-container-part-->
<div class="main-panel">
	<div class="content-wrapper">		
		<div class="row">			
			<div class="col-12 grid-margin stretch-card">
				<div class="card">
					<div class="card-body">
						<div class="d-flex align-items-center justify-content-between mb-3">
							<p class="card-title mb-0">Reminder Section</p>
						</div>
					<form action="<?php echo URL::to('/');?>/reminder/save" method="post" enctype="multipart/form-data" id="submitId">
					@csrf
						<div class="row">
							<div class="col-md-6">
								<div class="form-group row">
									<label class="col-sm-3 col-form-label">Title <span class="error">*</span></label>
									<div class="col-sm-9">
										<input type="text" class="form-control" placeholder="Enter Title" id="first_name" name="title" value="">
										  <span class="error mt-2 text-danger" id="first_name_error"><?php echo $errors->agency->first('title');?></span>
									  </div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group row">
									<label class="col-sm-3 col-form-label">Description <span class="error">*</span></label>
									<div class="col-sm-9">
										<textarea name="description" class="form-control" rows="4" cols="50" id="description_id" placeholder="Description"></textarea>
										  <span class="error mt-2 text-danger" id="description_error"><?php echo $errors->agency->first('description');?></span>
									  </div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group row">
									<label class="col-sm-3 col-form-label">Start Date <span class="error">*</span></label>
									<div class="col-sm-9">
										<input autocomplete="off" type="text" name="start_date" placeholder="Start Date" class="form-control" id="start_date">
										  <span class="error mt-2 text-danger" id="start_date_error"><?php echo $errors->agency->first('start_date');?></span>
									  </div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group row">
									<label class="col-sm-3 col-form-label">End Date <span class="error">*</span></label>
									<div class="col-sm-9">
										<input autocomplete="off" type="text" name="end_date"  placeholder="End Date" class="form-control" id="end_date">
										  <span class="error mt-2 text-danger" id="end_date_error"><?php echo $errors->agency->first('end_date');?></span>
									  </div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group row">
									<label class="col-sm-3 col-form-label">Start Time<span class="error">*</span></label>
									<div class="col-sm-9">
										<input autocomplete="off" type="text" name="start_time"  placeholder="Start Time" class="form-control" id="start_time" data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="HH:MM" im-insert="false">
										  <span class="error mt-2 text-danger" id="start_time_error"><?php echo $errors->agency->first('start_time');?></span>
									  </div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group row">
									<label class="col-sm-3 col-form-label">Assign To</label>
									<div class="col-sm-9">
										<select class="js-example-basic-multiple w-100" data-placeholder="Select assign to" name="assign_to[]" id="assign_to_id" multiple="multiple">
										  <?php if(count($users) >0){
											  forEach($users as $vs){
												 if(in_array($vs->user_type_fk,array(3,4,184))){
												  
											  ?>
										  <option value="<?php echo $vs->id;?>"><?php echo $vs->name;?></option> 
										  <?php } } } ?>
										</select>
										  <span class="error mt-2 text-danger" id="assign_to_id_error"></span>
									  </div>
								</div>
							</div>
						</div>
						<div class="row">
							
						</div>
						<div class="row">
							
						</div>
						<button type="submit" class="btn btn-primary mr-2">Save</button>
						</form>
					</div>
				</div>
			</div>


		</div>

	</div>


	@include('include/footer')
	<script src="<?= URL::to('assets/js/jquery.min.js') ?>"></script>
	<script src="<?= URL::to('assets/js/jquery-ui.min.js') ?>"></script>
	<script src="<?php echo URL::to('/');?>/assets/css/toastr/toastr.min.js"></script>
	  <script src="<?php echo URL::to('/');?>/assets/vendors/select2/select2.min.js"></script>

	<script src="<?php echo URL::to('/');?>/assets/js/select2.js"></script>
	<script src="<?php echo URL::to('/');?>/assets/vendors/inputmask/jquery.inputmask.bundle.js"></script>
<script>
$('#start_date').datepicker();
$('#end_date').datepicker();
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
		
	$('#submitId').submit(function(e){
		var title = $('#first_name').val();
		var description_id = $('#description_id').val();
		var start_date = $('#start_date').val();
		var end_date = $('#end_date').val();
		var start_time = $('#start_time').val();
	
		var cnt =0;
		$('#first_name_error').html("");
		$('#description_error').html("");
		$('#end_date_error').html("");
		$('#start_date_error').html("");
		$('#start_time_error').html("");
		
		if(title.trim() ==''){
			$('#first_name_error').html("Title is required");
			cnt =1;
		}
		if(description_id.trim() ==''){
			$('#description_error').html("Description is required");
			cnt =1;
		}
		if(start_date.trim() ==''){
			$('#start_date_error').html("Start Date is required");
			cnt =1;
		}
		if(end_date.trim() ==''){
			$('#end_date_error').html("End Date is required");
			cnt =1;
		}
		if(start_time.trim() ==''){
			$('#start_time_error').html("Start Time is required");
			cnt =1;
		}
		
		if(cnt ==1){
			return false;
		}else{
			return true;
		}
	});
	$(function($) {
	  'use strict';

	  // initializing inputmask
	  $(":input").inputmask();

	})
	</script>