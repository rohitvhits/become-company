<div class="modal fade" id="exampleModal-4" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Add Appointment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            
                <form class="forms-sample" enctype="multipart/form-data" action="<?php echo URL::to('/patient/appointment-add'); ?>" name="adduser" method="post" id="form">
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    <input type="hidden" name="id" value="<?php echo $record->id; ?>">
                <?php 
                    $locationsIds = [];
                    if(auth()->user()->agency_fk !=""){
                        $locationsIds = ['49','55'];
                    }
                ?>
                    <?php if ($record->type == 'Caregiver') { ?>
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Location<span style="color:red">*</span>:</label>
                            <select name="location_id" class="form-control" id="location_id" onchange="getTimeSearch()">
                                <option value="">Select Location</option>
                                    <?php foreach ($location_list as $ks) { 
                                        if(!in_array($ks->id,$locationsIds)){
                                        ?>
                                        <option value="<?php echo $ks->id; ?>" <?php if ($record->location_id == $ks->id) {
                                                                                    echo "selected='selected'";
                                                                                } ?>><?php echo $ks->address1; ?>
                                        </option>
                                    <?php } } ?>
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
                        <input readonly type="text" name="date" class="form-control getappoinmentdate" autocomplete="off" id="date_id" onchange="getTimeSearch()" value="<?php echo $dates; ?>">
                        <span id="date_error" class="error mt-2 text-danger" for="document_type"></span>
                        @if ($record->type == 'Caregiver')
                            <div id="date_time_div" class=""></div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="message-text" class="col-form-label">Appointment Time<span style="color:red">*</span>:</label>
                        <?php if ($record->type == 'Caregiver') { ?>
                            <select name="time" class="form-control" id="timeid">
                                <option value="">Select Appointment Time</option>
                            </select>

                        <?php } else { ?>
                            <input type="time" name="time" class="form-control" id="times_id" value="<?php echo $time; ?>">

                        <?php } ?>
                        <span id="time_error" class="error mt-2 text-danger" for="document_type"></span>
                        <div id="date_time_count_div" class=""></div>
                    </div>



                    <div class="form-group">

                        <label class="col-form-label">Services<span class="error mt-2">*</span></label>
                        <select class="js-example-basic-multiple w-100 new_service_id" multiple="multiple" name="service_id[]" id="service_id">
                            <option value="">Select Service</option>
                            @php $serviceArr = explode(',', $record->service_id);

                            @endphp

                            
                            @if (count($serviceList) > 0)
                            @foreach ($serviceList as $ks)
                            @if (strtolower($ks->types) == strtolower($record->type))

                            <option value="{{$ks->id}}" <?php if (in_array($ks->id, $serviceArr)) { ?>selected<?php } ?>>{{ $ks->name }}</option>
                            @endif
                            @endforeach
                            @endif
                        </select>
                        <span class="error mt-2 text-danger" id="service_error"></span>

                    </div>
                    @if ($record->type == 'Patient')
                    <div class="form-group">
                        <label for="message-text" class="col-form-label">Location<span style="color:red">*</span>:</label>
                        <select name="location_id" class="form-control" id="location_id">
                            <option value="">Select Location</option>
                            @if (count($locations) > 0)
                            @foreach ($locations as $location)
                                @if(!in_array($location->id,$locationsIds))
                                <option value="{{$location->id}}" {{($record->location_id ==  $location->id) ? 'selected' : '' }}>{{$location->address1}}
                                </option>
                                @endif
                            @endforeach
                            @endif
                        </select>
                        <span class="error mt-2 text-danger" id="location_error"></span>

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