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
				   <div class="col-sm-6" > <h4 class="card-title">Create Invoice</h4></div>
				   
 			</div>
    
            <div class="card-body">
              
			  <div class="row">
                <div class="col-12">
                  <div class="table-responsive">
                  <form action="<?php echo URL::to('/');?>/paidInvoice" method="post" enctype="multipart/form-data" id="submitId">
							<input type="hidden" name="_token" value="<?php echo csrf_token();?>">
							
								<input type="hidden" name="paidId" id="paid_amount" value="">
								<input type="hidden" name="agency_fk"  value="<?php echo $agency_fk;?>">

								
									<table class="table table-bordered">
									  <thead>
										<tr class="">
											<th></th>
											<th>#</th>
											
											<th>Item</th>
											<th>Record Name</th>
											<th>Medicaid</th>
										
											<th class="text-right">Amount</th>
											
										  </tr>
									  </thead>
									  <tbody>
										<?php if(!empty($query)){ $cnt =1;
											foreach($query as $val){  ?>
											<tr class="text-right">
												<td class="text-left">
												<input type="checkbox"  name="check_ids[]" onclick="getTotalPrice('<?php echo $val->rate_price;?>',<?php echo $val->subid;?>,<?php echo $val->id;?>)" class="check_all_id<?php echo $val->subid;?>" value="<?php echo $val->subid;?>" data-id="<?php echo $val->rate_price;?>" <?php if($val->payment_status =='paid'){ ?>disabled checked="checked"<?php } ?>>
														<input type="hidden" name="record_id[]" id="r<?php echo $val->id;?><?php echo $val->subid;?>">
												</td>
												<td class="text-left"><?php echo $cnt++;?></td>
												<td class="text-left"><?php echo $val->name;?></td>
												<td class="text-left"><a href="<?php echo URL::to('/');?>/record/<?php echo $val->subid;?>" target="_blank"><?php echo $val->first_name.' '.$val->last_name .'( '.$val->cin .')';?></a></td>
												<td class="text-left"><?php echo $val->cin;?></td>
												
												<td>$<?php echo $val->rate_price;?></td>
											
											</tr>
										<?php } } ?>
									  </tbody>
									</table>
								
						  	</form>		 
							<?php if(count($query) >0){ ?>		
							<div class="pull-right pegination-margin">
						
								<input type="button" name="submit" onclick="getSubmit()" value="Generate Invoice" class="btn btn-primary">
							
							</div>
							<?php }?>
                     
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
	function getTotalPrice(price,id,recordId){
		var check  = $('.check_all_id'+id).prop("checked");
		var paid_amount =  $('#paid_amount').val();
		var dua_price1=0;
		
		if(check ==true){
			if(paid_amount ==''){
				paid_amount= 0;
			}
			paid_amount = parseFloat(paid_amount) + parseFloat(price);
			$('#r'+recordId+id).val(recordId);
		}else{
			paid_amount = parseFloat(paid_amount) - parseFloat(price);
			$('#r'+recordId+id).val('');
		}
		$('#paid_amount').val(paid_amount)
		console.log(pullRecord);
	}
	
	function getSubmit(){
		var AppoveVisitIds = [];
		var cnt =0;
		$.each($("input[name='check_ids[]']:checked"), function() {
			AppoveVisitIds.push($(this).val());
		});
		if(AppoveVisitIds ==''){
			alert("Please select checkbox.");
			return false;
		}
		
		$('#submitId').submit();
	}
</script>
