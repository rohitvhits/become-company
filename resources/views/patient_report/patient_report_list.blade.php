 @include('include/header')
 @include('include/sidebar')
 
  <link rel="stylesheet" href="<?php echo URL::to('/');?>/assets/vendors/select2/select2.min.css">
  <link rel="stylesheet" href="<?php echo URL::to('/');?>/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo URL::to('/');?>/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
  <link href="<?php echo URL::to('/');?>/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
  <style>
  span.select2.select2-container.select2-container--default {
    width: 200px !important;
}
</style>
 <div class="main-panel">
   <?php
    $auth = auth()->user();
    ?>
   <div class="content-wrapper">
     
     <div class="card">
       <div class="row list-name">
         <div class="col-sm-6 card-title">
           <h4 class="card-title">Patient Appointments Report</h4>
         </div>
         <div class="col-sm-6">
          <a href="" class="btn btn-success btn-rounded btn-sm btn-fw pull-right" id="test_agency" onclick="export_data()"><i class="mdi mdi-file-export"></i>Export</a>
           <a href="<?php echo URL::to("/"); ?>/patient" class="btn btn-danger btn-rounded btn-fw btn-sm pull-right"><i class="mdi mdi-reload"></i> Reset</a>
          
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
                       <?php if (in_array($user->user_type_fk, array(3, 184))) { ?>
                         <th>Agency Name</th>
                       <?php } ?>
                       <th>Doctor Name</th>
                       <th>Type</th>
                       <th>Full Name</th>

                       <th>Phone</th>
					   <th>Mobile</th>
                       <th>Date of Birth</th>
                       <th>Location</th>
                       <th>Appointment Date</th>
                       <th>Appointment Time</th>
                       <th>Service</th>
					   <th>Created Date</th>
                       <th>SMS Status</th>
                       <th>Status</th>

                       <th></th>
                     </tr>
					 <form method="get" action="">
                     <tr>

                       <td></td>
                       <?php

                        if (in_array($user->user_type_fk, array(3, 184))) { ?>
                         <td>
                           <?php if (in_array($user->user_type_fk, array(3, 184))) { ?>
                             <select name="agency_fk" id="agency_fk" class="form-control">
                               <option value="">Select agency</option>
                               <?php foreach ($agencyList as $rwAgency) { ?>
                                 <option value="<?php echo $rwAgency->id ?>" <?php echo (($agency_fk) == $rwAgency->id) ? 'selected' : ''; ?>><?php echo $rwAgency->agency_name; ?></option>
                               <?php } ?>
                             </select><?php } ?>
                         </td>
                       <?php } ?>

                       <td></td>
					    <td></td>
						 <td></td>

						 <td></td>
						  <td></td>
						   <td></td>
						    <td></td>
							 <td></td>
							  <td></td>
							   <td></td>
							   
                       
                       
                       
                
                     
					   <td>
						<input type="text" name="created_date" value="<?php echo $created_date;?>" class="datepickernn">
					   </td>
					   <td></td>
                       <td>
                         <select name="status" id="status_id" class="form-control">
                           <option value=""></option>
                           <option value="Pending" <?php if ($status == 'Pending') {
                                                      echo "selected='selected'";
                                                    } ?>>Pending</option>
                           <option value="booked" <?php if ($status == 'booked') {
                                                        echo "selected='selected'";
                                                      } ?>>Booked</option>
                           <option value="completed" <?php if ($status == 'completed') {
                                                        echo "selected='selected'";
                                                      } ?>>Completed</option>
							<option value="cancelled" <?php if ($status == 'cancelled') {
                                                        echo "selected='selected'";
                                                      } ?>>Cancelled</option>
							  <option value="cancelled" <?php if ($status == 'noshow') {
                                                        echo "selected='selected'";
                                                      } ?>>No Show</option>
                         </select>

                       </td>
                       <td><input type="submit" name="search" class="btn btn-primary btn-sm btn-rounded btn-fw  pull-right" value="search"></td>

                     </tr>
					</form>
                   </thead>
                   <tbody>
				   
                     <?php if ($query->total() != 0) {
                        $i = 1 + (($query->currentPage() - 1) * $query->perPage());
                        foreach ($query as $row) {  ?>
                         <tr>

                           <td><a href="<?php echo URL::to('/'); ?>/patient/view/<?php echo $row->id; ?>"><?= '#' . ' ' . $row->id ?></a></td>
                           <?php if (in_array($user->user_type_fk, array(3, 184))) { ?>
                             <td><?= $row->agency_name ?></td>
                           <?php } ?>
                           <td><?php echo $row->full_name; ?></td>
                           <td><?php echo $row->type; ?></td>
                           <td><?php echo $row->first_name . ' ' . $row->middle_name . ' ' . $row->last_name; ?></td>

                           <td><?php echo $row->phone; ?></td>
                           <td><?php echo $row->mobile; ?></td>
                           <td><?php if ($row->dob != '0000-00-00') {
                                  echo Common::convertMDY($row->dob);
                                } ?></td>
                           <td><?php echo $row->address1.' '.$row->city; ?></td>
                           <td><?php if ($row->appointment_date != '') {
                                  echo Common::convertMDY($row->appointment_date);
                                } ?></td>
                           <td><?php if ($row->start_time != '' && $row->end_time) {
								$start_time = date('h:i A', strtotime($row->start_time));
								$end_time = date('h:i A', strtotime($row->end_time));
								
                                  echo $start_time.' - '.$end_time;
                                } ?></td>
								
								<td><?php echo $row->name;?></td>
								<td><?php echo date('m-d-Y h:i A',strtotime($row->created_date));?></td>
								<td><?php if($row->patient_sms_flag ==1){ echo "<span class='badge badge-success'>Sent</span>";}else{ echo "<span class='badge badge-primary'>Pending</span>";};?></td>
                           <td>
                             <?php

                              if ($row->status == 'Pending') {
                              ?>
                               <label class='badge badge-warning badge-pill'>Pending</label>

                             <?php } ?>
                             <?php

                              if ($row->status == 'booked') {
                              ?>
                               <label class='badge badge-info badge-pill'>Booked</label>

                             <?php } ?>
                             <?php

                              if ($row->status == 'completed') {
                              ?>
                               <label class='badge badge-success badge-pill'>Completed</label>

                             <?php } ?>
                             <?php

                              if ($row->status == 'cancelled') {
                              ?>
                               <label class='badge badge-danger badge-pill'>Cancelled</label>

                             <?php } ?>

                           </td>
                           <td>
                          
                           </td>
                         </tr>
                       <?php }
                      } else { ?>
                       <tr>
                         <td colspan="15">
                           <center><b>Data not found</b></center>
                         </td>
                       </tr>
                     <?php } ?>
                   </tbody>
                 </table>
           
               <div class="pull-right pegination-margin">
                 {{$query->links("pagination::bootstrap-4")}}
               </div>

             </div>
           </div>
         </div>
       </div>
     </div>
   </div>
   
      
  
  

