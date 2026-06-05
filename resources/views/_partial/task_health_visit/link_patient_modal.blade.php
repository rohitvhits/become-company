<style>
    #thLinkPatientModal { z-index:1080 !important; }
    .th-lp-info-grid { display:grid; grid-template-columns:1fr 1fr 1fr; gap:10px 14px; margin-bottom:4px; }
    @media(max-width:600px) { .th-lp-info-grid { grid-template-columns:1fr 1fr; } }
    .th-lp-field { background:#f8f9fa; border-radius:5px; padding:7px 10px; }
    .th-lp-label { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:#9ca3af; margin-bottom:2px; }
    .th-lp-value { font-size:12px; font-weight:600; color:#1f2937; }
    .th-lp-section-title { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#6c757d; margin:12px 0 6px; padding-bottom:4px; border-bottom:1px solid #e9ecef; }
    #th-lp-found-alert { background:#d4edda; border:1px solid #c3e6cb; border-radius:6px; padding:10px 14px; margin-bottom:12px; color:#155724; font-size:13px; }
</style>
<div class="modal fade" id="thLinkPatientModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" style="margin-top:60px;">
        <div class="modal-content" style="border:none;border-radius:8px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.35);">
            <div class="modal-header" style="background:linear-gradient(135deg,#1e1e2f,#2d3a4a);color:#fff;padding:12px 20px;border-bottom:none;">
                <h6 class="modal-title" style="font-size:13px;font-weight:700;display:flex;align-items:center;gap:8px;">
                    <i class="mdi mdi-account-check-outline"></i>
                    <span id="th-lp-modal-title">Create &amp; Link Patient Record</span>
                </h6>
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:.75;font-size:20px;">&times;</button>
            </div>
            <div class="modal-body" style="padding:18px 22px;max-height:75vh;overflow-y:auto;">
                {{-- Patient info (read-only) --}}
                <div class="th-lp-section-title"><i class="mdi mdi-account-outline"></i> Patient Information (from Task Health)</div>
                <div class="th-lp-info-grid" id="th-lp-info-grid"></div>

                {{-- Found existing patient notice --}}
                <div id="th-lp-found-alert" style="display:none;"></div>

                {{-- Input fields (shown only when no local patient match) --}}
                <div id="th-lp-inputs" style="display:none;">
                    <div class="th-lp-section-title"><i class="mdi mdi-form-textbox"></i> Additional Information</div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group" style="margin-bottom:10px;">
                                <label style="font-size:11px;font-weight:700;color:#6c757d;margin-bottom:4px;">Discipline</label>
                                <select class="form-control form-control-sm" id="th-lp-discipline">
                                    <option value="">Select Discipline</option>
                                    @foreach($disciplineOptions as $disc)
                                        <option value="{{ $disc->name }}">{{ $disc->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group" style="margin-bottom:10px;">
                                <label style="font-size:11px;font-weight:700;color:#6c757d;margin-bottom:4px;">Services <span style="color:#dc3545;">*</span></label>
                                <select class="form-control form-control-sm" id="th-lp-services" multiple style="width:100%;">
                                    @foreach($patientServices as $svc)
                                        <option value="{{ $svc->id }}">{{ $svc->name }}</option>
                                    @endforeach
                                </select>
                                <small id="th-lp-service-error" class="text-danger" style="display:none;font-size:11px;">Please select at least one service.</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group" style="margin-bottom:6px;">
                                <label style="font-size:11px;font-weight:700;color:#6c757d;margin-bottom:4px;">Followup Date </label>
                                <input type="text" class="form-control form-control-sm" id="th-lp-followup-date" placeholder="MM/DD/YYYY" autocomplete="off" data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" im-insert="false">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group" style="margin-bottom:6px;">
                                <label style="font-size:11px;font-weight:700;color:#6c757d;margin-bottom:4px;">Due Date </label>
                                <input type="text" class="form-control form-control-sm" id="th-lp-due-date" placeholder="MM/DD/YYYY" autocomplete="off" data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" im-insert="false">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="padding:10px 20px;background:#f8f9fa;border-top:1px solid #dee2e6;">
                <button type="button" class="btn btn-sm btn-light" data-dismiss="modal" style="font-size:12px;">
                    <i class="mdi mdi-close"></i> Cancel
                </button>
                <button type="button" class="btn btn-sm btn-warning" id="th-lp-proceed-btn" style="font-size:12px;font-weight:600;min-width:120px;">
                    <i class="mdi mdi-check"></i> <span id="th-lp-proceed-label">Confirm &amp; Link</span>
                </button>
            </div>
        </div>
    </div>
</div>
