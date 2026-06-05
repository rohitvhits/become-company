
@include('include/header')

@include('include/sidebar')
 <div class="main-panel">        
        <div class="content-wrapper">
          <div class="row">
                <div class="col-12 grid-margin">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Edit Doctor Paper Work</h4>
                  <form class="form-sample" action='{{url("doctor-paper-work/update")}}/{{$doctor_paper_list->id}}' name="adduser" method="post" id="notes_submit_update">
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    <input type="hidden" name="flag" value="123">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Name </label>
							<div class="col-sm-9">
								<input type="text" class="form-control" placeholder="Enter Name " id="doc_agency_name_edit" name="name" value="{{$doctor_paper_list->name}}">
								<span  id="doc_name_edit_error" class="error mt-2 text-danger"><?php echo $errors->add_agency->first('name'); ?></span>
							</div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Portal Id</label>
							<div class="col-sm-9">
								<input type="text" class="form-control" placeholder="Enter Portal Id" id="portal_id" name="portal_id" value="{{$doctor_paper_list->portal_id}}">
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
													<input type="radio" class="form-check-input" id="monthly_bill"  name="gender" value="Male" @if(strtolower($doctor_paper_list->gender) =='male') checked='checked' @endif>
													Male
													<i class="input-helper"></i>
													</label>
												</div>
                                  			</div>
											<div class="col-sm-5">
                            					<div class="form-check">
                              						<label class="form-check-label">
													<input type="radio" class="form-check-input" id="monthly_bill"   @if(strtolower($doctor_paper_list->gender) =='female') checked='checked' @endif name="gender" value="Female">
													Female
													<i class="input-helper"></i>
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
											<input type="text" class="form-control" placeholder="Enter dob" id="dob" name="dob" data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" im-insert="false" value="@if($doctor_paper_list->dob !='' && $doctor_paper_list->dob !='1970-01-01') {{date('m/d/Y',strtotime($doctor_paper_list->dob))}} @endif">
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
											<input type="text" class="form-control" placeholder="Enter Doctors name" id="doctor_name" name="doctor_name" value="{{$doctor_paper_list->doctor_name}}">
											   <span  id="doctor_name_error" class="error mt-2 text-danger"><?php echo $errors->add_agency->first('doctor_name'); ?></span>
										  </div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group row">
										  <label class="col-sm-3 col-form-label">Phone</label>
											<div class="col-sm-9">
												<input type="text" class="form-control" placeholder="Enter Phone" id="phone_no" maxlength="15" onkeypress="return isNumber(event)" name="phone_no" value="{{$doctor_paper_list->phone}}">
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
											<input type="text" class="form-control" placeholder="Enter Doctor Fax" id="doctor_fax" name="doctor_fax" value="{{$doctor_paper_list->fax}}">
											   <span  id="doctor_fax_error" class="error mt-2 text-danger"><?php echo $errors->add_agency->first('doctor_fax'); ?></span>
										  </div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group row">
										  <label class="col-sm-3 col-form-label">Agency </label>
										  <div class="col-sm-9">
											<input type="text" class="form-control" placeholder="Enter Agency" id="agency" name="agency" value="{{$doctor_paper_list->agency}}">
											   <span  id="agency_error" class="error mt-2 text-danger"><?php echo $errors->add_agency->first('agency'); ?></span>
										  </div>
										</div>
									</div>
								
								</div>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group row">
											<label class="col-sm-3 col-form-label">Rep</label>
											<div class="col-sm-9">
											<select name="rep_id" id="rep_id_edit" class="form-control">
													<option value="">Selecy EMC Rep</option>
													@if(!empty($emc_list[0]))
														@foreach($emc_list as $vls)
														<option value="{{$vls->id}}" @if($doctor_paper_list->rep ==$vls->id) selected='selected' @endif>{{$vls->name}}</option>
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
											<input type="text" class="form-control" placeholder="Enter Fax Date" data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" im-insert="false" id="fax_date" name="fax_date" value="@if($doctor_paper_list->fax_date !='' && $doctor_paper_list->fax_date !='1970-01-01') {{date('m/d/Y',strtotime($doctor_paper_list->fax_date))}} @endif">
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
											<textarea class="form-control" type="text" placeholder="Notes Rep" id="progress_notes" name="notes_rep" >{{$doctor_paper_list->notes_rep}}</textarea>
											
											  <span  id="notes_rep_error" class="error mt-2 text-danger"><?php echo $errors->add_agency->first('notes_rep'); ?></span>
										  </div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group row">
										  <label class="col-sm-3 col-form-label">Medical report</label>
										  <div class="col-sm-9">
											<textarea class="form-control" type="text" placeholder="Medical report" id="progress_notes" name="medical_report" >{{$doctor_paper_list->medical_report}}</textarea>
											
											  <span  id="notes_rep_error" class="error mt-2 text-danger"><?php echo $errors->add_agency->first('notes_rep'); ?></span>
										  </div>
										</div>
								
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group row">
											<label class="col-sm-3 col-form-label">Progress Notes</label>
											<div class="col-sm-9">
												<textarea class="form-control" type="text" placeholder="Progress Notes" id="progress_notes" name="progress_notes" >{{$doctor_paper_list->progress_notes}}</textarea>
												
												  <span  id="notes_rep_error" class="error mt-2 text-danger"><?php echo $errors->add_agency->first('progress_notes'); ?></span>
											</div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group row">
										  <label class="col-sm-3 col-form-label"> Date</label>
										  <div class="col-sm-9">
											<input type="text" class="form-control" placeholder="Enter Date" data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" im-insert="false" id="date" name="date" value="@if($doctor_paper_list->date !='' && $doctor_paper_list->date !='1970-01-01') {{date('m/d/Y',strtotime($doctor_paper_list->date))}} @endif">
											 <span  id="fax_date_error" class="error mt-2 text-danger"><?php echo $errors->add_agency->first('date'); ?></span>
										  </div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-12">
							
										<div class="col-md-11">
											<div id="mainIdNewEdit">
											
												@php 
													$i =1;
													$unqid = uniqid();
												@endphp
												@if(count($doctor_paper_list_notes) >0)
													@foreach($doctor_paper_list_notes as $key=>$vdf)
														<div class="copy_idsn row" id="{{$unqid}}{{$i}}">
															<div class="col-md-6">
																<div class="form-group row">
																
																
																  <label class="col-sm-3 col-form-label"> @if($key ==0) NOTES @endif</label>
																  
																  <div class="col-sm-9">
																	<input type="text" class="form-control" placeholder="Enter Notes" id="date" name="notes_name[]" style="width:400px" value="{{$vdf->notes}}">
																	 
																  </div>
																</div>
															</div>
															<div class="col-md-2" style="margin-left:6%" >
																<a class="btn btn-info" onclick="getRemoveNew('{{$unqid}}{{$i}}')" style="@if(count($doctor_paper_list_notes) ==1) display:none @endif" id="lastIdNewEdit"><i class="fa fa-minus"></i></a>
															</div>
														</div>
														<?php $i++;?>
													@endforeach
												@endif
												@if(count($doctor_paper_list_notes) ==0)
													<div class="copy_idsn row" id="1">
															<div class="col-md-6">
																<div class="form-group row">
																  <label class="col-sm-3 col-form-label"> NOTES</label>
																  <div class="col-sm-9">
																	<input type="text" class="form-control" placeholder="Enter Notes" id="date" name="notes_name[]" value="" style="width:400px">
																	 
																  </div>
																</div>
															</div>
															<div class="col-md-2" style="margin-left:6%" >
																<a class="btn btn-info" onclick="getRemoveNew(1)" style="display:none" id="lastIdNewEdit"><i class="fa fa-minus"></i></a>
															</div>
														</div>
												
												@endif
											
											
											</div>
										</div>
										<a class="btn btn-primary" style="margin-left:52%" onclick="addMoreEdit()"><i class="fa fa-plus"></i></a>
									</div>
									 <button type="submit" class="btn btn-success" >Submit</button>
								</div>
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
  $('#notes_submit_update').submit(function(e){
						var names = $('#doc_agency_name_edit').val();
						var rep_id = $('#rep_id_edit').val();
						$('#doc_name_error').html("");
						$('#rep_id_error').html("");
						var cnt =0;
						if(names.trim() ==''){
							$('#doc_name_edit_error').html("Required");
							cnt =1;
						}
						/*if(rep_id ==''){
							$('#rep_id_error').html("Required");
							cnt =1;
						}*/
						
						if(cnt ==1){
							return false;
						}else{
							var doctor_paper_work = $('#doctor_paper_workupdate')[0];
							var forms = new FormData(doctor_paper_work);
							$.ajax({
								type:"POST",
								url:'{{url("doctor-paper-work/update")}}/'+$('#doc_update_id').val(),
								data: $('#doctor_paper_workupdate').serialize(),
								
								success:function(res){
									if(res ==1){
										toastr.success("Doctor Paper work successfully update.");
										$('#doc_paperwork_closed_edir').click();
										ajaxDoctorPaper();
										
									}else{
										toastr.error('Sorry, something went wrong. Please try again.');
									}
								}
							})
							
						}
					});
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
   
	
	function addMoreEdit(){
		var uniqid = Math.floor(100000000 + Math.random() * 900000000);
		var lengts = $('.copy_idsn').length;
		if(lengts ==1){
			$('#lastIdNewEdit').attr('style','');
		}
		var urlhemlt = '<div class="copy_idsn row" id="'+uniqid+'">'
						+'<div class="col-md-6">'
							+'<div class="form-group row">'
							  +'<label class="col-sm-3 col-form-label"></label>'
							  +'<div class="col-sm-9">'
								+'<input type="text" class="form-control" placeholder="Enter Notes" name="notes_name[]"  style="width:400px">'
								
							  +'</div>'
							+'</div>'
						+'</div>'
						+'<div class="col-md-2" style="margin-left:6%">'
							+'<a class="btn btn-info" onclick="getRemoveNew('+uniqid+')" id="lastId"><i class="fa fa-minus"></i></a>'
						+'</div></div>';
						
		$('#mainIdNewEdit').append(urlhemlt);
	}
	
	function getRemoveNew(id){
		$('#'+id).remove();
		var lengts = $('.copy_idsn').length;
		if(lengts ==1){
			$('#lastIdNewEdit').attr('style','display:none');
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
