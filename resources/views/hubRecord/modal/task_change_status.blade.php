  <div class="modal fade" id="exampleModal-change-task-staus" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel">Change Status</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="forms-sample" enctype="multipart/form-data" action="{{url('task-change-status')}}"
                        name="adduser" method="post" id="task_form">
                        <input type="hidden" name="_token" value="{{csrf_token()}}">
                        <input type="hidden" name="id" id="edit_id" value="">
                        <input type="hidden" name="recordId" id="recordId" value="{{Request()->id}}">
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Status<span
                                    style="color:red">*</span>:</label>
                            <select name="status" class="form-control" id="status_id">
                                <option value="">Select Status</option>
                                <option value="Urgent">Urgent</option>
                                <option value="Outstanding">Outstanding</option>
                                <option value="Pending">Pending</option>
                                <option value="Completed">Completed</option>
                            </select>
                            <span id="task_status_error" class="error mt-2" for="document_type"></span>
                        </div>
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Notes:</label>
                            <textarea class="form-control" type="text" class="form-control" name="task_description"
                                placeholder="Enter Task Description" id="task_description" rows="4"
                                cols="50"></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" onclick="getTaskChangeStatus()" class="btn btn-success">Save</button>
                            <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>