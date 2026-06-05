<style>
    /* Employee View Modal Styles */
    .employee-view-modal {
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

    .employee-view-modal.show {
        display: block;
    }

    .employee-modal-content {
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

    .employee-modal-header {
        padding: 20px;
        border-bottom: 1px solid #dee2e6;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-shrink: 0;
        background: #1e1e2f !important;
        color: white;
        border-radius: 8px 8px 0 0;
    }

    .employee-modal-header h4 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 600;
    }

    .employee-modal-close {
        font-size: 28px;
        font-weight: bold;
        color: #fff;
        cursor: pointer;
        background: none;
        border: none;
        padding: 0;
        width: 30px;
        height: 30px;
        line-height: 1;
    }

    .employee-modal-close:hover,
    .employee-modal-close:focus {
        color: #ccc;
    }

    .employee-modal-body {
        padding: 0;
        overflow: hidden;
        flex: 1;
        display: flex;
    }

    .employee-tabs {
        display: flex;
        flex-direction: column;
        border-right: 2px solid #dee2e6;
        background-color: #f8f9fa;
        width: 250px;
        flex-shrink: 0;
        overflow-y: auto;
        padding: 10px 0;
    }

    .employee-tab-button {
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

    .employee-tab-button i {
        font-size: 18px;
        min-width: 20px;
    }

    .employee-tab-button:hover {
        background-color: #e9ecef;
        color: #007bff;
    }

    .employee-tab-button.active {
        color: #007bff;
        border-left-color: #007bff;
        background-color: #fff;
        font-weight: 600;
    }

    .employee-tab-content-wrapper {
        flex: 1;
        overflow-y: auto;
        background-color: #fff;
    }

    .employee-tab-content {
        display: none;
        padding: 30px;
        animation: fadeIn 0.3s;
        min-height: 100%;
    }

    .employee-tab-content.active {
        display: block;
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

    /* Responsive - Stack vertically on mobile */
    @media (max-width: 768px) {
        .employee-modal-body {
            flex-direction: column;
        }

        .employee-tabs {
            width: 100%;
            flex-direction: row;
            overflow-x: auto;
            border-right: none;
            border-bottom: 2px solid #dee2e6;
            padding: 0;
        }

        .employee-tab-button {
            border-left: none;
            border-bottom: 3px solid transparent;
            padding: 12px 15px;
            justify-content: center;
            min-width: 120px;
        }

        .employee-tab-button.active {
            border-left-color: transparent;
            border-bottom-color: #007bff;
        }

        .employee-tab-button span {
            display: none;
        }

        .employee-tab-content {
            padding: 20px;
        }
    }

    /* Medical Table Styling */
    #medical-content-table thead th {
        background-color: #f8f9fa;
        font-weight: 600;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #495057;
        border-bottom: 2px solid #dee2e6;
    }

    #medical-content-table tbody td {
        font-size: 14px;
        vertical-align: middle;
        padding: 12px;
    }

    #medical-content-table tbody tr:hover {
        background-color: #f8f9fa;
    }
</style>

<div id="employeeDetailsModal" class="employee-view-modal" aria-labelledby="employeeModalTitle" aria-hidden="true">
    <div class="employee-modal-content">
        <!-- Modal Header -->
        <div class="employee-modal-header">
            <h4 id="employeeModalTitle">Employee Details - <span id="modal-employee-code">Loading...</span></h4>
            <button class="employee-modal-close" onclick="closeEmployeeModal()" aria-label="Close modal">&times;</button>
        </div>

        <!-- Modal Body with Vertical Tabs -->
        <div class="employee-modal-body">
            <!-- Vertical Tab Navigation -->
            <div class="employee-tabs" role="tablist">
                <button class="employee-tab-button active" role="tab" aria-selected="true" aria-controls="demographic-panel" id="demographic-tab" onclick="switchEmployeeTab('demographic')">
                    <i class="mdi mdi-account"></i>
                    <span>Demographic Details</span>
                </button>
                <button class="employee-tab-button" role="tab" aria-selected="false" aria-controls="medical-panel" id="medical-tab" onclick="switchEmployeeTab('medical')">
                    <i class="mdi mdi-medical-bag"></i>
                    <span>Medical</span>
                </button>
                <input type="hidden" id="third_party_employee_code">
            </div>

            <!-- Tab Content Wrapper -->
            <div class="employee-tab-content-wrapper">
                <!-- Demographic Tab Panel -->
                <div class="employee-tab-content active" id="demographic-panel" role="tabpanel" aria-labelledby="demographic-tab">
                    <div class="row mb-3">
                        <div class="col-12">
                            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 10px 8px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                <h4 class="mb-0 text-white">
                                    <i class="mdi mdi-account-details"></i> Demographic Details
                                </h4>
                                <small class="text-white" style="opacity: 0.9;">Comprehensive employee demographic information</small>
                            </div>
                        </div>
                    </div>

                    <!-- Loader -->
                    <div id="visiting-demographic-loader" style="display:none;">
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

                    <!-- Content Container -->
                    <div id="visiting-demographic-content">
                        <div class="row" id="visiting-demographic-data">
                            <div class="col-12 text-center text-muted py-5">
                                <i class="mdi mdi-account-circle" style="font-size: 48px;"></i>
                                <p class="mt-2">Loading demographic details...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Medical Tab Panel -->
                <div class="employee-tab-content" id="medical-panel" role="tabpanel" aria-labelledby="medical-tab">
                    <div class="row mb-3">
                        <div class="col-12">
                            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 10px 8px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                <h4 class="mb-0 text-white">
                                    <i class="mdi mdi-medical-bag"></i> Medical Information
                                </h4>
                                <small class="text-white" style="opacity: 0.9;">Employee medical records and history</small>
                            </div>
                        </div>
                    </div>

                    <!-- Loader -->
                    <div id="medical-loader" style="display:none;">
                        <div class="shimmer-wrapper">
                            <div class="shimmer shimmer-header"></div>
                            <div class="shimmer-card">
                                <div class="shimmer shimmer-line long"></div>
                                <div class="shimmer shimmer-line medium"></div>
                                <div class="shimmer shimmer-line long"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Medical Table Container -->
                    <div id="medical-content">
                        <div class="card">
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered mb-0" id="medical-content-table">
                                        <thead class="thead-light">
                                            <tr>
                                                <th width="60">No</th>
                                                <th width="150">Medical ID</th>
                                                <th>Medical Name</th>
                                                <th width="120">Status</th>
                                                <th width="120">Due Date</th>
                                            </tr>
                                        </thead>
                                        <tbody id="medical-table-body">
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-5">
                                                    <i class="mdi mdi-file-document-outline" style="font-size: 48px;"></i>
                                                    <p class="mt-2">No medical records found</p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
