@include('include/header')
@include('include/sidebar')
<?php $flag ='';if(!empty($query)){
	foreach($query as $val){
		$flag =0;
		if($val->payment_status =='due'){
			$flag = 1;
		}
	}
	
}
$paymentSum = 0;
if(!empty($invoice_PDetails)){
	foreach($invoice_PDetails as $vals){
		$paymentSum = $paymentSum+$vals->amount;
	}
}

?>
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
<div class="main-panel">          
        <div class="content-wrapper">
          <div class="row">
              <div class="col-lg-12">
                  <div class="card px-2">
                      <div class="card-body">
                          <div class="container-fluid">
                            <h3 class="text-right my-5">Invoice&nbsp;&nbsp;#INV-<?php echo $invoide_details->invoice_number;?></h3>
                            <hr>
                          </div>
                          <div class="container-fluid d-flex justify-content-between">
                            <div class="col-lg-3 pl-0">
                              <p class="mt-5 mb-2"><b><?php echo $agency_details->agency_name;?></b></p>
                              <p><?php echo $agency_details->address1;?>,<?php echo $agency_details->address2;?><br><?php echo $agency_details->city;?>,<br><?php echo $agency_details->state;?>,<?php echo $agency_details->zip_code;?>.</p>
                            </div>
                            
                          </div>
                          <div class="container-fluid d-flex justify-content-between">
                            <div class="col-lg-3 pl-0">
                              <p class="mb-0 mt-5">Invoice Date : <?php echo date('d M Y',strtotime($invoide_details->invoice_date));?></p>
                              
                            </div>
                          </div>
						   <div class="container-fluid d-flex justify-content-between">
                            <div class="col-lg-3 pl-0">
                              <p class="mb-0 mt-5">Invoice : 
								<?php if($invoide_details->status =='Created'){
									$status = '<label class="label label-default">Created</label>';
								}
								if($invoide_details->status =='Sent'){
									$status = '<label class="label label-warning">Sent</label>';
								}
								if($invoide_details->status =='Partially Paid'){
									$status = '<label class="label label-danger">Partially Paid</label>';
								}
								if($invoide_details->status =='Paid'){
									$status = '<label class="label label-success"> Paid</label>';
								} echo $status;?>
							  
							  </p>
                              
                            </div>
                          </div>
						<form action="<?php echo URL::to('/');?>/paidInvoice" method="post" enctype="multipart/form-data" id="submitId">
							<input type="hidden" name="_token" value="<?php echo csrf_token();?>">
							<input type="hidden" name="actual_amount" value="<?php echo $invoide_details->total_amount;?>">
							<input type="hidden" name="paid_amount" id="paid_amount" value="0">
							<input type="hidden" name="due_amount" id="due_amount" value="<?php echo $invoide_details->total_amount -$paymentSum;?>">
							<input type="hidden" name="invoice_id" id="invoice_ids" value="<?php echo $id;?>">
							<input type="hidden" name="agency_fk" value="<?php echo $agency_fk;?>">
							<input type="hidden" name="receivePayment" value="<?php echo $paymentSum;?>">
							<?php if($flag ==1){?>
							<div class="pull-right">
								<input type="submit" name="submit" value="Pay" class="btn btn-primary">
							</div>
							<?php }?>
							
							  <div class="container-fluid mt-5 d-flex justify-content-center w-100">
								
								<div class="table-responsive w-100">
									<table class="table">
									  <thead>
										<tr class="bg-dark text-white">
											<th></th>
											<th>#</th>
											
											<th>Item</th>
										
											<th class="text-right">Amount</th>
											
										  </tr>
									  </thead>
									  <tbody>
										<?php if(!empty($query)){ $cnt =1;
											foreach($query as $val){   ?>
											<tr class="text-right">
												<td class="text-left"><input type="checkbox"  name="check_ids[]" onclick="getTotalPrice('<?php echo $val->rates;?>',<?php echo $val->id;?>)" class="check_all_id<?php echo $val->id;?>" value="<?php echo $val->id;?>" data-id="<?php echo $val->rates;?>" <?php if($val->payment_status =='paid'){ ?>disabled checked="checked"<?php } ?>></td>
												<td class="text-left"><?php echo $cnt++;?></td>
												<td class="text-left"><?php echo $val->name;?></td>
												
												<td>$<?php echo $val->rates;?></td>
											
											</tr>
										<?php } } ?>
									  </tbody>
									</table>
									
								  </div>
								 
							  </div>
							   <div class="row">
								  <div class="col-md-6">
									<div class="form-group row">
									  <label class="col-sm-3 col-form-label">Remark</label>
									  <div class="col-sm-9">
										<textarea style="height: 80px !important;" class="form-control" name="remark" id="remarkid"></textarea>
										
										  <span id="bill_date_error" class="error mt-2 text-danger"></span>
									  </div>
									</div>
								  </div>
								</div>
						  	</form>
							  <div class="container-fluid mt-5 w-100">
								
								<p class="text-right mb-2" >Actual amount: $<?php echo $invoide_details->total_amount - $paymentSum;?></p>
								<p class="text-right">Due Amount: $ <span id="due_amount_id">0</span></p>
								<h4 class="text-right mb-5">Total : $<span id="total_id">0</span></h4>
								<hr>
							  </div>
							  <div class="container-fluid w-100">
								<a href="#" class="btn btn-primary float-right mt-4 ml-2"><i class="mdi mdi-printer mr-1"></i>Print</a>
								<a href="#" class="btn btn-secondary float-right mt-4"><i class="mdi mdi-telegram mr-1"></i>Send Invoice</a>
							  </div>
							
                      </div>
                  </div>
              </div>
			  
          </div>
			<div class="row" style="margin-top:2%">
				<div class="col-lg-12">
					<div class="card px-2">
						<div class="card-body">
							<div class="container-fluid">
								<h3 class=" my-5">Invoice Payment Details</h3>
								<hr>
							</div>
							
							<div class="row">
								<div class="col-12">
									<div class="table-responsive">
										<table id="" class="table">
											<thead>
												<tr>
													<th>#</th>
													<th>Payment Date</th>
													
													<th>Total Amount</th>
													
													<th>Remark</th>
													
													<th></th>
												</tr>
		                      	
											</thead>
										<tbody>
											<?php if(count($invoice_PDetails)){ $cnt1=1;foreach($invoice_PDetails as $val){?>
											<tr>
												
												<td><?php echo $cnt1++;?></td>
												<td><?php echo date('m/d/Y',strtotime($val->created_date));?></td>
					
												<td>$<?php echo $val->amount;?></td>
												<td><?php echo $val->remark;?></td>
												
											</tr>
											<?php } }  ?>
										</tbody>
									</table>
                  		 
                    
								</div>
							</div>
						</div>
					</div>
                  </div>
              </div>
			  
          </div>
		  
		
        </div>
    
@include('include/footer')
<script>
	function getTotalPrice(price,id){
		var check  = $('.check_all_id'+id).prop("checked");

		var dua_price =  $('#due_amount').val();
		var paid_amount =  $('#paid_amount').val();
		var dua_price1=0;
		if(check ==true){
			dua_price1 = dua_price - price;
			paid_amount = parseFloat(paid_amount) + parseFloat(price);
			
		}else{
			dua_price1 = parseFloat(dua_price) + parseFloat(price);
			paid_amount = parseFloat(paid_amount) - parseFloat(price);
		}
		$('#due_amount_id').text(dua_price1);
		$('#due_amount').val(dua_price1)
		$('#total_id').text(paid_amount);
		$('#paid_amount').val(paid_amount)
		
	}
	$('#submitId').submit(function(e){
		var AppoveVisitIds = [];
		var remarkid = $('#remarkid').val();
		$.each($("input[name='check_ids[]']:checked"), function() {
			AppoveVisitIds.push($(this).val());
		});
		if(AppoveVisitIds ==''){
			alert("Please select checkbox.");
			return false;
		}
		if(remarkid ==''){
			$('#bill_date_error').html("Required !");
			return false;
		}
	});
	
	</script>