<div class="tab-pane" id="alaycare-employee-notes">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <p class="card-title mb-0">Employee Notes</p>
        <p class="mb-0 tx-13">
            <a href="javascript:void(0)" class="btn btn-primary" data-target="#exampleModal-alaya-notes" data-toggle="modal" data-whatever="@mdo">Add Notes</a>
        </p>
    </div>
    <div class="row">
        
        <div class="col-12">
            <div class="loader-main" id="loaderAlayaNotes" style="display:none">
                <div class="loader-inner">
                <img src="{{ asset('/ajax-loader.gif') }}" class="" alt="loader" >
                </div>
            </div>
            <table class="table table-bordered" >
                <thead>
                    <th>No</th>
                    <th>Notes</th>
                    <th>Type</th>
                    <th>Created Date</th> 
                    <th>Status</th> 
                 
                    
                </thead>
                <tbody id="alayacare_notes_id">

                </tbody>
            </table>
        </div>
        <div class="col-md-12 mt-3" id="pagin">
        <a class="pull-right btn btn-primary btn-rounded  btn-sm" href="javascript:void(0)" id="nextNotesId" style="display:none"   onClick="nextNotes()">Next</a></li>
            <a class="pull-right btn btn-secondary btn-rounded  btn-sm" href="javascript:void(0)" id="previousNotesId" style="display:none" onClick="previousNotes()">Prev</a></li>
            
        </div>

    </div>

</div>

<div class="modal fade" id="exampleModal-alaya-notes" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Add Employee Note</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="clearEmployeeNotes()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="recipient-name" class="col-form-label">Note Type <span class="error">*</span>:</label>
                    <select class="form-control" name="alaya_notes_id" id="alaya_notes_type_id"></select>
                    <span class="error" id="alaya_notes_type_id_error"></span>
                </div>
                
                <div class="form-group">
                    <label for="recipient-name" class="col-form-label">Note<span class="error">*</span>:</label>
                    <textarea class="form-control" id="alaya_notes_id"  rows="4" cols="50"></textarea>
                    <span class="error" id="alaya_notes_id_error"></span>
                </div>
            </div>

            <div class="modal-footer">
            <img src="{{ asset('/ajax-loader.gif') }}" class="" alt="loader" id="loaderAlayaNotesSubmit" style="display: none; ">
            <button type="button" class="btn btn-primary"  onclick="submitEmployeeNotes()">Submit</button>
                <button type="button" class="btn btn-light" data-dismiss="modal"  onclick="clearEmployeeNotes()">Close</button>
            </div>

          
        </div>
    </div>
</div>