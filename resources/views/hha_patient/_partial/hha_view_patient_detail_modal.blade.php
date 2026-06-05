<style>
    .hha-patient-view-modal {
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

    .hha-patient-view-modal.show {
        display: block;
    }

    .hha-patient-modal-content {
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

    .hha-patient-modal-header {
        padding: 20px;
        border-bottom: 1px solid #dee2e6;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-shrink: 0;
    }

    .hha-patient-modal-header h4 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 600;
    }

    .hha-patient-modal-close {
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

    .hha-patient-modal-close:hover,
    .hha-patient-modal-close:focus {
        color: #000;
    }

    .hha-patient-modal-body {
        padding: 0;
        overflow: hidden;
        flex: 1;
        display: flex;
    }

    .hha-patient-tabs {
        display: flex;
        flex-direction: column;
        border-right: 2px solid #dee2e6;
        background-color: #f8f9fa;
        width: 250px;
        flex-shrink: 0;
        overflow-y: auto;
        padding: 10px 0;
    }

    .hha-patient-tab-button {
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

    .hha-patient-tab-button i {
        font-size: 18px;
        min-width: 20px;
    }

    .hha-patient-tab-button:hover {
        background-color: #e9ecef;
        color: #007bff;
    }

    .hha-patient-tab-button.active {
        color: #007bff;
        border-left-color: #007bff;
        background-color: #fff;
        font-weight: 600;
    }

    .hha-patient-tab-content-wrapper {
        flex: 1;
        overflow-y: auto;
        background-color: #fff;
    }

    .hha-patient-tab-content {
        display: none;
        padding: 30px;
        animation: fadeIn 0.3s;
        min-height: 100%;
    }

    .hha-patient-tab-content.active {
        display: block;
    }

    /* Responsive - Stack vertically on mobile */
    @media (max-width: 768px) {
        .hha-patient-modal-body {
            flex-direction: column;
        }

        .hha-patient-tabs {
            width: 100%;
            flex-direction: row;
            overflow-x: auto;
            border-right: none;
            border-bottom: 2px solid #dee2e6;
            padding: 0;
        }

        .hha-patient-tab-button {
            border-left: none;
            border-bottom: 3px solid transparent;
            padding: 12px 15px;
            justify-content: center;
            min-width: 120px;
        }

        .hha-patient-tab-button.active {
            border-left-color: transparent;
            border-bottom-color: #007bff;
        }

        .hha-patient-tab-button span {
            display: none;
        }

        .hha-patient-tab-content {
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
    #patientFullCalendar {
        max-width: 100%;
        margin: 0 auto;
    }

    #patientFullCalendar .fc-toolbar {
        margin-bottom: 20px;
    }

    #patientFullCalendar .fc-event {
        cursor: pointer;
        border-radius: 3px;
        padding: 2px 5px;
        font-size: 0.85rem;
    }

    #patientFullCalendar .fc-event:hover {
        opacity: 0.8;
    }

    #patientFullCalendar .fc-day-grid-event {
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
    
    #statsSection1 .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        color: white !important;
        padding: 0.875rem !important;
        border-radius: 8px !important;
        margin-bottom: 0.75rem;
        border: none !important;
        box-shadow: 0 1px 6px rgba(0, 0, 0, 0.1) !important;
    }

    #statsSection1 .stats-card h3 {
        font-size: 1.75rem !important;
        font-weight: 700 !important;
        margin: 0 !important;
        color: white !important;
    }

    #statsSection1 .stats-card p {
        margin: 0 !important;
        opacity: 0.9;
        color: white !important;
        font-size: 0.75rem !important;
    }

    #statsSection1 .stats-card i {
        color: white !important;
        font-size: 1.25rem !important;
    }
