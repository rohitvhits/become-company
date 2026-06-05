<div class="modal fade" id="exampleModal-assign" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel"><span id="" style="text-transform:capitalize"></span>Assign NyBest User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Assign NyBest User<span class="error">*</span>:</label>
                        <select name="assign_nybest_user" class="form-control" id="assign_nybest_user">
                            <option value="">Select Assign NyBest User</option>
                            @if (!empty($assign_user_list[0]))
                            @foreach ($assign_user_list as $val)
                            <option value="{{ $val->id }}" @if ($val->id == $record->assign_user_id) selected='selected' @endif>
                                {{ $val->name }}
                            </option>
                            @endforeach
                            @endif

                        </select>
                        <span id="assign_nybest_user_error" class="error"></span>
                    </div>
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Notes:</label>
                        <textarea name="notes" class="form-control" rows="4" cols="50" id="notes_ny_id"></textarea>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" onclick="getNyBestUpdate()">Save</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                    </div>

                </div>
            </div>
        </div>
    </div>