 @include('include/header')
 @include('include/sidebar')
 <div class="main-panel">
 	<div class="content-wrapper">
 		<div class="row">
 			<div class="col-12 grid-margin">
 				
 				<div class="card">
 					<div class="card-body">
 						<h4 class="card-title">Dropdowns Type</h4>
 						<form class="form-sample" action='<?php echo URL::to('/master') ?>' name="add_master" method="get" onsubmit="return validation();">

 							<div class="row">
 								<div class="col-md-6">
 									<div class="form-group row">
 										<label class="col-sm-3 col-form-label">View Type</label>
 										<div class="col-sm-9">
 											<select class="form-control" name="master_type_fk" id="master_type_fk" onclick="searchType()">
 												<option value="">Select Type</option>
 												<?php foreach ($masterType as  $rwType) { ?>
 													<option value="<?php echo $rwType->id; ?>" <?php if (isset($masterDetail)) {
																									echo ($masterDetail->master_type_fk == $rwType->id) ? 'selected' : '';
																								} ?>><?php echo ucwords(str_replace('_', ' ', $rwType->name)); ?></option>
 												<?php } ?>
 											</select>
 											<span class="error mt-2 text-danger" id="type_error"><?php echo $errors->add_master->first('master_type_fk'); ?></span>
 										</div>
 									</div>
 								</div>
 								<div class="col-md-3">
 									<button type="submit" class="btn btn-success">View Type</button>
 								</div>
 							</div>
 						</form>
 					</div>
 				</div>
 			</div>
 		</div>
 	</div>
 	@include('include/footer')
 	<script>
 		function validation() {

 			var temp = 0;



 			var type = $('#master_type_fk').val();

 			if (type == "") {
 				$('#type_error').html("Required");
 				temp++;
 			} else {
 				$('#type_error').html("");
 			}


 			if (temp == 0) {

 				return true;

 			} else {

 				return false;

 			}

 		}
 	</script>