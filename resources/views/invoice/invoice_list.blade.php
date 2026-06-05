 @include('include/header')
@include('include/sidebar')
 <style>
 .label {
    display: inline;
    padding: .2em .6em .3em;
    font-size: 100%;
    font-weight: 700;
    line-height: 1;
    color: #fff;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: .25em;
}
 .label-success {
    background-color: #5cb85c;
}
.label-danger {
    background-color: #d9534f;
}.label-warning {
    background-color: #f0ad4e;
}.label-default {
    background-color: #777;
}
 </style>
  <link rel="stylesheet" href="<?php echo URL::to('/');?>/assets/vendors/select2/select2.min.css">
  <link rel="stylesheet" href="<?php echo URL::to('/');?>/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
 <div class="main-panel">

        <div class="content-wrapper">
			<div class="card" style="margin-bottom:10px;">
				<div class="card-body">
					<form action="">
					<div class="row">
						<div class="col-md-3">
							<div class="form-group row">
								<label  class="col-sm-3 col-form-label">Invoice Date</label>
								<div class="col-sm-9">
									<input type="text" name="daterange" value="<?php if(isset($daterange) && $daterange !=''){ echo $daterange;} ?>"  class="form-control datepicker" />
								</div>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group row">
								<label  class="col-sm-3 col-form-label">Agency</label>
								<div class="col-sm-9">
									<?php if($user->user_type_fk ==3){ ?>
											<select class="js-example-basic-multiple w-100" multiple="multiple" name="agency_fk[]" id="agency_fk">
													<option value="">Select agency</option>
													<?php foreach ($agencyList as $rwAgency) { 
														$selected = '';
														if(isset($agency_fk) && $agency_fk !=''){
															if($user->user_type_fk ==3){ 
																if(in_array($rwAgency->id ,$agency_fk)){
																	$selected = 'selected="selected"';
																}
															}
														}
													?>
														<option value="<?php echo $rwAgency->id ?>" <?php echo $selected;?> ><?php echo $rwAgency->agency_name; ?></option>
														<?php } ?>
											</select>
									<?php } ?>
								</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group row">
								<label  class="col-sm-3 col-form-label">Status</label>
								<div class="col-sm-9">
									<select class="form-control" name="status_id" id="">
										<option value="">Select Status</option>
										<option value="Draft" <?php if($status_id =='Draft'){ echo "selected='selected'";}?>>Draft</option>
										<option value="Sent" <?php if($status_id =='Sent'){ echo "selected='selected'";}?>>Sent</option>
										
										<option value="Paid" <?php if($status_id =='Paid'){ echo "selected='selected'";}?>>Paid</option>
										<option value="Viewed" <?php if($status_id =='Viewed'){ echo "selected='selected'";}?>>Viewed</option>
									</select>
								</div>
							</div>
						</div>
						<div class="col-md-1">
							<div class="form-group row">
								<label  class="col-sm-3 col-form-label"></label>
								<div class="col-sm-9">
								<input  type="submit" name="search" class="btn btn-primary btn-sm btn-rounded btn-fw  pull-right"  value="search">
								</div>

							</div>
						</div>
					</div>
					</form>
					@if($user->user_type_fk ==3)
								
								<a href="{{URL::to('/invoice/get-all-clients')}}" target="_blank" style="float:right"> Sync Invoice </a>
								@endif
				</div>
			</div>
			<div class="card">
          	  <div class="row list-name">
				   <div class="col-sm-6" > <h4 class="card-title">Invoice List</h4></div>
				   
 			</div>
    
            <div class="card-body">
              
			  <div class="row">
                <div class="col-12">
                  <div class="table-responsive">
                  		    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
		                    <table id="" class="table table-bordered">
		                      <thead>
		                        <tr>
											<th>#</th>
											<th>Invoice Number #</th>
											
											<th>Invoice Date</th>
											<th>Agency Name</th>
											<th>Total Amount</th>
											<th>Due Amount</th>
											<th>No of Record</th>
											<th>Status</th>
											<th>Action</th>
											
										</tr>
		                      	
		                      </thead>
		                      <tbody>
										<?php 
										if($invoiceList->total() > 0) {
											$i = 1 +(($invoiceList->currentPage()-1) * $invoiceList->perPage());
										
										foreach ($invoiceList as $row) {  ?>
											<tr>
												<td scope="row"><?= $i++ ?></td>
												<td><a href="<?php echo URL::to('/');?>/invoice-detail-new/<?php echo $row->invoice_number;?>" target="_blank"><?php echo $row->invoice_number;?></a></td>
												<td><?php echo date('m/d/Y',strtotime($row->invoice_date));?></td>
												<td><?php echo ucfirst($row->agency_name);?></td>
												<td><?php echo '$'.$row->total_amount;?></td>
												<td><?php if($row->due_amount !=''){  echo '$'.$row->due_amount;} ?></td>
												<td><?php echo $row->total_record;?></td>
												<td><?php 
												$status =  '<label class="label label-default">'.$row->status.'</label>'; 

												if($row->status =='Paid'){ 
															$status = '<label class="label label-success"> Paid</label>';
														} 
														if($row->status =='Partially Paid') { 
															$status = '<label class="label label-danger">Partially Paid</label>';
														} 
														if($row->status =='Sent'){ 
															$status = '<label class="label label-warning">Sent</label>';
														}
														if($row->status =='Created'){ 
															$status =  '<label class="label label-default">Created</label>'; 
														} 
														echo $status;?></td>
												<td>
													<a target="_blank" href="<?php echo $row->ninja_invitation_link ;?>"><i class="fa fa-file" aria-hidden="true"></i></a>
													
														<!-- <a href="<?php echo URL::to('/');?>/invoice-detail/<?php echo $row->id;?>/<?php echo $row->agency_fk;?>"><i class="fa fa-file" aria-hidden="true"></i></a> -->
												
												</td>
											</tr>
										<?php } }else { ?>
												<tr><td colspan="12"><center><b>Data not found</b></center></td></tr>
										<?php }?>
									</tbody>
		                    </table>
                  		  
                    <div class="pull-right pegination-margin">
						{{$invoiceList->appends(request()->input())->links("pagination::bootstrap-4")}}
						</div>
                     
                  </div>
                </div>
              </div>
           </div>
            </div>
          </div>
		  


@include('include/footer')
<script type="text/javascript" src="<?php echo URL::to('/');?>/assets/js/moment.min.js"></script>
<script type="text/javascript" src="<?php echo URL::to('/');?>/assets/js/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo URL::to('/');?>/assets/css/vertical-layout-light/daterangepicker.css" />
<script src="<?php echo URL::to('/');?>/assets/vendors/select2/select2.min.js"></script>
<script src="<?php echo URL::to('/');?>/assets/vendors/select2/select2.min.js"></script>
  <script src="<?php echo URL::to('/');?>/assets/js/select2.js"></script>
<script>
$(document).ready(function(){

			var start = moment().subtract(0, 'days');
			var end = moment();
			$('.datepicker').daterangepicker({
				startDate: start,
				endDate: end,
				autoUpdateInput: false,
				
			});
			$('.datepicker').on('apply.daterangepicker', function(ev, picker) {
      $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
  });

});
</script>