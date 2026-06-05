<style>
    /* Caregiver View Modal Styles */
    .caregiver-view-modal {
        display: none;
        position: fixed;
        z-index: 1050;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.5);
    }

    .caregiver-view-modal.show {
        display: block;
    }

    .caregiver-modal-content {
        background-color: #fefefe;
        margin: 2% auto;
        border-radius: 8px;
        width: 90%;
        max-width: 1200px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        max-height: 90vh;
        display: flex;
        flex-direction: column;
    }

    .caregiver-modal-header {
        padding: 20px;
        border-bottom: 1px solid #dee2e6;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-shrink: 0;
    }

    .caregiver-modal-header h4 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 600;
    }

    .caregiver-modal-close {
        font-size: 28px;
        font-weight: bold;
        color: #aaa;
        cursor: pointer;
        background: none;
        border: none;
        padding: 0;
        width: 30px;
        height: 30px;
        line-height: 1;
    }

    .caregiver-modal-close:hover,
    .caregiver-modal-close:focus {
        color: #000;
    }

    .caregiver-modal-body {
        padding: 0;
        overflow: hidden;
        flex: 1;
        display: flex;
    }

    .caregiver-tabs {
        display: flex;
        flex-direction: column;
        border-right: 2px solid #dee2e6;
        background-color: #f8f9fa;
        width: 250px;
        flex-shrink: 0;
        overflow-y: auto;
        padding: 10px 0;
    }

    .caregiver-tab-button {
        padding: 15px 20px;
        cursor: pointer;
        border: none;
        background: none;
        font-size: 14px;
        font-weight: 500;
        color: #495057;
        border-left: 4px solid transparent;
        transition: all 0.3s ease;
        white-space: nowrap;
        text-align: left;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .caregiver-tab-button i {
        font-size: 18px;
        min-width: 20px;
    }

    .caregiver-tab-button:hover {
        background-color: #e9ecef;
        color: #007bff;
    }

    .caregiver-tab-button.active {
        color: #007bff;
        border-left-color: #007bff;
        background-color: #fff;
        font-weight: 600;
    }

    .caregiver-tab-content-wrapper {
        flex: 1;
        overflow-y: auto;
        background-color: #fff;
    }

    .caregiver-tab-content {
        display: none;
        padding: 30px;
        animation: fadeIn 0.3s;
        min-height: 100%;
    }

    .caregiver-tab-content.active {
        display: block;
    }

    /* Responsive - Stack vertically on mobile */
    @media (max-width: 768px) {
        .caregiver-modal-body {
            flex-direction: column;
        }

        .caregiver-tabs {
            width: 100%;
            flex-direction: row;
            overflow-x: auto;
            border-right: none;
            border-bottom: 2px solid #dee2e6;
            padding: 0;
        }

        .caregiver-tab-button {
            border-left: none;
            border-bottom: 3px solid transparent;
            padding: 12px 15px;
            justify-content: center;
            min-width: 120px;
        }

        .caregiver-tab-button.active {
            border-left-color: transparent;
            border-bottom-color: #007bff;
        }

        .caregiver-tab-button span {
            display: none;
        }

        .caregiver-tab-content {
            padding: 20px;
        }
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .info-group {
        margin-bottom: 20px;
    }

    .info-group h5 {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 15px;
        color: #333;
        border-bottom: 2px solid #007bff;
        padding-bottom: 8px;
    }

    .info-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 15px;
        margin-bottom: 15px;
    }

    .info-item {
        padding: 10px;
        background-color: #f8f9fa;
        border-radius: 5px;
    }

    .info-label {
        font-weight: 600;
        color: #6c757d;
        font-size: 0.875rem;
        margin-bottom: 5px;
    }

    .info-value {
        color: #212529;
        font-size: 1rem;
    }

    /* Shimmer Effect */
    .shimmer-wrapper {
        padding: 20px;
    }

    .shimmer-card {
        background: #fff;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .shimmer {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
        border-radius: 4px;
    }

    .shimmer-line {
        height: 16px;
        margin-bottom: 12px;
    }

    .shimmer-line.title {
        height: 24px;
        width: 40%;
        margin-bottom: 20px;
    }

    .shimmer-line.short {
        width: 60%;
    }

    .shimmer-line.medium {
        width: 80%;
    }

    .shimmer-line.long {
        width: 100%;
    }

    .shimmer-header {
        height: 60px;
        margin-bottom: 20px;
        border-radius: 8px;
    }

    @keyframes shimmer {
        0% {
            background-position: 200% 0;
        }
        100% {
            background-position: -200% 0;
        }
    }

    /* FullCalendar Custom Styling */
    #caregiverFullCalendar {
        max-width: 100%;
        margin: 0 auto;
    }

    #caregiverFullCalendar .fc-toolbar {
        margin-bottom: 20px;
    }

    #caregiverFullCalendar .fc-event {
        cursor: pointer;
        border-radius: 3px;
        padding: 2px 5px;
        font-size: 0.85rem;
    }

    #caregiverFullCalendar .fc-event:hover {
        opacity: 0.8;
    }

    #caregiverFullCalendar .fc-day-grid-event {
        margin: 1px 2px;
    }

    .calendar-loading {
        text-align: center;
        padding: 20px;
        font-size: 16px;
        color: #666;
    }

    /* Calendar legend */
    .calendar-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 15px;
        padding: 10px;
        background-color: #f8f9fa;
        border-radius: 5px;
    }

    .calendar-legend-item {
        display: flex;
        align-items: center;
        font-size: 0.9rem;
    }

    .calendar-legend-color {
        width: 20px;
        height: 20px;
        border-radius: 3px;
        margin-right: 8px;
    }