<script src="<?php echo URL::to('/');?>/assets/vendors/select2/select2.min.js"></script>
  <script src="<?php echo URL::to('/');?>/assets/js/select2.js"></script>
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
   <script>
     function export_data() {

       var agency_name = $('#agency_name').val();
       var email = $('#middle_name').val();
       var last_name = $('#last_name').val();
       var phone = $('#phone').val();
       var datepicks = $('.datepicker1').val();
       var age ='';

         var agency_fk = $('#agency_fk').val();
       var temp1 = '<?php echo URL::to("/") ?>/patient-report/patient-report-export?agency_fk=' + agency_fk + '&created_date=<?php echo $created_date;?>&status=<?php echo $status;?>';
      $('#test_agency').attr("style", '');
       $('#test_agency').attr("href", temp1);
      
     }
   </script>

   @include('include/footer')
  
   <script src="<?= URL::to('/js/jquery.min.js') ?>"></script>
   <link rel="stylesheet" href="<?= URL::to('/css/jquery-ui.css') ?>">
   <script src="<?= URL::to('assets/js/jquery-ui.min.js') ?>"></script>
   <script type="text/javascript" src="<?php echo URL::to('/'); ?>/assets/js/moment.min.js"></script>
   <script type="text/javascript" src="<?php echo URL::to('/'); ?>/assets/js/daterangepicker.min.js"></script>
   <link rel="stylesheet" type="text/css" href="<?php echo URL::to('/'); ?>/css/daterangepicker.css" />
   
  