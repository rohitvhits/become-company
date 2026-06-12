<!-- AI Analysis Result Modal -->
<div class="modal fade" id="aiAnalysisModal" tabindex="-1" role="dialog" aria-labelledby="aiAnalysisModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content" style="border-radius:16px;overflow:hidden;box-shadow:0 8px 40px rgba(0,0,0,0.22);">
            <div class="modal-header" style="background:linear-gradient(270deg,#e0d7ff,#c8e6ff,#d4f1f9,#e8d5f5,#e0d7ff);background-size:300% 300%;animation:aiGradientMoveBtn 4s ease infinite;border-radius:16px 16px 0 0;">
                <h5 class="modal-title" id="aiAnalysisModalLabel" style="display:flex;align-items:center;gap:8px;color:#5a3e9e;font-weight:700;font-size:15px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="#5a3e9e"><path d="M12 3c-1.2 5.4-5.4 7.8-9 9 3.6 1.2 7.8 3.6 9 9 1.2-5.4 5.4-7.8 9-9-3.6-1.2-7.8-3.6-9-9z"/><path d="M5 3c-.6 2.7-2.3 3.7-4 4 1.7.3 3.4 1.3 4 4 .6-2.7 2.3-3.7 4-4-1.7-.3-3.4-1.3-4-4z" opacity=".8"/></svg>
                    AI Document Analysis
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#5a3e9e;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="aiAnalysisLoading" style="text-align:center;padding:40px 20px;">
                    <div style="width:40px;height:40px;border:3px solid #e0dcff;border-top-color:#6c5ce7;border-radius:50%;animation:hmwSpin 0.8s linear infinite;margin:0 auto 12px;"></div>
                    <p class="mt-3 text-muted">Analysing document with AI, please wait...</p>
                </div>
                <div id="aiAnalysisResult" style="display:none;">
                    {{-- Patient mismatch warning --}}
                    <div id="aiPatientMismatchAlert" style="display:none; background:#fff3cd; border:2px solid #ffc107; border-radius:8px; padding:14px 16px; margin-bottom:14px;">
                        <div style="display:flex; align-items:flex-start; gap:10px;">
                            <i class="mdi mdi-alert" style="font-size:22px; color:#e65100; margin-top:2px; flex-shrink:0;"></i>
                            <div style="flex:1;">
                                <div style="font-weight:700; font-size:14px; color:#7b3f00; margin-bottom:6px;">
                                    <i class="mdi mdi-account-alert mr-1"></i>Patient Mismatch Warning
                                </div>
                                <div style="font-size:13px; color:#5d3a00; margin-bottom:8px;">
                                    The document appears to belong to a <strong>different patient</strong>. The following details in the document do not match this patient's record:
                                </div>
                                <div id="aiMismatchDetails" style="font-size:13px;"></div>
                                <div style="margin-top:10px; font-size:12px; color:#7b3f00; background:#ffe082; border-radius:6px; padding:8px 12px;">
                                    <i class="mdi mdi-information mr-1"></i>
                                    Please verify you are uploading the correct document.<span id="aiMismatchSaveNote"> You can still proceed with saving if this is intentional.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-info mb-3" style="border-left: 4px solid #667eea; background: #f0f4ff; display:flex; align-items:center; gap:10px;">
                        <i class="mdi mdi-file-document-outline" style="font-size:18px;"></i>
                        <div>
                            <div style="font-size:13px; color:#374151;"><strong>File:</strong> <span id="aiAnalysisDocName"></span></div>
                            <div id="aiAnalysisDocLabelRow" style="display:none; margin-top:3px;">
                                <span style="font-size:11px; color:#6b7280; text-transform:uppercase; font-weight:600; letter-spacing:.4px;">Identified as &nbsp;</span>
                                <span id="aiAnalysisDocLabel" style="font-size:12px; font-weight:700; color:#4f46e5; background:#ede9fe; border-radius:4px; padding:2px 8px;"></span>
                            </div>
                        </div>
                    </div>
                    <div id="aiAnalysisContent" style="max-height:65vh;overflow-y:auto;padding-right:4px;"></div>
                </div>
                <div id="aiAnalysisError" style="display:none;">
                    <div class="alert alert-warning">
                        <i class="mdi mdi-alert-circle mr-1"></i>
                        <span id="aiAnalysisErrorMsg"></span>
                        <div class="mt-1" style="font-size:12px;">You can still save the document without the AI summary.</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="aiAnalysisConfirmSave" style="display:none;">
                    <i class="mdi mdi-check mr-1"></i>Confirm & Save
                </button>
                <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
