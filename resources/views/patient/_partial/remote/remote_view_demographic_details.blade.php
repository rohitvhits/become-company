<div class="tab-pane" id="remote-demographic-details">
    <!-- Loader Overlay -->
    <div class="loader-overlay" id="demographicLoaderOverlay" style="display:none;">
        <div class="loader-spinner">
            <div class="spinner-border" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <p class="loader-text">Loading demographic details...</p>
        </div>
    </div>

    <div class="d-flex align-items-center justify-content-between mb-2">
        <p class="card-title mb-0">Remote Demographic Details</p>
    </div>

    <div class="row">
        <div class="col-12" id="demographicDetailsContainer">
            <!-- Patient Info Card -->
            <div class="info-card gradient-card mb-2">
                <div class="info-card-header">
                    <i class="mdi mdi-account-circle"></i> Patient Information
                </div>
                <div class="info-card-body">
                    <div class="row">
                        <div class="col-md-3 col-sm-6 info-item">
                            <span class="info-label">Patient ID</span>
                            <span class="info-value" id="patient_id">-</span>
                        </div>
                        <div class="col-md-3 col-sm-6 info-item">
                            <span class="info-label">External ID</span>
                            <span class="info-value" id="external_id">-</span>
                        </div>
                        <div class="col-md-3 col-sm-6 info-item">
                            <span class="info-label">First Name</span>
                            <span class="info-value" id="first_name">-</span>
                        </div>
                        <div class="col-md-3 col-sm-6 info-item">
                            <span class="info-label">Last Name</span>
                            <span class="info-value" id="last_name">-</span>
                        </div>
                        <div class="col-md-3 col-sm-6 info-item">
                            <span class="info-label">Date of Birth</span>
                            <span class="info-value" id="dob">-</span>
                        </div>
                        <div class="col-md-3 col-sm-6 info-item">
                            <span class="info-label">Gender</span>
                            <span class="info-value" id="gender">-</span>
                        </div>
                        <div class="col-md-3 col-sm-6 info-item">
                            <span class="info-label" style="letter-spacing:0px !important">Enrolled Program Status</span>
                            <span class="" id="enrolled_program_status_badge">-</span>
                        </div>
                        <div class="col-md-3 col-sm-6 info-item">
                            <span class="info-label">Legacy ID</span>
                            <span class="info-value text-muted small" id="legacy_id">-</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Healthcare Team Cards -->
            <div class="row mb-2">
                <div class="col-md-4">
                    <div class="info-card team-card">
                        <div class="team-card-icon referral-icon">
                            <i class="mdi mdi-hospital-building"></i>
                        </div>
                        <div class="team-card-content">
                            <h6 class="team-card-title">Referral Source</h6>
                            <p class="team-card-name" id="referral_source_name">-</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-card team-card">
                        <div class="team-card-icon provider-icon">
                            <i class="mdi mdi-doctor"></i>
                        </div>
                        <div class="team-card-content">
                            <h6 class="team-card-title">Provider</h6>
                            <p class="team-card-name" id="provider_name">-</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-card team-card">
                        <div class="team-card-icon clinician-icon">
                            <i class="mdi mdi-stethoscope"></i>
                        </div>
                        <div class="team-card-content">
                            <h6 class="team-card-title">Clinician</h6>
                            <p class="team-card-name" id="clinician_name">-</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Insurance Information -->
            <div class="info-card insurance-card">
                <div class="info-card-header">
                    <i class="mdi mdi-shield-account"></i> Insurance Information
                </div>
                <div class="info-card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 modern-table">
                            <thead>
                                <tr>
                                    <th width="50">#</th>
                                    <th>Status</th>
                                    <th>Policy Number</th>
                                    <th>Type</th>
                                    <th>Plan Name</th>
                                </tr>
                            </thead>
                            <tbody id="insurance_list">
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">No insurance information available</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- No Data Message -->
        <div class="col-12 hide" id="no_data_message">
            <div class="alert alert-info text-center">
                <i class="mdi mdi-information-outline mr-2"></i> No demographic details available for this patient.
            </div>
        </div>
        
    </div>
</div>
