<div class="modal fade" id="add-notes" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title documens" id="ModalLabel">Add Notes</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeHubDoc()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="forms-sample" enctype="multipart/form-data" action="" name="adduser" method="post" id="formnew">
                    <div class="modal-body">

                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                        <input type="hidden" name="id" value="<?php echo $record->id; ?>">
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Subject Name<span class="error">*</span>:</label>
                            <select class="form-control" id="subjectNotesId" name="subjectNotesId">
                                    <option value="">Select Subject</option>
                                    @foreach($masterSubjectData as $master)
                                        <option value="{{$master->id}}">{{$master->name}}</option>
                                    @endforeach
                                </select>
                            <span id="subject_name_error" class="error mt-2" for="subject_name"></span>
                        </div>
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Notes<span class="error">*</span>:</label>
                            <span class="input-box notes_content">
                                <textarea name="msg-box" id="text-sms-box" class="tribute-demo-input form-control text-share h-25" style="min-height:50px" rows="10"></textarea>
                                <div id="suggestions-container"></div>
                                <input type="hidden" name="selectedEmail" id="selectedEmail">
                            </span>
                            <span id="notes_error_msg" class="text-danger"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" id="notesSave" onclick="sendMessagefile()">Save</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal" onclick="closeNotes()">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>