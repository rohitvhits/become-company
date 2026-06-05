<div class="modal fade" id="hub_edit_modal" aria-modal="true" role="dialog" style="padding-right: 17px; display: none;">
    <div class="modal-dialog modal-lg modal-dialog-centered" style="margin-top:10px">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit dependents</h4>
                <button onclick="clearModal()" type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="javascript:voide(0)" method="POST" id="edit_new_hub">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="modal-body">

                    <div class="row">
                        <input type="hidden" id="edit_agency_id" name="agency_id" value="{{ $record->agency_id }}">
                        <input type="hidden" id="dependent_id" name="dependent_id" value="">


                        <div class="col-md-4">
                            <div class="form-group  mb-2">
                                <label class="mb-0">First Name<span class="text-danger mt-2">*</span></label>
                                <input type="text" class="form-control charCls form-control-sm"
                                    placeholder="Enter First Name " id="edit_dep_first_name" name="first_name"
                                    value="">
                                <span id="edit_dep_first_name_error" class="error mt-2 error_html"></span>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group mb-2">
                                <label class="mb-0">Last Name<span class="text-danger mt-2">*</span></label>

                                <input type="text" class="form-control charCls form-control-sm"
                                    placeholder="Enter Last Name " id="edit_dep_last_name_id" name="last_name"
                                    value="<?php echo old('last_name'); ?>">
                                <span id="edit_dep_last_name_error" class="error mt-2 error_html"></span>

                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group  mb-2">
                                <label class="mb-0">Email</label>

                                <input type="text" class="form-control form-control-sm" placeholder="Enter Email "
                                    id="edit_dep_email" name="email" value="<?php echo old('email'); ?>">
                                <span id="edit_dep_email_error" class="error mt-2 error_html"></span>

                            </div>
                        </div>
                        <div class="col-md-4 ">
                            <div class="form-group mb-2">
                                <label class="mb-0">Date of Birth<span class="text-danger mt-2">*</span></label>

                                <input type="text"  class="form-control bill_date form-control-sm " autocomplete="off" placeholder="Enter Date of Birth" id="edit_dep_dob_id" name="dob" data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" im-insert="false" value="<?php echo old('dob'); ?>">
                                <span id="edit_dep_dob_error" class="error mt-2 error_html"></span>

                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-2">
                                <label class="mb-0">Mobile<span class="text-danger mt-2">*</span></label>
                                <input type="text" class="form-control form-control-sm" placeholder="Enter Mobile"
                                    id="edit_dep_mobile_no" onkeypress="return isNumber(event)" name="mobile"
                                    value="<?php echo old('mobile'); ?>" data-inputmask-alias="(999) 999-9999" maxlength="15">
                                <span id="edit_dep_mobile_error" class="error mt-2 error_html"></span>

                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-2">
                                <label class="mb-0">Phone</label>

                                <input type="text" class="form-control form-control-sm" placeholder="Enter Phone"
                                    id="edit_dep_phone" onkeypress="return isNumber(event)" name="phone"
                                    value="<?php echo old('phone'); ?>" data-inputmask-alias="(999) 999-9999" maxlength="15">
                                <span id="edit_dep_phone_error" class="error mt-2 error_html"></span>

                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group mb-2">
                                <label class="mb-0">SSN<span class="text-danger mt-2">*</span></label>
                                <input type="text" class="form-control form-control-sm" placeholder="Enter SSN"
                                    id="edit_dep_ssn" name="ssn" value="<?php echo old('ssn'); ?>">
                                <span id="edit_dep_ssn_error" class="error mt-2 error_html"></span>

                            </div>
                        </div>

                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="updateHub">Update dependents</button>
                    <button type="button" onclick="clearModal()" class="btn btn-secondary"
                        data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
