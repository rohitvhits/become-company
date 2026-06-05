<div class="modal fade" id="exampleModal-languages" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Change Language</h5>
                <button type="button" class="close" id="close_language" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <form class="forms-sample" enctype="multipart/form-data" name="adduser" method="post" id="language_form_submit_id">
                <div class="modal-body">
                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                    <input type="hidden" name="recordId" id="recordId" value="{{$record->id}}">
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Language<span style="color:red">*</span>:</label>
                            <select name="language_id" class="form-control" id="language_id">
                                <option value="">Select Language</option>
                                @foreach($language_list as $lang)
                                <option value="{{ $lang->id}}">{{ $lang->name}}</option>
                                @endforeach
                        </select>
                        <span id="language_error" class="error mt-2" for="document_type"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="updatePatientLanguage()" class="btn btn-success">Save</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                </div>
            </form>
            
        </div>
    </div>
</div>