<div class="tab-pane" id="c">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <p class="card-title mb-0">HHA Caregiver Notes</p>
        @if ($auth->agency_fk != 106)
            <p class="mb-0 tx-13">
                <a data-toggle="modal" class="pull-right btn btn-info btn-sm d-none d-md-block"
                    onclick="getHHACaregiverSubject()" data-whatever="@mdo"><i class="mdi mdi-plus"></i>
                    Add</a>
            </p>
        @endif
    </div>
        <table class="table table-bordered" id="chat-messages-news-dataTable">
            <thead>
                <th>No</th>
                <th>Notes</th>
                <th>Created Date</th>
            </thead>
            <tbody id="chat-messages-news">

            </tbody>
        </table>
        <div id="hha-caregiver-notes-pagination" class="mt-2"></div>
</div>

<div class="modal fade" id="exampleModal-notes" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title documens" id="ModalLabel">Add Caregiver Notes </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form class="forms-sample" enctype="multipart/form-data" name="adduser" method="post"
                    id="hha_caregivers_notes">
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Subject<span class="error">*</span>:</label>
                        <select class="form-control" id="subjectId" name="subjectId">

                        </select>
                        <span id="hha_subject_id_error" class="error mt-2"></span>
                    </div>

                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Notes<span class="error">*</span>:</label>
                        <textarea type="text" rows="4" cols="50" class="form-control" id="hha_caregivers_notes_id"></textarea>
                        <span id="hha_caregivers_notes_id_error" class="error mt-2"
                            for="hha_caregivers_notes_type"></span>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" id="hhaCaregiverSave">Save</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
