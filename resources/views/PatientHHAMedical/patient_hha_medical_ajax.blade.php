 <style>
	.error{
		color:red;
	}
 </style>
 <table id="order-listing1" class="table table-bordered">
   <thead>
	 <tr>
	  
	   <th><input type="checkbox" id="checkboxId"><br>
		<span id="cbox_error" class="error"></span>
	   </th>
	   <th>#</th>
	   <th>Caregiver Code</th>
	   <th>First Name</th>
	   <th>Last Name</th>
	   <th>Date of Birth</th>
	   <th>Phone</th>
	   <th>Mobile</th>
	   <th>Gender</th>
	   <th>Language</th>
	   <th>Service</th>
	   <th>Service Expiry Date</th>
	   
	  
	   
	   

	   <th></th>
	 </tr>
	</thead>
	<tbody>
		@php
			$i = ($page * 50) - 49
		
		@endphp
		@if(count($query) >0)
			@foreach($query as $val)
				<tr>
					<td><input type="checkbox" class="cbox" id="{{$val->id}}" name="cbox[]" value="{{$val->id }}"></td>
					<td>{{ $i++ }}</td>
					<td>{{ $val->caregiver_code }}</td>
					<td>{{ $val->caregiver_first_name }}</td>
					<td>{{ $val->caregiver_last_name }}</td>
					<td>{{ date('m/d/Y',strtotime($val->caregiver_dob)) }}</td>
					<td>{{ $val->phone }}</td>
					<td>{{ $val->mobile }}</td>
					<td>{{ $val->gender }}</td>
					<td>{{ $val->language }}</td>
					<td>{{ $val->medical_name }}</td>
					<td> @if($val->due_date != '0000-00-00') {{ date('m/d/Y',strtotime($val->due_date)) }} @endif</td>
					
				</tr>
			
			@endforeach
		@endif
	</tbody>
</table>
<div class="pull-right pegination-margin">
			   <!-- {{ $query->appends(request()->query())->links()  }}-->
                {{$query->links("pagination::bootstrap-4")}}
               </div>
			   <script>
			   $("#checkboxId").click(function(){
		console.log("RERER");
		$('.cbox').not(this).prop('checked', this.checked);
	});
	setTimeout(function(e) { 
		var total = {{ $query->total()}};
		
		$('#total_count_id').html(total);
	},3);
	</script>