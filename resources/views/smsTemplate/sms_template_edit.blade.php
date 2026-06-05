@include('include/header')

@include('include/sidebar')
<style>
	.error{
		color:red;
	}
</style>
<div class="main-panel">        
        <div class="content-wrapper">
          <div class="row">
                <div class="col-12 grid-margin">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">SMS Template</h4>
				  <form method='post' action='<?php echo URL::to('/sms-template/update'); ?>' name="addPhysician" role="form" id="addPhysician" enctype="multipart/form-data">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                            <input type="hidden" name="id" value="<?php echo $sms_template->id;?>"/>
					
                        <div class="form-group">
                         <label for="FirstName">Name <span class="error">*</span></label>
                          <div class="col-sm-9">
								<input type="text" class="form-control" id="FirstName"  name="name" value="<?php echo $sms_template->name;?>">
								<span style="color:red;" id="tempname_error"><?php echo $errors->template->first('name'); ?></span>
                          </div>
                        </div>
						
						<div class="form-group">
                         <label for="FirstName">Message<span class="error">*</span></label>
                          <div class="col-sm-9">
								<textarea class="form-control" id="FirstNamel" rows="10" name="message"><?php echo $sms_template->message;?></textarea>
						
								<span style="color:red;" id="tempnamem_error"><?php echo $errors->template->first('message'); ?></span>
                          </div>
                        </div>
                    
                      <button type="submit" class="btn btn-primary mr-2">Update</button>
                  </form>
                </div>
              </div>
            </div>

		</div>
	</div>
<!-- /Main Content -->

<!-- /Page Content -->
<script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=" crossorigin="anonymous"></script>

      
<script type="text/javascript">
 
	
	$('#addPhysician').submit(function(e){
		var FirstName = $('#FirstName').val();
		var document_id = $('#FirstNamel').val();
		
		var cnt =0;
		
		$('#tempname_error').html(" ");
		$('#tempnamem_error').html(" ");
		
		if(FirstName ==''){
			$('#tempname_error').html(" Required ! ");
			cnt =1;
		}
		if(document_id ==''){
			$('#tempnamem_error').html(" Required ! ");
			cnt =1;
		}
		
   
		if(cnt ==1){
			return false;
		}else{
			return true;
		}
	});
</script>

  <!-- End Date Picker -->
@include('include/footer')