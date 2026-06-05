<div class="modal fade" id="add-date-wise-agency-view-use" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title notification-emails" id="ModalLabel">Add Date Wise Agency View Access</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="resetNotificationEmail()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="forms-sample" enctype="multipart/form-data" action=''method="post" id="formAgencyViewSubmit">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" id="agency_id" name="agency_id" value="{{ $id }}">
                   
                    <!-- <div class="form-group">
                        <label for="recipient-name" class="col-form-label mb-0"><b>Type</b><span class="text-danger">*</span></label>
                        <select class="form-control" name="type" id="agency_date_type">
                            <option value="All">All</option>
                            <option value="Caregiver">Caregiver</option>
                            <option value="Patient">Patient</option>
                        </select>
                        <span id="agency_date_type_error" class="error"></span>
                    </div> -->
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label mb-0"><b>Permission</b><span class="text-danger">*</span></label>
                        <select class="js-example-basic-multiple" name="type[]" id="agency_view_permission" multiple>
                            <option value="All">Select Permission</option>
                            @forelse(Common::staticDateWiseAgencyAccess() as $key=>$permission)
                                <option value="{{ $key }}">{{ $permission }}</option>
                            @empty
                                <option value="">No permissions available</option>
                            @endforelse
                        </select>
                        <span id="agency_view_permission_error" class="error"></span>
                    </div>
                   
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label mb-0"><b>Start Date</b><span class="text-danger">*</span></label>
                        <input type="text" data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" class="form-control form-control-sm " autocomplete="off" placeholder="Select Start Date" id="permission_start_date" name="start_date" value="" min="1000-01-01" max="9999-12-31" im-insert="false">
                        <span id="start_date_error" class="error"></span>
                    </div>
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label mb-0"><b>End Date</b><span class="text-danger">*</span></label>
                        <input type="text" data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" class="form-control form-control-sm " autocomplete="off" placeholder="Select End Date" id="permission_end_date" name="end_date" value=""  min="1000-01-01" max="9999-12-31" im-insert="false">
                        <span id="end_date_error" class="error"></span>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="submit_view_agency_access" class="btn btn-success">Save</button>
                <button type="button" class="btn btn-light" data-dismiss="modal" >Close</button>
            </div>
        </div>
    </div>
</div>