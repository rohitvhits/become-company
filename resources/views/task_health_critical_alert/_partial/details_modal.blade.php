<div class="modal fade" id="caDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border:none;border-radius:8px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.2);">

            <div id="ca-modal-header" class="modal-header" style="border-bottom:none;padding:13px 20px;">
                <h5 class="modal-title" style="font-size:15px;font-weight:600;color:#fff;display:flex;align-items:center;gap:8px;">
                    <i class="mdi mdi-alert-circle" id="ca-modal-icon"></i>
                    <span id="ca-modal-title-text">Critical Alert Detail</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:.8;">&times;</button>
            </div>

            <div class="modal-body p-0">

                {{-- Meta strip --}}
                <div style="display:flex;flex-wrap:wrap;border-bottom:1px solid #e9ecef;background:#fff;">
                    <div style="padding:10px 18px;border-right:1px solid #f0f2f5;min-width:110px;">
                        <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:#9ca3af;margin-bottom:2px;">Task ID</div>
                        <div style="font-size:14px;font-weight:700;color:#007bff;" id="ca-modal-task-id">—</div>
                    </div>
                    <div style="padding:10px 18px;border-right:1px solid #f0f2f5;min-width:110px;">
                        <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:#9ca3af;margin-bottom:2px;">Patient ID</div>
                        <div style="font-size:14px;font-weight:700;color:#1f2937;" id="ca-modal-patient-id">—</div>
                    </div>
                    <div style="padding:10px 18px;">
                        <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:#9ca3af;margin-bottom:2px;">Received At</div>
                        <div style="font-size:13px;font-weight:600;color:#495057;" id="ca-modal-received">—</div>
                    </div>
                </div>

                <div style="padding:16px 20px;display:flex;flex-direction:column;gap:14px;">

                    {{-- Summary --}}
                    <div id="ca-modal-summary-wrap">
                        <div style="font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#6c757d;margin-bottom:7px;display:flex;align-items:center;gap:6px;">
                            <span style="width:3px;height:12px;background:#007bff;border-radius:2px;display:inline-block;flex-shrink:0;"></span> Summary
                        </div>
                        <div id="ca-modal-summary" style="font-size:13.5px;color:#1f2937;line-height:1.7;background:#f8f9fa;border:1px solid #e9ecef;border-radius:6px;padding:10px 14px;"></div>
                    </div>

                    {{-- Findings --}}
                    <div id="ca-modal-findings-wrap">
                        <div style="font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#6c757d;margin-bottom:7px;display:flex;align-items:center;gap:6px;">
                            <span style="width:3px;height:12px;background:#dc3545;border-radius:2px;display:inline-block;flex-shrink:0;"></span>
                            Findings <span id="ca-modal-findings-count" style="background:#dee2e6;color:#6c757d;border-radius:9px;font-size:10px;padding:1px 7px;font-weight:700;margin-left:2px;"></span>
                        </div>
                        <div id="ca-modal-findings-list" style="display:flex;flex-direction:column;gap:7px;"></div>
                    </div>

                    {{-- Resolution --}}
                    <div id="ca-modal-resolved-wrap" style="display:none;">
                        <div style="font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#6c757d;margin-bottom:7px;display:flex;align-items:center;gap:6px;">
                            <span style="width:3px;height:12px;background:#28a745;border-radius:2px;display:inline-block;flex-shrink:0;"></span> Resolution
                        </div>
                        <div style="background:#f0fff4;border:1px solid #c3e6cb;border-radius:6px;padding:12px 14px;">
                            <div style="display:flex;flex-wrap:wrap;gap:20px;margin-bottom:8px;">
                                <div>
                                    <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:#9ca3af;">Resolved By</div>
                                    <div style="font-size:13px;font-weight:700;color:#1a7a4a;" id="ca-modal-resolved-by">—</div>
                                </div>
                                <div>
                                    <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:#9ca3af;">Resolved At</div>
                                    <div style="font-size:13px;font-weight:600;color:#495057;" id="ca-modal-resolved-at">—</div>
                                </div>
                            </div>
                            <div id="ca-modal-resolved-notes-wrap" style="display:none;">
                                <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:#9ca3af;margin-bottom:4px;">Notes</div>
                                <div id="ca-modal-resolved-notes" style="font-size:13px;color:#1f2937;line-height:1.6;"></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="modal-footer" style="padding:10px 20px;background:#fff;border-top:1px solid #dee2e6;">
                <button type="button" id="ca-detail-resolve-btn" class="btn btn-success btn-sm" style="display:none;"
                        onclick="openCaResolveFromDetail()">
                    <i class="mdi mdi-check-circle"></i> Mark as Resolved
                </button>
                <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>
