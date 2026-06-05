<div class="modal fade" id="document_type" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" style="display: none;" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Document Type</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            <form action="<?php echo URL::to('/'); ?>/documentInsert" method="post" id="submitid">
                <div class="modal-body">

                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    <div class="form-group">
                        <label>Name <span style="color:red;">*</span></label>
                        <input type="text" name="document_name" value="" placeHolder="Enter Name" class="form-control"
                            id="name_id">
                        <span class="error" id="name_error"></span>
                    </div>

                </div>
                <div class="modal-footer">
                <button type="submit" class="btn btn-primary cust-right-btn btn-sm">Save</button>
                    <button type="button" class="btn btn-secondary pull-left cust-right-btn btn-sm" data-dismiss="modal">Close</button>
                    
                </div>
            </form>
        </div>
    </div>
</div>