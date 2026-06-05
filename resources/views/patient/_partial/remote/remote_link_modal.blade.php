<style>
    #exampleModal-link-remote-id .modal-footer {
        padding: 12px 20px !important;
    }
    #exampleModal-link-remote-id .modal-header {
        padding: 8px 16px !important;
    }

    #exampleModal-link-remote-id .modal-content {
        border-radius: 8px !important;
    }

    /* Button Styling */
    #exampleModal-link-remote-id #update-remote-id {
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(40, 167, 69, 0.2);
    }

    #exampleModal-link-remote-id #update-remote-id:hover {
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
        transform: translateY(-2px);
    }

    #exampleModal-link-remote-id #update-remote-id:active {
        transform: translateY(0);
    }

    #exampleModal-link-remote-id .btn-light {
        transition: all 0.3s ease;
    }

    #exampleModal-link-remote-id .btn-light:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    #exampleModal-link-remote-id .modal-header .close {
        opacity: 1;
        text-shadow: none;
    }

    #exampleModal-link-remote-id .modal-header .close:hover {
        opacity: 0.8;
    }

    /* Form Control Focus */
    #exampleModal-link-remote-id .form-control:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }
</style>
<div class="modal fade" id="exampleModal-link-remote-id" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white" style="background-color:#000000 !important">
                <h5 class="modal-title font-weight-bold" id="ModalLabel">
                    <i class="mdi mdi-account-tie mr-2"></i>Remote Employee
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" onclick="CloseRemoteEmployeePopup()" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
               <form id="lnkhhx_remote_id"></form>
                    <div class="row">
                        <div class="col-md-6">

                            <div class="card mb-2 shadow-sm">
                                <div class="card-header bg-light d-flex align-items-center py-1 px-2">
                                <strong class="small">Search Remote Patient</strong>
                                </div>
                                <div class="card-body py-2 px-2">
                                    <div class="form-row">
                                        <div class="form-group col-md-6 mb-1">
                                            <label for="first_name" class="small mb-1">First Name:</label>
                                            <input type="text" class="form-control form-control-sm" id="emmacare_first_name" name="emmacare_first_name" placeholder="First name" autofocus>
                                        </div>
                                        <div class="form-group col-md-6 mb-1">
                                            <label for="last_name" class="small mb-1">Last Name:</label>
                                            <input type="text" class="form-control form-control-sm" id="emmacare_last_name" name="emmacare_last_name" placeholder="Last name">
                                        </div>
                                        <div class="form-group col-md-6 mb-1">
                                            <label for="emmacare_externalId" class="small mb-1">External Id:</label>
                                            <input type="text" class="form-control form-control-sm" id="emmacare_externalId" name="emmacare_externalId" placeholder="External Id">
                                        </div>

                                        <div class="form-group col-md-6 mb-1">
                                            <label for="emmacare_dob" class="small mb-1">Date of Birth:</label>
                                            <input type="text" class="form-control form-control-sm" id="emmacare_dob" name="emmacare_dob" placeholder="Date of Birth"  data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" >
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer py-1 px-2">
                                    <a href="javascript:void(0)" id="searchBtn" class="btn btn-sm btn-primary" onclick="searchEmmacareEmployee()">
                                        <i class="fa fa-search" id="searchIcon"></i>
                                        <span id="searchText">Search</span>
                                        <i class="fa fa-spinner fa-spin d-none" id="searchLoader"></i>
                                    </a>
                                    <a  href="javascript:void(0)"  id="clearSearchRemoteFocus" class="btn btn-sm btn-secondary" onclick="clearEmmacareSearch()"><i class="fa fa-refresh"></i><span>Clear</span></a>
                                </div>
                            </div>

                        </div>
                        <div class="col-md-6">
                            <div class="card mb-2 shadow-sm">
                                <div class="card-header bg-light d-flex align-items-center py-1 px-2">
                                    <strong class="small">Selected Patient Information</strong>
                                </div>
                                <div class="card-body py-2 px-2">
                                    <div class="form-group mb-2">
                                        <label for="hha_remote_id" class="small mb-1">Search Existing Patient:</label>
                                        <input type="text" name="hha_remote_id" class="form-control"   id="hha_remote_id" placeholder="Enter Employee ID">
                       
                                    </div>
                                    <div class="form-group mb-0">
                                        <label for="hha_remote_name" class="small mb-1">Patient Name:</label>
                                        <input type="text" name="hha_remote_name" class="form-control form-control-sm" value="@if(isset($record->robort_name)){{ $record->remote_name }} @endif" id="hha_remote_name" placeholder="Patient Name" readonly>
                                    </div>
                                    <input type="hidden" name="hha_remote_patient_id" id="hha_remote_patient_id" value="@if(isset($record->robort_id)){{ $record->robort_id }} @endif">
                                    <input type="hidden" name="hha_remote_uuid" id="hha_remote_uuid">
                                    <input type="hidden" name="hha_remote_external_id" id="hha_remote_external_id">
                                    <span class="error hha_remote_id_error text-danger d-block mt-2"></span>
                                    <div  class="form-group mb-0"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12" id="emmacare_remote_div_id" style="display:none">
                            <div class="card shadow-sm">
                                <div class="card-header bg-light py-1 px-2">
                                    <strong class="small">Search Results</strong>
                                    <span id="emmacareResultCount" class="float-right"></span>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm mb-0">
                                            <thead class="thead-light">
                                                <tr>
                                                <th scope="col" class="small">#</th>
                                                <th scope="col" class="small">Patient ID</th>
                                                <th scope="col" class="small">External ID</th>
                                                <th scope="col" class="small">Patient Name</th>
                                                <th scope="col" class="small">DOB</th>
                                                <th scope="col" class="small">Gender</th>
                                                <th scope="col" class="small">Status</th>
                                                <th scope="col" class="small text-center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="emmacareAPLoader" class="shimmer-loader" style="display:none">
                                                <tr>
                                                <td colspan="8" class="text-center py-3">
                                                    <i class="fa fa-spinner fa-spin"></i> Loading...
                                                </td>
                                                </tr>
                                            </tbody>
                                            <tbody id="emmacareCId">
                                                <!-- Patient rows go here -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="card-footer py-1 px-2 bg-light">
                                    <div id="emmacarePaginationContainer"></div>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="modal-footer border-top-0 bg-light">
                <div class="d-flex justify-content-end align-items-center w-100">
                    <button type="button" class="btn btn-success btn-sm px-4 mr-2" id="update-remote-id">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        <span>Save</span>
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm px-4" data-dismiss="modal" onclick="CloseRemoteEmployeePopup()">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>