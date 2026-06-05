@include('include/header')
@include('include/sidebar')
  <link rel="stylesheet" href="<?php echo URL::to('/');?>/assets/vendors/select2/select2.min.css">
  <link rel="stylesheet" href="<?php echo URL::to('/');?>/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
  <link href="<?php echo URL::to('/');?>/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
<style type="text/css">
	#order-listing_length,#order-listing_paginate,#order-listing_info{
		display: none;
	}
	#order-listing_filter{
		text-align: right;
	}
	..select2-container{
		width:200px !important;
	}
	   .wmd-view-topscroll, .wmd-view {
            overflow-x: scroll;
            overflow-y: hidden;
            border: none 0px red;
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
             width: 1300px; 
         }
</style>
 <div class="main-panel">

        <div class="content-wrapper">
          <div class="card">
          	  <div class="row list-name">
				   <div class="col-sm-5" > <h4 class="card-title">Activity Report List ({{$record_list->total()}})</h4></div>
				   <div class="col-sm-7 pull-right" >
				   <a href="javascript:void(0)"  data-toggle="modal" data-target="#modal-default-patient" data-whatever="@mdo" class="btn btn-info btn-fw btn-sm pull-right"><i class="mdi mdi-reload"></i>Send Mail</a>
					
				   	<a href="<?php echo URL::to("/"); ?>/expert-medicaid-report" class="btn btn-danger pull-right btn-fw btn-sm"><i class="mdi mdi-reload"></i> Reset</a>
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
              		    
		                    <table id="order-listing1" class="table table-bordered">
								<thead>
									<tr>
										<th>#</th>
										
										<th>Record Id</th>
										@if(in_array($user->user_type_fk,array('3','4')))
										<th>Agency Name</th>
										@endif
										<th>Patient Name</th>
										<th>Type</th>
										<th>Subject</th>
										<th>Notes</th>
										<th>Status</th>
										<th>Created Date</th>
										<th>Created By</th>
										
									</tr>
                                    <form>
										<tr>
											<td>
												<input type="submit" name="search" class="btn btn-primary btn-fw pull-right btn-sm" value="search">
											</td>
											
											<td>
												<input type="text" class="form-control" name="record_id" id="record_id" value="{{ $record_id }}">
											</td>
											@if(in_array($user->user_type_fk,array('3','4')))
												<td>
													<select name="agency_fk" class="form-control" id="agency_id">
														<option value="">Select Agency</option>
														@foreach($agencyList as $agencys)
															<option value="{{ $agencys->id }}" @if($agency_fk == $agencys->id) selected @endif>{{ $agencys->agency_name }}</option>
														
														@endforeach
													</select>
												</td>
											@else
												<input type="hidden" name="agency_fk" value="{{ $user->agency_fk}}" id="agency_id">
											@endif
											<td><input type="text" class="form-control" name="patient_name" id="patient_name" value="{{ $patient_name }}"></td>
											<td></td>
											<td></td>
											<td></td>
											<td>
												<select name="status" id="status" class="form-control">
													<option value="">Select Status</option>
													@foreach($masterData as $val)
														<option value="{{ $val->name }}" @if($status ==$val->name) selected @endif>{{ $val->name }}</option>
													
													@endforeach
												</select>
											</td>
											<td><input type="text" class="form-control datepickernn" name="created_date" value="{{ $created_date }}"></td>
										</tr>

									</form>
								</thead>
								<tbody>
								@php
									$i = ($page *50 )- 49
								@endphp 
									@if(count($record_list) >0)
										@foreach($record_list as $val)
											<tr>
												<td>{{ $i++ }}</td>
												
												<td> <a href=" {{ url('record')}}/{{ $val->record_id}}">{{ $val->record_id }}</a></td>
												@if(in_array($user->user_type_fk,array('3','4')))
													<td>{{ $val->agency_name}}</td>
												@endif
												<td>{{ $val->rfname}} {{ $val->rlname}}</td>
												<td> {{ $val->type }}</a></td>
												<td> {{ $val->subject }}</a></td>
												<td style="white-space:pre-wrap"> {{ $val->notes }}</a></td>
												<td> {{ $val->status }}</a></td>
												<td> {{ date('m/d/Y h:i A',strtotime($val->created_date)) }}</a></td>
												<td> {{ $val->first_name }}</a></td>
											</tr>
										
										@endforeach
									@endif
									@if(count($record_list) ==0)
									<tr>
												<td colspan="9">No record available</td>
											</tr>
									@endif
								</tbody>
		                    </table>
                  		
						<div class="pull-right pegination-margin">
						{{$record_list->appends(request()->input())->links("pagination::bootstrap-4")}}
						</div>             
					</div>
					</div>
                </div>
              </div>
           </div>
            </div>
          </div>
		  
 <div class="modal fade" id="modal-default" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg" role="document">
			   <div id="messages_id" ></div>
			</div>
		  </div>
		  		  
 <div class="modal fade" id="modal-default-patient" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="exampleModalLabel">Send Mail </h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                          </button>
                        </div>
						
                        <div class="modal-body">
						<form action="" method="post" enctype="multipart/form-data" id="patient_record_submit">
							<input type="hidden" name="patient_record_id" id="patient_record_id">
							<div class="form-group">
								<label for="recipient-name" class="col-form-label">Email:</label>
								
									<textarea name="email" id="email_id" class="form-control" rows="4" cols="50"></textarea>
								
								<span >(Commas separate)</span>
							</div>
							
							</form>
                        </div>
						
                        <div class="modal-footer">
                          <button type="button" class="btn btn-success" onclick="sendMail()">Submit</button>
                          <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                        </div>
                      </div>
                    </div>
		  </div>
		  
@include('include/footer')

		

 <script type="text/javascript" src="<?php echo URL::to('/');?>/assets/js/moment.min.js"></script>
<script type="text/javascript" src="<?php echo URL::to('/');?>/assets/js/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo URL::to('/');?>/css/daterangepicker.css" />
  <script src="<?php echo URL::to('/');?>/assets/css/toastr/toastr.min.js"></script>
		
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
			
			$('.datepicker1').daterangepicker({
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

				$('.datepicker1').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format('MM/DD/YYYY'));
			})
			$('.datepicker_date').daterangepicker({
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

				$('.datepicker_date').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format('MM/DD/YYYY'));
			})
			
		});
// Binds the hidden input to be used as datepicker.
$(function(){
    $(".wmd-view-topscroll").scroll(function(){
        $(".wmd-view")
            .scrollLeft($(".wmd-view-topscroll").scrollLeft());
    });
    $(".wmd-view").scroll(function(){
        $(".wmd-view-topscroll")
            .scrollLeft($(".wmd-view").scrollLeft());
    });
});

function sendMail(){
	var email_id = $('#email_id').val();
	var status = $('#status').val();
	var record_id = $('#record_id').val();
	var datepickernn = $('.datepickernn').val();
	var agency_id = $('#agency_id').val();
	$.ajax({
		type:'POST',
		url:" {{ url('user-export/send-mail')}}",
		data:{
			'email':email_id,
			'record_id':record_id,
			'status':status,
			'created_date':datepickernn,
			'agency_id':agency_id,
			'_token':"{{ csrf_token() }}"
		},
		success:function(res){
			toastr.success('Mail successfully send');
				$('#email_id').val('');
				$('.close').click();
		}
	})
	
}
</script>
