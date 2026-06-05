<div class="modal fade" id="pocTasksModal" tabindex="-1" aria-labelledby="pocTasksModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white" style="background-color:#000000 !important;padding: 8px 16px !important" >
                <h5 class="modal-title font-weight-bold" id="pocTasksModalLabel" style="font-size: 15px !important;">
                    <i class="mdi mdi-clipboard-list mr-2"></i>POC Tasks
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="tasksTable">
                        <thead class="thead-light">
                            <tr id="tasksTableHeader">
                                <!-- Headers will be generated dynamically by JavaScript -->
                            </tr>
                        </thead>
                        <tbody id="tasksTableBody">
                            <tr>
                                <td colspan="13" class="text-center">No tasks available</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-top-0 bg-light">
                <div class="d-flex justify-content-end align-items-center w-100">
                    <button type="button" class="btn btn-secondary btn-sm px-4" data-dismiss="modal">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>