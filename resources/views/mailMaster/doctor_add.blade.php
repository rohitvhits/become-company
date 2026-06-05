
@include('include/header')

@include('include/sidebar')
 <div class="main-panel">        
        <div class="content-wrapper">
          <div class="row">
                <div class="col-12 grid-margin">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Add Doctor</h4>
                  <form class="form-sample" action='<?php echo URL::to('/doctor/save') ?>' name="adduser" method="post" onsubmit="return validation();" >
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Full Name </label>
                          <div class="col-sm-9">
                            <input type="text" class="form-control" placeholder="Enter full Name " id="agency_name" name="full_name" value="<?php echo old('full_name'); ?>">
                            <span  id="agency_name_error" class="error mt-2 text-danger"><?php echo $errors->add_agency->first('full_name'); ?></span>
                          </div>
                        </div>
                      </div>
					</div>
					<div class="row">
                      <div class="col-md-6">
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Email</label>
                          <div class="col-sm-9">
                            <input type="email" class="form-control" placeholder="Enter Email" id="email" name="email" value="<?php echo old('email'); ?>">
                             <span  id="email_error" class="error mt-2 text-danger"><?php echo $errors->add_agency->first('email'); ?></span>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                       <div class="col-md-6">
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Phone</label>
                          <div class="col-sm-9">
                            <input type="text" class="form-control" placeholder="Enter phone" maxlength="15" onkeypress="return isNumber(event)" id="phone" name="phone" value="<?php echo old('phone'); ?>">
                             <span  id="phone_error" class="error mt-2 text-danger"><?php echo $errors->add_agency->first('phone'); ?></span>
                          </div>
                        </div>
                      </div>
                     
                    </div>
                   
					<div class="row">
                      <div class="col-md-6">
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Gender</label>
                          
							  <div class="col-sm-4">
								<div class="form-check">
								  <label class="form-check-label">
									<input type="radio" class="form-check-input" id="msp" name="gender" value="male" <?php if(old('gender') =='male'){ echo "checked='checked'";}?>> Male <i class="input-helper"></i></label>
								</div>
							  </div>
							<div class="col-sm-5">
								<div class="form-check">
								  <label class="form-check-label">
									<input type="radio" class="form-check-input" id="msp" name="gender" value="female" <?php if(old('gender') =='female'){ echo "checked='checked'";}?>> Female<i class="input-helper"></i></label>
								</div>
							</div>  
                               <span  id="address2_error" class="error mt-2 text-danger" style="margin-left:27%;"><?php echo $errors->add_agency->first('gender'); ?></span>
                          
                        </div>
                      </div>
                       

                     
                    </div>
					<div class="row">
						 <div class="col-md-6">
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Notes</label>
								<div class="col-sm-9">
									<textarea class="form-control" placeholder="Notes" name="message" style="height: 50px"><?php echo old('message'); ?></textarea>
								   
							  </div>
								 
							</div>
						</div>
                       

                     
                    </div>
                    
                  
                    
                   
                      <button type="submit" class="btn btn-primary mr-2">Save</button>
                  </form>
                </div>
              </div>
            </div>

<!-- /Main Content -->

<!-- /Page Content -->

<script>
  function validation() {

    var temp = 0;

    var agency_name = $('#agency_name').val();
    var email = $('#email').val();
    var phone = $('#phone').val();
    var gender = $('input[name="gender"]').is(":checked");
   $("#agency_name_error").html("");
   $("#email_error").html("");
   $("#phone_error").html("");
     function ValidateEmail(email) {
            var expr = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
            return expr.test(email);
        };
		if (agency_name == "") {
		  $('#agency_name_error').html("Required");
		  temp++;
		}
		if (email == "") {
		  $('#email_error').html("Required");
		  temp++;
		}else if(!ValidateEmail(email))
		{
		  $('#email_error').html("Please enter a valid email address..");
		  temp++;
		}
		 if (phone == "") {
		  $('#phone_error').html("Required");
		  temp++;
		}
		if(gender ==false){
			$('#address2_error').html("Required");
			 temp++;
		}

      if (temp == 0) {

        return true;

      } else {

        return false;

      }

    }
     function isLatter(evt) {
                evt = (evt) ? evt : window.event;
                var charCode = (evt.which) ? evt.which : evt.keyCode;
              if(!(charCode >= 65 && charCode <= 120) && (charCode != 32 && charCode != 0)) {
                    return false;
                }
                return true;
            }
              function isNumber(evt) {
        
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (( charCode != 46 ||  $(this).val().indexOf('.') != -1)  && (charCode < 48 || charCode > 57 )) {
            
            return false;
        }
        return true;
    }
    function getCountyByZipCode(val){
   
      $.ajax({
        async: false,
        global: false,
        url:"<?= URL::to('get-county') ?>",
        type:"post",
        data:{zip_code: val ,_token: '<?php echo csrf_token(); ?>' },
        success:function (response){
            
            $('#county').val(response);
        }
      });
    }

</script>
<!-- Date Picker -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
<script>
  $("#bill_date").datepicker();
 
</script>


  <!-- End Date Picker -->
@include('include/footer')