</style>

<div id="caregiverViewModal" class="caregiver-view-modal" aria-labelledby="caregiverModalTitle" aria-hidden="true">
            <div class="caregiver-modal-content">
                <!-- Modal Header -->
                <div class="caregiver-modal-header" style="background:#1e1e2f !important;    color: white;">
                    <h4 id="caregiverModalTitle">Caregiver Details - <span id="caregiverName">Loading...</span></h4>
                    <button class="caregiver-modal-close" onclick="closeCaregiverModal()" aria-label="Close modal">&times;</button>
                </div>

                <!-- Modal Body with Vertical Tabs -->
                <div class="caregiver-modal-body">
                    <!-- Vertical Tab Navigation -->
                    <div class="caregiver-tabs" role="tablist">
                        <button class="caregiver-tab-button active" role="tab" aria-selected="true" aria-controls="demographic-panel" id="demographic-tab">
                            <i class="mdi mdi-account"></i>
                            <span>Demographic Details</span>
                        </button>
                        <button class="caregiver-tab-button" role="tab" aria-selected="false" aria-cont rols="calendar-panel" id="calendar-tab">
                            <i class="mdi mdi-calendar"></i>
                            <span>Calendar</span>
                        </button>
                        <button class="caregiver-tab-button" role="tab" aria-selected="false" aria-controls="availability-panel" id="availability-tab">
                            <i class="mdi mdi-clock"></i>
                            <span>Availability</span>
                        </button>
                        <button class="caregiver-tab-button" role="tab" aria-selected="false" aria-controls="notes-panel" id="notes-tab">
                            <i class="mdi mdi-note-text"></i>
                            <span>Notes</span>
                        </button>
                        <button class="caregiver-tab-button" role="tab" aria-selected="false" aria-controls="inservice-panel" id="inservice-tab">
                            <i class="mdi mdi-school"></i>
                            <span>InService</span>
                        </button>
                        <button class="caregiver-tab-button" role="tab" aria-selected="false" aria-controls="medical-panel" id="medical-tab">
                            <i class="mdi mdi-medical-bag"></i>
                            <span>Medical</span>
                        </button>
                        <button class="caregiver-tab-button" role="tab" aria-selected="false" aria-controls="compliance-panel" id="compliance-tab">
                            <i class="mdi mdi-file-check"></i>
                            <span>Other Compliance</span>
                        </button>
                        <button class="caregiver-tab-button" role="tab" aria-selected="false" aria-controls="document-panel" id="document-tab">
                            <i class="mdi mdi-file-document"></i>
                            <span>Document</span>
                        </button>
                        <button class="caregiver-tab-button" role="tab" aria-selected="false" aria-controls="preferences-panel" id="preferences-tab">
                            <i class="fa fa-gear"></i>
                            <span>Preferences</span>
                        </button>
                    </div>

                    <!-- Tab Content Wrapper -->
                    <div class="caregiver-tab-content-wrapper">
                        <!-- Tab Content Panels -->
                        <div class="caregiver-tab-content active" id="demographic-panel" role="tabpanel" aria-labelledby="demographic-tab">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 15px 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                        <h4 class="mb-0 text-white">
                                            <i class="mdi mdi-account-details"></i> Demographic Details
                                        </h4>
                                        <small class="text-white" style="opacity: 0.9;">Comprehensive caregiver demographic information</small>
                                    </div>
                                </div>
                            </div>
                            <div id="demographicContent">
                                
                                <div class="shimmer-wrapper">
                                    <div class="shimmer shimmer-header"></div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="shimmer-card">
                                                <div class="shimmer shimmer-line title"></div>
                                                <div class="shimmer shimmer-line long"></div>
                                                <div class="shimmer shimmer-line medium"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="shimmer-card">
                                                <div class="shimmer shimmer-line title"></div>
                                                <div class="shimmer shimmer-line medium"></div>
                                                <div class="shimmer shimmer-line long"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="caregiver-tab-content" id="calendar-panel" role="tabpanel" aria-labelledby="calendar-tab">
                            <!-- Calendar Header -->
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 15px 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                        <h4 class="mb-0 text-white">
                                            <i class="mdi mdi-calendar-month"></i> Caregiver Calendar
                                        </h4>
                                        <small class="text-white" style="opacity: 0.9;">View caregiver schedule, visits, and appointments</small>
                                    </div>
                                </div>
                            </div>

                            <div id="calendarContent">
                                <div class="card">
                                    <div class="card-body">
                                        <!-- Calendar Legend -->
                                        <div class="calendar-legend">
                                            <div class="calendar-legend-item">
                                                <div class="calendar-legend-color" style="background-color: #007bff;"></div>
                                                <span>Scheduled</span>
                                            </div>
                                            <div class="calendar-legend-item">
                                                <div class="calendar-legend-color" style="background-color: #28a745;"></div>
                                                <span>Completed</span>
                                            </div>
                                            <div class="calendar-legend-item">
                                                <div class="calendar-legend-color" style="background-color: #ffc107;"></div>
                                                <span>Pending</span>
                                            </div>
                                            <div class="calendar-legend-item">
                                                <div class="calendar-legend-color" style="background-color: #dc3545;"></div>
                                                <span>Cancelled</span>
                                            </div>
                                            <div class="calendar-legend-item">
                                                <div class="calendar-legend-color" style="background-color: #e83e8c;"></div>
                                                <span>Missed</span>
                                            </div>
                                        </div>

                                        <!-- FullCalendar Container -->
                                        <div id="caregiverFullCalendar"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="caregiver-tab-content" id="availability-panel" role="tabpanel" aria-labelledby="availability-tab">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 15px 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                        <h4 class="mb-0 text-white">
                                            <i class="mdi mdi-account-details"></i> Weekly Availability
                                        </h4>
                                       
                                    </div>
                                </div>
                            </div>
                            <div id="availabilityContent">
                                <div class="shimmer-wrapper">
                                    <div class="shimmer shimmer-header"></div>
                                    <div class="shimmer-card">
                                        <div class="shimmer shimmer-line long"></div>
                                        <div class="shimmer shimmer-line medium"></div>
                                        <div class="shimmer shimmer-line long"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="caregiver-tab-content" id="notes-panel" role="tabpanel" aria-labelledby="notes-tab">
                        <div class="row mb-2">
                        <div class="col-12">
                            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 12px 16px; border-radius: 6px;">
                                <div class="d-flex justify-content-between align-items-center flex-wrap">
                                    <h5 class="mb-0 text-white">
                                        <i class="mdi mdi-note-text"></i> Notes
                                    </h5>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                            <div id="notesContent">
                                <div class="shimmer-wrapper">
                                    <div class="shimmer shimmer-header"></div>
                                    <div class="shimmer-card">
                                        <div class="shimmer shimmer-line title"></div>
                                        <div class="shimmer shimmer-line long"></div>
                                        <div class="shimmer shimmer-line medium"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="caregiver-tab-content" id="inservice-panel" role="tabpanel" aria-labelledby="inservice-tab">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 15px 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                        <h4 class="mb-0 text-white">
                                            <i class="mdi mdi-school"></i> InService
                                        </h4>

                                    </div>
                                </div>
                            </div>
                            <div id="inserviceContent">
                                <div class="shimmer-wrapper">
                                    <div class="shimmer shimmer-header"></div>
                                    <div class="shimmer-card">
                                        <div class="shimmer shimmer-line long"></div>
                                        <div class="shimmer shimmer-line medium"></div>
                                        <div class="shimmer shimmer-line long"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="caregiver-tab-content" id="medical-panel" role="tabpanel" aria-labelledby="medical-tab">
                            <div class="row mb-3">
                                <div class="col-12">
                                    
                                    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 15px 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                                            <h4 class="mb-0 text-white">
                                                <i class="mdi mdi-medical-bag"></i> Medical
                                            </h4>
                                            <div class="d-flex align-items-center mt-2 mt-md-0">
                                                <select class="form-control" id="hha_status_medical_id" onchange="loadCaregiverTab('medical')">
                                                    <option value="">Select</option>
                                                    <option value="Pending">Pending</option>
                                                    <option value="Completed">Completed</option>
                                                    <option value="Overdue">Overdue</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        
                                    </div>
                                </div>
                            </div>
                            <div id="medicalContent">
                                <div class="shimmer-wrapper">
                                    <div class="shimmer shimmer-header"></div>
                                    <div class="shimmer-card">
                                        <div class="shimmer shimmer-line long"></div>
                                        <div class="shimmer shimmer-line medium"></div>
                                        <div class="shimmer shimmer-line long"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="caregiver-tab-content" id="compliance-panel" role="tabpanel" aria-labelledby="compliance-tab">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 15px 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                                            <h4 class="mb-0 text-white">
                                                <i class="mdi mdi-file-check"></i> Other Compliance
                                            </h4>
                                            <button type="button" class="btn btn-light btn-sm mt-2 mt-md-0" onclick="openOtherComplianceModal()">
                                                <i class="mdi mdi-plus"></i> Create
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="complianceContent">
                                <div class="shimmer-wrapper">
                                    <div class="shimmer shimmer-header"></div>
                                    <div class="shimmer-card">
                                        <div class="shimmer shimmer-line long"></div>
                                        <div class="shimmer shimmer-line medium"></div>
                                        <div class="shimmer shimmer-line long"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="caregiver-tab-content" id="document-panel" role="tabpanel" aria-labelledby="document-tab">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 15px 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                        <h4 class="mb-0 text-white">
                                            <i class="mdi mdi-file-document"></i> Document List
                                        </h4>
                                      
                                    </div>
                                </div>
                            </div>
                            <div id="documentContent">
                                <div class="shimmer-wrapper">
                                    <div class="shimmer shimmer-header"></div>
                                    <div class="shimmer-card">
                                        <div class="shimmer shimmer-line long"></div>
                                        <div class="shimmer shimmer-line medium"></div>
                                        <div class="shimmer shimmer-line long"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="caregiver-tab-content" id="preferences-panel" role="tabpanel" aria-labelledby="preferences-tab">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 15px 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                        <h4 class="mb-0 text-white">
                                            <i class="fa fa-gear"></i> Preferences
                                        </h4>
                                      
                                    </div>
                                </div>
                            </div>
                            <div id="preferencesContent">
                                <div class="shimmer-wrapper">
                                    <div class="shimmer shimmer-header"></div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="shimmer-card">
                                                <div class="shimmer shimmer-line medium"></div>
                                                <div class="shimmer shimmer-line long"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="shimmer-card">
                                                <div class="shimmer shimmer-line long"></div>
                                                <div class="shimmer shimmer-line medium"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<!-- Other Compliance Modal -->
