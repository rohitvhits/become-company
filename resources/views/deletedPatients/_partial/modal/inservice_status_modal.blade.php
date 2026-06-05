<div class="modal fade" id="exampleModal-inservice_status" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel">Inservice Status</h5>
                    <button type="button" class="close" data-dismiss="modal" onclick="CloseInserviceStatus()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="lnddkhhx_alaycare_id">
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Inservice Status: <span class="error">*</span></label>
                            <select name="inservice_status" class="form-control" id="inservice_status">
                                    <option value="">Select Inservice Status</option>
                                    <option value="Completed" @if(isset($record->inservice_status) && $record->inservice_status=='Completed') selected @endif>Completed</option>
                                    <option value="Processing" @if(isset($record->inservice_status) && $record->inservice_status=='Processing') selected @endif>Processing</option>
                                    <option value="Refused"  @if(isset($record->inservice_status) && $record->inservice_status=='Refused') selected @endif>Refused</option>
                                    <option value="Unable To Reach"  @if(isset($record->inservice_status) && $record->inservice_status=='Unable To Reach') selected @endif>Unable To Reach</option>
                                    <option value="Need To Assistance"  @if(isset($record->inservice_status) && $record->inservice_status=='Need To Assistance') selected @endif>Need To Assistance</option>
                                    <option value="3 Hours"  @if(isset($record->inservice_status) && $record->inservice_status=='3 Hours') selected @endif>3 Hours</option>
                                    <option value="6 Hours"  @if(isset($record->inservice_status) && $record->inservice_status=='6 Hours') selected @endif>6 Hours</option>
                                    <option value="12 Hours"  @if(isset($record->inservice_status) && $record->inservice_status=='12 Hours') selected @endif>12 Hours</option>
                            </select>
                            <span class="error inservice_status_error"></span>
                        </div>
                    </form> 
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" id="update-inservice-status" >Save</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal" onclick="CloseInserviceStatus()">Close</button>
                    </div>

                </div>
            </div>
        </div>

    </div>
