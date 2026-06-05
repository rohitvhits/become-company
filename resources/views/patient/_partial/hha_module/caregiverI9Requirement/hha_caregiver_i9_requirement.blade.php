<!-- Modern Header Section -->
<style>
/* Header Styles */
.modern-header-section {
    position: relative;
    background: linear-gradient(135deg, #1093df 0%, #1093df 100%);
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 1rem;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
}

.header-background {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    opacity: 0.3;
}

.header-content-wrapper {
    position: relative;
    z-index: 2;
    padding: 10px;
}

.modern-header-section .header-content {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.header-icon-large {
    width: 30px;
    height: 30px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    color: white;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.header-text {
    flex: 1;
}

.modern-header-section .page-title {
    font-weight: 700;
    font-size: 14px;
    color: white;
    margin: 0;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.page-subtitle {
    color: rgba(255, 255, 255, 0.9);
    font-size: 12px;
    margin: 0.1rem 0;
}

.compliance-status {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    margin-top: 0.15rem;
}

.status-indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    animation: pulse 2s infinite;
}

.status-indicator.pending {
    background: #ffc107;
}

.status-indicator.verified {
    background: #28a745;
}

.status-indicator.expired {
    background: #dc3545;
}

.status-text {
    color: rgba(255, 255, 255, 0.9);
    font-size: 0.9rem;
    font-weight: 500;
}

.header-actions {
    display: flex;
    gap: 0.4rem;
    align-items: center;
}

.btn-modern-primary {
    background: rgba(255, 255, 255, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: white;
    border-radius: 8px;
    padding: 0.4rem 0.8rem;
    font-weight: 600;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}

.btn-modern-primary:hover {
    background: rgba(255, 255, 255, 0.3);
    border-color: rgba(255, 255, 255, 0.5);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
}

.btn-modern-secondary {
    background: transparent;
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: white;
    border-radius: 8px;
    padding: 0.4rem 0.8rem;
    font-weight: 600;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}

.btn-modern-secondary:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.5);
    color: white;
    transform: translateY(-2px);
}

/* Loading Styles */
.modern-loading-container {
    background: white;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    padding: 1rem;
    text-align: center;
}

.loading-content {
    max-width: 400px;
    margin: 0 auto;
}

.loading-spinner-modern {
    position: relative;
    width: 35px;
    height: 35px;
    margin: 0 auto 0.75rem;
}

.spinner-ring {
    position: absolute;
    width: 100%;
    height: 100%;
    border: 3px solid transparent;
    border-top: 3px solid #667eea;
    border-radius: 50%;
    animation: spin 1.5s linear infinite;
}

.spinner-ring:nth-child(2) {
    animation-delay: 0.5s;
    border-top-color: #764ba2;
}

.spinner-ring:nth-child(3) {
    animation-delay: 1s;
    border-top-color: #f093fb;
}

.loading-title {
    color: #495057;
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.loading-subtitle {
    color: #6c757d;
    margin-bottom: 0.75rem;
}

.loading-progress {
    width: 100%;
    height: 4px;
    background: #e9ecef;
    border-radius: 2px;
    overflow: hidden;
}

.modern-loading-container .progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #667eea, #764ba2);
    border-radius: 2px;
    animation: progress 2s ease-in-out infinite;
}

/* Card Styles */
.modern-content.info-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    overflow: hidden;
}

.modern-content.info-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.card-header-modern {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 8px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    display: flex;
    align-items: center;
    gap: 0.4rem;
}

.header-icon {
    width: 28px;
    height: 28px;
    border-radius: 8px;
    background: rgba(0, 123, 255, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
}

.card-title-modern {
    font-weight: 700;
    font-size: 1rem;
    margin: 0;
    color: #495057;
}

.card-body-modern {
    padding: 0.75rem;
}

.info-item {
    margin-bottom: 0.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #f8f9fa;
}

.info-item:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
}

.info-label {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-weight: 600;
    color: #6c757d;
    font-size: 0.75rem;
    margin-bottom: 0.3rem;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.info-label i {
    margin-right: 0.4rem;
    color: #495057;
}

.label-text {
    flex: 1;
    display: flex;
    align-items: center;
}

.data-completeness {
    display: flex;
    align-items: center;
    gap: 0.2rem;
    font-size: 0.7rem;
    padding: 0.2rem 0.4rem;
    border-radius: 10px;
    font-weight: 500;
}

.data-completeness.complete {
    background: #d4edda;
    color: #155724;
}

.data-completeness.incomplete {
    background: #f8d7da;
    color: #721c24;
}

.data-completeness.warning {
    background: #fff3cd;
    color: #856404;
}

.data-completeness.optional {
    background: #d1ecf1;
    color: #0c5460;
}

.info-value {
    display: block;
    color: #495057;
    font-size: 0.85rem;
    line-height: 1.4;
    word-break: break-word;
    font-weight: 500;
}

/* Animations */
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

@keyframes progress {
    0% { width: 0%; }
    50% { width: 70%; }
    100% { width: 100%; }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.info-card {
    animation: fadeInUp 0.6s ease forwards;
}

.info-card:nth-child(1) { animation-delay: 0.1s; }
.info-card:nth-child(2) { animation-delay: 0.2s; }
.info-card:nth-child(3) { animation-delay: 0.3s; }

/* Responsive Design */
@media (max-width: 768px) {
    .header-content-wrapper {
        padding: 8px;
    }
    
    .modern-header-section .header-content {
        flex-direction: column;
        text-align: center;
        gap: 0.75rem;
    }
    
    .header-actions {
        flex-direction: column;
        width: 100%;
        gap: 0.6rem;
    }
    
    .btn-modern-primary,
    .btn-modern-secondary {
        width: 100%;
        justify-content: center;
    }
    
    .modern-header-section .page-title {
        font-size: 14px;
    }
    
    .card-header-modern {
        padding: 0.9rem;
    }
    
    .card-body-modern {
        padding: 0.9rem;
    }
    
    .info-item {
        margin-bottom: 0.75rem;
        padding-bottom: 0.75rem;
    }
}

@media (max-width: 576px) {
    .header-content-wrapper {
        padding: 8px;
    }
    
    .header-icon-large {
        width: 40px;
        height: 40px;
        font-size: 1.2rem;
    }
    
    .modern-header-section .page-title {
        font-size: 1.1rem;
    }
    
    .card-header-modern {
        padding: 0.75rem;
        flex-direction: column;
        text-align: center;
        gap: 0.5rem;
    }
    
    .card-body-modern {
        padding: 0.75rem;
    }
}

#nyportal_highlight .highlight {
    background: linear-gradient(120deg, #f6d365, #fda085);
    color: #333;
    padding: 0.3em 0.6em;
    border-radius: 8px;
    font-weight: bold;
    box-shadow: 0 2px 5px rgba(0,0,0,0.15);
    transition: transform 0.2s;
  }
</style>
<div class="modern-header-section">
    <div class="header-background"></div>
    <div class="header-content-wrapper">
        <div class="d-flex align-items-center justify-content-between">
            <div class="header-content">
                <div class="header-icon-large">
                    <i class="mdi mdi-account-card-details"></i>
                </div>
                <div class="header-text">
                    <h3 class="page-title mb-1">Caregiver I9 Requirements</h3>
                    <p class="page-subtitle mb-0">Update Caregiver I9 Requirements</p>
                    
                </div>
            </div>
    @can('edit-hha-caregiver-i9-requirement')
                <div class="header-actions">
                    <button type="button" class="btn btn-primary btn-modern-primary" data-toggle="modal" onclick="refreshCaregiverI9DocumentRequirement()" data-whatever="@mdo">
                        <i class="mdi mdi-plus me-2"></i>
                        <span class="btn-text">Edit Caregiver I9 Requirements</span>
                    </button>
                  
                </div>
    @endcan
        </div>
    </div>
</div>

<!-- Loading State -->
<div class="modern-loading-container" id="loader-caregiver" style="display: none;">
    <div class="loading-content">
        <div class="loading-animation">
            <div class="loading-spinner-modern">
                <div class="spinner-ring"></div>
                <div class="spinner-ring"></div>
                <div class="spinner-ring"></div>
            </div>
        </div>
        <h4 class="loading-title">Loading I-9 Compliance Data</h4>
        <p class="loading-subtitle">Please wait while we retrieve employment verification information...</p>
        <div class="loading-progress">
            <div class="progress-bar"></div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="modern-content" id="hha_caregiver_basic1">
    <div class="row g-4">
        <!-- Employment Information Card -->
        <div class="col-lg-6 col-md-12">
            <div class="info-card h-100">
                <div class="card-header-modern">
                    <div class="header-icon">
                        <i class="mdi mdi-calendar-clock text-primary"></i>
                    </div>
                    <h5 class="card-title-modern">Employment Timeline</h5>
                </div>
                <div class="card-body-modern">
                    <div class="info-item">
                        <label for="hires" class="info-label">
                            <i class="mdi mdi-calendar-check"></i>
                            <span class="label-text">Hire Date</span>
                            
                        </label>
                        <span class="info-value" id="html_hha_caregiver_i9_hire_date"></span>
                    </div>
                    
                    <div class="info-item">
                        <label for="i9_expi" class="info-label">
                            <i class="mdi mdi-calendar-alert"></i>
                            <span class="label-text">I-9 Document Expiration Date</span>
                            
                        </label>
                        <span class="info-value" id="html_hha_caregiver_i9_expiredate_date"></span>
                    </div>
                    
                    <div class="info-item">
                        <label for="i9_nt" class="info-label">
                            <i class="mdi mdi-note-text"></i>
                            <span class="label-text">I-9 Notes</span>
                            
                        </label>
                        <span class="info-value" id="html_hha_caregiver_i9_notes"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Document Verification Card -->
        <div class="col-lg-6 col-md-12">
            <div class="info-card h-100">
                <div class="card-header-modern">
                    <div class="header-icon">
                        <i class="mdi mdi-file-document text-success"></i>
                    </div>
                    <h5 class="card-title-modern">Document Verification</h5>
                </div>
                <div class="card-body-modern">
                    <div class="info-item">
                        <label for="i9_ab_doc" class="info-label">
                            <i class="mdi mdi-file-document"></i>
                            <span class="label-text">Column A+B Documents</span>
                            
                        </label>
                        <span class="info-value" id="html_hha_caregiver_i9_ab_document"></span>
                    </div>
                    
                    <div class="info-item">
                        <label for="i9_av" class="info-label">
                            <i class="mdi mdi-check-circle"></i>
                            <span class="label-text">I-9 Verified</span>
                            
                        </label>
                        <span class="info-value" id="html_hha_caregiver_i9_verified"></span>
                    </div>
                    
                    <div class="info-item">
                        <label for="i9_c_doc" class="info-label">
                            <i class="mdi mdi-file-document"></i>
                            <span class="label-text">Column C Documents</span>
                            
                        </label>
                        <span class="info-value" id="html_hha_caregiver_i9_cdocument"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- E-Verify Information Card -->
        <div class="col-lg-12 mt-3">
            <div class="info-card">
                <div class="card-header-modern">
                    <div class="header-icon">
                        <i class="mdi mdi-shield-check text-info"></i>
                    </div>
                    <h5 class="card-title-modern">E-Verify Information</h5>
                </div>
                <div class="card-body-modern">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item">
                                <label for="i9_vsd" class="info-label">
                                    <i class="mdi mdi-numeric"></i>
                                    <span class="label-text">E-Verify Number</span>
                                    
                                </label>
                                <span class="info-value" id="html_hha_caregiver_i9_everify_no"></span>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modern CSS Styles -->