<div class="modal fade" id="otherComplianceModal" tabindex="-1" role="dialog" aria-labelledby="otherComplianceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h5 class="modal-title text-white" id="otherComplianceModalLabel">
                    <i class="mdi mdi-file-check"></i> Create Other Compliance
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="otherComplianceForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row">
                        <!-- Document Type -->
                        <div class="col-md-6 mb-3">
                            <label for="document_type" class="form-label">
                                Document Type <span class="text-danger">*</span>
                            </label>
                            <select class="form-control" id="document_type" name="document_type" required>
                                <option value="">Select Document Type</option>
                                <option value="License">License</option>
                                <option value="Certificate">Certificate</option>
                                <option value="Clearance">Clearance</option>
                                <option value="Training">Training</option>
                                <option value="Other">Other</option>
                            </select>
                            <small class="text-danger d-none" id="document_type_error"></small>
                        </div>

                        <!-- Medical Dropdown -->
                        <div class="col-md-6 mb-3">
                            <label for="medical_id" class="form-label">
                                Medical <span class="text-danger">*</span>
                            </label>
                            <select class="form-control" id="medical_id" name="medical_id" required>
                                <option value="">Loading...</option>
                            </select>
                            <small class="text-danger d-none" id="medical_id_error"></small>
                        </div>

                        <!-- Date Performed -->
                        <div class="col-md-6 mb-3">
                            <label for="date_performed" class="form-label">
                                Date Performed <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control datepicker" id="date_performed" name="date_performed" placeholder="Select date" autocomplete="off" required>
                            <small class="text-danger d-none" id="date_performed_error"></small>
                        </div>

                        <!-- Due Date -->
                        <div class="col-md-6 mb-3">
                            <label for="due_date" class="form-label">
                                Due Date <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control datepicker" id="due_date" name="due_date" placeholder="Select date" autocomplete="off" required>
                            <small class="text-danger d-none" id="due_date_error"></small>
                        </div>

                        <!-- Document Upload -->
                        <div class="col-md-12 mb-3">
                            <label for="document_upload" class="form-label">
                                Document Upload
                            </label>
                            <input type="file" class="form-control" id="document_upload" name="document_upload" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            <small class="text-muted">Allowed formats: PDF, DOC, DOCX, JPG, PNG (Max 5MB)</small>
                            <small class="text-danger d-none" id="document_upload_error"></small>
                        </div>

                        <!-- Medical Result (Auto-populated) -->
                        <div class="col-md-12 mb-3 d-none" id="medical_result_container">
                            <div class="alert alert-info">
                                <strong>Medical Result:</strong>
                                <div id="medical_result_content">No result available</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="mdi mdi-close"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="saveComplianceBtn">
                        <i class="mdi mdi-content-save"></i> Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
