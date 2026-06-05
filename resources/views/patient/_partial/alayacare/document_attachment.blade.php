<div class="tab-pane" id="alaycare-document-attachment">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <p class="card-title mb-0">Documents / Attachments</p>
        <p class="mb-0 tx-13">
        <!-- <a href="javascript:void(0)" class="btn btn-primary" data-target="#exampleModal-alaya-document" data-toggle="modal" data-whatever="@mdo">Add Document</a> -->
        </p>
    </div>
    <div class="row">
        
        <div class="col-12">
            <div class="loader-main" id="loaderAlayaAttachmentList" style="display:none">
                <div class="loader-inner">
                <img src="{{ asset('/ajax-loader.gif') }}" class="" alt="loader" >
                </div>
            </div>
            <table class="table table-bordered" >
                <thead>
                    <th>No</th>
                    <th>Name</th>
                    <th>Created Date</th>
                    <th>Action</th>
                    
                    
                </thead>
                <tbody id="alayacare_document_list">

                </tbody>
            </table>
        </div>
        <div class="col-md-12 mt-3" id="pagin">
        <a class="pull-right btn btn-primary btn-rounded  btn-sm" href="javascript:void(0)" id="nextVisitId" style="display:none"   onClick="nextVisit()">Next</a></li>
            <a class="pull-right btn btn-secondary btn-rounded  btn-sm" href="javascript:void(0)" id="previousVisitId" style="display:none" onClick="previousVisit()">Prev</a></li>
            
        </div>

    </div>

</div>

<div class="modal fade" id="exampleModal-alaya-document" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Add Document/Attachment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="clearEmployeeDocument()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="alayacare_document_upload_id">
                <div class="modal-body">
                    <div class="row col-md-6">
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Document <span class="error">*</span>:</label>
                            <input type="file" name="alaya_document">
                            <span class="error" id="alaya_document_error"></span>
                        </div>
                    </div>
                    
                    
                </div>
            </form>

            <div class="modal-footer">
            <img src="{{ asset('/ajax-loader.gif') }}" class="" alt="loader" id="loaderAlayaDocumentSubmit" style="display: none; ">
           
            <button type="button" class="btn btn-primary" onclick="submitEmployeeDocument()">Submit</button>
                <button type="button" class="btn btn-light" data-dismiss="modal" onclick="clearEmployeeDocument()">Close</button>
            </div>

          
        </div>
    </div>
</div>