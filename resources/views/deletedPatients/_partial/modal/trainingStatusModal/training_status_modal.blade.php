
<div class="modal fade" id="exampleModal-training_status" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel">Training Status</h5>
                    <button type="button" class="close" data-dismiss="modal" onclick="CloseTrainingStatus()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="lnddkhhxs_alaycare_id">
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Training Status: <span class="error">*</span></label>
                            <select name="training_status" class="form-control" id="training_status">
                                <option value="">Select Status</option>
                                @foreach($masterData as $hms)
                                  @if($hms->master_type_fk ==25)
                                  <option value="{{ $hms->name}}" @if($record->training_status ==$hms->name) selected @endif>{{ $hms->name}}</option>
                                  @endif
                                @endforeach
                            </select>
                            <!-- <input placeholder="Training Status"   name="training_status" class="form-control" id="training_status" value="{{ $record->training_status}}">  -->
                            <span class="error training_status_error"></span>
                        </div>
                    </form> 
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" id="update-training-status" >Save</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal" onclick="CloseTrainingStatus()">Close</button>
                    </div>

                </div>
            </div>
        </div>

    </div>