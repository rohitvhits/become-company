
<link href="{{ asset('css/custom.css')}}" rel="stylesheet" >
   <style>
       .recordtabletdwidth th:nth-child(9), .recordtabletdwidth td:nth-child(9){
        min-width: 115px;
    max-width: 115px;
    width: 115px;
       }
       .recordtabletdwidth th:nth-child(10), .recordtabletdwidth td:nth-child(10){
        min-width: 115px;
    max-width: 115px;
    width: 115px;
       }
       .recordtabletdwidth th:nth-child(11), .recordtabletdwidth td:nth-child(11){
        min-width: 120px;
    max-width: 120px;
    width: 120px;
       }
    </style>
<div class="table-responsive">

				  <table id="order-listing1" class="table table-bordered table-head-fix recordtabletdwidth">
                      <thead>
                        <tr>
                            <th style="white-space:nowrap">
                                <div class="sorting-div"><span>Record #</span>
                                    <div class="sorting-btn">
                                        <button type="button" class="record_id" data-field="id" data-sort="asc"><i
                                                class="fa fa-sort-up"></i> </button><button type="button" class="record_id"
                                            data-field="id" data-sort="desc"><i class="fa fa-sort-down"></i> </button>
                                    </div>
                                </div>
                            </th>
                            @if(in_array($user->user_type_fk,array(3,4)))
                            <th style="white-space:nowrap">
                                <div class="sorting-div"><span>Agency Name</span>
                                    <div class="sorting-btn">
                                        <button type="button" class="record_id" data-field="agency_name" data-sort="asc"><i
                                                class="fa fa-sort-up"></i> </button><button type="button" class="record_id"
                                            data-field="agency_name" data-sort="desc"><i class="fa fa-sort-down"></i> </button>
                                    </div>
                                </div>
                            </th>
                            @endif
                            <th style="white-space:nowrap">
                                <div class="sorting-div"><span>Name</span>
                                    <div class="sorting-btn">
                                        <button type="button" class="record_id" data-field="name" data-sort="asc"><i
                                                class="fa fa-sort-up"></i> </button><button type="button" class="record_id"
                                            data-field="name" data-sort="desc"><i class="fa fa-sort-down"></i> </button>
                                    </div>
                                </div>
                            </th>
                           
                            <th style="white-space:nowrap">
                                <div class="sorting-div"><span>Email</span>
                                    <div class="sorting-btn">
                                        <button type="button" class="record_id" data-field="email" data-sort="asc"><i
                                                class="fa fa-sort-up"></i> </button><button type="button" class="record_id"
                                            data-field="email" data-sort="desc"><i class="fa fa-sort-down"></i> </button>
                                    </div>
                                </div>
                            </th>
                            <th style="white-space:nowrap">
                                <div class="sorting-div"><span>CIN No</span>
                                    <div class="sorting-btn">
                                        <button type="button" class="record_id" data-field="cin" data-sort="asc"><i
                                                class="fa fa-sort-up"></i> </button><button type="button" class="record_id"
                                            data-field="cin" data-sort="desc"><i class="fa fa-sort-down"></i> </button>
                                    </div>
                                </div>
                            </th>
                            <th style="white-space:nowrap">
                                <div class="sorting-div"><span>EMC User</span>
                                    <div class="sorting-btn">
                                        <button type="button" class="record_id" data-field="emc_rep" data-sort="asc"><i
                                                class="fa fa-sort-up"></i> </button><button type="button" class="record_id"
                                            data-field="emc_rep" data-sort="desc"><i class="fa fa-sort-down"></i> </button>
                                    </div>
                                </div>
                            </th>
                            <th style="white-space:nowrap">
                                <div class="sorting-div"><span>Medicaid Issue</span>
                                    <div class="sorting-btn">
                                        <button type="button" class="record_id" data-field="medicaid" data-sort="asc"><i
                                                class="fa fa-sort-up"></i> </button><button type="button" class="record_id"
                                            data-field="medicaid" data-sort="desc"><i class="fa fa-sort-down"></i> </button>
                                    </div>
                                </div>
                            </th>
                            <th style="white-space:nowrap;">
                                <div class="sorting-div"><span>Record Form</span>
                                    <div class="sorting-btn">
                                        <button type="button" class="record_id" data-field="ny_medicare_id" data-sort="asc"><i
                                                class="fa fa-sort-up"></i> </button><button type="button" class="record_id"
                                            data-field="ny_medicare_id" data-sort="desc"><i class="fa fa-sort-down"></i> </button>
                                    </div>
                                </div>
                            </th>
                            <th style="white-space:nowrap">
                                <div class="sorting-div"><span>3 Month Date</span>
                                    <div class="sorting-btn">
                                        <button type="button" class="record_id" data-field="month" data-sort="asc"><i
                                                class="fa fa-sort-up"></i> </button><button type="button" class="record_id"
                                            data-field="month" data-sort="desc"><i class="fa fa-sort-down"></i> </button>
                                    </div>
                                </div>
                            </th>
                            <th style="white-space:nowrap">
                                <div class="sorting-div"><span>5 Month Date</span>
                                    <div class="sorting-btn">
                                        <button type="button" class="record_id" data-field="five_month" data-sort="asc"><i
                                                class="fa fa-sort-up"></i> </button><button type="button" class="record_id"
                                            data-field="five_month" data-sort="desc"><i class="fa fa-sort-down"></i> </button>
                                    </div>
                                </div>
                            </th>
                            <th style="white-space:nowrap">
                                <div class="sorting-div"><span>Trust Approved</span>
                                    <div class="sorting-btn">
                                        <button type="button" class="record_id" data-field="trust_approved" data-sort="asc"><i
                                                class="fa fa-sort-up"></i> </button><button type="button" class="record_id"
                                            data-field="trust_approved" data-sort="desc"><i class="fa fa-sort-down"></i> </button>
                                    </div>
                                </div>
                            </th>
                            <th style="white-space:nowrap">
                                <div class="sorting-div"><span>Disability</span>
                                    <div class="sorting-btn">
                                        <button type="button" class="record_id" data-field="disability" data-sort="asc"><i
                                                class="fa fa-sort-up"></i> </button><button type="button" class="record_id"
                                            data-field="disability" data-sort="desc"><i class="fa fa-sort-down"></i> </button>
                                    </div>
                                </div>
                            </th>
                            
                            <th style="white-space:nowrap">
                                <div class="sorting-div"><span>Status</span>
                                    <div class="sorting-btn">
                                        <button type="button" class="record_id" data-field="patient_status" data-sort="asc"><i
                                                class="fa fa-sort-up"></i> </button><button type="button" class="record_id"
                                            data-field="patient_status" data-sort="desc"><i class="fa fa-sort-down"></i> </button>
                                    </div>
                                </div>
                            </th>
                            <th style="white-space:nowrap">
                                <div class="sorting-div"><span>Created Date</span>
                                    <div class="sorting-btn">
                                        <button type="button" class="record_id" data-field="patiencreated_att_status" data-sort="asc"><i
                                                class="fa fa-sort-up"></i> </button><button type="button" class="record_id"
                                            data-field="created_at" data-sort="desc"><i class="fa fa-sort-down"></i> </button>
                                    </div>
                                </div>
                            </th>
                            <th style="white-space:nowrap">
                                <div class="sorting-div"><span>Created By</span>
                                    <div class="sorting-btn">
                                        <button type="button" class="record_id" data-field="created_by" data-sort="asc"><i
                                                class="fa fa-sort-up"></i> </button><button type="button" class="record_id"
                                            data-field="created_by" data-sort="desc"><i class="fa fa-sort-down"></i> </button>
                                    </div>
                                </div>
                            </th>
                        </tr>
                            <form method="get" action="" >
                                <input type="hidden" id="fields" value="id">
                                <input type="hidden" id="sorting" value="desc">
                                <tr>
									<!--<td><input type="checkbox" name="" id="main_checkBox1"><br>
										<span class="main_checkBox1_error" style="color:red"></span>
									</td>-->
		                      		<td><input type="button"  id="search_id_new" name="search" class="btn btn-primary btn-fw pull-right btn-sm"  value="search"></td>
		                      		@if(in_array($user->user_type_fk,array(3,4)))
									
		                      		<td>
		                      				
		                      			<select class="form-control" name="agency_fk1" id="agency_fk" onchange="getUserList(this.value)">
		                      			<option value="">Select agency</option>
		                      			<?php foreach ($agencyList as $rwAgency) { ?>
									        <option value="<?php echo $rwAgency->id ?>" <?php echo (($agency_fk)==$rwAgency->id)? 'selected' : ''; ?> ><?php echo $rwAgency->agency_name; ?></option>
									        <?php } ?>
		                      			</select>
		                      		</td>
		                      		@endif
											
		                      		<td><input class="form-control" type="text" name="name" id="name" value="<?php echo $name ?>"></td>
		                      		<td><input class="form-control" type="text" name="email" id="email" value="<?php echo $email ?>"></td>
		                    		<td><input class="form-control" type="text" name="phone" id="phone" value="<?php echo  $phone ?>"></td>
									<td>@if(in_array($user->user_type_fk,array(3,4)))
										<select name="emcuser" class="form-control" id="emc_user_id">
											<option value="">Select Emc User</option>
											<?php if(!empty($userList)){
												foreach($userList as $ke){?>
												<option value="<?php echo $ke->id;?>" <?php if($emcuser ==$ke->id) { echo "selected='selected'";}?>><?php echo $ke->first_name.' '.$ke->last_name;?></option>
												<?php } }?>
										</select>
										@endif
									</td>
									<td>
		                      		
		                      			 <select class="form-control"  name="medicaid_issue" id="medicaid_issue" >
					                         <option value="">Medicaid Issue  </option>
					                          <?php
					                          foreach ($masterData as $rwStatusd) {
					                             if (in_array($rwStatusd->master_type_fk, array("4"))) {?> 
					                              <option value="<?= $rwStatusd->id ?>"<?= ($medicaid_issue ==$rwStatusd->id)?"selected":'' ?>><?= $rwStatusd->name ?> </option>
					                          <?php } }?>
		                      			</select>
		                      		</td> 
									<td>
										<select name="record_form" class="form-control" id="record_form">
									   <option value="">Select</option>
										<option value="1" <?php if (isset($record_form) && $record_form == 1) { echo "selected='selected'";} ?>>Ny Best Medical Care</option>
										<option value="0" <?php if (isset($record_form) && $record_form == 0) { echo "selected='selected'";} ?>>NY Best Medicalss</option>
									
									 </select>
								   </td>
									<td>
										<input type="text" name="month" class="form-control datepickernn" id="datepickernn" value="@if(isset($month) && $month !='') {{$month}} @endif">
									</td>
		                    		<td><input autocomplete="off" type="text" name="five_month" id="five_month" class="form-control datepicker1" value="<?php if($five_month !='') { echo $five_month;} ?>"></td>
									<td>
										
									</td>
									<td>
									<select class="form-control"  name="disability" id="disability" >
					                        <option value="">Select Disability </option>
											<option value="Yes" @if($disability =="Yes")  selected @endif>Yes</option>
											<option value="No" @if($disability !='' && $disability =="No")  selected @endif>No</option>
					                         
		                      			</select>
									</td>
									<td>
		                      		
		                      			 <select class="form-control"  name="patient_status" id="patient_status" >
					                         <option value="">Select Status  </option>
					                          <?php
					                          foreach ($masterData as $rwStatus) {
					                             if (in_array($rwStatus->master_type_fk, array("3"))) {?> 
					                              <option value="<?= $rwStatus->id ?>"<?= ($patient_status ==$rwStatus->id)?"selected":'' ?>><?= $rwStatus->name ?> </option>
					                          <?php } }?>
		                      			</select>
		                      		</td>
									<td><input autocomplete="off" type="text" name="created_date" class="form-control datepicker_date_new" value=""></td>
		                      		<td> 
									  
									  </td>
                                      </tr>
								</form>
								
									
						
                      </thead>
                      <tbody>
				
                  
                      	
			           
                      <?php 
									
                                    if(count($query) > 0) {
                                        $i = 1 +(($query->currentPage()-1) * $query->perPage());
                                    
                                        foreach ($query as $row) { ?>
                                            <tr>
                                            <!--<td><input type="checkbox" class="cbox_id" value="<?php echo $row->id;?>" id="cbox_id<?php echo $row->id;?>"></td>-->
                                                <td><a href="<?php echo URL::asset("/"); ?>record/<?= $row->id ?>"><?= $row->id ?> </a></td>
                                                @if(in_array($user->user_type_fk,array(3,4)))
                                
                                                <td>
                                                     <span  id="changeAgencyList<?= $row->id?>">
                                                         <?php /* onclick="changeAgency(<?= $row->id?>)" */?>
                                                        <span  id="<?php echo $row->id;?>"><?= $row->agency_name ?></span>
                                                        <?php  /*
                                                        <span id="dropid<?= $row->id?>" style="display:none">
                                                            <select onchange="saveToDatabase(this.value,'agency_fk',<?php echo $row->id;?>)" class="form-control" name="agency_fk" id="agency_fk">
                                                                <option value="">Select agency</option>
                                                                <?php foreach ($agencyList as $rwAgency) { ?>
                                                                    <option value="<?php echo $rwAgency->id ?>" <?php echo (($row->agency_fk)==$rwAgency->id)? 'selected' : ''; ?> ><?php echo $rwAgency->agency_name; ?></option>
                                                                    <?php } ?>
                                                            </select>
                                                        </span> */
                                                        ?>
                                                    </span>
                                                 
                                                    </td>
                                                    @endif
                                                
                                                <td >
                                                    @if(in_array($user->user_type_fk,array(3,4)))

                                                    <span>
                                                    <?= ($row->first_name!='')?$row->first_name:'  '?>
                                                        
                                                    </span> 
                                                    <span ><?= ($row->middle_name!='')?$row->middle_name:'  '; ?> </span> 
                                                    <span ><?= ($row->last_name!='')?$row->last_name:' '; ?></span>
                                                    @endif

                                                    @if(!in_array($user->user_type_fk,array(3,4)))
                                                        {{$row->first_name}} {{$row->middle_name}} {{$row->last_name}}


                                                    @endif

                                                </td> 


                                        <!-- 	<td><?= $row->first_name.' '.$row->middle_name.' '. $row->last_name ?></td> -->
                                                <td >
                                                    <?= $row->email ?>	</td>
                                                <td ><?= $row->cin ?>
                                                    
                                                </td>
                                                <td><?php if(isset($userArray[$row->emc_rep]) && $userArray[$row->emc_rep] !=''){ echo $userArray[$row->emc_rep];} ?>
                                                    
                                                </td>
                                                <td>
                                                <?php if(isset($masterDataArray[$row->medicaid_issue])) { echo $masterDataArray[$row->medicaid_issue]; }else{ echo '-';} ?>
                                                </td>
                                                <td>
                                                   <?php

                                                      if ($row->ny_medicare_id != '') {?>
                                                       <label class='badge badge-primary badge-pill'>Ny Best Medical Care</label>
                                                      
                                                      <?php }else{?>
                                                     <label class='badge badge-info badge-pill'>NY Best Medicalss</label>
                                                      <?php } ?>
                                                        
                                                      
                                                      
                                                   </td>
                                                <td><?php if($row->month !=''){ echo date('m/d/Y',strtotime($row->month)); }?></td>
                                                <td  >
                                                <?php if($row->five_month !=''){ echo date('m/d/Y',strtotime($row->five_month)); } ?>
                                                </td>
                                                <td> @if($row->trust_approved ==1)  Yes @else No @endif</td>
                                                <td> @if($row->disability =="Yes") Yes @else No @endif</td>
                                                <td>
                                                    <span  id="change_patient_status<?= $row->id?>"><service_md_appointment><?php if(isset($masterDataArray[$row->patient_status])) { echo $masterDataArray[$row->patient_status]; }else{ echo '-';} ?></span></span>
                                                    </td>
                                                    <td ><?php if(isset($row->created_at) && $row->created_at !=''){ echo date('m/d/Y',strtotime($row->created_at)); }?></td>
                                                
                                                        <td>{{ $row->username}}</td>
                                                
                                            </tr>
                                    <?php } }else { ?>
                                            <tr><td colspan="12"><center><b>Data not found</b></center></td></tr>
                                    <?php }?>
							</tbody>
                    </table>

                    
                   
                  </div>
                  <div class="pull-right pegination-margin">
                
                    {{$query->links("pagination::bootstrap-4")}}
                    </div>
                    <script>
                        $(function () {

                            var start = moment().subtract(0, 'days');
                            var end = moment();
                        $('input[name="month"]').daterangepicker({
                            startDate: start,
                            endDate: end,
                            autoUpdateInput: false,
                            defaultDate: null,
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

                            $('input[name="month"]').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format('MM/DD/YYYY'));

                        });
                        $('input[name="five_month"]').daterangepicker({
                            startDate: start,
                            endDate: end,
                            autoUpdateInput: false,
                            defaultDate: null,
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

                            $('input[name="five_month"]').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format('MM/DD/YYYY'));

                        });
                        $('input[name="created_date"]').daterangepicker({
                            startDate: start,
                            endDate: end,
                            autoUpdateInput: false,
                            defaultDate: null,
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

                            $('input[name="created_date"]').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format('MM/DD/YYYY'));

                        });
                    });

                        </script>