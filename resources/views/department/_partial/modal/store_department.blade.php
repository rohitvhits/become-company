<div class="modal fade" id="departmentModal" tabindex="-1" role="dialog" aria-labelledby="departmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" style="width:900px !important">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color:#0f0f17 !important">
                <h5 class="modal-title" id="departmentModalLabel">Add Department</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="color: white !important;">&times;</span>
                </button>
            </div>
            <form id="departmentForm">
                @csrf
                <input type="hidden" id="department_id" name="department_id" value="">

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Department Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter department name">
                                <div class="invalid-feedback" id="name-error"></div>
                            </div>

                            <div class="form-group">
                                <label for="user_select">Select User to Add <span class="text-danger">*</span></label>
                                <input type="text" name="user_select" id="user_select">
                                <input type="hidden" name="user_select_id" id="user_select_id">
                                <input type="hidden" name="user_select_name" id="user_select_name">
                                <div class="invalid-feedback" id="user-error"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="table-responsive" style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; border-radius: 4px;">
                                    <table class="table table-sm table-bordered mb-0" id="assignedUsersTable">
                                        <thead class="thead-light">
                                            <tr>
                                                <th width="5%">#</th>
                                                <th>Name</th>
                                                <th width="15%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="assignedUsersBody">
                                            <tr id="noUsersRow">
                                                <td colspan="3" class="text-center text-muted">No users assigned</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 bg-light">
                    <div class="d-flex justify-content-end align-items-center w-100">
                        <button type="submit" class="btn btn-success btn-sm px-4 mr-2" id="submitBtn">
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            <span class="btn-text submit-text">Save</span>
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm px-4" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>