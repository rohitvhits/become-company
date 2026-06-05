<div class="modal fade " id="exampleModal-51" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel"><span id="Commsas" style="text-transform:capitalize"></span> Reminder Section</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="closed_id">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="post" id="reminder_id">
                    @csrf
                    <input type="hidden" name="patient_id" value="<?php echo $record->id; ?>">
                    <div class="modal-body">


                        <div class="form-group" style="margin-bottom:0px !important">
                            <label for="recipient-name" class="col-form-label">Email<span class="error">*</span>:</label>
                            <input type="text" name="email" class="form-control" id="remail" autocomplete="off">
                            <span id="remail_status_error" class="error"></span>
                        </div>
                        <div class="form-group" style="margin-bottom:0px !important">
                            <label for="recipient-name" class="col-form-label">Mobile:</label>
                            <input type="text" name="mobile" class="form-control" id="rmobile" onkeypress="return isNumber(event)" autocomplete="off">
                            <span id="mobile_status_error" class="error"></span>
                        </div>
                        <div class="form-group" style="margin-bottom:0px !important">
                            <label for="recipient-name" class="col-form-label">Notes<span class="error">*</span>:</label>
                            <textarea name="notes" id="rnotes" class="form-control"></textarea>
                            <span id="rnotes_status_error" class="error"></span>
                        </div>
                        <div class="form-group" style="margin-bottom:0px !important;margin-left:-10px">
                            <label class="col-sm-3 col-form-label">Type<span class="error mt-2 text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="radio" name="rtype" value="EveryDate" onclick="getResponse('EveryDate')"> On Date
                                <input type="radio" name="rtype" value="EveryMonth" onclick="getResponse('EveryMonth')"> Every Month<br>
                                <span id="rtype_error" class="error"></span>

                            </div>
                        </div>
                        <div class="form-group" id="dates_id" style="display:none">
                            <label class="col-sm-3 col-form-label">Date<span class="error mt-2 text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" name="date" id="rdates" class="form-control" autocomplete="off">
                                <span id="rdate_error" class="error"></span>

                            </div>
                        </div>
                        <div class="form-group" id="month_id" style="display:none">
                            <label class="col-sm-3 col-form-label">Month<span class="error mt-2 text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select name="every_month" class="form-control" id="rmonth" onchange="getConvertDate(this.value)">
                                    <option value="">Select Month</option>
                                    <option value="1">Every Month</option>
                                    <option value="3">3 Month</option>
                                    <option value="6">6 Month</option>
                                    <option value="12">Every Year</option>

                                </select>
                                <span id="every_month_error" class="error"></span>

                            </div>
                            <p class="mb-0 text-success font-weight-bold test_id append_id" style="margin-left:10px">Tester</p>
                        </div>



                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" onclick="getReminder()">Save</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                    </div>
                </form>

            </div>
        </div>
    </div>