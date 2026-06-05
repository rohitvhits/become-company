
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
                  <h4 class="card-title">Edit Directory</h4>
                 <form class="form-sample" action="{{ url('directory') }}/update/{{$contacts_list->id}}" name="adduser" method="post" enctype="multipart/form-data" onsubmit="return validation();" >
				  @method('POST')
				  
						@csrf
                    <div class="row">
                       <div class="col-md-6">
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Full Name <span class="error">*</span></label>
                          <div class="col-sm-9">
                            <input type="text" class="form-control" placeholder="Enter Full Name " id="agency_name" name="full_name" value="{{$contacts_list->name}}">
                            <span  id="agency_name_error" class="error mt-2 text-danger">{{$errors->add_agency->first('full_name')}}</span>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Email</label>
                          <div class="col-sm-9">
                            <input type="text" class="form-control" placeholder="Enter Email" id="email" name="email" value="{{$contacts_list->email}}">
                             <span  id="email_error" class="error mt-2 text-danger"></span>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                       <div class="col-md-6">
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Phone</label>
                          <div class="col-sm-9">
                            <input type="text" class="form-control" placeholder="Enter phone" maxlength="15"  id="phone" name="phone" value="{{$contacts_list->mobile}}" data-inputmask="&quot;mask&quot;: &quot;(999) 999-9999&quot;" data-mask="" inputmode="text">
                             <span  id="phone_error" class="error mt-2 text-danger">{{$errors->add_agency->first("phone")}}</span>
                          </div>
                        </div>
                      </div>
					  <div class="col-md-6">
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Other Mobile </label>
                          <div class="col-sm-9">
                            <input type="text" class="form-control" placeholder="Enter Other Mobile" maxlength="15"  id="phone" name="other_modile" value="{{$contacts_list->other_modile}}" data-inputmask="&quot;mask&quot;: &quot;(999) 999-9999&quot;" data-mask="" inputmode="text">
                             <span  id="" class="error mt-2 text-danger">{{$errors->add_agency->first('other_modile')}}</span>
                          </div>
                        </div>
                      </div>
					  <div class="col-md-6">
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Address 1</label>
                          <div class="col-sm-9">
                            <input type="text" class="form-control" placeholder="Enter address" id="address1" name="address1" value="{{$contacts_list->address}}">
                               <span  id="address1_error" class="error mt-2 text-danger"></span>
                          </div>
                        </div>
                      </div>
					  <div class="col-md-6">
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Language</label>
                          <div class="col-sm-9">
                            <select class="form-control" name="language">
								<option value="">Select Language</option>
								<option value="English" @if($contacts_list->language =="English") selected @endif>English</option>
								<option value="Spanish" @if($contacts_list->language =="Spanish") selected @endif>Spanish</option>
								<option value="Russian" @if($contacts_list->language =="Russian") selected @endif>Russian</option>
								<option value="Chinese" @if($contacts_list->language =="Chinese") selected @endif>Chinese</option>
							</select>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Image</label>
                          <div class="col-sm-9">
                            <input type="file" class="form-control" name="profile_img" >
                               <span  id="profiles_error" class="error mt-2 text-danger"></span>
                          </div>
                        </div>
                      </div>
					  <div class="col-md-6">
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Notes</label>
                          <div class="col-sm-9">
							<textarea name="notes" class="form-control">{{$contacts_list->notes}}</textarea>
                               
                          </div>
                        </div>
                      </div>
					  <div class="col-md-6">
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label"></label>
                          <div class="col-sm-9">
							@if($contacts_list->image !='')
								<img src="{{asset('contacts/')}}/{{$contacts_list->image}}" style="width:50px;height:50px">
							@endif
                               
                          </div>
                        </div>
                      </div>
					</div>
                    
                   
                     
                    
                      <button type="submit" class="btn btn-primary mr-2">Save</button>
                  </form>
                </div>
              </div>
            </div>
  <!-- End Date Picker -->
@include('include/footer')
<script src="{{asset('assets/vendors/inputmask/jquery.inputmask.bundle.js')}}"></script>
<script src="{{asset('assets/js/inputmask.js')}}"></script>
<script>
	function validation(){
		var name = $('#agency_name').val();
		
		var phone = $('#phone').val();
		
		var profile_img = $('input[name="profile_img"]').prop('files');
		var cnt =0;
		$('#agency_name_error').html("");
		$('#email_error').html("");
		$('#phone_error').html("");
		$('#address1_error').html("");
		$('#profiles_error').html("");
		if(name.trim() ==''){
			$('#agency_name_error').html("Required");
			cnt =1;
		}
		
		/*if(phone.trim() ==''){
			$('#phone_error').html("Required");
			cnt =1;
		}*/
		if(profile_img.length !=0){
			var  FileUploadPath = profile_img[0].name;
				
			var Extension = FileUploadPath.substring(FileUploadPath.lastIndexOf('.') + 1).toLowerCase();
			if(Extension =='jpg' || Extension =='png' || Extension =='gif' || Extension =='jpeg'){
					
			}else{
				$('#profiles_error').html("Photo only allows image types of PNG, JPG, JPEG");
				  cnt =1
				  
			}
			
		}
		
		if(cnt ==1){
			return false;
		}else{
				return true;
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