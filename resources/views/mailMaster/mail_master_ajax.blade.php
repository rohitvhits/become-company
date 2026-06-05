 <table id="exmple1" class="table table-bordered dataTable no-footer">
			<thead>
			  <tr>
				<th></th>
				<th>#</th>
				
			   <th>Full Name</th>
			   
				<th>Homecare</th>
				<th>Address</th>
				<th>Mail</th>
				<th>Language</th>
				<th>Send Date</th>
			  </tr>
			 </thead>
			 <tbody>
			 <?php 
			 
			 ?>
				@if(count($query) >0)
					@foreach($query as $va)
					
					<tr>
						<td><input type="checkbox" id="cbox{{$va->id}}" onclick="checkedMail({{$va->id}})" @if($va->mail_status ==1) checked='checked' @endif></td>
						<td>{{$va->id}}</td>
						<td>{{$va->full_name}}</td>
						<td>{{$va->homecare}}</td>
						<td>{{$va->address}}</td>
						<td>{{$va->mail}}</td>
						<td>{{$va->language}}</td>
						<td>{{date('m/d/Y',strtotime($va->send_date))}}</td>
						
					</tr>
					@endforeach
				@endif
				@if(count($query) ==0)
				<tr><td colspan="5">No record available</td></tr>
				@endif
			</tbody>
		  </table>
		 <script>
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