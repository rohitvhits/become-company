<div class="modal fade" id="show-patient-cretae-poc-modal" aria-modal="true" style="padding-right: 17px; display: none;">
    <div class="modal-dialog modal-xl modal-dialog-centered" style="margin-top:10px">
        <div class="modal-content border-0 shadow-lg">
            <!-- Modal Header -->
            <div class="modal-header text-white" style="background-color:#000000 !important">
                <h5 class="modal-title font-weight-bold">
                    <i class="mdi mdi-file-document-outline mr-2"></i> Create Patient Plan of Care (POC)
                </h5>
                <button type="button" class="close text-white" onclick="clearPaientPOCData();" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form action="javascript:void(0)" method="POST" id="add_patient_poc">
                <input type="hidden" name="_token" value="{{ csrf_token()}}">

                <div class="modal-body p-4">

                    <!-- Basic Information Section -->
                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-header bg-light border-bottom">
                            <h6 class="mb-0 font-weight-bold"><i class="mdi mdi-information-outline mr-2"></i> Basic Information</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row">
                                <!-- Office ID -->
                                <div class="col-md-3 mb-3">
                                    <label for="" class="font-weight-semibold">Office </label><br>
                                    <span id="hha_office_poc_id"></span>
                                </div>

                                <!-- Shift -->
                                <div class="col-md-3 mb-3">
                                    <label for="shift" class="font-weight-semibold">Shift <span class="text-danger">*</span></label>
                                    <select id="shift" name="shift" class="form-control">
                                        <option value="">-- Select Shift --</option>
                                        <option value="-1">All</option>
                                        <option value="1">Shift 1</option>
                                        <option value="2">Shift 2</option>
                                        <option value="3">Shift 3</option>
                                    </select>
                                    <span id="shift_error" class="error mt-2 text-danger d-block"></span>
                                </div>

                                <!-- Start Date -->
                                <div class="col-md-3 mb-3">
                                    <label for="start_date" class="font-weight-semibold">
                                        <i class="mdi mdi-calendar-start mr-1"></i> Shift Start Date <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="start_date" class="form-control" id="start_date_id"
                                           placeholder="MM/DD/YYYY"
                                           data-inputmask="'alias': 'datetime'"
                                           data-inputmask-inputformat="mm/dd/yyyy">
                                    <span id="start_date_id_error" class="error mt-2 text-danger d-block"></span>
                                </div>

                                <!-- Stop Date -->
                                <div class="col-md-3 mb-3">
                                    <label for="stop_date" class="font-weight-semibold">
                                        <i class="mdi mdi-calendar-end mr-1"></i> Shift Stop Date <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="stop_date" class="form-control" id="stop_date_id"
                                           placeholder="MM/DD/YYYY"
                                           data-inputmask="'alias': 'datetime'"
                                           data-inputmask-inputformat="mm/dd/yyyy">
                                    <span id="stop_date_id_error" class="error mt-2 text-danger d-block"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tasks Section -->
                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-header bg-light border-bottom">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0 font-weight-bold"><i class="mdi mdi-clipboard-list-outline mr-2"></i> Task Details</h6>
                                    <small class="text-muted">Add tasks for this Plan of Care</small>
                                </div>
                                <div class="d-flex align-items-center">
                                    <small class="text-info mr-3">
                                        <i class="mdi mdi-information-outline"></i> Select an office above to load available tasks
                                    </small>
                                   
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered mb-0" id="pocTaskTable">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width:3%" class="text-center">#</th>
                                            <th style="width:17%">Task</th>
                                            <th style="width:9%" class="text-center">Minutes</th>
                                            <th style="width:7%" class="text-center">
                                                As<br/>Requested
                                            </th>
                                            <th style="width:14%" class="text-center">
                                                Times/Week<br/>
                                                <small class="text-muted">(Min - Max)</small>
                                            </th>
                                            <th style="width:16%">Instruction</th>
                                            <th style="width:28%">Days of Week</th>
                                            <th style="width:6%" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="pocTaskTableBody">
                                        {{-- Row 1 is rendered by default; additional rows added dynamically --}}
                                        <tr class="poc-task-row" data-row="1">
                                            <!-- Row Number -->
                                            <td class="text-center align-middle font-weight-bold poc-row-number">1</td>

                                            <!-- Task Selection -->
                                            <td>
                                                <select id="task_id_1" name="task_id[]" class="form-control form-control-sm task">
                                                    <option value="">Select Task</option>
                                                </select>
                                                <span id="task_id_1_error" class="error text-danger d-block" style="font-size: 0.75rem;"></span>
                                            </td>

                                            <!-- Minutes -->
                                            <td>
                                                <input type="text"
                                                       onkeypress="return isNumber(event)"
                                                       class="form-control form-control-sm text-center"
                                                       id="minutes_1"
                                                       name="minutes[]"
                                                       placeholder="0">
                                                <span id="minutes_1_error" class="error text-danger d-block" style="font-size: 0.75rem;"></span>
                                            </td>

                                            <!-- As Requested Checkbox -->
                                            <td class="text-center align-middle">
                                                <div class="form-check d-flex justify-content-center">
                                                    <input type="checkbox"
                                                           name="as_requested[]"
                                                           value="0"
                                                           class="form-check-input"
                                                           id="as_requested_1"
                                                           style="position: relative; margin: 0;">
                                                </div>
                                            </td>

                                            <!-- Times a Week (Min-Max) -->
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <input type="text"
                                                           onkeypress="return isNumber(event)"
                                                           class="form-control form-control-sm text-center"
                                                           id="min_time_1"
                                                           name="mintime[]"
                                                           placeholder="Min"
                                                           style="width: 60px;">
                                                    <span class="mx-1">-</span>
                                                    <input type="text"
                                                           onkeypress="return isNumber(event)"
                                                           class="form-control form-control-sm text-center"
                                                           id="maxtime_1"
                                                           name="maxtime[]"
                                                           placeholder="Max"
                                                           style="width: 60px;">
                                                </div>
                                                <span id="times_week_1_error" class="error text-danger d-block" style="font-size: 0.75rem;"></span>
                                            </td>

                                            <!-- Instruction -->
                                            <td>
                                                <input type="text"
                                                       class="form-control form-control-sm"
                                                       name="instruction[]"
                                                       id="instruction_1"
                                                       placeholder="Task instructions...">
                                                <span id="instruction_1_error" class="error text-danger d-block" style="font-size: 0.75rem;"></span>
                                            </td>

                                            <!-- Days of Week -->
                                            <td>
                                                <div class="d-flex flex-wrap">
                                                    @foreach(['Sat', 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri'] as $day)
                                                        <div class="form-check form-check-inline mb-0" style="min-width: 50px;">
                                                            <label class="form-check-label" style="font-size: 0.85rem;">
                                                                <input type="checkbox"
                                                                       class="form-check-input"
                                                                       name="days_1[]"
                                                                       value="{{ $day }}">
                                                                {{ $day }}
                                                                <i class="input-helper"></i>
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <span id="days_1_error" class="error text-danger d-block" style="font-size: 0.75rem;"></span>
                                            </td>

                                            <!-- Remove Button -->
                                            <td class="text-center align-middle">
                                                <button type="button" class="btn btn-outline-danger btn-sm remove-task-row-btn" title="Remove Task" disabled>
                                                    <i class="mdi mdi-trash-can-outline"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn btn-outline-success btn-sm mt-2 mb-2 ml-2" id="addTaskRowBtn" title="Add Task Row">
                                <i class="mdi mdi-plus-circle-outline mr-1"></i> Add Task
                            </button>
                        </div>
                    </div>

                    <!-- POC Notes Section -->
                    <div class="card mb-3 border-0 shadow-sm">
                        <div class="card-header bg-light border-bottom">
                            <h6 class="mb-0 font-weight-bold"><i class="mdi mdi-note-text-outline mr-2"></i> Additional Notes</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="form-group mb-0">
                                <label for="poc_note" class="font-weight-semibold">POC Note</label>
                                <textarea class="form-control"
                                          name="poc_note"
                                          id="poc_note"
                                          rows="4"
                                          placeholder="Enter any additional notes or special instructions for this Plan of Care..."></textarea>
                                <span id="poc_note_error" class="error mt-2 text-danger d-block"></span>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Modal Footer -->
                <div class="modal-footer border-top-0 bg-light">
                    <div class="d-flex justify-content-end align-items-center w-100">
                        <div class="loader-inner d-none mr-3">
                            <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                            <span class="ml-2">Processing...</span>
                        </div>
                        <button type="button"
                                class="btn btn-success btn-sm px-4 mr-2"
                                id="savePatientPOCdetails">
                            <span id="btn-save-text">Save</span>
                        </button>
                        <button type="button"
                                onclick="clearPaientPOCData();"
                                class="btn btn-secondary btn-sm px-4"
                                data-dismiss="modal">
                            Cancel
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Custom styles for POC Modal */
    #show-patient-cretae-poc-modal .modal-footer {
        padding: 4px 1px !important;
    }

    #show-patient-cretae-poc-modal .modal-content {
        border-radius: 8px;
    }

    #show-patient-cretae-poc-modal .card {
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    #show-patient-cretae-poc-modal .card-header {
        padding: 12px 20px;
        background-color: #f8f9fa !important;
    }

    #show-patient-cretae-poc-modal .card-body {
        background-color: #fff;
    }

    #show-patient-cretae-poc-modal .form-control:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }

    #show-patient-cretae-poc-modal .form-control-lg {
        font-size: 1rem;
        padding: 0.5rem 0.75rem;
    }

    #show-patient-cretae-poc-modal .table-hover tbody tr:hover {
        background-color: #f8f9fc;
    }

    #show-patient-cretae-poc-modal label {
        margin-bottom: 8px;
        color: #495057;
        font-size: 0.95rem;
    }

    #show-patient-cretae-poc-modal .font-weight-semibold {
        font-weight: 600;
    }

    #show-patient-cretae-poc-modal .thead-light th {
        background-color: #f8f9fc;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
        padding: 12px 8px;
        vertical-align: middle;
        font-size: 0.9rem;
    }

    #show-patient-cretae-poc-modal .table td {
        padding: 10px 8px;
        vertical-align: middle;
    }

    #show-patient-cretae-poc-modal .shadow-sm {
        box-shadow: 0 .125rem .25rem rgba(0,0,0,.075) !important;
    }

    #show-patient-cretae-poc-modal .shadow-lg {
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15) !important;
    }

    #show-patient-cretae-poc-modal .error {
        font-size: 0.875rem;
    }

    #show-patient-cretae-poc-modal .btn-sm {
        padding: 0.5rem 1rem;
        font-size: 0.95rem;
    }

    #show-patient-cretae-poc-modal .poc-task-row {
        transition: background-color 0.2s ease;
    }

    #show-patient-cretae-poc-modal .remove-task-row-btn:not(:disabled):hover {
        background-color: #dc3545;
        color: #fff;
    }

    #show-patient-cretae-poc-modal #addTaskRowBtn:hover {
        background-color: #28a745;
        color: #fff;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        #show-patient-cretae-poc-modal .modal-body {
            padding: 20px !important;
        }

        #show-patient-cretae-poc-modal .card-body {
            padding: 20px !important;
        }

        #show-patient-cretae-poc-modal .btn-sm {
            font-size: 0.875rem;
        }
    }
</style>
