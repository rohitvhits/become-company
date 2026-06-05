<div class="modal fade" id="exampleModal-create-mq-order" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel"><span id="update_mq_order_text" style="text-transform:capitalize">Create MD Order</span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <form id="mq_order_form_submit_id">
                        <input type="hidden" id="mq_order_id" >
                        <input type="hidden" name="patient_mq_order_document_id" id="mq_order_document_id_hidden" >
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Start Date<span class="error">*</span>:</label>
                            <input  type="text" name="mq_order_start_date" class="form-control" autocomplete="off" id="mq_order_start_date" placeholder="Please select Start Date"  data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" >
                            <span id="mq_order_start_date_error" class="error"></span>
                        </div>
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">End Date<span class="error">*</span>:</label>
                            <input  type="text" name="mq_order_end_date" class="form-control" autocomplete="off" id="mq_order_end_date" placeholder="Please select End Date"  data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" >
                            <span id="mq_order_end_date_error" class="error"></span>
                        </div>
                        <div class="form-group" id="document-div">
                            <label for="recipient-name" class="col-form-label">Document<span class="error">*</span>:</label>
                            <select class="form-control mq_order_document_id" id="mq_order_document_id" name="mq_order_document_id">
                                <option value="">Select Document</option>
                            
                            </select>
                            <span id="mq_order_document_error" class="error"></span>
                        </div>

                        <small style="display: block; margin-top: 8px; padding: 8px 10px; background: #e7f3ff; border-left: 3px solid #0d6efd; color: #084298; font-size: 13px;">
                            <i class="fa fa-info-circle" style="color: #0d6efd;"></i> <strong>Note:</strong> Automated email notifications are sent to configured users when an MDO is created.
                        </small>
                    </form>
                </div>
                <div class="modal-footer">
                    <img src="{{ asset('/ajax-loader.gif') }}" id="show_create_mq_order_loader" class="hide" alt="loader">
                
                    <button type="button" id="button_mq_orderId" class="btn btn-success" onclick="storeMDOrders()">Save</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>