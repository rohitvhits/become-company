<style>
    #hha_mdo_patient_add_modal .modal-footer {
        padding: 4px 1px !important;
    }
    
    #hha_mdo_patient_add_modal .select2-container--default .select2-selection--multiple {
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        min-height: 38px;
    }

    #hha_mdo_patient_add_modal .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    #hha_mdo_patient_add_modal .table-wrapper {
        background: white;
        border-radius: 0.25rem;
        padding: 1rem;
        border: 1px solid #dee2e6;
    }

    #hha_mdo_patient_add_modal .table thead th {
        background-color: #f8f9fa;
        color: #495057;
        font-weight: 600;
        font-size: 0.875rem;
        border-bottom: 2px solid #dee2e6;
        padding: 0.75rem;
    }

    #hha_mdo_patient_add_modal .table tbody tr {
        transition: background-color 0.2s ease;
    }

    #hha_mdo_patient_add_modal .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    #hha_mdo_patient_add_modal .table tbody td {
        padding: 0.75rem;
        vertical-align: middle;
    }

    #patient-demographics {
        margin-bottom: 1rem;
        background: #ffffff;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 1rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    #patient-demographics .info-section {
        margin-bottom: 0.75rem;
        padding: 0.75rem;
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 4px;
    }

    #patient-demographics .info-section:last-child {
        margin-bottom: 0;
    }

    #patient-demographics .section-title {
        font-size: 0.8rem;
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.5rem;
        padding-bottom: 0.25rem;
        border-bottom: 1px solid #007bff;
        display: flex;
        align-items: center;
    }

    #patient-demographics .section-title i {
        margin-right: 0.25rem;
        color: #007bff;
        font-size: 0.9rem;
    }

    #patient-demographics .info-item {
        margin-bottom: 0.25rem;
        padding: 0.25rem 0;
    }

    #patient-demographics .info-item:last-child {
        margin-bottom: 0;
    }

    #patient-demographics .info-label {
        font-size: 0.7rem;
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 0.1rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    #patient-demographics .info-value {
        font-size: 0.8rem;
        color: #212529;
        font-weight: 500;
        line-height: 1.3;
    }

    #patient-demographics .col-md-6,
    #patient-demographics .col-md-12 {
        padding: 0.25rem;
    }

    #patient-demographics .row {
        margin: 0;
    }
    
    #patient-demographics .scroll-section{
        max-height:200px;
        overflow-y:scroll
    }
</style>

<div class="modal fade" id="hha_mdo_patient_add_modal" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" >
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white" style="background-color:#000000 !important">
                <h5 class="modal-title font-weight-bold" id="ModalLabel">
                    <i class="mdi mdi-account-plus mr-2"></i>Add HHA Patient
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form class="forms-sample" enctype="multipart/form-data" name="adduser" method="post" id="submitHHAMdoPatient">
                <div class="modal-body p-4">
                    <input type="hidden" name="modal_patient_id" id="modal_patient_id">

                    <!-- Patient Demographics Section -->
                    <div class="row" id="patient-demographics" style="max-height:350px;overflow-y:scroll"></div>
                    <div class="col-md-12">
                        <!-- Services Selection -->
                        <div class="row form-group">
                            <label for="service_id" class="font-weight-semibold">
                                <i class="mdi mdi-briefcase-check mr-1"></i>Choose Services
                                <span class="text-danger">*</span>
                            </label>
                            <select class="js-example-basic-multiple w-100" multiple="multiple" name="service_id[]" id="service_id">
                                @foreach($serviceList as $val)
                                    <option value="{{ $val->id}}">{{ $val->name}}</option>
                                @endforeach
                            </select>
                            <span id="hha_mdo_patient_service_id_error" class="error mt-2 text-danger d-block"></span>
                        </div>
                    </div>
                    
                </div>

                <div class="modal-footer border-top-0 bg-light">
                    <div class="d-flex justify-content-end align-items-center w-100">
                        <button type="button" class="btn btn-success btn-sm px-4 mr-2" id="update-appointment" onclick="singleMDODataAppointmentNew()">
                        <span id="loader-hha-mdo-appointment" class="spinner-border spinner-border-sm d-none mr-1" aria-hidden="true"></span>
                        <span id="hha-mdo-save-appointment-text">Create Record</span>
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm px-4" data-dismiss="modal">
                            Cancel
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
