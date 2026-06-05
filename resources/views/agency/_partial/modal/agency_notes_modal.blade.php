{{-- Agency Notes Add Modal --}}
<div class="modal fade" id="agencyNoteModal" tabindex="-1" role="dialog" aria-labelledby="agencyNoteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background:#1a1a2e;">
                <h5 class="modal-title text-white" id="agencyNoteModalLabel"><i class="mdi mdi-note-plus"></i> Add Agency Note</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Note Type <span class="text-danger">*</span></label>
                    <select class="form-control" id="agency_note_type">
                        <option value="info">Info (Blue) — General information</option>
                        <option value="warning">Warning (Yellow) — Caution / take note</option>
                        <option value="danger">Alert (Red) — Critical / do not ignore</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Note <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="agency_note_text" rows="4" maxlength="1000" placeholder="Enter note about this agency..."></textarea>
                    <small class="text-muted">Max 1000 characters</small>
                    <div class="text-danger mt-1" id="agency_note_error"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="saveAgencyNoteBtn" onclick="saveAgencyNote()">
                    <i class="mdi mdi-content-save"></i> Save Note
                </button>
                <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
