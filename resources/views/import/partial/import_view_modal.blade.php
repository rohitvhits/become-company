<div class="modal fade" id="exampleModal-patient-view-import" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white" style="background-color:#000000 !important">
                <h5 class="modal-title font-weight-bold" id="ModalLabel">
                    <i class="mdi mdi-table-edit mr-2"></i>Map CSV Columns
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"  style="color:white !important">&times;</span>
                </button>
            </div>

            <form action="{{ url('patient/patient-import')}}" method="post" enctype="multipart/form-data" id="submitId">
                <input type="hidden" name="order_data" value="" id="order_data">
                @csrf

                <div class="modal-scroll-mr">
                    <div class="modal-scroll-inside">
                        <div class="modal-body p-4" id="formnewNN">
                            
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top-0 bg-light">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <div>
                            <span class="text-muted">
                                <i class="mdi mdi-information-outline mr-1"></i>
                                <small>Map each CSV column to the corresponding field</small>
                            </span>
                            <span class="error mt-2 text-danger d-block" id="row_error"></span>
                        </div>
                        <div>
                            <button type="submit" name="submit" class="btn btn-primary btn-sm px-4 mr-2">
                                <span class="spinner-border spinner-border-sm d-none" id="import_loaderss_id" aria-hidden="true"></span>
                                </i>Confirm & Import
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm px-4" data-dismiss="modal">
                            </i>Cancel
                            </button>
                                
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
