<div class="modal fade" id="convertModal" tabindex="-1" role="dialog" aria-labelledby="convertModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="convertModalLabel">Convert Task Health Log</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="convert_log_id" value="">
                <div class="form-group">
                    <label for="convert_agency_id">Agency Name <span class="text-danger">*</span></label>
                    <select class="form-control select2-design" name="convert_agency_id" id="convert_agency_id">
                        <option value="">Select Agency</option>
                        @foreach($agencyList as $agency)
                            <option value="{{ $agency->id }}">{{ $agency->agency_name }}</option>
                        @endforeach
                    </select>
                    <span class="text-danger" id="convert_agency_error"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="convertSubmitBtn" onclick="submitConvertLog()">Convert</button>
            </div>
        </div>
    </div>
</div>
