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
</style>
 <div class="main-panel">

        <div class="content-wrapper">
          <div class="card">
          	  <div class="row list-name">
				   <div class="col-sm-6" > <h4 class="card-title">Patient Details ({{ $query->total() }})</h4></div>
				   
			</div>
    
            <div class="card-body compact-view">
              <div class="row">
                <div class="col-12">
                  <div class="table-responsive">
                  		
              		    
		                    <table id="order-listing1" class="table table-bordered">
		                      <thead>
		                        <tr>
											<th>#Record</th>
											<th>Agency Name</th>
										
											<th>Name</th>
											<th>Email</th>
											<th>Phone</th>
											<th>EMC User</th>
											<th>CIN</th>
											
											<th>File Date</th>
											<th>Status</th>
										
										</tr>
		                      	
								</thead>
								<tbody>
									<?php 
										if(count($query) > 0) {
											$i = 1 +(($query->currentPage()-1) * $query->perPage());
										
											foreach ($query as $row) {
												?>
												<tr>
													<td><a href="<?php echo URL::asset("/"); ?>record/<?= $row->id ?>"><?= $row->id ?> </a></td>
													<td><?php if(isset($agencysArray[$row->agency_fk])) { echo $agencysArray[$row->agency_fk]; }else{ echo '-';} ?></td>
													
													<td >
														{{$row->first_name}} {{$row->middle_name}} {{$row->last_name}}

													</td> 
													<td><?= $row->email ?></td>
													<td><?= $row->phone ?></td>

													<td><?php if(isset($userArray[$row->emc_rep]) && $userArray[$row->emc_rep] !=''){ echo $userArray[$row->emc_rep];} ?>
														
													</td>
													<td><?php echo $row->cin;?></td>
													
													<td ><?php if(isset($row->file_date) && $row->file_date !=''){ echo date('m/d/Y',strtotime($row->file_date)); }?></td>
													<td>
														<span  id="change_patient_status<?= $row->id?>"><span onclick="changeStatus(<?= $row->id?>)"><?php if(isset($masterDataArray[$row->patient_status])) { echo $masterDataArray[$row->patient_status]; }else{ echo '-';} ?></span></span>
														</td>
														
													
												</tr>
										<?php } }else { ?>
												<tr><td colspan="12"><center><b>Data not found</b></center></td></tr>
										<?php }?>
									</tbody>
		                    </table>
                  		
         <div class="pull-right pegination-margin">
						{{$query->appends(request()->input())->links("pagination::bootstrap-4")}}
						</div>             
                  </div>
                </div>
              </div>
           </div>
            </div>
          </div>
		  

		  
@include('include/footer')
