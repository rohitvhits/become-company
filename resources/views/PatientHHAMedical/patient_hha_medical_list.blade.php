 @include('include/header')
 @include('include/sidebar')
 
  <link rel="stylesheet" href="<?php echo URL::to('/');?>/assets/vendors/select2/select2.min.css">
  <link rel="stylesheet" href="<?php echo URL::to('/');?>/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo URL::to('/');?>/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
  <link href="<?php echo URL::to('/');?>/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
  <style>
  #order-listing_length,#order-listing_paginate,#order-listing_info{
		display: none;
	}
	#order-listing_filter{
		text-align: right;
	}
  span.select2.select2-container.select2-container--default {
    width: 200px !important;
}

.wmd-view-topscroll, .wmd-view {
            overflow-x: scroll;
            overflow-y: hidden;
            border: none 0px red; 
        }
.wmd-view{
  overflow: auto;
  height: calc(100vh - 250px);
}
        .wmd-view-topscroll { height: 20px; }
        .scroll-div1 { 
          
            overflow-x: scroll;
            overflow-y: hidden;
            height:20px;
        }
        .scroll-div2 { 
            height:20px;
        }
        .scroll-div1 , .scroll-div2{  
             width: 1000px; 
         }
		 td 
{
  table-layout:fixed;
  width:20px;
  overflow:hidden;
  word-wrap:break-word;
}
</style>
 <div class="main-panel">
   <?php
    $auth = auth()->user();
    ?>
   <div class="content-wrapper">
     <div class="card">
       <div class="row list-name">
         <div class="col-sm-5 card-title">
           <h4 class="card-title">HHA Data ({{ $query->total()}})</h4>
         </div>
         <div class="col-sm-7">
			
           
           <a href="javascript:void(0)" onclick="submitDetails();" class="btn btn-primary btn-rounded btn-fw btn-sm pull-right"> Submit Patient</a>
          
	   </div>
       </div>
       <div class="card-body compact-view">
         <div class="row">
           <div class="col-12">
				<div class="wmd-view-topscroll">
					<div class="scroll-div1">
					</div>
				</div>
				<div class="wmd-view">
             <div class="scroll-div2">
			 <form>
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
						<tr>
							
							<td><input type="submit" name="submit" value="Submit" class="btn btn-primary"></td>
							<td></td>
							<td><input type="text" name="code" class="form-control" placeHolder="Caregiver Code" value="{{ $code }}"></td>
							<td><input type="text" name="fname" class="form-control" placeHolder="First Name" value="{{ $fname }}"></td>
							<td><input type="text" name="lname"  class="form-control" placeHolder="Last Name" value="{{ $lname }}"></td>
							<td><input type="text" name="dob" class="form-control datepicker" placeHolder="Date of Birth" value="{{ $dob }}"></td>
							<td><input type="text" name="phone" class="form-control" placeHolder="Phone" value="{{ $phone }}"></td>
							<td><input type="text" name="mobile" class="form-control" placeHolder="Mobile"  value="{{ $mobile }}"></td>
							<td>
								<select name="gender" class="form-control">
									<option value="">Select Gender</option>
									<option value="MALE" @if($gender =="MALE") selected @endif>Male</option>
									<option value="FEMALE" @if($gender =="FEMALE") selected @endif>Female</option>
								</select>
							</td>
							
							<td>
								<input type="text" name="language" class="form-control" placeHolder="Enter Language" value="{{ $language }}">
							</td>
							<td>
								<input type="text" name="service_name" class="form-control" placeHolder="Enter Service" value="{{ $service_name }}">
							</td>
							<td>
								<input type="text" name="service_exp" class="form-control datepickernn" placeHolder="Enter Service Expiry Date" value="{{ $service_exp }}">
							</td>
							
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
						
						@if(count($query) ==0)
							<tr>
								<td colspan="12">No record available</td>							
							</tr>
						@endif
					</tbody>
				</table>
				</form>
					<div class="pull-right pegination-margin">
				   <!-- {{ $query->appends(request()->query())->links()  }}-->
					{{$query->links("pagination::bootstrap-4")}}
				   </div>
             </div>
			 </div>
           </div>
         </div>
       </div>
     </div>
   </div>
   
        <div class="row" style='margin-top: 25px;'>
            <pre id='toastrOptions'></pre>
        </div>
   
  

   @include('include/footer')
  <script src="<?php echo URL::to('/');?>/assets/css/toastr/toastr.min.js"></script>
  <script type="text/javascript" src="<?php echo URL::to('/');?>/assets/js/moment.min.js"></script>
<script type="text/javascript" src="<?php echo URL::to('/');?>/assets/js/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo URL::to('/');?>/css/daterangepicker.css" />
<script>
$('.datepicker').datepicker();
	$(function () {
			var start = moment().subtract(0, 'days');
			var end = moment();
			$('.datepickernn').daterangepicker({
				startDate: start,
				endDate: end,
				autoUpdateInput: false,
				startOfWeek: 'sunday',
				ranges: {
					'Today': [moment(), moment()],
					'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
					'Last 7 Days': [moment().subtract(6, 'days'), moment()],
					'Last 30 Days': [moment().subtract(29, 'days'), moment()],
					'This Month': [moment().startOf('month'), moment().endOf('month')],
					'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
					'Next Month': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month').endOf('month')],
					'Next Week': [moment().add(1, 'weeks').startOf('isoWeek'), moment().add(1, 'weeks').endOf('isoWeek')],
					'Last Week': [moment().subtract(1, 'weeks').startOf('isoWeek'), moment().subtract(1, 'weeks').endOf('isoWeek')],
				}
			}, function (chosen_date, end_date) {

				$('.datepickernn').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format('MM/DD/YYYY'));
			})
			
	});
	toastr.options.closeButton = true;
    toastr.options.tapToDismiss = false;
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": false,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "500",
        "timeOut": "3000",
        "extendedTimeOut": 0,
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut",
        "tapToDismiss": false
    };
	function submitDetails(){
		var final_arral=[];
		var checkedornot = $('input[name="cbox[]"]').is(":checked");
		$('#cbox_error').html("");
		if(checkedornot ==false){
				$('#cbox_error').html("Required");
				return false;
		}
		$('input[name="cbox[]"]').each(function(i,v){
			   
		   var checked = $(this).is(":checked"); 
			if(checked ==true){
				var cboxs = $(this).val();
				final_arral.push(cboxs)
			}
		});
		
		$.ajax({
            type:"POST",
            url:"{{url('/add-patient-hha')}}",
            data:{'id':final_arral,'_token':'{{ csrf_token() }}'},
            success:function(res){
				
                if(res.status ==1){
					toastr.success(res.error_msg);
					location.reload();
				}else{
					toastr.error(res.error_msg);
				}
                
            }
        })
		
	}
</script>
<script>
	$("#checkboxId").click(function(){
		console.log("RERER");
		$('.cbox').not(this).prop('checked', this.checked);
	});
	
	</script>