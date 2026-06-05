<style>
    #show-patient-services .modal-footer {
        padding: 4px 1px !important;
    }

    #show-patient-services .select2-container--default .select2-selection--multiple {
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        min-height: 38px;
    }

    #show-patient-services .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    #show-patient-services .table-wrapper {
        background: white;
        border-radius: 0.25rem;
        padding: 1rem;
        border: 1px solid #dee2e6;
    }

    #show-patient-services .table thead th {
        background-color: #f8f9fa;
        color: #495057;
        font-weight: 600;
        font-size: 0.875rem;
        border-bottom: 2px solid #dee2e6;
        padding: 0.75rem;
    }

    #show-patient-services .table tbody tr {
        transition: background-color 0.2s ease;
    }

    #show-patient-services .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    #show-patient-services .table tbody td {
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

<div class="modal fade" id="show-patient-services" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white" style="background-color:#000000 !important">
                <h5 class="modal-title font-weight-bold" id="ModalLabel">
                    <i class="mdi mdi-account-plus mr-2"></i>Add HHA Patient
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" onclick="hideDataAppointment()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form class="forms-sample" enctype="multipart/form-data" name="adduser" method="post" id="hha-appointment-save">
                <div class="modal-body p-4">
                    <input type="hidden" name="appointmentId" id="appointments_id">

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
                            <span id="hha_document_complience_type_id_error" class="error mt-2 text-danger d-block"></span>
                        </div>
                       
                        
                    </div>
                     <!-- Existing Records Table -->
                    <div class="row">
                        <div class="col-md-12  form-group">
                            <label class="font-weight-semibold">
                                <i class="mdi mdi-clipboard-list mr-1"></i>Existing Records
                            </label>
                            <div class="table-wrapper">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Record Id</th>
                                                <th>Agency Name</th>
                                                <th>Full Name</th>
                                                <th>Status</th>
                                                <th>Type</th>
                                            </tr>
                                        </thead>
                                        <tbody id="existing_record_id">
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-3">
                                                    <i class="mdi mdi-folder-open-outline" style="font-size: 1.5rem; opacity: 0.5;"></i>
                                                    <p class="mb-0 mt-2">No record available</p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    
                </div>

                <div class="modal-footer border-top-0 bg-light">
                    <div class="d-flex justify-content-end align-items-center w-100">
                        <button type="button" class="btn btn-success btn-sm px-4 mr-2" id="update-appointment" onclick="singleDataAppointmentNew()">
                        <span id="loader-update-appointment" class="spinner-border spinner-border-sm d-none mr-1" role="status" aria-hidden="true"></span> 
                        <span id="msg-save-appointment-text">Create Record</span>
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm px-4" data-dismiss="modal" onclick="hideDataAppointment()">
                            Cancel
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
