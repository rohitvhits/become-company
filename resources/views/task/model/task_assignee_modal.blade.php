<div class="modal fade modal-container" id="task_assignee_modal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Assignee <span class="" style="font-size: 18px;"><span id="task_id"></span></h4>
                <div class="modal-close-wrapper ml-4">
                    <button type="button" class="close form-clear url-clear view_task_modal_close" data-dismiss="modal" aria-label="Close" style="margin: unset;">
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="#fff" xmlns="http://www.w3.org/2000/svg">
                            <path d="M13.3 0.709956C13.1131 0.522704 12.8595 0.417471 12.595 0.417471C12.3305 0.417471 12.0768 0.522704 11.89 0.709956L6.99997 5.58996L2.10997 0.699956C1.92314 0.512704 1.66949 0.407471 1.40497 0.407471C1.14045 0.407471 0.886802 0.512704 0.699971 0.699956C0.309971 1.08996 0.309971 1.71996 0.699971 2.10996L5.58997 6.99996L0.699971 11.89C0.309971 12.28 0.309971 12.91 0.699971 13.3C1.08997 13.69 1.71997 13.69 2.10997 13.3L6.99997 8.40996L11.89 13.3C12.28 13.69 12.91 13.69 13.3 13.3C13.69 12.91 13.69 12.28 13.3 11.89L8.40997 6.99996L13.3 2.10996C13.68 1.72996 13.68 1.08996 13.3 0.709956Z"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="modal-body position-relative">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="field-label" for="due_date">Assignee<span class="text-danger">*</span></label>
                            <select name="assign_to_user_select" id="assign_to_user_select" placeholder="Select Assignee" class="form-control select2">
                                @if(!empty($user_list[0])) @foreach($user_list as $va)
                                <option value="{{$va->id}}">{{$va->name}}</option>
                                @endforeach @endif
                            </select>
                            <span id="assign_user_id_error" class="error"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick="assignUserById();">Update</button>
                <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>