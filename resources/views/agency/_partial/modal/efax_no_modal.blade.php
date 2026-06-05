<div class="modal fade" id="addEditEfaxModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Edit Efaxno</h5>
                <button type="button" onclick="closeEditEfaxNo()" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="forms-sample" enctype="multipart/form-data" name="adduser" method="post" id="efaxUpdateForm">
                <div class="modal-body">
                
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" id="agency_id" name="agency_id" value="{{ $id }}">
                   

                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label"><b>Efax No</b></label>
                        <input type="text" id="edit_efaxno_id" PlaceHolder="Efax No" class="form-control">
                        
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="updateEfaxno" class="btn btn-success">Save</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal" onclick="closeEditEfaxNo()">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>