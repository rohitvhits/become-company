<div class="page-title-main">
    <h5 class="mb-0 font-weight-bold">{{ ucfirst($listHeadingName) }} ({{ $open_record_list->total() }})</h5>
    <div class="page-rightbtns">
        <div>
            @if ($listHeadingName == 'Archived Appointments')
                <a href="javascript:void(0)" onclick="getArchive()" class="btn btn-info btn-rounded btn-fw btn-sm"><i
                        class="mdi mdi-reload"></i>Appointments
                    Unarchive</a>
            @endif
            <a href="<?php echo URL::to('/'); ?>/{{ $appointmentUrl }}" class="btn btn-light btn-sm btn-rounded btn-fw ml-1"><i
                    class="mdi mdi-reload"></i> Reset</a>
            <button class="btn btn-dark btn-rounded btn-fw btn-sm ml-1 srch-icon" id="searchbtns"><i
                    class="fa fa-search"></i></button>
        </div>
    </div>
</div>

<div class="row ">
    <div class="col-sm-12">
        <div class="card search-card1" id="search-div" style="display: none;">
            <div class="card-body">
                <form method="get" id="formsubmit">
                    <input type="hidden" name="_token" value="T2fdzK1ShOFrIaDGtfR43XwT91A6Ahjq88isXJeQ">
                    <input type="hidden" name="status_update" id="status_update" value="{{ $listHeadingName }}">
                    <div class="row">

                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-sm-12 ">SMS Status</label>
                                <div class="col-sm-12 ">
                                    <select name="sms_status[]" id="sms_status"
                                        class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100"
                                        multiple="multiple">
                                        <option value="0" <?php if (in_array(0, $selected_sms_status)) {
                                            echo "selected='selected'";
                                        } ?>>Pending</option>
                                        <option value="1" <?php if (in_array(1, $selected_sms_status)) {
                                            echo "selected='selected'";
                                        } ?>>Sent</option>

                                    </select>
                                </div>
                            </div>
                        </div>
                        @if ($appointmentUrl == 'upcomming-appoinment' || $appointmentUrl == 'archive-list')
                            <div class="col-md-3">
                                <div class="form-group row">
                                    <label class="col-sm-12 ">Status</label>
                                    <div class="col-sm-12">
                                        <select name="status[]" id="status_id"
                                            class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100"
                                            multiple="multiple">
                                            <option value=""></option>
                                            <option value="Pending" <?php if (in_array('Pending', $selected_status)) {
                                                echo "selected='selected'";
                                            } ?>>Pending</option>
                                            <option value="booked" <?php if (in_array('booked', $selected_status)) {
                                                echo "selected='selected'";
                                            } ?>>Booked</option>
                                            <option value="completed" <?php if (in_array('completed', $selected_status)) {
                                                echo "selected='selected'";
                                            } ?>>Completed</option>

                                            <option value="noshow" <?php if (in_array('noshow', $selected_status)) {
                                                echo "selected='selected'";
                                            } ?>>No Show</option>

                                            <option value="arrived" <?php if (in_array('arrived', $selected_status)) {
                                                echo "selected='selected'";
                                            } ?>>Arrived</option>
                                            <option value="processing" <?php if (in_array('processing', $selected_status)) {
                                                echo "selected='selected'";
                                            } ?>>Processing</option>
                                            <option value="Not interested" <?php if (in_array('Not interested', $selected_status)) {
                                                echo "selected='selected'";
                                            } ?>>Not Interested
                                            </option>
                                            <option value="hospitalized/rehab" <?php if (in_array('hospitalized/rehab', $selected_status)) {
                                                echo "selected='selected'";
                                            } ?>>
                                                Hospitalized/Rehab</option>
                                            <option value="unableToContact" <?php if (in_array('unableToContact', $selected_status)) {
                                                echo "selected='selected'";
                                            } ?>>Unable To Contact
                                            </option>
                                            <option value="refused" <?php if (in_array('refused', $selected_status)) {
                                                echo "selected='selected'";
                                            } ?>>Refused</option>
                                            <option value="checkin" <?php if (in_array('checkin', $selected_status)) {
                                                echo "selected='selected'";
                                            } ?>>Mark as CheckIn</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if (in_array($user->user_type_fk, [3, 184]))
                            <div class="col-md-3">
                                <div class="form-group row">
                                    <label class="col-sm-12 ">Agency Name</label>
                                    <div class="col-sm-12">
                                        <select name="agency_fk[]" id="agency_fk"
                                            class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100"
                                            multiple="multiple">
                                            <?php foreach ($agencyList as $rwAgency) { ?>
                                            <option value="<?php echo $rwAgency->id; ?>" <?php echo in_array($rwAgency->id, $selected_agency_fk) ? 'selected' : ''; ?>>
                                                <?php echo $rwAgency->agency_name; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-sm-12 ">Name</label>
                                <div class="col-sm-12">
                                    <input autocomplete="off" type="text" class="form-control" name="first_name"
                                        id="agency_name" value="<?php echo $full_name; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-sm-12 ">Mobile</label>
                                <div class="col-sm-12">
                                    <input autocomplete="off" type="text" class="form-control" name="mobile"
                                        id="mobile" value="<?php echo $mobile; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-sm-12 ">Services</label>
                                <div class="col-sm-12">
                                    <select class="js-example-basic-multiple w-100 select2-design" multiple="multiple"
                                        name="service_id[]" id="service_id">
                                        <?php
                 foreach ($serviceList as $service) { ?>
                                        <option value="<?php echo $service->id; ?>" <?php if (in_array($service->id, $selected_service_id)) {
                                            echo 'selected';
                                        } ?>>
                                            <?php echo $service->name; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-sm-12 ">Assign To</label>
                                <div class="col-sm-12">
                                    <select name="assign_user_id[]"
                                        class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100"
                                        multiple="multiple" id="assign_user_id">
                                        @if (!empty($assign_user_list[0]))
                                            @foreach ($assign_user_list as $assigns)
                                                <option value="{{ $assigns->id }}"
                                                    @if (in_array($assigns->id, $selected_assign_user_id)) selected='selected' @endif>
                                                    {{ $assigns->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-sm-12 ">Due Date</label>
                                <div class="col-sm-12">
                                    <input type="text" name="due_date" value="<?php echo $due_date; ?>"
                                        class="due_datenn form-control" id="due_date">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-sm-12 ">Appointment Date</label>
                                <div class="col-sm-12">
                                    <input type="text" autocomplete="off" name="appointment_date"
                                        class="datepicker1 form-control" value="<?php echo $appointment_date; ?>"
                                        id="appointment_date">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-sm-12 ">Location</label>
                                <div class="col-sm-12">
                                    <select name="locationId[]"
                                        class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100"
                                        multiple="multiple" id="locationId">
                                        <?php foreach ($location_list as $vsl) { ?>
                                        <option value="<?php echo $vsl->id; ?>" <?php if (in_array($vsl->id, $selected_location_id)) {
                                            echo 'selected';
                                        } ?>>
                                            <?php echo $vsl->location_name; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-sm-12 ">Created Date</label>
                                <div class="col-sm-12">
                                    <input type="text" name="created_date" value="<?php echo $created_date; ?>"
                                        class="datepickernn form-control" id="created_date">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="search-main1">
                        <div class="search-inner">
                            <div>
                                <input type="button" name="search"
                                    class="btn btn-primary search-btn1 searchAppoinment" id="search-data"
                                    value="Search">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