</style>
<div id="hhaPatientViewModal" class="hha-patient-view-modal" aria-labelledby="patientModalTitle" aria-hidden="true">
            <div class="hha-patient-modal-content">
                <!-- Modal Header -->
                <div class="hha-patient-modal-header" style="background:#1e1e2f !important;    color: white;">
                    <h4 id="patientModalTitle">Patient Details - <span id="hhaPatientName">Loading...</span></h4>
                    <button class="hha-patient-modal-close" onclick="closePatientModal()" aria-label="Close modal">&times;</button>
                </div>

                <!-- Modal Body with Vertical Tabs -->
                <div class="hha-patient-modal-body">
                    <!-- Vertical Tab Navigation -->
                    <div class="hha-patient-tabs" role="tablist">
                        <button class="hha-patient-tab-button active" role="tab" aria-selected="true" aria-controls="patient-demographic-panel" id="hha-patient-demographic-tab">
                            <i class="mdi mdi-account"></i>
                            <span>Demographic Details</span>
                        </button>
                        <button class="hha-patient-tab-button" role="tab" aria-selected="false" aria-controls="calendar-panel" id="hha-patient-calendar-tab">
                            <i class="mdi mdi-calendar"></i>
                            <span>Calendar</span>
                        </button>
                        <button class="hha-patient-tab-button" role="tab" aria-selected="false" aria-controls="authorization-panel" id="hha-patient-authorization-tab">
                            <i class="mdi mdi-clock"></i>
                            <span>Authorization Info Section</span>
                        </button>
                        <button class="hha-patient-tab-button" role="tab" aria-selected="false" aria-controls="notes-panel" id="hha-patient-notes-tab">
                            <i class="mdi mdi-note-text"></i>
                            <span>Notes</span>
                        </button>
                        <button class="hha-patient-tab-button" role="tab" aria-selected="false" aria-controls="clinical-panel" id="hha-patient-clinical-tab">
                            <i class="mdi mdi-school"></i>
                            <span>Clinical</span>
                        </button>
                        <button class="hha-patient-tab-button" role="tab" aria-selected="false" aria-controls="poc-info-panel" id="hha-patient-poc-info-tab">
                            <i class="mdi mdi-medical-bag"></i>
                            <span>POC Info</span>
                        </button>
                        <button class="hha-patient-tab-button" role="tab" aria-selected="false" aria-controls="document-panel" id="hha-patient-document-tab">
                            <i class="mdi mdi-file-document"></i>
                            <span>Document</span>
                        </button>
                        <button class="hha-patient-tab-button" role="tab" aria-selected="false" aria-controls="contract-panel" id="hha-patient-contract-tab">
                            <i class="mdi mdi-file-document"></i>
                            <span>Contract</span>
                        </button>
                        <button class="hha-patient-tab-button" role="tab" aria-selected="false" aria-controls="discipline-panel" id="hha-patient-discipline-tab">
                            <i class="mdi mdi-cog"></i>
                            <span>Discipline</span>
                        </button>
                        
                        <button class="hha-patient-tab-button" role="tab" aria-selected="false" aria-controls="preferences-panel" id="hha-patient-preferences-tab">
                            <i class="mdi mdi-cog"></i>
                            <span>Preferences</span>
                        </button>
                        <button class="hha-patient-tab-button" role="tab" aria-selected="false" aria-controls="mdorder-panel" id="hha-patient-mdorder-tab">
                            <i class="mdi mdi-file-document-edit"></i>
                            <span>MDOrder</span>
                        </button>
                    </div>

                    <!-- Tab Content Wrapper -->
                    <div class="hha-patient-tab-content-wrapper">
                        <!-- Tab Content Panels -->
                        <div class="hha-patient-tab-content active" id="demographicPatient-panel" role="tabpanel" aria-labelledby="hha-patient-demographic-tab">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 15px 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                        <h4 class="mb-0 text-white">
                                            <i class="mdi mdi-account-details"></i> Demographic Details
                                        </h4>
                                        <small class="text-white" style="opacity: 0.9;">Comprehensive patient demographic information</small>
                                    </div>
                                </div>
                            </div>
                            <div id="demographicPatientContent">
                                
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

                        <div class="hha-patient-tab-content" id="pcalendar-panel" role="tabpanel" aria-labelledby="hha-patient-calendar-tab">
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

                            <div id="pcalendarContent">
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
                                        <div id="patientFullCalendar"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="hha-patient-tab-content" id="authorization-panel" role="tabpanel" aria-labelledby="hha-patient-authorization-tab">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 15px 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                        <h4 class="mb-0 text-white">
                                            <i class="mdi mdi-account-details"></i> Authorization Info Section
                                        </h4>
                                       
                                    </div>
                                </div>
                            </div>
                            <div id="authorizationContent">
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

                        <div class="hha-patient-tab-content" id="pnotes-panel" role="tabpanel" aria-labelledby="hha-patient-notes-tab">
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
                            <div id="pnotesContent">
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

                        <div class="hha-patient-tab-content" id="clinical-panel" role="tabpanel" aria-labelledby="hha-patient-clinical-tab">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 15px 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                        <h4 class="mb-0 text-white">
                                            <i class="mdi mdi-school"></i> Clinical
                                        </h4>
                                        <small class="text-white" style="opacity: 0.9;">Complete training history and certifications</small>
                                    </div>
                                </div>
                            </div>
                            <div id="clinicalContent">
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

                        <div class="hha-patient-tab-content" id="pocInfo-panel" role="tabpanel" aria-labelledby="hha-patient-poc-info-tab">
                            <div class="row mb-3">
                                <div class="col-12">
                                    
                                    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 15px 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                                            <h4 class="mb-0 text-white">
                                                <i class="mdi mdi-medical-bag"></i> POC Info
                                            </h4>
                                            
                                        </div>
                                        
                                        
                                    </div>
                                </div>
                            </div>
                            <div id="pocInfoContent">
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

                        <div class="hha-patient-tab-content" id="pdocument-panel" role="tabpanel" aria-labelledby="hha-patient-document-tab">
                            <div class="row mb-3">
                                <div class="col-12">
                                    
                                    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 15px 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                                            <h4 class="mb-0 text-white">
                                                <i class="mdi mdi-medical-bag"></i> Document
                                            </h4>
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="pdocumentContent">
                                
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

                        <div class="hha-patient-tab-content" id="contract-panel" role="tabpanel" aria-labelledby="hha-patient-contract-tab">
                            <div class="row mb-3">
                                    <div class="col-12">
                                        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 15px 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                            <h4 class="mb-0 text-white">
                                                <i class="mdi mdi-file-check"></i>Contract
                                            </h4>
                                        
                                        </div>
                                    </div>
                                </div>
                            <div id="contractContent">
                                
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

                        <div class="hha-patient-tab-content" id="discipline-panel" role="tabpanel" aria-labelledby="hha-patient-discipline-tab">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 15px 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                        <h4 class="mb-0 text-white">
                                            <i class="mdi mdi-file-check"></i>Discipline
                                        </h4>
                                    
                                    </div>
                                </div>
                            </div>
                            <div id="disciplineContent">
                                
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
                        <div class="hha-patient-tab-content" id="ppreferences-panel" role="tabpanel" aria-labelledby="hha-patient-preferences-tab">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 15px 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                        <h4 class="mb-0 text-white">
                                            <i class="mdi mdi-file-check"></i>Preferences
                                        </h4>
                                    
                                    </div>
                                </div>
                            </div>
                            <div id="ppreferencesContent">
                                
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
                        <div class="hha-patient-tab-content" id="mdorder-panel" role="tabpanel" aria-labelledby="hha-patient-mdorder-tab">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 15px 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                        <h4 class="mb-0 text-white">
                                            <i class="mdi mdi-file-document-edit"></i> MD Order
                                        </h4>
                                        <small class="text-white" style="opacity: 0.9;">Medical Doctor Order information and history</small>
                                    </div>
                                </div>
                            </div>
                         
                            <div class="row" id="statsSection1">
                                <div class="col-md-3">
                                    <div class="stats-card">
                                        <i class="fa fa-file-o fa-2x mb-2"></i>
                                        <h3 id="totalDocuments_patient">0</h3>
                                        <p>Total Documents</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="stats-card" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                                        <i class="fa fa-check-circle fa-2x mb-2"></i>
                                        <h3 id="sentDocuments_patient">0</h3>
                                        <p>Sent Documents</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="stats-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                        <i class="fa fa-clock-o fa-2x mb-2"></i>
                                        <h3 id="pendingDocuments_patient">0</h3>
                                        <p>Pending Documents</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="stats-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                                        <i class="fa fa-paper-plane fa-2x mb-2"></i>
                                        <h3 id="receivedDocuments_patient">0</h3>
                                        <p>Receive Documents</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="stats-card" style="background: linear-gradient(135deg, #4facfe 0%, #155724 100%);">
                                        <i class="fa fa-pencil fa-2x mb-2"></i>
                                        <h3 id="signedDocuments_patient">0</h3>
                                        <p>Signed Documents</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="mdorderContent">
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
                    </div>
                </div>
            </div>
        </div>