/**
 * Open Other Compliance Modal
 */
function openOtherComplianceModal() {
    // Check if caregiver is selected
    if (typeof currentCaregiversId === 'undefined' || !currentCaregiversId) {
        toastr.error('No caregiver selected');
        return;
    }

    if (typeof agencyId === 'undefined' || !agencyId) {
        toastr.error('No agency selected');
        return;
    }

    // Reset form
    $('#otherComplianceForm')[0].reset();
    $('.text-danger').addClass('d-none');
    $('#medical_result_container').addClass('d-none');

    // Initialize datepickers
    initializeComplianceDatePickers();

    // Load medical dropdown
    loadMedicalDropdown();

    // Show modal
    $('#otherComplianceModal').modal('show');
}

/**
 * Initialize Date Pickers for Compliance Modal
 */
function initializeComplianceDatePickers() {
    setTimeout(function() {
        if ($('#date_performed').length && !$('#date_performed').hasClass('hasDatepicker')) {
            $('#date_performed').datepicker({
                dateFormat: 'mm/dd/yy',
                changeMonth: true,
                changeYear: true,
                yearRange: '-100:+10'
            });
        }

        if ($('#due_date').length && !$('#due_date').hasClass('hasDatepicker')) {
            $('#due_date').datepicker({
                dateFormat: 'mm/dd/yy',
                changeMonth: true,
                changeYear: true,
                yearRange: '-100:+10',
                minDate: 0 // Only future dates
            });
        }
    }, 300);
}

