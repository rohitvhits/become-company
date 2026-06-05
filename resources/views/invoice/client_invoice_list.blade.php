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
			<div class="card">
          	  <div class="row list-name">
				   <div class="col-sm-6" > <h4 class="card-title">Pending Invoices</h4></div>
				   
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
												<td><?php echo $row->invoice_number;?></td>
												<td><?php echo date('m/d/Y',strtotime($row->invoice_date));?></td>
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
												<tr><td colspan="12"><center><b>No any pending invoice found.</b></center></td></tr>
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