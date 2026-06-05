<div class="modal fade " id="editModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel">Edit Location Schedule</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                    <form class="form-sample" name="editTelehealthSchedule" id="editTelehealthSchedule" method="post">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" name="edit_id" id="edit_id" value="">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Title<span class="error">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="edit_title" name="title" placeholder="Enter Schedule Title">
                                            <span id="edit_title_error" class="error mt-2"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Telehealth Configuration Type<span class="error">*</span></label>
                                        <div class="col-sm-9">
                                            <select name="telehealth_config_type" id="edit_telehealth_config_type" class="form-control">
                                                <option value="">Select Telehealth Configuration Type</option>
                                                <option value="patient">Patient</option>
                                                <option value="caregiver">Caregiver</option>
                                            </select>
                                            <span id="edit_telehealth_config_type_error" class="error mt-2"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Location<span class="error">*</span></label>
                                        <div class="col-sm-9">
                                            <select class="form-control" id="edit_location_id" name="location_id">
                                                <option value="">Select Location</option>
                                            </select>
                                            <span id="edit_location_id_error" class="error mt-2"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Days<span class="error">*</span></label>
                                        <div class="col-sm-9">
                                            <div class="days-checkbox-container">
                                                <div class="row">
                                                    <div class="col-md-6 mb-2">
                                                        <div class="custom-checkbox">
                                                            <input class="form-check-input" type="checkbox" name="days[]" value="monday" id="monday">
                                                            <label class="form-check-label" for="monday">Monday</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-2">
                                                        <div class="custom-checkbox">
                                                            <input class="form-check-input" type="checkbox" name="days[]" value="tuesday" id="tuesday">
                                                            <label class="form-check-label" for="tuesday">Tuesday</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-2">
                                                        <div class="custom-checkbox">
                                                            <input class="form-check-input" type="checkbox" name="days[]" value="wednesday" id="wednesday">
                                                            <label class="form-check-label" for="wednesday">Wednesday</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-2">
                                                        <div class="custom-checkbox">
                                                            <input class="form-check-input" type="checkbox" name="days[]" value="thursday" id="thursday">
                                                            <label class="form-check-label" for="thursday">Thursday</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-2">
                                                        <div class="custom-checkbox">
                                                            <input class="form-check-input" type="checkbox" name="days[]" value="friday" id="friday">
                                                            <label class="form-check-label" for="friday">Friday</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-2">
                                                        <div class="custom-checkbox">
                                                            <input class="form-check-input" type="checkbox" name="days[]" value="saturday" id="saturday">
                                                            <label class="form-check-label" for="saturday">Saturday</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-2">
                                                        <div class="custom-checkbox">
                                                            <input class="form-check-input" type="checkbox" name="days[]" value="sunday" id="sunday">
                                                            <label class="form-check-label" for="sunday">Sunday</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <span id="edit_day_error" class="error mt-2"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Start Time<span class="error">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="time" class="form-control" placeholder="Enter Start Time" id="edit_start_time" name="start_time" value="">
                                            <span id="edit_start_error" class="error mt-2"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">End Time<span
                                            class="error">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="time" class="form-control" placeholder="Enter End Time" id="edit_end_time" name="end_time">
                                            <span id="edit_end_time_error" class="error mt-2"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Time Slot (In Minutes)<span
                                            class="error">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="edit_slot" name="slot" value="" onkeypress="return isNumber(event)">
                                            <span id="edit_slot_error" class="error mt-2"><?php echo $errors->add_agency->first('slot'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="text-center mt-0 mb-0" id="loader_edit" style="display:none"><i class="fa fa-spinner fa-spin fa-2x mt-2 mb-0"></i></div>
                            <button type="button" class="btn btn-primary mr-2 btn-sm" onclick="editTeleLocationSchedule()">Save</button>
                            <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div> 
        </div>
    </div>