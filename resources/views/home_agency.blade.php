@include('include/header')
@include('include/sidebar')
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>

<script type="text/javascript" src="<?php echo URL::to('/');?>/js/moment.min.js"></script>
<script type="text/javascript" src="<?php echo URL::to('/');?>/js/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo URL::to('/');?>/css/daterangepicker.css" />
<div class="main-panel">
        <div class="content-wrapper">
        	<div class="row grid-margin-top">  
          <div class="col-8 ">  
                                    
			<div class="card" style="height:476px;overflow-y:auto;">
		
            <div class="card-body" >
            		<div class="row list-name">
					<div class="col-md-3 card-title">  <h4 class="card-title">Follow Up</h4></div>
					<div class="col-md-9">
						<form action="<?php echo URL::to('/');?>/home" method="get">
							<div class="row">
							<div class="col-9">
							<!-- <input type="text" name="date" id="reportrange" class="form-control"> -->
								<select  name="agent_name" class="form-control">
								<option value="">Select EMC Rep</option>
								
								<?php if(!empty($agent_list)){ foreach($agent_list as $val){ ?>
									<option value="<?php echo $val->id;?>" <?php if($agent_name == $val->id){ echo "selected='selected'";}?>><?php echo $val->first_name.' '.$val->last_name;?></option>
									<?php } } ?>
							</select>
						</div>
						<div class="col-3">
							<input type="submit" class="btn btn-primary btn-sm btn-rounded btn-fw  pull-right">
					</div>
					</div>
						</form>
					</div>
					
				</div>
              <div class="row">
                <div class="col-12">
                  <div class="table-responsive">
                  
                    <table id="" class="table">
						<thead>
							<tr>
							  <th>Record#</th>
							  <th>Record Name</th>
							  <th>Contact Number</th>
							  <th>Agency Name</th>
							  <th>EMC Rep</th>
							  <th>Follow Up </th>
							  
							<!--  <th>Action</th> -->
							</tr>
						</thead>
						<tbody>
							<?php if(count($record_list) >0){
							$cnt = 1;
								foreach($record_list as $kkys){   ?>

									<tr>
										<td><a href="<?php echo URL::to('/');?>/record/<?php echo $kkys->id;?>">Record #<?php echo $kkys->id;?></a></td>
										<td><?php echo ucwords($kkys->first_name.' '.$kkys->last_name);?></td>
										<td><?php echo $kkys->phone;?></td>
										<td><?php echo $kkys->agency_name;?></td>
										<td><a href="<?php echo URL::to('/');?>/user-view/<?php echo $kkys->userid;?>"><?php echo ucwords($kkys->uname.' '.$kkys->lname);?></a></td>
										<td><?php echo $kkys->follow_date ?></td>
									
									</tr>
							<?php } } if(count($record_list) ==0) { ?>
								<tr><td colspan="6">No record available</td></tr>
							<?php } ?>
                        </tbody>
                    </table>
                    <div class="pull-right pegination-margin">
            {{$record_list->links("pagination::bootstrap-4")}}
					</div>
                    
                  </div>
                </div>
              </div>
            </div>
		  	

			</div> 	
		  
        </div>
        <div class="col-md-4 grid-margin stretch-card">
							<div class="card" style="height:476px;overflow-y:auto;">
								<div class="card-body">
									<h4 class="card-title">Text SMS</h4>

										<?php if(count($message_list) >0){
							$cnt = 1;
								foreach($message_list as $ks){ ?>
									<div class="d-flex align-items-center py-2 border-bottom">
										
										<div class="ml-1">
											<h6 class="mb-1"><a href="<?php echo URL::to('/');?>/record/<?php echo $ks->record_id;?>">Record #<?php echo $ks->record_id;?> <?php echo $ks->first_name.' '.$ks->last_name;?></a></h6>
											<p><?php echo $ks->message;?></p>
											<p class="text-muted mb-0 tx-12"><i class="mdi mdi-map-marker mr-1"></i><?php echo $ks->agency_name;?></p>
										</div>
										<i class="mdi mdi-check-circle-outline font-weight-bold ml-auto px-1 py-1 text-info mdi-24px"></i>
									</div>
								<? } } ?>
								</div>
							</div>
						</div>
		
			
		  </div> 
			
</div>
<script>
$('#reportrange').daterangepicker({
    ranges: {
        'Today': [moment(), moment()],
        'Tomorrow': [moment().add(1, 'days'), moment().add(1, 'days')],
        'Next 7 Days': [ moment(),moment().add(6, 'days')],
        'Next 30 Days': [moment(),moment().add(29, 'days') ],
        'This Month': [moment().add('month'), moment().endOf('month')],
        'Next Month': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month').endOf('month')]
    }});
</script>
@include('include/footer')