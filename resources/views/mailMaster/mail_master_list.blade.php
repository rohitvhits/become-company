 @include('include/header')
@include('include/sidebar')
  <link href="{{asset('assets/css/toastr/toastr.min.css')}}" rel="stylesheet" type="text/css" />

<style>
	.error{
		color:red;
	}
</style>
 <div class="main-panel">
        <div class="content-wrapper">
          <div class="card">
             <div class="row list-name">
           <div class="col-sm-6 card-title" >  <h4 class="card-title">Mail Master List</h4></div>
			<div class="col-sm-6"><a href="javascript:void(0)" data-target="#exampleModal-mail" data-toggle="modal" data-whatever="@mdo" class="btn btn-primary btn-rounded btn-fw btn-sm pull-right"><i class="mdi mdi-plus"> </i>Add Mail Master</a></div>
		   </div>
          
            <div class="card-body">
				
                  <div class="table-responsive">
                  
                    <table id="" class="table table-bordered">
                      <thead>
                        <tr>
                  <th></th>
                  <th>#</th>
                  <th>Full Name</th>
				  <th>EMC User</th>
                  <th>Homecare</th>
                  <th>Address</th>
                  <th>What To Mail</th>
                  <th>Language</th>
                  
                 <th>Send On</th>
                 <th>Action</th>
                </tr>
                      </thead>
                    <tbody>
						<form>
							<tr>
								<td></td>
								<td><input type="submit" class="btn btn-primary" name="submit" value="Search"></td>
								
								<td><input type="text" class="form-control" name="full_name" value="{{$full_name}}" placeHolder="Enter full name"></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
						</form>
                      @if(count($list) >0)
						  @foreach($list as $val)
					  <tr>
					  <td><input type="checkbox" id="cbox{{$val->id}}" onclick="checkedMail({{$val->id}})" @if($val->mail_status ==1) checked='checked' @endif></td>
						<td>{{$val->id}}</td>
						<td>{{$val->full_name}}</td>
						<td>{{$val->emcname}}</td>
						<td>{{$val->homecare}}</td>
						<td>{{$val->address}}</td>
						<td>{{$val->mail}}</td>
						<td>{{$val->language}}</td>
						<td>{{date('m/d/Y',strtotime($val->send_date))}}</td>
						<td><a href="{{url('/mail-master/delete/')}}/{{$val->id}}" onclick="return confirm('Are you sure remove this record?');" title="Delete"><i class="fa fa-trash-o"></i></a></td>
					  </tr>
					  @endforeach
					 @endif
					 @if(count($list) ==0)
						 <tr><td colspan="7">No record available</td></tr>
					@endif
              </tbody>
                    </table>
               
                    <div class="pull-right pegination-margin">
            {{$list->links("pagination::bootstrap-4")}}
            </div>
                    
                 
              </div>
            </div>
          </div>
        </div>
        <!-- Rate Start -->
        
        <!-- Rate End -->
<div class="modal fade" id="exampleModal-mail" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
	  <div class="modal-content">
		<div class="modal-header">
		  <h5 class="modal-title" id="ModalLabel">Add Mail</h5>
		  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		  </button>
		</div>
		<div class="modal-body">
		  <form class="forms-sample" enctype="multipart/form-data" name="adduser" method="post" id="mails_id">
			<input type="hidden" name="_token" value="{{csrf_token()}}">
			<input type="hidden" name="flag" value="1">
		   
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
					  <label for="recipient-name" class="col-form-label">Full Name<span class="error">*</span>:</label>
					  <input type="text" name="full_name" id="full_name" class="form-control"  value="" placeHolder="Enter full name">
					  <span class="error" id="full_name_error"></span>
					</div>
				
				</div>
				<div class="col-md-6">
					<div class="form-group">
					  <label for="recipient-name" class="col-form-label">Homecare:</label>
					  <input type="text" name="homecare" id="homecare" class="form-control" value=""  placeHolder="Enter Homecare">
					  <span class="error" id="homecare_error"></span>
					</div>
				
				</div>
				<div class="col-md-6">
					<div class="form-group">
					  <label for="recipient-name" class="col-form-label">Address<span class="error">*</span>:</label>
					  <textarea type="text" name="address"  id="address" class="form-control" placeHolder="Enter Address"></textarea>
					  <span class="error" id="address_error"></span>
					</div>
				
				</div>
				<div class="col-md-6">
					<div class="form-group">
					  <label for="recipient-name" class="col-form-label">Mail<span class="error">*</span>:</label>
					   <textarea type="text" name="mail" id="mail" class="form-control" placeHolder="Enter Mail" ></textarea>
					   <span class="error" id="mail_error"></span>
					</div>
				
				</div>
				<div class="col-md-6">
					<div class="form-group">
					  <label for="recipient-name" class="col-form-label">Language<span class="error">*</span>:</label>
					   <input type="text" name="language" id="language" class="form-control"  placeHolder="Enter Language">
					   <span class="error" id="language_error"></span>
					</div>
				
				</div>
				<div class="col-md-6">
					<div class="form-group">
					  <label for="recipient-name" class="col-form-label">Send Date<span class="error">*</span>:</label>
					   <input type="text" name="send_date" id="send_date" class="form-control datepicker" value="">
					    <span class="error" id="send_date_error"></span>
					</div>
				
				</div>
			
			</div>
			
			
			
			
			
			
			<div class="modal-footer">
			  <button type="button" class="btn btn-success" onclick="getMailSubmit()">Save</button>
			  <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
			</div>
		  </form>
		</div>
		
	  </div>
	</div>
  </div>

@include('include/footer')
  <script src="{{asset('assets/css/toastr/toastr.min.js')}}"></script>

<script>
function getMailSubmit(){
	var full_name = $('#full_name').val();
	var address = $('#address').val();
	var language = $('#language').val();
	var homecare = $('#homecare').val();
	var mail = $('#mail').val();
	var send_date = $('#send_date').val();
	var cnt =0;
	$('#full_name_error').html("");
	$('#address_error').html("");
	$('#language_error').html("");
	$('#homecare_error').html("");
	$('#mail_error').html("");
	$('#send_date_error').html("");
	if(full_name.trim() ==''){
		$('#full_name_error').html("Required");
		cnt =1;
	}
	
	
	if(mail.trim() ==''){
		$('#mail_error').html("Required");
		cnt =1;
	}
	if(send_date.trim() ==''){
		$('#send_date_error').html("Required");
		cnt =1;
	}
	
	if(cnt ==1){
		return false;
	}else{
		$.ajax({
			url:"{{url('mail-master-save')}}",
			type:"POST",
			data:$('#mails_id').serialize(),
			success:function(re){
				if(re.status==1){
					toastr.success(re.error_msg);
				location.reload();
				}else{
					toastr.error(re.error_msg);
				}
			}
		})
	}
		
		
	}
	$('.datepicker').datepicker({
changeMonth: true,
        changeYear: true,
});
function checkedMail(id){
	$.ajax({
		type:"GET",
		url:'{{url("/mail-master/status/")}}/'+id,
		success:function(res){
			if(res.status ==1){
				var checked = $('#cbox'+id).is(":checked");
				toastr.success(res.error_msg);
				if(checked ==true){
					$('#cbox'+id).prop("checked",true);
				}else{
					$('#cbox'+id).prop("checked",false);
				}
				
				
			}else{
				toastr.error(res.error_msg);
			}
			
		}
	})
}
</script>