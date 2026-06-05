

<div class="modal fade" id="show-create-patient-document-modal" aria-modal="true" role="dialog" style="padding-right: 17px; display: none;">
    <div class="modal-dialog" style="margin-top:10px">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Create Document</h4>
                <button type="button" class="close" onclick="clearpatientDocData();" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="javascript:voide(0)" method="POST" id="add_patient_doc">
                <input type="hidden" name="_token" value="{{ csrf_token()}}">
                <input type="hidden" name="id" id="patient_id" value="">
                <div class="modal-body">
                    <div class="form-group row">
                        <div class="col-sm-6">
                            <label class="col-form-label">Document Type Id <span class="text-danger">*</span></label>
                            <select type="text" onchange="getHHAdocumentData();" id="patient_document_type_id" name="patient_document_type_id" class="form-control">
                                <option value=""> Select Document Type</option> 
                            </select>
                            <span id="patient_document_type_id_error" class="text-danger"></span>
                        </div>

                        <div class="col-sm-6">
                            <label class="col-form-label">File Name</label>
                            <input type="text" class="form-control" name="patient_file_name" value="" id="patient_file_name" />
                            <span id="patient_file_name_error" class="text-danger"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">Description <span class="text-danger">*</span></label>
                        <textarea rows="4" cols="50" class="form-control" name="patient_description" value="" id="patient_description"></textarea>
                        <span id="patient_description_error" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">File Stream</label>
                        <textarea rows="4" cols="50" class="form-control" name="patient_file_stream" value="" id="patient_file_stream"></textarea>
                        <span id="patient_file_stream_error" class="text-danger"></span>
                        <p class="text-muted mb-0 tx-12">BASE64 String Required</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="loader-inner d-none">
                        <img src="{{ asset('/ajax-loader.gif') }}" class="" alt="loader">
                    </div>
                    <button type="button" onclick="clearPatientDocData();" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="savePatientDocument">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
