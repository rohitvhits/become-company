 <div class="modal fade" id="schedule_appointment_with_service" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
     <div class="modal-dialog" role="document">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title" id="ModalLabel">Scheduler Appointment</h5>
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                 </button>
             </div>
             <form class="forms-sample" enctype="multipart/form-data">
                 <div class="modal-body">

                     <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                     <input type="hidden" name="id" id="id" value="<?php echo $record->id; ?>">
                     <input type="hidden" name="patient_service_id" id="patient_service_id">
                     <input type="hidden" name="patient_id" id="patient_id">
                     <?php if ($record->type == 'Caregiver') { ?>
                         <div class="form-group">
                             <label for="recipient-name" class="col-form-label">Location<span style="color:red">*</span>:</label>
                             <select name="location_id" class="form-control" id="location_id_schedule" onchange="getTimeSearchForSchedule()">
                                 <option value="">Select Location</option>
                                 <?php foreach ($location_list as $ks) { ?>
                                     <option value="<?php echo $ks->id; ?>" <?php if ($record->location_id == $ks->id) {
                                                                                echo "selected='selected'";
                                                                            } ?>>
                                         <?php echo $ks->address1; ?>
                                     </option>
                                 <?php } ?>
                             </select>

                             <span id="location_error" class="error mt-2 text-danger" for="document_type"></span>
                         </div>
                     <?php } ?>
                     <?php
                        $dates = '';
                        $time = '';
                        if ($record->appointment_date != '') {
                            $dates = date('m/d/Y', strtotime($record->appointment_date));
                            $time = date('H:i:s', strtotime($record->appointment_date));
                        } ?>
                     <div class="form-group setDate">
                         <label for="recipient-name" class="col-form-label">Appointment Date <span style="color:red">*</span>:</label>
                         <input readonly type="text" name="date" class="form-control getappoinmentdate" autocomplete="off" id="schedule_date_id" onchange="getTimeSearchForSchedule()" value="<?php echo $dates; ?>">
                         <span id="date_error" class="error mt-2 text-danger" for="document_type"></span>
                     </div>

                     <div class="form-group">
                         <label for="message-text" class="col-form-label">Appointment Time<span style="color:red">*</span>:</label>
                         <?php if ($record->type == 'Caregiver') { ?>
                             <select name="time" class="form-control" id="time_id_schedule">
                                 <option value="">Select Appointment Time</option>
                             </select>

                         <?php } else { ?>
                             <input type="time" name="time" class="form-control" id="time_id_schedule" value="<?php echo $time; ?>">

                         <?php } ?>
                         <span id="schedule_time_error" class="error mt-2 text-danger" for="document_type"></span>
                     </div>


                     <div class="form-group">
                         <label class="col-form-label">Services<span class="error">*</span></label>
                         <select class="js-example-basic-multiple w-100" multiple="multiple" name="service_id[]"
                             id="service_id_schedule_appointment">
                             <option value="">Select Service</option>
                         </select>
                         <span class="error mt-2 text-danger" id="service_oid_error"></span>
                     </div>

                     @if ($record->type == 'Patient')
                     <div class="form-group">
                         <label for="message-text" class="col-form-label">Location<span style="color:red">*</span>:</label>
                         <select name="location_id" class="form-control" id="location_id">
                             <option value="">Select Location</option>
                             @if (count($locations) > 0)
                             @foreach ($locations as $location)
                             <option value="{{$location->id}}" {{($record->location_id ==  $location->id) ? 'selected' : '' }}>{{$location->address1}}
                             </option>
                             @endforeach
                             @endif
                         </select>
                         <span class="error mt-2 text-danger" id="location_error"></span>

                     </div>
                     @endif 
                 </div>
                 <div class="modal-footer">
                     <button type="button" onclick="saveScheduleAppointmentWithService()" class="btn btn-success">Save</button>
                     <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                 </div>
             </form>
         </div>
     </div>
 </div>