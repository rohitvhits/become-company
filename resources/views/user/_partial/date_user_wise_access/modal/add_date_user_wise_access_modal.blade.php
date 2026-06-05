<style>
    #add-date-wise-user-view-use .modal-footer {
        padding: 4px 1px !important;
    }
    #add-date-wise-user-view-use .modal-header {
        padding: 8px 16px !important;
    }
</style>

<div class="modal fade" id="add-date-wise-user-view-use" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white" style="background-color:#000000 !important">
                <h5 class="modal-title font-weight-bold" id="ModalLabel">
                    <i class="mdi mdi-calendar-clock mr-2"></i>Add Date Wise User View Access
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" onclick="refreshDateWiseUserAccess()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="forms-sample" enctype="multipart/form-data" action=''method="post" id="formUserViewSubmit">
                <div class="modal-body p-4">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" id="user_id" name="user_id" value="{{ $id }}">


                    <div class="form-group">
                        <label for="recipient-name" class="mb-0">
                            Permission
                            <span class="text-danger">*</span>
                        </label>
                        <select class="form-control js-example-basic-multiple" name="type[]" id="user_view_permission" multiple>

                            @forelse(Common::staticDateWiseAgencyAccess() as $key=>$permission)
                                <option value="{{ $key }}">{{ $permission }}</option>
                            @empty
                                <option value="">No permissions available</option>
                            @endforelse
                        </select>
                        <span id="agency_view_permission_error" class="error error_date_html text-danger d-block"></span>
                    </div>

                    <div class="form-group">
                        <label for="recipient-name" class="mb-0">
                            Start Date
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text"  class="form-control form-control-sm datepicker" autocomplete="off" placeholder="Select Start Date" id="permission_start_date" name="start_date" value="">
                        <span id="start_date_error" class="error error_date_html text-danger d-block"></span>
                    </div>
                    <div class="form-group">
                        <label for="recipient-name" class="mb-0">
                            End Date
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control form-control-sm datepicker" autocomplete="off" placeholder="Select End Date" id="permission_end_date" name="end_date" value="" >
                        <span id="end_date_error" class="error error_date_html text-danger d-block"></span>
                    </div>
                </div>
                <div class="modal-footer border-top-0 bg-light">
                    <div class="d-flex justify-content-end align-items-center w-100">
                        <button type="button" id="submit_view_user_access" class="btn btn-success btn-sm px-4 mr-2">
                        <span class="spinner-border spinner-border-sm d-none" id="create-date-wise-loader" role="status" aria-hidden="true"></span>
                            <span id="btn-save-text-date-wise">Save</span>
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm px-4" data-dismiss="modal">
                            Cancel
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>