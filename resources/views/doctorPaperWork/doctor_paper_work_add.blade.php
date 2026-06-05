
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
                  <h4 class="card-title">Add Doctor Paper Work</h4>
                  <form class="form-sample" action='{{url("doctor-paper-work")}}' name="adduser" method="post" onsubmit="return validation();" >
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    <input type="hidden" name="flag" value="123">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Name </label>
                          <div class="col-sm-9">
                            <input type="text" class="form-control" placeholder="Enter Name " id="agency_name" name="name" value="<?php echo old('name'); ?>">
                            <span  id="name_error" class="error mt-2 text-danger"><?php echo $errors->add_agency->first('name'); ?></span>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Portal Id</label>
                          <div class="col-sm-9">
                            <input type="text" class="form-control" placeholder="Enter Portal Id" id="portal_id" name="portal_id" value="<?php echo old('portal_id'); ?>">
                             <span  id="portal_id_error" class="error mt-2 text-danger"><?php echo $errors->add_agency->first('portal_id'); ?></span>
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
									<input type="radio" class="form-check-input" id="monthly_bill"  name="gender" value="Male">
									Male
								  </label>
								</div>
							  </div>
							  <div class="col-sm-5">
								<div class="form-check">
								  <label class="form-check-label">
									<input type="radio" class="form-check-input" id="monthly_bill"  name="gender" value="Female">
									Female
								  </label>
								</div>
							  </div>
								<span class="error mt-2 text-danger"  id="monthly_bill_error"><?php echo $errors->add_agency->first('gender'); ?></span>

							</div>
						</div>
                      <div class="col-md-6">
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Date of Birth</label>
                          <div class="col-sm-9">
                            <input type="text" class="form-control" placeholder="Enter dob" id="dob" name="dob" data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" im-insert="false" value="<?php echo old('dob'); ?>">
                               <span  id="dob_error" class="error mt-2 text-danger"><?php echo $errors->add_agency->first('dob'); ?></span>
                          </div>
                        </div>
                      </div>
                     
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Doctors name </label>
                          <div class="col-sm-9">
                            <input type="text" class="form-control" placeholder="Enter Doctors name" id="doctor_name" name="doctor_name" value="<?php echo old('doctor_name'); ?>">
                               <span  id="doctor_name_error" class="error mt-2 text-danger"><?php echo $errors->add_agency->first('doctor_name'); ?></span>
                          </div>
                        </div>
                      </div>
                        <div class="col-md-6">
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Phone</label>
                          <div class="col-sm-9">
                            <input type="text" class="form-control" placeholder="Enter Phone" id="phone_no" maxlength="15" onkeypress="return isNumber(event)" name="phone_no" value="<?php echo old('phone_no'); ?>">
                               <span  id="phone_no_error" class="error mt-2 text-danger"><?php echo $errors->add_agency->first('phone_no'); ?></span>
                          </div>
                        </div>
                      </div>

                     
                    </div>
                    <div class="row">
                     
                       <div class="col-md-6">
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Doctor Fax</label>
                          <div class="col-sm-9">
                            <input type="text" class="form-control" placeholder="Enter Doctor Fax" id="doctor_fax" name="doctor_fax" value="<?php echo old('doctor_fax'); ?>">
                               <span  id="doctor_fax_error" class="error mt-2 text-danger"><?php echo $errors->add_agency->first('doctor_fax'); ?></span>
                          </div>
                        </div>
                      </div>
                        <div class="col-md-6">
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Agency </label>
                          <div class="col-sm-9">
                            <input type="text" class="form-control" placeholder="Enter Agency" id="agency" name="agency" value="<?php echo old('agency'); ?>">
                               <span  id="agency_error" class="error mt-2 text-danger"><?php echo $errors->add_agency->first('agency'); ?></span>
                          </div>
                        </div>
                      </div>
                    
                    </div>
                   
					<div class="row">
						<div class="col-md-6">
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">EMC Rep <span class="error">*</span></label>
								<div class="col-sm-9">
									<select name="rep_id" id="rep_id" class="form-control">
										<option value="">Selecy EMC Rep</option>
										@if(!empty($emc_list[0]))
											@foreach($emc_list as $vls)
											<option value="{{$vls->id}}" @if(old('rep_id') ==$vls->id) selected='selected' @endif>{{$vls->name}}</option>
											@endforeach
										@endif
									</select>
									<span  id="rep_id_error" class="error mt-2 text-danger"><?php echo $errors->add_record->first('rep_id'); ?></span>
								
								
							</div>
						</div>
					</div>
                      <div class="col-md-6">
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Fax Date</label>
                          <div class="col-sm-9">
                            <input type="text" class="form-control" placeholder="Enter Fax Date" data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" im-insert="false" id="fax_date" name="fax_date" value="<?php echo old('fax_date'); ?>">
                             <span  id="fax_date_error" class="error mt-2 text-danger"><?php echo $errors->add_agency->first('fax_date'); ?></span>
                          </div>
                        </div>
                      </div>
                     
                      
                    </div>
                    <div class="row">
						<div class="col-md-6">
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Notes Rep</label>
								<div class="col-sm-9">
									<textarea class="form-control" type="text" placeholder="Notes Rep" id="progress_notes" name="notes_rep" ><?php echo old('notes_rep'); ?></textarea>
									
									  <span  id="notes_rep_error" class="error mt-2 text-danger"><?php echo $errors->add_agency->first('notes_rep'); ?></span>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Medical report</label>
								<div class="col-sm-9">
									<textarea class="form-control" type="text" placeholder="Medical report" id="progress_notes" name="medical_report" ><?php echo old('notes_rep'); ?></textarea>
									
									  <span  id="notes_rep_error" class="error mt-2 text-danger"><?php echo $errors->add_agency->first('notes_rep'); ?></span>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Progress Notes</label>
								<div class="col-sm-9">
									<textarea class="form-control" type="text" placeholder="Progress Notes" id="progress_notes" name="progress_notes" ><?php echo old('progress_notes'); ?></textarea>
									
									  <span  id="notes_rep_error" class="error mt-2 text-danger"><?php echo $errors->add_agency->first('progress_notes'); ?></span>
								</div>
							</div>
						</div>
                        <div class="col-md-6">
							<div class="form-group row">
							  <label class="col-sm-3 col-form-label"> Date</label>
							  <div class="col-sm-9">
								<input type="text" class="form-control" placeholder="Enter Date" data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" im-insert="false" id="date" name="date" value="<?php echo old('date'); ?>">
								 <span  id="fax_date_error" class="error mt-2 text-danger"><?php echo $errors->add_agency->first('date'); ?></span>
							  </div>
							</div>
						</div>
                    </div>
                    <div class="row">
						<div class="col-md-12">
							
								<div class="col-md-11">
									<div id="mainId">
										<div class="copy_id row" id="1">
											<div class="col-md-6">
												<div class="form-group row">
												  <label class="col-sm-3 col-form-label"> NOTES</label>
												  <div class="col-sm-9">
													<input type="text" class="form-control" placeholder="Enter Date" id="date" name="notes_name[]" value="" style="width:400px">
													 
												  </div>
												</div>
											</div>
											<div class="col-md-2" style="margin-left:6%" >
												<a class="btn btn-info" onclick="getRemove(1)" style="display:none" id="lastId"><i class="fa fa-minus"></i></a>
											</div>
										</div>
									
									
								</div>
							</div>
							<a class="btn btn-primary" style="margin-left:52%" onclick="addMore()"><i class="fa fa-plus"></i></a>
						</div>
						
						
						
					</div>
					  
					
					
					
                    <button type="submit" class="btn btn-primary mr-2">Save</button>
                </form>
            </div>
        </div>
    </div>

