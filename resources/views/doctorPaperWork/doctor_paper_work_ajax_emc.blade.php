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
                       

                       <th></th>
                     </tr>
                   </thead>
                   <tbody>
				   @php 
						$i = 1 + (($doctor_paper_list->currentPage() - 1) * $doctor_paper_list->perPage());
				   @endphp
					@if($doctor_paper_list->total() != 0)
						
						@foreach($doctor_paper_list as $row)
							<tr id="msgs-{{$row->id}}">
								<td scope="row">{{$i++}}</td>
								<td>
									{{$row->id}}
								</td>
								<td>{{$row->name}}</td>
								<td><a href="{{url('/record/')}}/{{$row->portal_id}}">{{$row->portal_id}}</a></td>
								<td>{{$row->gender}}</td>
								<td>@if($row->dob !='1970-01-01'){{date('m/d/Y',strtotime($row->dob))}} @endif</td>
								<td>{{$row->doctor_name}}</td>
								<td>{{$row->phone}}</td>
								<td>{{$row->fax}}</td>
								<td>{{$row->agency}}</td>
								<td>{{$row->first_name}} {{$row->last_name}}</td>
								<td>
									@if($row->status =='Pending')
										<span class="badge badge-info">Pending</span>
									@elseif($row->status =='Completed')
									<span class="badge badge-success">Completed</span>
									
									@endif
								</td>
								
								<td>
									<a onclick="addNotesDocPaperWork({{$row->id}})" data-toggle="modal" data-target="#exampleModal-doc-paper" data-whatever="@mdo" href="javascript:void(0)"><i class="fa fa-edit"></i></a>
									<a onclick="Approved({{$row->id}},'Completed')" href="javascript:void(0)" title="Completed"><i class="fa fa-thumbs-up"></i></a>
									<a onclick="Approved({{$row->id}},'Pending')" href="javascript:void(0)" title="Pending"><i class="fa fa-thumbs-down"></i></a>
									<a onclick="viewLog({{$row->id}})" href="javascript:void(0)" title="View Log"><i class="fa fa-eye"></i></a>
									
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
			   <script>
			   $(document).on('click', '.pagination a', function(event) {
					$('li').removeClass('active');
					$(this).parent('li').addClass('active');
					event.preventDefault();
					var myurl = $(this).attr('href');
					var page = $(this).attr('href').split('page=')[1];
					getDoctorPaperWork(page);
				});
				
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
			   </script>