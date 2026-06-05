@include('include/header')
@include('include/sidebar')
<?php if(!empty($query)){
	foreach($query as $val){
		$flag =0;
		if($val->payment_status =='due'){
			$flag = 1;
		}
	}
}?>
<div class="main-panel">          
        <div class="content-wrapper">
          <div class="row">
              <div class="col-lg-12">
                  <div class="card px-2">
                      <div class="card-body">
                          <div class="container-fluid">
                            <h3 class="text-right my-5">Invoice&nbsp;&nbsp;<?php echo date('M Y');?></h3>
                            <hr>
                          </div>
						<form action="<?php echo URL::to('/');?>/getInvoiceStore/<?php echo $agency_fk;?>" method="post" enctype="multipart/form-data" id="submitId">
							<div class="container-fluid mt-5 d-flex justify-content-center w-100">
								<div class="table-responsive w-100">
								
									<input type="hidden" name="_token" value="<?php echo csrf_token();?>">
									<input type="hidden" name="paid_amount" id="paid_amount" value="0">
									<input type="hidden" name="agency_fk" value="<?php echo $agency_fk;?>">
									<input type="hidden" name="msp" value="Y">
									<table class="table">
										<thead>
											<tr class="bg-dark text-white">
												<th></th>
												<th>#</th>
												<th>Description</th>
											   
												<th class="text-right">Total</th>
											</tr>
										</thead>
										<tbody>
											<?php if(count($invoide_details)>0){
												$cnt =1;
												foreach($invoide_details as $val){?>
											
												<tr class="text-right">
													<td><input type="checkbox"  name="check_ids[]" onclick="getTotalPrice('<?php echo $val->rates;?>',<?php echo $val->id;?>)" class="check_all_id<?php echo $val->id;?>" value="<?php echo $val->id;?>" data-id="<?php echo $val->rates;?>"></td>
													<td class="text-left"><?php echo $cnt++;?></td>
													<td class="text-left"><?php echo $val->name;?></td>
													<td>$<?php echo $val->rates;?></td>
												</tr>
											<?php } }?>
										</tbody>
									</table>
								
								</div>
							</div>
							<div class="container-fluid mt-5 w-100">
                         
								<h4 class="text-right mb-5">Total : $<span id="total_id">0</span></h4>
								<hr>
							</div>
							<div class="container-fluid w-100">
								<input type="submit" name="submit" class="btn btn-secondary float-right mt-4" value="Generate Invoice">
							</div>
						</form>
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
			
			paid_amount = parseFloat(paid_amount) + parseFloat(price);
			
		}else{
			
			paid_amount = parseFloat(paid_amount) - parseFloat(price);
		}
	
		$('#total_id').text(paid_amount);
		$('#paid_amount').val(paid_amount)
		
	}
	$('#submitId').submit(function(e){
		var AppoveVisitIds = [];
		$.each($("input[name='check_ids[]']:checked"), function() {
			AppoveVisitIds.push($(this).val());
		});
		if(AppoveVisitIds ==''){
			alert("Please select checkbox.");
			return false;
		}
	});
</script>