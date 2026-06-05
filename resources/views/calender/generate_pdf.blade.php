<meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<style>
		body, html{
			padding:0;
			margin:0;
		}
	</style>
<table id="order-listing1" class="table table-bordered" style="font-size:7px;width:100%">
                   <thead>
                     <tr>
                    
                      
                         <th style="border:1px solid #000;text-align:center;white-space:nowrap">Agency Name</th>
                  
                     
                       <th style="border:1px solid #000;text-align:center;white-space:nowrap">Type</th>
                       <th style="border:1px solid #000;text-align:center;white-space:nowrap">Full Name</th>

                   
					   <th style="border:1px solid #000;text-align:center;white-space:nowrap">Mobile</th>
                       <th style="border:1px solid #000;text-align:center;white-space:nowrap">Date of Birth</th>
                       <th style="border:1px solid #000;text-align:center;white-space:nowrap">Location</th>
                       <th style="border:1px solid #000;text-align:center;white-space:nowrap">Appointment Date</th>
                       <th style="border:1px solid #000;text-align:center;white-space:nowrap">Appointment Time</th>
                       <th style="border:1px solid #000;text-align:center;white-space:nowrap">Service</th>
					   <th style="border:1px solid #000;text-align:center;white-space:nowrap">Created Date</th>
                       
                       <th style="border:1px solid #000;text-align:center;white-space:nowrap">Status</th>

                       <th></th>
                     </tr>
					 
                   </thead>
                   <tbody>
				   
                     <?php
					 if(count($query) >0){
                        foreach ($query as $row) {  ?>
                         <tr>

                           
                             <td style="border:1px solid #000;text-align:center"><?= $row->agency_name ?></td>
                     
                          
                      
                           <td style="border:1px solid #000;text-align:center"><?php echo $row->first_name . ' ' . $row->middle_name . ' ' . $row->last_name; ?></td>

                           <td style="border:1px solid #000;text-align:center"><?php echo $row->phone; ?></td>
                           <td style="border:1px solid #000;text-align:center"><?php echo $row->mobile; ?></td>
                           <td style="border:1px solid #000;text-align:center"><?php if ($row->dob != '0000-00-00') {
                                  echo Common::convertMDY($row->dob);
                                } ?></td>
                           <td style="border:1px solid #000;text-align:center"><?php echo $row->address1.' '.$row->city; ?></td>
                           <td style="border:1px solid #000;text-align:center"><?php if ($row->appointment_date != '') {
                                  echo Common::convertMDY($row->appointment_date);
                                } ?></td>
                           <td style="border:1px solid #000;text-align:center"><?php if ($row->start_time != '' && $row->end_time) {
								$start_time = date('h:i A', strtotime($row->start_time));
								$end_time = date('h:i A', strtotime($row->end_time));
								
                                  echo $start_time.' - '.$end_time;
                                } ?></td>
								
								<td style="border:1px solid #000;text-align:center"><?php echo $row->serviceName;?></td>
								<td style="border:1px solid #000;text-align:center"><?php echo date('m-d-Y',strtotime($row->created_date));?></td>
								 <td style="border:1px solid #000;text-align:center">
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
                          
                         </tr>
                       <?php }
                      } else { ?>
                       <tr>
                         <td colspan="15" style="border:1px solid #000;text-align:center">
                           <center><b>Data not found</b></center>
                         </td>
                       </tr>
                     <?php } ?>
                   </tbody>
                 </table>