/**
 * Load Medical Dropdown via AJAX
 */
function loadMedicalDropdown() {
    if (!currentCaregiversId || !agencyId) {
        toastr.error('Caregiver ID or Agency ID not found');
        return;
    }

    $.ajax({
        url: '{{ url("hha/hha-other-compliances/all-other-compliance-list") }}',
        type: 'GET',
        data: {
            id: currentCaregiversId,
            agency_id: agencyId
        },
        success: function(response) {
            var options = '<option value="">Select Medical</option>';

            if (response.success && response.data && response.data.length > 0) {
                $.each(response.data, function(index, item) {
                    var medicalId = item.id || item.medical_id || item.MedicalID;
                    var medicalName = item.name || item.medical_name || item.MedicalName || 'N/A';
                    options += '<option value="' + medicalId + '">' + medicalName + '</option>';
                });
            } else {
                options += '<option value="" disabled>No medical records available</option>';
            }

            $('#medical_id').html(options);
        },
        error: function(xhr) {
            toastr.error('Failed to load medical dropdown');
            $('#medical_id').html('<option value="">Error loading data</option>');
        }
    });
}

/**
 * Medical Dropdown Change Event - Get Result
 */
$('#medical_id').on('change', function() {
    var selectedMedicalId = $(this).val();

    if (!selectedMedicalId) {
        $('#medical_result_container').addClass('d-none');
        return;
    }

    if (!currentCaregiversId || !agencyId) {
        toastr.error('Caregiver ID or Agency ID not found');
        return;
    }

    // Show loading
    $('#medical_result_content').html('<i class="mdi mdi-loading mdi-spin"></i> Loading...');
    $('#medical_result_container').removeClass('d-none');

    $.ajax({
        url: '{{ url("hha/hha-other-compliances/hha-complience-medical-results") }}',
        type: 'GET',
        data: {
            id: currentCaregiversId,
            agency_id: agencyId,
            medicaid_id: selectedMedicalId
        },
        success: function(response) {
            if (response.success && response.data) {
                var result = response.data;
                var resultHtml = '<ul class="mb-0">';

                // Display result fields
                if (result.result) resultHtml += '<li><strong>Result:</strong> ' + result.result + '</li>';
                if (result.status) resultHtml += '<li><strong>Status:</strong> ' + result.status + '</li>';
                if (result.description) resultHtml += '<li><strong>Description:</strong> ' + result.description + '</li>';
                if (result.notes) resultHtml += '<li><strong>Notes:</strong> ' + result.notes + '</li>';

                resultHtml += '</ul>';
                $('#medical_result_content').html(resultHtml);
            } else {
                $('#medical_result_content').html('No result available');
            }
        },
        error: function(xhr) {
            $('#medical_result_content').html('<span class="text-danger">Failed to load medical result</span>');
        }
    });
});

