<div class="modal fade" id="hub_company_generate_token" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
<div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title notification-emails" id="ModalLabel">Generate Token</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="forms-sample" enctype="multipart/form-data" action="" method="post" id="hubFormGenerateSubmit">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" id="agency_id" name="agency_id" value="{{$id}}">
                   
                   
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Block IP Address (commas seperate)</label>
                        <textarea name="ip_block" class="form-control" id="ip_block"></textarea>
                        <span id="ip_block_error" class="error mt-2" for="document_type"></span>
                    </div>
                    
                    
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="save-hub-company-token" class="btn btn-success">Save</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>