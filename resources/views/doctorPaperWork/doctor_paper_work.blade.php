 @include('include/header')
 @include('include/sidebar')
 <link href="<?php echo URL::to('/');?>/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
 <div class="main-panel">
   <div class="content-wrapper">
     <div class="col-12 grid-margin-top">
     
     </div>
     <div class="card">
       <div class="row list-name">
         <div class="col-sm-6 card-title">
           <h4 class="card-title">Doctor Paper Work List</h4>
         </div>
         <div class="col-sm-6">
           
           <a href="{{url('doctor-paper-work-csv')}}?record_id={{$record_id}}&name={{$name}}&portal_id={{$portal_id}}&gender={{$gender}}&dob={{$dob}}&doctor_name={{$doctor_name}}&doctor_no={{$doctor_no}}&doctor_fax={{$doctor_fax}}&agency_name={{$agency_name}}&status={{$status}}" class="btn btn-warning btn-sm pull-right" style="margin-left:10px;"><i class="mdi mdi-file"> </i> Export CSV</a>&nbsp;&nbsp;
		   <a href="<?php echo URL::to('/doctor-paper-work/create') ?>" class="btn btn-primary btn-sm pull-right"><i class="mdi mdi-plus"> </i> Add Doctor Paper Work </a>
		   </div>
       </div>
       <div class="card-body">
         <div class="row">
           <div class="col-12">
             <div class="table-responsive">
               
                 <table id="" class="table table-bordered">
                   <thead>
                     <tr>
                       <th>#</th>
                       <th>Record#</th>
                       <th>Name</th>
                       <th>Portal ID</th>
                       <th>Gender </th>
                       <th>DOB</th>
                       <th>Doctors name </th>
                       <th>Doctors #</th>
                       <th>Doctors Fax </th>
                       <th>Agency </th>
                       <th>EMC Rep</th>
                       <th>Status</th>
                       

                       <th>Action</th>
                     </tr>
                   </thead>
                   <tbody>
				   <form>
						<tr>
							<td><input class="btn btn-primary" type="submit" name="submit" value="Search"></td>
							<td><input class="form-control" name="record_id" value="{{$record_id}}"></td>
							<td><input class="form-control" name="name" value="{{$name}}"></td>
							<td><input class="form-control" name="portal_id" value="{{$portal_id}}"></td>
							<td>
								<select name="gender" class="form-control" style="width:100px;">
									<option value="">Select Gender</option>
									<option value="male" @if($gender =='male') selected='selected' @endif>Male</option>
									<option value="female"  @if($gender =='female') selected='selected' @endif>Female</option>
								</select>
							</td>
							<td><input class="form-control datepicker" name="dob" style="width:100px;" value="@if($dob !='') {{ date('m/d/Y',strtotime($dob)) }} @endif"></td>
							<td><input class="form-control" name="doctor_name" value="{{$doctor_name}}"></td>
							<td><input class="form-control" name="doctor_no" value="{{$doctor_no}}"></td>
							<td><input class="form-control" name="doctor_fax" value="{{$doctor_fax}}"></td>
							<td><input class="form-control" name="agency_name" value="{{$agency_name}}"></td>
							<td></td>
							<td>
								<select name="status" class="form-control" style="width:100px;">
									<option value="">Select Status</option>
									<option value="Pending" @if($status =='Pending') selected='selected' @endif>Pending</option>
									<option value="Case on Hold" @if($status =='Case on Hold') selected='selected' @endif>Case on Hold</option>
									<option value="Completed" @if($status =='Completed') selected='selected' @endif>Completed</option>
								</select>
							</td>
							<td></td>
						</tr>
				   </form>
				   @php 
						$i = 1 + (($doctor_paper_list->currentPage() - 1) * $doctor_paper_list->perPage());
				   @endphp
					@if($doctor_paper_list->total() != 0)
						
						@foreach($doctor_paper_list as $row)
							<tr id="row{{$row->id}}">
								<td scope="row">{{$i++}}</td>
								<td>{{$row->id}}</td>
								<td>{{$row->name}}</td>
								<td>@if($row->portal_id !='') <a href="{{url('/record/')}}/{{$row->portal_id}}">{{$row->portal_id}}</a> @endif</td>
								<td>{{$row->gender}}</td>
								<td>@if($row->dob !='1970-01-01') {{date('m/d/Y',strtotime($row->dob))}} @endif</td>
								<td>{{$row->doctor_name}}</td>
								<td>{{$row->phone}}</td>
								<td>{{$row->fax}}</td>
								<td>{{$row->agency}}</td>
								<td>{{$row->first_name}} {{$row->last_name}}</td>
								<td id="status{{$row->id}}">
									@if($row->status =='Completed')
										<span class="badge badge-success">Completed</span>
									@endif
									@if($row->status =='Pending')
										<span class="badge badge-info">Pending</span>
									@endif
									@if($row->status =='Case on Hold')
										<span class="badge badge-primary">Case on Hold</span>
									@endif
								</td>
								<td>
									<a onclick="addNotesDocPaperWork({{$row->id}})" data-toggle="modal" title="Add Notes" data-target="#exampleModal-doc-paper" data-whatever="@mdo" href="javascript:void(0)"><i class="fa fa-sticky-note"></i></a>
									<a onclick="viewLog({{$row->id}})" href="javascript:void(0)" title="View Log"><i class="fa fa-eye"></i></a>
									<a onclick="Approved({{$row->id}},'Completed')" href="javascript:void(0)" title="Completed"><i class="fa fa-thumbs-up"></i></a>
									<a onclick="Approved({{$row->id}},'Pending')" href="javascript:void(0)" title="Pending"><i class="fa fa-thumbs-down"></i></a>
									<a onclick="Approved({{$row->id}},'Case on Hold')" href="javascript:void(0)" title="Case on Hold"><i class="fa fa-pause-circle"></i></a>
									<a href="{{url('doctor-paper-work/'.$row->id.'/edit')}}?flag=123"><i class="fa fa-edit"></i></a>
									<a onclick="getDeletes({{$row->id}})" href="javascript:void(0)"><i class="fa fa-trash"></i></a>
								</td>
							</tr>
						@endforeach
					@endif
                     @if($doctor_paper_list->total() == 0)
						<tr>
							<td colspan="12">
								<center><b>Data not found</b></center>
							</td>
                       </tr>
					@endif
                   </tbody>
                 </table>
               
               <div class="pull-right pegination-margin">
                 {{$doctor_paper_list->links("pagination::bootstrap-4")}}
               </div>

             </div>
           </div>
         </div>
       </div>
     </div>
   </div>
  <div class="modal fade" id="view_log_html" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
				  <h5 class="modal-title" id="exampleModalLabel">View Notes </h5>
				  <button type="button" class="close" id="doc_paperwork_closed" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				  </button>
				</div>
				
				<div class="modal-body">
						<span id="response_id_view"></span>
				</div>
			</div>
		</div>
	</div>