<!-- /Main Content -->

<!-- /Page Content -->
 <script src="{{asset('assets/vendors/inputmask/jquery.inputmask.bundle.js')}}"></script>
 <script>
$(":input").inputmask();
 
 

</script>
<script>
  function validation() {
		var agency_name = $('#agency_name').val();
		var rep_id = $('#rep_id').val();
		var cnt =0;
		$('#name_error').html("");
		$('#rep_id_error').html("");
		if(agency_name.trim() ==''){
			$('#name_error').html("Required");
			cnt =1;
		}
		if(rep_id ==''){
			$('#rep_id_error').html("Required");
			cnt =1;
		}
		if(cnt ==1){
			return false;
		}else{
			return true;	
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
   
	
	function addMore(){
		var uniqid = Math.floor(100000000 + Math.random() * 900000000);
		var lengts = $('.copy_id').length;
		if(lengts ==1){
			$('#lastId').attr('style','');
		}
		var urlhemlt = '<div class="copy_id row" id="'+uniqid+'">'
						+'<div class="col-md-6">'
							+'<div class="form-group row">'
							  +'<label class="col-sm-3 col-form-label"></label>'
							  +'<div class="col-sm-9">'
								+'<input type="text" class="form-control" placeholder="Enter Notes" name="notes_name[]"  style="width:400px">'
								
							  +'</div>'
							+'</div>'
						+'</div>'
						+'<div class="col-md-2" style="margin-left:6%">'
							+'<a class="btn btn-info" onclick="getRemove('+uniqid+')" id="lastId"><i class="fa fa-minus"></i></a>'
						+'</div></div>';
						
		$('#mainId').append(urlhemlt);
	}
	
	function getRemove(id){
		$('#'+id).remove();
		var lengts = $('.copy_id').length;
		if(lengts ==1){
			$('#lastId').attr('style','display:none');
		}
	}
	 function isNumber(evt) {
        
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (( charCode != 46 ||  $(this).val().indexOf('.') != -1)  && (charCode < 48 || charCode > 57 )) {
            
            return false;
        }
        return true;
    }
</script>

@include('include/footer')
