@include('include/header')

@include('include/sidebar')
<div class="main-panel">        
        <div class="content-wrapper">
        	<div class="dashboard-header d-flex flex-column grid-margin ml-2">
            <div class="d-flex align-items-center justify-content-between flex-wrap border-bottom pb-3 mb-3">
              <div class="d-flex align-items-center">
                <h4 class="mb-0 font-weight-bold">Edit Document Type</h4>
              </div>
            </div>
          </div>
		 
          <div class="row">
          	
            <div class="col-12 grid-margin">
              
			<form class="form-sample" action='<?php echo URL::to('/document-item/document-update') ?>' name="adduser" method="post" onsubmit="return validation();"  >
			  <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
			  <input type="hidden" name="id" value="<?php echo $edit_details->id;?>">
			  <input type="hidden" name="flag" value="No">
				<div class="col-md-12 grid-margin">
					<div class="card">
						<div class="card-body">
						  
							<div class="row">
								<div class="col-md-6">
									<div class="form-group row">
									  <label class="col-sm-3 col-form-label">Name (required)</label>
									  <div class="col-sm-9">
										<input type="text" class="form-control"  placeholder="Enter  Name" id="first_name" name="name" value="<?php echo $edit_details->name; ?>">
										  <span class="error mt-2 text-danger" id="first_name_error" ><?php echo $errors->add_user->first('name'); ?></span>
									  </div>
									</div>
								</div>
								

							</div>
							<button type="submit" class="btn btn-primary mr-2">Update</button>
						</div>
					</div>
					
				</div>
			
		</form>
	</div>
	</div>
</div>

<!-- /Main Content -->

<!-- /Page Content -->

<script>
  function validation() {

    var temp = 0;

    var first_name = $('#first_name').val();
          $("#first_name_error").html("");
    if (first_name == "") {
      $('#first_name_error').html("Required");
      temp++;
    }
      if (temp == 0) {

        return true;

      } else {

        return false;

      }

    }
</script>


  <!-- End Date Picker -->
@include('include/footer')