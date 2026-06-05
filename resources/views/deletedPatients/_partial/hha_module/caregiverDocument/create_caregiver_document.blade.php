

<div class="modal fade" id="show-create-caregiver-document-modal" aria-modal="true" role="dialog" style="padding-right: 17px; display: none;">
    <div class="modal-dialog" style="margin-top:10px">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Create Document</h4>
                <button type="button" class="close" onclick="clearCaregiverDocData();" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="javascript:voide(0)" method="POST" id="add_caregiver_doc">
                <input type="hidden" name="_token" value="{{ csrf_token()}}">
                <input type="hidden" name="id" id="caregiver_id" value="">
                <input type="hidden" name="doc_hha_patient_id" value="{{ $record->id??''}}">
                <input type="hidden" name="doc_hha_patient_type" value="{{ $record->type??''}}">
                <div class="modal-body">
                    <div class="form-group row">
                        <div class="col-sm-6">
                            <label class="col-form-label">Document Type Id <span class="text-danger">*</span></label>
                            <select type="text" onchange="getHHAdocumentData();" id="document_type_id" name="document_type_id" class="form-control">
                                <option value=""> Select Document Type</option> 
                            </select>
                            <span id="document_type_id_error" class="text-danger"></span>
                        </div>

                        <div class="col-sm-6">
                            <label class="col-form-label">File Name</label>
                            <input type="text" class="form-control" name="file_name" value="" id="file_name" />
                            <span id="file_name_error" class="text-danger"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">Description <span class="text-danger">*</span></label>
                        <textarea rows="4" cols="50" class="form-control" name="description" value="" id="description"></textarea>
                        <span id="description_error" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">File Stream</label>
                        <textarea rows="4" cols="50" class="form-control" name="file_stream" value="" id="file_stream"></textarea>
                        <span id="file_stream_error" class="text-danger"></span>
                        <p class="text-muted mb-0 tx-12">BASE64 String Required</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="loader-inner d-none">
                        <img src="{{ asset('/ajax-loader.gif') }}" class="" alt="loader">
                    </div>
                    <button type="button" onclick="clearCaregiverDocData();" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveCaregiverDocument">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
