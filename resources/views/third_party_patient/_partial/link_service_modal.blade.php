<div class="modal fade" id="show_link_service" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Link Service Request</h5>
                <button type="button" class="close" id="close-modal" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form class="forms-sample" enctype="multipart/form-data" action='' method="post" id="form_link_service_request">
                <div class="modal-body">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="patient_id" id="patient_id" value="">
                    <input type="hidden" name="third_party_id" id="third_party_id" value="">
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Request Services<span class="error">*</span>:</label>
                        <select class="form-control select2 w-100" name="request_service_id" id="request_service_id" style="width:100%">
                        </select>
                        <span class="request_service_id_error error"></span> 
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" id="linkToServiceRequestModal">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>