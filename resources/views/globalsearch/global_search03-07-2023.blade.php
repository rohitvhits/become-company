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
 <div class="main-panel">

        <div class="content-wrapper">
          <?php 
			if(count($globalSearchList) > 0) {?>
		  <div class="card">
          	  <div class="row list-name m-3">
				   <div class="col-sm-6" > <h4 class="card-title">Search By <?php echo $search;?></h4></div>
				   
			</div>
    
			<div class="card-body">
              <div class="row">
                <div class="col-12">
                  <div class="table-responsive">
                  		<table class="table table-bordered">
							<thead>
								<th>#</th>
											<th>Record #</th>
											<th>Agency Name</th>
											<th>Name</th>
											<th>Email</th>
											<th>Phone</th>
											<th>Follow Date</th>
											<th>File Date</th>
											<th>Status</th>
											<th>Invoice Id</th>
							</thead>
								<tbody>
									<?php 
										if(count($globalSearchList) > 0) {
											$i = 1 +(($globalSearchList->currentPage()-1) * $globalSearchList->perPage());
										
											foreach ($globalSearchList as $row) {  ?>
												<tr>
													<th scope="row"><?= $i++ ?></th>
													<td><a href="<?php echo URL::asset("/"); ?>record/<?= $row->id ?>"> # <?= $row->id ?> </a></td>
													
													
													<td ><a href="<?php echo URL::to('/');?>/agency-view/<?php echo $row->agencyId;?>"><?= ($row->agency_name!='')?$row->agency_name:'  '?></a></td> 
													<td><a href="<?php echo URL::to('/');?>/record/<?php echo $row->id;?>"><?= $row->first_name.' '.$row->middle_name.' '. $row->last_name ?></a></td>
													<td ><a href="<?php echo URL::to('/');?>/record/<?php echo $row->id;?>"><?= $row->email ?></a></td>
													<td ><a href="<?php echo URL::to('/');?>/record/<?php echo $row->id;?>"><?= $row->phone ?></a></td>
													<td>
															<?php if(isset($row->follow_date) && $row->follow_date !=''){ echo date('m/d/Y',strtotime($row->follow_date)); }?> 
													</td>
													<td ><?php if(isset($row->file_date) && $row->file_date !=''){ echo date('m/d/Y',strtotime($row->file_date)); }?></td>
													<td><?php if(isset($masterDataArray[$row->patient_status])) { echo $masterDataArray[$row->patient_status]; }else{ echo '-';} ?></td>
													<td><?php echo $row->invoice_id;?></td>
												</tr>
										<?php } }else { ?>
												<tr><td colspan="12"><center><b>Data not found</b></center></td></tr>
										<?php }?>
									</tbody>
						</table>
              		     <div class="pull-right pegination-margin">
						 <?php if(count($globalSearchList) >0){?>
						{{$globalSearchList->appends(request()->input())->links()}}
						 <?php } ?>
						</div>
                  </div>
                </div>
              </div>
           </div>
            </div>
			<?php } ?>
			
			<?php 
			if(count($invoiceDetails) > 0) {?>
		  <div class="card">
          	  <div class="row list-name m-3">
				   <div class="col-sm-6" > <h4 class="card-title">Search By <?php echo $search;?></h4></div>
				   
			</div>
    
			<div class="card-body">
              <div class="row">
                <div class="col-12">
                  <div class="table-responsive">
                  		<table class="table table-bordered">
							<thead>

											<th>Record #</th>
											<th>Invoice Number</th>
											<th>Invoice Date</th>
											<th>Agency Name</th>
											<th>Total Amount</th>
											<th>Due Amount</th>
											<th>Status</th>
											
							</thead>
								<tbody>
									<?php 
										if(count($invoiceDetails) > 0) {
											$i = 1 +(($invoiceDetails->currentPage()-1) * $invoiceDetails->perPage());
										
											foreach ($invoiceDetails as $row) {  ?>
												<tr>
													<td><a href="<?php echo URL::asset("/"); ?>record/<?= $row->id ?>"> # <?= $row->id ?> </a></td>
													
													
													<td ><a href="<?php echo $row->ninja_invitation_link;?>"><?= $row->invoice_number?></a></td> 
													<td><?php echo date('m/d/Y',strtotime($row->invoice_date));?></a></td>
													<td ><?php if(isset($agencyArray[$row->agency_fk]) && $agencyArray[$row->agency_fk] !=''){ echo $agencyArray[$row->agency_fk];}?></td>
													<td ><?= '$'. $row->total_amount ?></a></td>
													<td ><?= '$'.$row->due_amount ?></a></td>
													<td ><?php 
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
													
												</tr>
										<?php } }else { ?>
												<tr><td colspan="12"><center><b>Data not found</b></center></td></tr>
										<?php }?>
									</tbody>
						</table>
              		     <div class="pull-right pegination-margin">
						 <?php if(count($invoiceDetails) >0){?>
						{{$invoiceDetails->appends(request()->input())->links()}}
						 <?php } ?>
						</div>
                  </div>
                </div>
              </div>
           </div>
            </div>
			<?php } ?>
			<?php 
			if(count($hospitalDetails) > 0) {?>
		  <div class="card">
          	  <div class="row list-name m-3">
				   <div class="col-sm-6" > <h4 class="card-title">Search By <?php echo $search;?></h4></div>
				   
			</div>
    
			<div class="card-body">
              <div class="row">
                <div class="col-12">
                  <div class="table-responsive">
                  		<table class="table table-bordered">
							<thead>

										<th>#</th>
						   <?php if(in_array($user->user_type_fk,array(3,100))){ ?>
						  <th>Agency Name</th>
						   <?php }?>
						  <th>Full Name</th>
						
						  <th>Phone</th>
						  <th>Date of Birth</th>
						  <th>Gender</th>
						  <th>Apoointment Date</th>
						  <th>Apoointment Time</th>
						  <th>Status</th>
											
							</thead>
								<tbody>
									<?php 
										if(count($hospitalDetails) > 0) {
											$i = 1 +(($hospitalDetails->currentPage()-1) * $hospitalDetails->perPage());
										
											foreach ($hospitalDetails as $rows) {  ?>
												<tr>
													 <td><a href="<?php echo URL::to('/');?>/patient/view/<?php echo $rows->id;?>"><?= '#'.' '.$rows->id ?></a></td>
                   <?php if(in_array($user->user_type_fk,array(3,100))){ ?>
					<td><?= $rows->agency_name ?></td>
				   <?php } ?>
                    <td><?php echo $rows->first_name.' '.$rows->middle_name.' '.$rows->last_name;?></td>
                  
                    <td><?php echo $rows->phone;?></td>
					<td><?php if($rows->dob !='0000-00-00'){ echo Common::convertMDY($rows->dob); }?></td>
                    <td><?php echo $rows->gender;?></td>
					<td><?php if($rows->appointment_date !=''){ echo Common::convertMDY($rows->appointment_date);} ?></td>
					<td><?php if($rows->appointment_date !=''){ echo date('h:i A',strtotime($rows->appointment_date));} ?></td>
                    <td>
					<?php

										if($rows->status =='Pending'){
											?>
											<label class='badge badge-warning badge-pill'>Pending</label>
										
										<?php }?>
										 <?php

										if($rows->status =='scheduled'){
											?>
											<label class='badge badge-info badge-pill'>Scheduled</label>
										
										<?php }?>
										 <?php

										if($rows->status =='completed'){
											?>
											<label class='badge badge-success badge-pill'>Completed</label>
										
										<?php }?>
										<?php

										if($rows->status =='cancelled'){
											?>
											<label class='badge badge-danger badge-pill'>Cancelled</label>
										
										<?php }?>
					
					</td>
												</tr>
										<?php } }else { ?>
												<tr><td colspan="12"><center><b>Data not found</b></center></td></tr>
										<?php }?>
									</tbody>
						</table>
              		     <div class="pull-right pegination-margin">
						 <?php if(count($hospitalDetails) >0){?>
						{{$hospitalDetails->appends(request()->input())->links()}}
						 <?php } ?>
						</div>
                  </div>
                </div>
              </div>
           </div>
            </div>
			<?php } ?>
          </div>
		  

@include('include/footer')