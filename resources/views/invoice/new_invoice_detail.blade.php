@include('include/header')
@include('include/sidebar')
<?php 
			$total =0;						
	if(count($final)){ 
		foreach($final as $key=>$val){

				
				foreach($val as $new=>$record){ 
						$total = $record->rate_price + $total;
			}
		}
		}
	
?>
											
<div class="main-panel">          
        <div class="content-wrapper">
          <div class="row">
              <div class="col-lg-12">
                  <div class="card px-2">
                      <div class="card-body">
                          <div class="container-fluid">
                            <h3 class="text-right my-5">Invoice&nbsp;&nbsp;#INV-<?php echo $bill_details->invoice_number;?></h3>
                            <hr>
                          </div>
                          <div class="container-fluid d-flex justify-content-between">
                            <div class="col-lg-3 pl-0">
                              <p class="mt-5 mb-2"><b><?php if(isset($agency_details->agency_name) && $agency_details->agency_name !='') { echo $agency_details->agency_name;} ?></b></p>
                            </div>
                          </div>
                          <div class="container-fluid d-flex justify-content-between">
                            <div class="col-lg-3 pl-0">
                              <p class="mb-0 mt-5">Invoice Date :<?php echo date('d M Y',strtotime($bill_details->invoice_date));?></p>
                              <p>Due Date : <?php echo date('d M Y',strtotime($bill_details->invoice_date));?></p>
                            </div>
                          </div>
                          <div class="container-fluid mt-5 d-flex justify-content-center w-100">
                            <div class="table-responsive w-100">
                                <table class="table">
                                  <thead>
                                    <tr class="bg-dark text-white">
                                        <th>#</th>
										<th>Item Name</th>
                                        <th>Description</th>
                                        <th class="text-right">Quantity</th>
                                        <th class="text-right">Unit Price</th>
                                        <th class="text-right">Total</th>
                                      </tr>
                                  </thead>
                                  <tbody>
									<?php 
									
										if(count($final)){ 
											$cnt  = 1;
											foreach($final as $key=>$val){ ?>
										
											<tr class="text-right">
											  <td class="text-left"><?php echo $cnt++;?></td>
											  <td class="text-left"><?php echo $key;?></td>
												<td class="text-left">
												<?php 
												$cnts=0;
												foreach($val as $new=>$record){ 
													$cnts++;
													$newprice = $record->rate_price;
													$price = $newprice * $cnts ;
													 
												?>
											  
												<?php echo ucfirst($record->first_name .' '.$record->last_name."<br>"); ?>
												
											 <?php } ?>
											 </td>
													<td><?php echo $cnts;?></td>
													<td><?php echo $record->rate_price;?></td>
													<td><?php echo '$'. $price;?></td>
											  
											</tr>
									<?php } } ?>
                                  </tbody>
                                </table>
                              </div>
                          </div>
                          <div class="container-fluid mt-5 w-100">

                            <h4 class="text-right mb-5">Total : <?php echo '$'. number_format($total,2);?></h4>
                            <hr>
                          </div>
                          <div class="container-fluid w-100">
                        @if($bill_details->status=="Draft") 
                          <a onclick="return confirm('Are you sure status change?')" href="<?php echo URL::to('/');?>/invoice/mark-sent/<?php echo $bill_details->invoice_number;?>/<?php echo $bill_details->agency_fk;?>" class="btn btn-secondary float-right mt-4"><i class="mdi mdi-telegram mr-1"></i>Mark as sent</a>
                          @endif

                          @if($bill_details->status!="Paid") 
                          <a onclick="return confirm('Are you sure status change?')" href="<?php echo URL::to('/');?>/invoice/mark-paid/<?php echo $bill_details->nin_invoice_id;?>/<?php echo $bill_details->agency_fk;?>" class="btn btn-secondary float-right mt-4"><i class="mdi mdi-telegram mr-1"></i>Mark as Paid</a>
                          @endif
                          </div>
                      </div>
                  </div>
              </div>
          </div>
        </div>
       
@include('include/footer')