<div class="modal fade" id="exampleModal-23" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel">Request for Appointment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="forms-sample" enctype="multipart/form-data" action="<?php echo URL::to('/patient/appointment-schedule'); ?>" name="adduser" method="post" id="appointmentForm">
                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                        <input type="hidden" name="id" value="<?php echo $record->id; ?>">
                        <?php if ($record->type == 'Caregiver') { ?>
                            <div class="form-group">
                                <label for="recipient-name" class="col-form-label">Location<span style="color:red">*</span>:</label>
                                <select name="location_id" class="form-control" id="location_eid" onchange="getTimeSearchForAgency()">
                                    <option value="">Select Location</option>
                                    <?php foreach ($location_list as $ks) { ?>
                                        <option value="<?php echo $ks->id; ?>" ><?php echo $ks->address1; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                                <input type="hidden" name="caregiver_type" value="<?php echo $record->type; ?>">
                                <span id="location_eid_error" class="error mt-2 text-danger" for="document_type"></span>
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
                            <input readonly type="text" name="date" class="form-control getappoinmentdate" autocomplete="off" id="date_eid" onchange="getTimeSearchForAgency()" value="">
                            <span id="date_eid_error" class="error mt-2 text-danger" for="document_type"></span>
                        </div>

                        <div class="form-group">
                            <label for="message-text" class="col-form-label">Appointment Time<span style="color:red">*</span>:</label>
                            <?php if ($record->type == 'Caregiver') { ?>
                                <select name="time" class="form-control" id="time_eid">
                                    <option value="">Select Appointment Time</option>
                                </select>

                            <?php } else { ?>
                                <input type="time" name="time" class="form-control" id="times_eid" value="">

                            <?php } ?>
                            <span id="time_eid_error" class="error mt-2 text-danger" for="document_type"></span>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label">Services<span class="error mt-2">*</span></label>
                            <select class="js-example-basic-multiple w-100" multiple="multiple" name="service_id[]" id="service_eid">
                                <option value="">Select Service</option>
                                @php $serviceArr = explode(',', $record->service_id);
                                echo "
                                <pre>";print_R($serviceArr);
                                @endphp
                                @if (count($serviceList) > 0)
                                @foreach ($serviceList as $ks)
                                @if ($ks->types == $record->type)
                                <option value="{{$ks->id}}">{{ $ks->name }}</option>
                                @endif
                                @endforeach
                                @endif
                            </select>
                            <span class="error mt-2 text-danger" id="service_eid_error"></span>

                        </div>
                        @if ($record->type == 'Patient')
                        <div class="form-group">
                            <label for="message-text" class="col-form-label">Location<span style="color:red">*</span>:</label>
                            <select name="location_id" class="form-control" id="location_eid">
                                <option value="">Select Location</option>
                                @if (count($locations) > 0)
                                @foreach ($locations as $location)
                                <option value="{{$location->id}}" >{{$location->location_name}}
                                </option>
                                @endforeach
                                @endif
                            </select>
                            <span class="error mt-2 text-danger" id="location_eid_error"></span>

                        </div>
                        @endif

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Save</button>
                            <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>