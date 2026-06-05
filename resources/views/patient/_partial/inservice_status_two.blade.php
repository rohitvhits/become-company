<div class="modal fade" id="exampleModal-inservice_status_two" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Inservice Status Second</h5>
                <button type="button" class="close" data-dismiss="modal" onclick="CloseInserviceStatus()" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="lnddkhhx_alaycare_id_two">

                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Inservice Status: <span class="error">*</span></label>
                        <select name="inservice_status_two" class="form-control" id="inservice_status_two">
                            <option value="">Select Inservice Status</option>
                            <option value="Completed" @if(isset($record->inservice_status_two) && $record->inservice_status_two=='Completed') selected @endif>Completed</option>
                            <option value="Processing" @if(isset($record->inservice_status_two) && $record->inservice_status_two=='Processing') selected @endif>Processing</option>
                            <option value="Refused" @if(isset($record->inservice_status_two) && $record->inservice_status_two=='Refused') selected @endif>Refused</option>
                            <option value="Unable To Reach" @if(isset($record->inservice_status_two) && $record->inservice_status_two=='Unable To Reach') selected @endif>Unable To Reach</option>
                            <option value="Need To Assistance" @if(isset($record->inservice_status_two) && $record->inservice_status_two=='Need To Assistance') selected @endif>Need To Assistance</option>
                            @if($record->agency_id !='106' && $record->agency_id !='319' )
                                <option value="1 Hour"  @if(isset($record->inservice_status) && $record->inservice_status=='1 Hour') selected @endif>1 Hour</option>
                            @endif
                            <option value="3 Hours" @if(isset($record->inservice_status_two) && $record->inservice_status_two=='3 Hours') selected @endif>3 Hours</option>
                            <option value="6 Hours" @if(isset($record->inservice_status_two) && $record->inservice_status_two=='6 Hours') selected @endif>6 Hours</option>
                            @if($record->agency_id !='106' && $record->agency_id !='319' )
                                    <option value="7 Hours"  @if(isset($record->inservice_status) && $record->inservice_status=='7 Hours') selected @endif>7 Hours</option>
                                    @endif
                            <option value="12 Hours" @if(isset($record->inservice_status_two) && $record->inservice_status_two=='12 Hours') selected @endif>12 Hours</option>
                        </select>
                        <span class="error inservice_status_two_error"></span>
                    </div>
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="update-inservice-status-two">Save</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal" onclick="CloseInserviceStatusTwo()">Close</button>
                </div>

            </div>
        </div>
    </div>

</div>