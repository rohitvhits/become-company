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
             width: 2000px; 
         }
</style>
<div class="table table-responsive">
<table id="order-listing1" class="table table-bordered">
		                      <thead>
		                        <tr>
											<!--<th></th>-->
											<th>Record</th>
											<?php if(in_array($user->user_type_fk,array(3,4))){?>
											<th>Agency Name</th>
											<?php }?>
											<th>Name</th>
											
											<th>Phone</th>
											<th>CIN No</th>
											<th>EMC User</th>
											<th>Medicaid Issue</th>
											
											<th>Filed Date</th>
											
											
											<th>Status</th>
											<th>Total Days</th>
											<th>30 Days Date</th>
											<th>45 Days Date</th>
											
										
											
											
										</tr>
										<form method="get" action="" >
									<!--<td><input type="checkbox" name="" id="main_checkBox1"><br>
										<span class="main_checkBox1_error" style="color:red"></span>
									</td>-->
		                      		<td><input type="button" name="search" class="btn btn-primary btn-fw pull-right btn-sm"  value="search" onclick="ajax3045Record(1)"></td>
		                      		<?php if(in_array($user->user_type_fk,array(3,4))){?>
									
		                      		<td>
		                      				
		                      			<select class="form-control" name="agency_fk1" id="agency_fk_thirty" onchange="getUserList(this.value)">
		                      			<option value="">Select agency</option>
		                      			<?php foreach ($agencyList as $rwAgency) { ?>
									        <option value="<?php echo $rwAgency->id ?>" <?php echo (($agency_fk)==$rwAgency->id)? 'selected' : ''; ?> ><?php echo $rwAgency->agency_name; ?></option>
									        <?php } ?>
		                      			</select>
		                      		</td>
		                      		<?php } ?>
											
		                      		<td><input class="form-control" type="text" name="name" id="name_thirty" value="<?php echo $name ?>"></td>
		                      		<td><input class="form-control" type="text" name="phone" id="phone_thirty" value="<?php echo  $phone ?>"></td>
									  <td><input class="form-control" type="text" name="cin" id="cin" value="<?php echo  $cin ?>"></td>
									<td><?php if(in_array($user->user_type_fk,array(3,4))){?>
										<select name="emcuser" class="form-control" id="emcuser__thirty">
											<option value="">Select Emc User</option>
											<?php if(!empty($userList)){
												foreach($userList as $ke){?>
												<option value="<?php echo $ke->id;?>" <?php if($emcuser ==$ke->id) { echo "selected='selected'";}?>><?php echo $ke->first_name.' '.$ke->last_name;?></option>
												<?php } }?>
										</select>
										<?php } ?>
									</td>
									<td>
		                      		
		                      			 <select class="form-control"  name="medicaid_issue" id="medicaid_issue_thirty" >
					                         <option value="">Medicaid Issue  </option>
					                          <?php
					                          foreach ($masterData as $rwStatusd) {
					                             if (in_array($rwStatusd->master_type_fk, array("4"))) {?> 
					                              <option value="<?= $rwStatusd->id ?>"<?= ($medicaid_issue ==$rwStatusd->id)?"selected":'' ?>><?= $rwStatusd->name ?> </option>
					                          <?php } }?>
		                      			</select>
		                      		</td> 
									
									<th>
										<input type="text" id="filed_date_thirty" name="filed_date" class="form-control datepickernn" value="<?php if(isset($filed_date) && $filed_date !='') { echo $filed_date;} ?>">
									</th>
		                    		
									<td>
		                      		
		                      			
		                      		</td>
									  <td></td>
									  <td></td>
									  <td></td>
									
									
									</form>
								</tr>
		                      	
								</thead>
								<tbody>
                                   
									<?php 
									
										if(count($query) > 0) {
											$i = 1 +(($query->currentPage()-1) * $query->perPage());
										
											foreach ($query as $row) { ?>
												<tr>
												<!--<td><input type="checkbox" class="cbox_id" value="<?php echo $row->id;?>" id="cbox_id<?php echo $row->id;?>"></td>-->
													<td><a href="<?php echo URL::asset("/"); ?>record/<?= $row->id ?>"><?= $row->id ?> </a></td>
													<?php if(in_array($user->user_type_fk,array(3,4))){ ?>
									
													<td>
													 	<span  id="changeAgencyList<?= $row->id?>">
													 		<?php /* onclick="changeAgency(<?= $row->id?>)" */?>
															<span  id="<?php echo $row->id;?>"><?= $row->agency_name ?></span>
															
														</span>
													 
														</td>
														<?php } ?>
													
													<td >
                                                    <?php if(in_array($user->user_type_fk,array(3,4))){ ?>

														<span contenteditable="true" onBlur="saveToDatabase(this,'first_name','<?php echo $row->id; ?>')" onClick="editRow(this);"  >
														<?= ($row->first_name!='')?$row->first_name:'  '?>
															
														</span> 
														<span contenteditable="true" onBlur="saveToDatabase(this,'middle_name','<?php echo $row->id; ?>')" onClick="editRow(this);" ><?= ($row->middle_name!='')?$row->middle_name:'  '; ?> </span> 
														<span contenteditable="true" onBlur="saveToDatabase(this,'last_name','<?php echo $row->id; ?>')" onClick="editRow(this);"><?= ($row->last_name!='')?$row->last_name:' '; ?></span>
                                                    <?php } ?>


													<?php if(!in_array($user->user_type_fk,array(3,4))){
                                                            echo $row->first_name.' '.$row->last_name;
                                                    }
                                                    ?>

                                                       
															



													</td> 
													<td ><?= $row->phone ?>
														
													</td>
													<td ><?= $row->cin ?>
														
													</td>
													<td><?php if(isset($userArray[$row->emc_rep]) && $userArray[$row->emc_rep] !=''){ echo $userArray[$row->emc_rep];} ?>
														
													</td>
													<td>
													<?php if(isset($masterDataArray[$row->medicaid_issue])) { echo $masterDataArray[$row->medicaid_issue]; }else{ echo '-';} ?>
													</td>
													
													<td><?php if($row->common_date !=''){ echo date('m/d/Y h:i A',strtotime($row->common_date)); }?></td>
													
													<td>
														<span ><span ><?php if(isset($masterDataArray[$row->patient_status])) { echo $masterDataArray[$row->patient_status]; }else{ echo '-';} ?></span></span>
														</td>
													<td> <?php echo  $row->total_days;?></td>
													<td> <?php if($row->field_start_date !='') { echo  date('m/d/Y',strtotime($row->field_start_date)); }?></td>
													<td> <?php if($row->field_end_date !='') { echo  date('m/d/Y',strtotime($row->field_end_date)); } ?></td>
													
													
												</tr>
										<?php } }else { ?>
												<tr><td colspan="12"><center><b>Data not found</b></center></td></tr>
										<?php }?>
									</tbody>
		                    </table>
						</div>
						<div class="pull-right pegination-margin">
                            <?php 
                                echo $query->appends(request()->input())->links("pagination::bootstrap-4");
                            ?>
						
						</div>  
					<script>
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
								
							});	

							
						</script>