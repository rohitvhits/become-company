<div class="modal fade" id="hub-nybest-add" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="nybest">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title documens" id="ModalLabel">Add NyBest Medical Request</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeHubDoc()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="forms-sample" enctype="multipart/form-data" name="adduser" method="post" id="formnewNybest">
                <div class="modal-body">

                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">

                    <div class="form-group" style="display: flex;">
                        <label for="recipient-name" class="col-form-label">Type<span class="error">*</span>:</label>
                        <div style="margin-top: 8px;margin-left: 8px;">
                            <input type="radio" checked="checked" name="type" value="Patient"> Patient
                            &nbsp;
                            <input type="radio" name="type" value="Caregiver">Caregiver
                        </div>
                        <span id="nybest_type_error" class="error mt-2" for="type"></span>
                    </div>
                    <div class="form-group image-cls">
                        <label for="message-text" class="col-form-label">Agency<span class="error">*</span>:</label>
                        <select name="agency" id="nybest_agency">

                        </select>
                        <span class="error mt-2" id="agency_error" for="agency_name"></span>
                    </div>
                    <div class="form-group image-cls">
                        <label for="message-text" class="col-form-label">Services<span class="error">*</span>:</label>
                        <select name="service[]" id="nyservice" multiple="multiple"
                            class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100"
                            style="    width: 100% !important;"></select>
                        <span class="error mt-2" id="service_error" for="service_name"></span>
                    </div>
                    <div class="form-group image-cls">
                        <label for="message-text" class="col-form-label">Booking Date<span
                                class="error">*</span>:</label>
                        <input type="text" class="form-control booking_date" readonly id="booking_date"
                            name="booking_date">
                        <span class="error mt-2 text-danger" id="booking_date_error"></span>
                    </div>
                    <div class="form-group image-cls">
                        <label for="message-text" class="col-form-label">Remarks<span class="error"></span>:</label>
                        <textarea class="form-control" id="hubremarks" name="remarks"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="nybestSave"
                        onclick="saveHubNybest()">Save</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal"
                        onclick="closeHubDoc()">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
