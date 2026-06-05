<div class="tab-pane tabs__toggle--active" id="appointment-section">
    <div class="page-title-main">
        <h5 class="mb-0 font-weight-bold">Flag Appointment List</h5>
    </div>
    <div class="row">
        <div class="col-12">
            <div class=" card search-card1" id="search-div">
                <div class="card-body">
                    <form method="get" id="formsubmit">
                        @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group row">
                                    <label class="col-sm-12 ">Status</label>
                                    <div class="col-sm-12">
                                        <select name="status[]" id="status_id" class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100" multiple="multiple">
                                            <option value=""></option>
                                            <option value="Pending">Pending</option>
                                            <option value="cancelled">Cancelled</option>

                                            <option value="booked">Booked</option>
                                            <option value="completed">Completed</option>

                                            <option value="noshow">No Show</option>

                                            <option value="arrived">Arrived</option>
                                            <option value="processing">Processing</option>
                                            <option value="Not interested">Not Interested
                                            </option>
                                            <option value="hospitalized/rehab">
                                                Hospitalized/Rehab</option>
                                            <option value="unableToContact">Unable To Contact
                                            </option>
                                            <option value="refused">Refused</option>
                                            <option value="checkin">Mark as CheckIn</option>

                                            <option value="Pending Termination">Pending Termination</option>
                                            <option value="Onhold">On Hold</option>
                                            <option value="On Leave">On Leave</option>
                                            <option value="Terminated">Terminated</option>
                                            <option value="inactive">Inactive</option>
                                            @foreach ($statuses as $key=> $status)
                                                <option value="{{ $key }}">
                                                    {{ $status }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            @if (in_array($user->user_type_fk, [3, 184]))
                            <div class="col-md-3">
                                <div class="form-group row">
                                    <label class="col-sm-12 ">Agency Name</label>
                                    <div class="col-sm-12">
                                        <select name="agency_fk[]" id="agency_fk" class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100" multiple="multiple">
                                            <?php foreach ($agencyList as $rwAgency) { ?>
                                                <option value="<?php echo $rwAgency->id; ?>">
                                                    <?php echo $rwAgency->agency_name; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            @endif
                            <div class="col-md-3">
                                <div class="form-group row">
                                    <label class="col-sm-12 ">Patient Code</label>
                                    <div class="col-sm-12">
                                        <input autocomplete="off" type="text" class="form-control" name="patient_code" id="patient_code">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group row">
                                    <label class="col-sm-12 ">Name</label>
                                    <div class="col-sm-12">
                                        <input autocomplete="off" type="text" class="form-control" name="first_name" id="agency_name">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group row">
                                    <label class="col-sm-12 ">Mobile</label>
                                    <div class="col-sm-12">
                                        <input autocomplete="off" type="text" class="form-control" name="mobile" id="mobile">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group row">
                                    <label class="col-sm-12 ">Services</label>
                                    <div class="col-sm-12">
                                        <select class="js-example-basic-multiple w-100 select2-design" multiple="multiple" name="service_id[]" id="service_id">
                                            <?php
                                            foreach ($serviceList as $service) { ?>
                                                <option value="<?php echo $service->id; ?>">
                                                    <?php echo $service->name; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group row">
                                    <label class="col-sm-12 ">Created Date</label>
                                    <div class="col-sm-12">
                                        <input type="text" name="created_date" class="datepickernn form-control" id="created_date">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group row">
                                    <label class="col-sm-12 ">Type</label>
                                    <div class="col-sm-12 ">
                                        <select class="form-control" name="type" id="type" class="form-control">
                                            <option value="">Select Type</option>
                                            <option value="Caregiver">Caregiver</option>
                                            <option value="Patient">Patient</option>

                                        </select>

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group row">
                                    <label class="col-sm-12 ">Created By</label>
                                    <div class="col-sm-12">
                                        @if(!empty($agency_user_list[0]))
                                        <select name="created_by" class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100" id="created_by">
                                            <option value="">Select Created By</option>
                                            @foreach($agency_user_list as $val)
                                            <option value="{{ $val->id}}">{{ $val->first_name}} {{ $val->last_name}}</option>

                                            @endforeach

                                        </select>
                                        @else
                                        <input type="text" name="created_by_ny" id="created_by_ny">
                                        <input type="hidden" name="created_by_ny_id" id="created_by_ny_id">
                                        <input type="hidden" name="created_by_ny_name" id="created_by_ny_name">

                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group row">
                                    <label class="col-sm-12 ">Date Of Birth</label>
                                    <div class="col-sm-12">
                                        <input type="text" name="dob" class="dob form-control" id="dob">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="search-main1">
                            <div class="search-inner">
                                <div>
                                    <input type="button" name="search" class="btn btn-primary search-btn1 searchAppoinment" id="search-data" value="Search">
                                    <a href="javascript::void();" onclick="loadAppointmentFlagList()" class="btn btn-light btn-rounded btn-fw btn-sm"><i class="mdi mdi-reload"></i> Reset</a>
                                    <img src="{{ asset('/ajax-loader.gif')}}" class="order-listing-loader1" alt="loader" id="loaderDashboardGraph" style="display:none">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class=" card">
                <div class="card-body compact-view">
                   
                    <div class="col-12">
                        <span id="resp"></span>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>