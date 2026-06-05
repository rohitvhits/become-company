<div class="modal fade" id="add-payment-data" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel"><span style="text-transform:capitalize"></span> Add Payment Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-5">

                        <div class="form-group mb-0">
                            <label for="recipient-name" class="col-form-label">Request Service<span class="error">*</span>:</label>
                            <select class="js-example-basic-single w-100 select2-design" name="pay_request_service_id" id="pay_request_service_id">
                                <option value="">Select Request Service</option>
                            </select>
                            <span id="add_service_req_error" class="error mt-2"></span>
                        </div>
                        
                        <div class="form-group mb-0">
                            <label for="recipient-name" class="col-form-label">Select Service<span class="error">*</span>:</label>
                            <select class="js-example-basic-multiple w-100 select2-design" multiple="multiple" name="pay_service_id" id="pay_service_id" onchange="showInvoiceDetails();">
                                <option value="">Select Service</option>
                            </select>
                            <span id="add_service_error" class="error mt-2"></span>
                        </div>
                        
                        <div class="form-group mb-0">
                            <input type="hidden" name="add_payment_id" id="add_payment_id">
                            <label for="recipient-name" class="col-form-label">Payment Type<span class="error">*</span>:</label>
                            <select class="form-control" name="add_status_payment_type" id="add_status_payment_type" onchange="addHandlePaymentChange(this)">
                                <option value="">Select Payment Type</option>
                                @if (count($masterData) > 0)
                                    @foreach ($masterData as $master)
                                        @if ($master->master_type_fk == 17)
                                            <option value="{{ $master->id }}">{{ $master->name }}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                            <span id="add_payment_type_error" class="error mt-2"></span>
                        </div>

                        <div class="form-group mb-0">
                            <label for="recipient-name" class="col-form-label">Location<span class="error">*</span>:</label>
                            <select class="form-control" name="add_status_location_id" id="add_status_location_id">
                                <option value="">Select Location</option>
                                @if (count($location_list) > 0)
                                @foreach ($location_list as $loc)
                                <option value="{{ $loc->id }}">{{ $loc->location_name }}</option>
                                @endforeach
                                @endif
                            </select>
                            <span id="add_status_location_error" class="error mt-2"></span>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="amount_document_div"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="add_payment">Save</button>
                <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>