<div class="modal fade" id="exampleModal-doc-paper" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="exampleModalLabel">Notes </h5>
                          <button type="button" class="close" id="doc_paperwork_closednew" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                          </button>
                        </div>
						
                        <div class="modal-body">
							<form class="form-sample" action='{{url("doctor-paper-work")}}' id="doctor_paper_work" name="adduser" method="post" >
                    			<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
								<input type="hidden" name="record_id" id="record_id_new" value="">
					
								
								
									<div class="form-group">
									  <label> NOTES</label>
									 
										<input type="text" class="form-control" placeholder="Enter Notes" id="notes_name" name="notes_name">
										 <span class="notes_name_error" style="color:red"></span> 
									</div>
								
							</form>
							
                        </div>
						
                        <div class="modal-footer">
                          <button type="button" id="notes_submit" class="btn btn-success" >Submit</button>
                          <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                        </div>
                      </div>
                    </div>
					
		  </div>
   @include('include/footer')
   
    <script src="<?php echo URL::to('/');?>/assets/css/toastr/toastr.min.js"></script>
	<script>
	@if(Session::has('success'))
		toastr.success('{{ Session::get("success")}}');
	@endif
	@if(Session::has('error'))
		toastr.error( '{{ Session::get("error") }}');
	@endif
	
	$('.datepicker').datepicker();
	function viewLog(id){
			$.ajax({
				type:"GET",
				url:"{{url('doctor-paper-work/')}}/"+id,
				success:function(res){
					var html_res = '';
					$('#response_id_view').html("");
					console.log(res.length); 
					if(res.status ==1){
						
						
						html_res = '<div class="chat-content col-12"><div class="chat-messages" id="chat-messages"><div id="chat-messages-inner">';
						$.each(res.data,function(i,v){
							
							html_res +='<p id="msg-1" class="user-" style=""><span class="msg-block"><strong>'+v.full_name+'</strong><span class="chat-sms-type-new"><span class="icon"> <i class="icon-reply"></i> </span></span>  <span class="time">'+v.created_date+'</span><span class="msg">'+v.notes+'</span>  </span></p>';
						});
						html_res +='</div></div></div>';
					}else{
						html_res ='<p id="msg-1" class="user-" style=""><span class="msg-block"><strong>No record available</strong></span></p></div></div></div>';
					}
					
					$('#response_id_view').html(html_res);
					$('#view_log_html').modal("show");
				}
			})
		}
		function Approved(id,status){
			var confirms = confirm("Are you sure change status?");
			if(confirms ==true){
				$.ajax({
					type:"GET",
					url:'{{url("change-status-paper-work")}}',
					data: {'id':id,'status':status},
					success:function(res){
						if(res ==1){
								toastr.success("Status successfully changed.");
							if(status =="Pending"){
								var label = '<span class="badge badge-info">Pending</span>';
							}else if(status =="Completed"){
								var label = '<span class="badge badge-success">Completed</span>';
							}else{
							var label = '<span class="badge badge-primary">Case on Hold</span>';
							}								
							$('#status'+id).html(label);
							
						}else{
							toastr.error('Sorry, something went wrong. Please try again.');
						}
					}
				})
			}
			
		}
		function getDeletes(id){
			var confirms = confirm("Are you sure delete this record?");
			if(confirms ==true){
				$.ajax({
					type:"GET",
					url:'{{url("doctor-paper-work-delete")}}/'+id,
					
					success:function(res){
						if(res ==1){
								toastr.success("Doctor Paper work successfully deleted.");
														
							$('#row'+id).remove();
							
						}else{
							toastr.error('Sorry, something went wrong. Please try again.');
						}
					}
				})
			}
			
		}
		$('#notes_submit').click(function(e){
			var names = $('#notes_name').val();
			$('#notes_name_error').html("");
			var cnt =0;
			console.log(names);
			if(names.trim() == ''){
				console.log("RERR");
				$('.notes_name_error').html("Required");
				cnt =1;
			}
			if(cnt ==1){
				return false;
			}else{
				var doctor_paper_work = $('#doctor_paper_work')[0];
				var forms = new FormData(doctor_paper_work);
				$.ajax({
					type:"POST",
					url:'{{url("doctor-paper-work-notes")}}',
					data: $('#doctor_paper_work').serialize(),
					success:function(res){
						if(res ==1){
							toastr.success("Notes successfully added.");
							$('.close').click();
							
						}else{
							toastr.error('Sorry, something went wrong. Please try again.');
						}
					}
				})
				
			}
		});
		function addNotesDocPaperWork(id){
				$('#record_id_new').val(id);
			}
	</script>