/**
 * Form Submission via AJAX
 */
$('#otherComplianceForm').on('submit', function(e) {
    e.preventDefault();

    // Clear previous errors
    $('.text-danger').addClass('d-none').text('');

    // Validate form
    var isValid = true;

    if (!$('#document_type').val()) {
        $('#document_type_error').removeClass('d-none').text('Document type is required');
        isValid = false;
    }

    if (!$('#medical_id').val()) {
        $('#medical_id_error').removeClass('d-none').text('Medical is required');
        isValid = false;
    }

    if (!$('#date_performed').val()) {
        $('#date_performed_error').removeClass('d-none').text('Date performed is required');
        isValid = false;
    }

    if (!$('#due_date').val()) {
        $('#due_date_error').removeClass('d-none').text('Due date is required');
        isValid = false;
    }

    // Validate file size
    var fileInput = $('#document_upload')[0];
    if (fileInput.files.length > 0) {
        var fileSize = fileInput.files[0].size / 1024 / 1024; // in MB
        if (fileSize > 5) {
            $('#document_upload_error').removeClass('d-none').text('File size must be less than 5MB');
            isValid = false;
        }
    }

    if (!isValid) {
        return false;
    }

    // Prepare form data
    var formData = new FormData(this);
    formData.append('caregiver_id', currentCaregiversId);
    formData.append('agency_id', agencyId);
    formData.append('_token', '{{ csrf_token() }}');

    // Disable save button
    $('#saveComplianceBtn').prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> Saving...');

    // Submit via AJAX
    $.ajax({
        url: '{{ url("hha/hha-other-compliances/store") }}',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                toastr.success(response.message || 'Other compliance created successfully');
                $('#otherComplianceModal').modal('hide');

                // Reload compliance tab
                if (typeof loadCaregiverTab === 'function') {
                    loadCaregiverTab('compliance');
                }
            } else {
                toastr.error(response.message || 'Failed to create compliance');
            }
        },
        error: function(xhr) {
            var errorMsg = 'Failed to save compliance';

            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }

            if (xhr.responseJSON && xhr.responseJSON.errors) {
                var errors = xhr.responseJSON.errors;
                $.each(errors, function(field, messages) {
                    var fieldError = $('#' + field + '_error');
                    if (fieldError.length) {
                        fieldError.removeClass('d-none').text(messages[0]);
                    }
                });
            }

            toastr.error(errorMsg);
        },
        complete: function() {
            // Re-enable save button
            $('#saveComplianceBtn').prop('disabled', false).html('<i class="mdi mdi-content-save"></i> Save');
        }
    });
});
</script>