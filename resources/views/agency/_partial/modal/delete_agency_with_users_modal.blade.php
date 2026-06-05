<!-- Delete Agency with User Merge Modal -->
<div class="modal fade" id="deleteAgencyModal" tabindex="-1" role="dialog" aria-labelledby="deleteAgencyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title" id="deleteAgencyModalLabel">
                    <i class="mdi mdi-delete"></i> Delete Agency
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="delete_agency_id" value="">

                <!-- Step 1: Confirmation Question -->
                <div id="step1_confirmation">
                    <div class="form-group mb-2">
                        <label class="font-weight-bold mb-2">Do you want to merge this agency's user data into another active agency?</label>
                        <div class="mt-2">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="merge_option" id="merge_yes" value="yes">
                                <label class="form-check-label" for="merge_yes">
                                    Yes, merge user data to another agency
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="merge_option" id="merge_no" value="no">
                                <label class="form-check-label" for="merge_no">
                                    No, just delete the agency
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="text-right mt-2">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                            <i class="mdi mdi-close"></i> Cancel
                        </button>
                        <button type="button" class="btn btn-primary btn-sm" id="continueBtn" disabled>
                            <i class="mdi mdi-arrow-right"></i> Continue
                        </button>
                    </div>
                </div>

                <!-- Step 2: Agency Selection & User List (Hidden initially) -->
                <div id="step2_merge_data" style="display: none;">
                    <div class="alert alert-info border-left-info mb-2 py-2">
                        <div class="d-flex align-items-center">
                            <i class="mdi mdi-information-outline mdi-24px mr-2"></i>
                            <div>
                                <strong>Step 2:</strong> Select target agency and choose users to merge
                            </div>
                        </div>
                    </div>

                    <!-- Active Agency Dropdown -->
                    <div class="form-group mb-2">
                        <label for="target_agency_select" class="font-weight-bold mb-1">
                            Select Target Agency <span class="text-danger">*</span>
                        </label>
                        <select id="target_agency_select" class="form-control form-control-sm select2-agency-target" style="width: 100%;">
                            <option value="">-- Select Active Agency --</option>
                        </select>
                        <small class="form-text text-muted mt-1">
                            <i class="mdi mdi-lightbulb-outline"></i> Choose the active agency where users will be transferred.
                        </small>
                    </div>

                    <!-- Loading Indicator -->
                    <div id="usersLoadingIndicator" style="display: none;">
                        <div class="text-center py-2">
                            <i class="mdi mdi-loading mdi-spin mdi-24px"></i>
                            <p class="mb-0">Loading users...</p>
                        </div>
                    </div>

                    <!-- Users List -->
                    <div id="usersListContainer" style="display: none;">
                        <div class="form-group mb-2">
                            <label class="font-weight-bold mb-1">Users from Current Agency</label>
                            <div class="table-responsive" style="max-height: 200px; overflow-y: auto;">
                                <table class="table table-bordered table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th width="50">
                                                <input type="checkbox" id="selectAllUsers">
                                            </th>
                                            <th>User Name</th>
                                            <th>Email</th>
                                            <th width="150">Create Domain</th>
                                        </tr>
                                    </thead>
                                    <tbody id="usersTableBody">
                                        <!-- Users will be loaded here via AJAX -->
                                    </tbody>
                                </table>
                            </div>
                            <small class="form-text text-muted mt-1">
                                <i class="mdi mdi-information"></i> Select users to merge and check "Create Domain" if needed.
                            </small>
                        </div>

                        <div class="alert alert-warning border-left-warning mb-2 py-2">
                            <div class="d-flex align-items-start">
                                <i class="mdi mdi-alert-outline mdi-24px mr-2"></i>
                                <div>
                                    <strong>Important:</strong> Selected users' agency will be updated to the target agency.
                                    Unselected users will remain with the deleted agency.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons with Back Button on Right -->
                    <div class="text-right mt-2" id="mergeActionButtons" style="display: none;">
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="backToStep1Btn">
                            <i class="mdi mdi-arrow-left"></i> Back
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                            <i class="mdi mdi-close"></i> Cancel
                        </button>
                        <button type="button" class="btn btn-success btn-sm" id="confirmMergeBtn">
                            <i class="mdi mdi-check"></i> Confirm & Delete Agency
                        </button>
                    </div>
                </div>

                <!-- Step 3: Direct Delete Confirmation (Hidden initially) -->
                <div id="step3_direct_delete" style="display: none;">
                    <div class="alert alert-danger border-left-danger mb-2 py-2">
                        <div class="d-flex align-items-start">
                            <i class="mdi mdi-alert-circle-outline mdi-24px mr-2"></i>
                            <div>
                                <strong>Final Confirmation:</strong> Are you sure you want to delete this agency without merging user data?
                                <br><small>This will set the agency's delete_flag to 'Y'. Users will remain associated with this deleted agency.</small>
                            </div>
                        </div>
                    </div>

                    <div class="text-right mt-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="backToStep1FromDeleteBtn">
                            <i class="mdi mdi-arrow-left"></i> Back
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                            <i class="mdi mdi-close"></i> Cancel
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" id="confirmDirectDeleteBtn">
                            <i class="mdi mdi-delete"></i> Yes, Delete Agency
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Delete Agency Modal Enhancements - Compact Design */
    #deleteAgencyModal .modal-header {
        background-color: #dc3545;
        border-bottom: 3px solid #bd2130;
        padding: 0.75rem 1rem;
    }

    #deleteAgencyModal .modal-body {
        padding: 1rem;
    }

    #deleteAgencyModal .modal-title {
        font-size: 1.1rem;
    }

    #deleteAgencyModal .border-left-info {
        border-left: 4px solid #17a2b8;
        background-color: #d1ecf1;
    }

    #deleteAgencyModal .border-left-warning {
        border-left: 4px solid #ffc107;
        background-color: #fff3cd;
    }

    #deleteAgencyModal .border-left-danger {
        border-left: 4px solid #dc3545;
        background-color: #f8d7da;
    }

    #deleteAgencyModal .alert {
        font-size: 0.9rem;
    }

    #deleteAgencyModal .alert i {
        font-size: 1.2rem;
    }

    #deleteAgencyModal .select2-container {
        z-index: 9999 !important;
    }

    #deleteAgencyModal .form-group {
        margin-bottom: 0.5rem;
    }

    #deleteAgencyModal .form-group label {
        font-size: 0.9rem;
        margin-bottom: 0.3rem;
    }

    #deleteAgencyModal .form-text {
        font-size: 0.8rem;
    }

    #deleteAgencyModal .form-check {
        position: relative;
        display: flex;
        align-items: center;
        padding-left: 0 !important;
        min-height: auto;
        margin-bottom: 8px;
    }

    #deleteAgencyModal .form-check-input {
        position: static !important;
        width: 18px;
        height: 18px;
        margin: 0 10px 0 0 !important;
        cursor: pointer;
        flex-shrink: 0;
    }

    #deleteAgencyModal .form-check-label {
        cursor: pointer;
        font-size: 0.9rem;
        margin: 0 !important;
        padding-left: 0 !important;
        line-height: 1.4;
    }

    #deleteAgencyModal .table {
        font-size: 0.85rem;
    }

    #deleteAgencyModal .table thead th {
        background-color: #f8f9fa;
        font-weight: 600;
        position: sticky;
        top: 0;
        z-index: 10;
        padding: 0.5rem;
    }

    #deleteAgencyModal .table tbody td {
        padding: 0.4rem;
    }

    #deleteAgencyModal .modal-content {
        border: none;
        border-radius: 8px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    }

    #deleteAgencyModal .btn {
        padding: 0.4rem 0.8rem;
        font-weight: 600;
        border-radius: 4px;
        transition: all 0.3s ease;
        font-size: 0.875rem;
    }

    #deleteAgencyModal .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }

    #selectAllUsers {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    .user-checkbox, .domain-checkbox {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }
</style>
