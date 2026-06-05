<div class="modal fade" id="nybest-user-modal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Assign Nybest User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="forms-sample" enctype="multipart/form-data" name="adduser" method="post">
                <div class="modal-body">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">NyBest user</label>
                        <div class="col-sm-9">
                            <div class="small text-muted mb-2">Select one or more NyBest users. Type to search;</div>
                            <input type="text" name="assign_nybest_user" class="form-control" id="assign_nybest_user">
                            <span id="assign_nybest_user_error" class="error mt-2"></span>
                        </div>
                    </div>
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" id="saveNydata" class="btn btn-success">Update</button>
                <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>