
 @include('include/header')
@include('include/sidebar')

<style type="text/css">
	#order-listing_length,#order-listing_paginate,#order-listing_info{
		display: none;
	}
	#order-listing_filter{
		text-align: right;
	}
</style>
 <link rel="stylesheet" href="<?php echo URL::to('/');?>/assets/vendors/select2/select2.min.css">
  <link rel="stylesheet" href="<?php echo URL::to('/');?>/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
<?php 
	$medieIssueArray = array();
	$StatusArray = array();
	if(!empty($medicarItem)){
		foreach($medicarItem as $val){
			$medieIssueArray[] = $val;
		}
	
	}
	
	if(!empty($status)){
		foreach($status as $vals){
			$StatusArray[] = $vals;
		}
	
	}

?>
 <div class="main-panel">

        <div class="content-wrapper">
          <div class="card">
          	  <div class="row list-name">
				   <div class="col-sm-6"> <h4 class="card-title">Todo Billing Item Summary </h4></div>
				   <div class="col-sm-6 pull-right">
					<a href="<?php echo URL::to('/');?>/report/todo-billing-summary/export-csv?agency=<?php echo $agency_fk;?>&record_id=<?php echo $record_id;?>&patient_name=<?php echo $patient_name;?>&medicarItem=<?php if(!empty($medieIssueArray)){ echo implode(',',$medieIssueArray);}?>&status=<?php if(!empty($StatusArray)){ echo implode(',',$StatusArray);}?>" class="btn btn-success pull-right btn-fw btn-sm" id="test_record"><i class="mdi mdi-file-export"></i>Export</a>
				   	<a href="<?php echo URL::to('/');?>/report/todo-billing-summary" class="btn btn-danger pull-right btn-fw btn-sm"><i class="mdi mdi-reload"></i> Reset</a>
				</div>
			</div>
            <div class="card-body compact-view">
              <div class="row">
                <div class="col-12">
                  <div class="table-responsive">
                  		
              		    
		                    <table id="order-listing1" class="table table-bordered">
								<thead>
									<tr>
										<th>#</th>
											<th>Agency Name</th>
											<th>Record Id</th>
											<th>Record Name</th>
											<th>Medicaid Item</th>
											<th>Status</th>
											<th>Total Billing</th>
											<th>Action</th>
									</tr>
										
									<tr>
										<form method="get" action="" >
												<td></td>
											<td>
		                      				
												<select class="form-control" name="agency" id="agency">
												<option value="">Select agency</option>
												<?php foreach ($agencyList as $rwAgency) { ?>
													<option value="<?php echo $rwAgency->id ?>" <?php echo (($agency_fk)==$rwAgency->id)? 'selected' : ''; ?> ><?php echo $rwAgency->agency_name; ?></option>
													<?php } ?>
												</select>
											</td>
											<td>
												<input type="text" name="record_id" value="<?php echo $record_id;?>" class="form-control" placeHolder="Enter record id">
											
											</td>
											<td>
												<input type="text" name="patient_name" value="<?php echo $patient_name;?>" class="form-control" placeHolder="Enter Record Name">
											
											</td>
											<td> 
												<select name="medicarItem[]" class="js-example-basic-multiple w-100" multiple>
													<option value="">Select medicare item</option>
													<?php
														if(count($masterData) >0){
															foreach($masterData as $agency){
															if (in_array($agency->master_type_fk, array("4"))) {
													?>
													<option value="<?php echo $agency->id;?>" <?php if(in_array($agency->id,$medieIssueArray)){ echo "selected='selected'";}?>><?php echo $agency->name;?></option>
													<?php } }  } ?>
												</select>
											
											</td>
											<td>
											<select name="status[]" class="js-example-basic-multiple w-100" multiple>
													<option value="">Select status</option>
													<?php
														if(count($masterData) >0){
															foreach($masterData as $agency){
															if (in_array($agency->master_type_fk, array("3"))) {
													?>
													<option value="<?php echo $agency->id;?>" <?php if( in_array($agency->id,$StatusArray)){ echo "selected='selected'";}?>><?php echo $agency->name;?></option>
													<?php } }  } ?>
												</select>
											</td>
											<td>
											</td>
											
											<td>
												<input type="submit" name="search" class="btn btn-primary btn-sm  btn-fw  pull-right" value="search">
											</td>





										</form>
									</tr>
								
								</thead>
								<tbody>
									<?php 
										if(count($report_list) > 0) {
											$i = 1 +(($report_list->currentPage()-1) * $report_list->perPage());
											foreach ($report_list as $row) {  
									?>
									<tr>
										<td><?php echo $i++;?></td>
										<td><a href="<?php echo URL::to('/');?>/agency-view/<?php echo $row['agency_fk'];?>"><?php echo ucfirst($row['agencyName']);?></a></td>
										<td><a href="<?php echo URL::to('/');?>/record/<?php echo $row['id'];?>" title="<?php echo ucfirst($row['first_name'].' '.$row['last_name']);?>"><?php echo ucfirst($row->id);?></a></td>
										<td><a href="<?php echo URL::to('/');?>/record/<?php echo $row['id'];?>" title="<?php echo ucfirst($row['first_name'].' '.$row['last_name']);?>"><?php echo ucfirst($row->first_name.' '.$row->last_name);?></a></td>

										<td><?php echo ucfirst($row['MadicareItem']);?></td>
										
										<td><?php echo ucfirst($row['statusName']);?></td>
										<td><?php echo ucfirst($row['totalBilling']);?></td>
										<td></td>
									</tr>
									
									<?php } } if(count($report_list) ==0){  ?>
									<tr>
										<td colspan="13">No record available</td>
									
									</tr>
									<?php } ?>
								</tbody>
		                    </table>
                  		
						<div class="pull-right pegination-margin">
							{{$report_list->appends(request()->input())->links("pagination::bootstrap-4")}}
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
		  
@include('include/footer')
<script src="<?php echo URL::to('/');?>/assets/vendors/select2/select2.min.js"></script>
	<script src="<?php echo URL::to('/');?>/assets/js/select2.js"></script>