{{-- HHA Upload Confirmation Modal — must render above the vd-overlay (z-index 1055) --}}
<style>
    #vdHhaUploadModal         { z-index: 1100 !important; }
    /* #vdHhaUploadModal + .modal-backdrop,
    .modal-backdrop.show:last-of-type { z-index: 1090 !important; } */
</style>
<div class="modal fade" id="vdHhaUploadModal" tabindex="-1" role="dialog" aria-hidden="true" style="z-index:1100;">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="mdi mdi-hospital-building" style="color:#1a73e8;"></i> Confirm Upload to HHA</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-3" style="font-size:13px;">
                    Are you sure you want to send the following document to the HHA patient below?
                </p>
                <table class="table table-sm table-bordered mb-3">
                    <tr>
                        <th style="width:35%;background:#f8f9fa;">Document</th>
                        <td id="vd-hha-confirm-doc-title" style="font-weight:600;">—</td>
                    </tr>
                </table>
                <div id="vd-hha-patient-loader" style="text-align:center;padding:20px;">
                    <img src="{{ asset('/ajax-loader.gif') }}" alt="searching">
                    <div class="text-muted mt-2" style="font-size:12px;">Searching HHA patient…</div>
                </div>
                <div id="vd-hha-patient-error" class="alert alert-warning" style="display:none;font-size:13px;"></div>
                <div id="vd-hha-patient-info" style="display:none;">
                    <p style="font-size:11px;color:#8c9db5;margin-bottom:6px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">HHA Patient Found</p>
                    <table class="table table-sm table-bordered">
                        <tr><th style="width:35%;background:#f8f9fa;">Patient ID</th><td id="vd-hha-confirm-pid">—</td></tr>
                        <tr><th style="background:#f8f9fa;">Name</th><td id="vd-hha-confirm-name">—</td></tr>
                        <tr><th style="background:#f8f9fa;">Date of Birth</th><td id="vd-hha-confirm-dob">—</td></tr>
                        <tr><th style="background:#f8f9fa;">Address</th><td id="vd-hha-confirm-address">—</td></tr>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm" id="vd-hha-confirm-upload-btn" disabled onclick="vdConfirmHhaUpload()">
                    <i class="mdi mdi-upload"></i> Send to HHA
                </button>
            </div>
        </div>
    </div>
</div>
