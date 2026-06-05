<div class="modal fade" id="assign-dept-modal" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header py-2 px-3">
          <h6 class="modal-title" id="ModalLabel">Assign Department</h6>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="close_assign_dept_id">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body py-2 px-3">
          <div class="form-group">
              <label for="assign_dept" class="small mb-1">Department:</label>
              <select class="form-control assign_dept" id="assign_dept">
                  <option value="">Select Department</option>
              </select>
              <span class="error assign_dept_error"></span>
          </div>
        </div>
        <div class="modal-footer py-2 px-3">
          <button type="button" class="btn btn-success btn-sm" onclick="updatePortalDepartment()">Save</button>
          <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Close</button>
        </div>
      
    </div>
  </div>
</div>