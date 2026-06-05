<div class="modal fade" id="caResolveModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document" style="max-width:440px;">
        <div class="modal-content" style="border:none;border-radius:8px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.2);">
            <div class="modal-header ca-modal-header-green" style="border-bottom:none;padding:13px 20px;">
                <h5 class="modal-title" style="font-size:15px;font-weight:600;color:#fff;display:flex;align-items:center;gap:8px;">
                    <i class="mdi mdi-check-circle"></i> Mark as Resolved
                </h5>
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:.8;">&times;</button>
            </div>
            <div class="modal-body" style="padding:20px;">
                <input type="hidden" id="ca-resolve-id">
                <div class="form-group mb-0">
                    <label style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.3px;color:#505a65;margin-bottom:4px;display:block;">
                        Resolution Notes <span style="color:#9ca3af;font-weight:400;text-transform:none;">(optional)</span>
                    </label>
                    <textarea id="ca-resolve-notes" rows="4" class="form-control" style="resize:vertical;font-size:13px;"
                              placeholder="Enter any notes about how this alert was resolved…"></textarea>
                </div>
            </div>
            <div class="modal-footer" style="padding:10px 20px;background:#fff;border-top:1px solid #dee2e6;">
                <button type="button" id="ca-resolve-save-btn" class="btn btn-success btn-sm">
                    <i class="mdi mdi-check"></i> Save
                </button>
                <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>