<div class="modal fade" id="agency_update_token" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
    <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Edit Token Name</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <div class="modal-content">
           
            <form class="forms-sample" enctype="multipart/form-data" action='{{ url("agency/token-update")}}' name="adduser" method="post">
                <div class="modal-body">
                    
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <input type="hidden" id="agency_token_id" name="agency_token_id">
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Name<span style="color:red">*</span></label>
                        <select class="form-control" id="agency_update_notes_token" name="notes">
                            <option value="">Select Name</option>
                            @foreach($agencyGenerateUpdateArray as $agencyTokens)
                            <option value="{{ $agencyTokens['id']}}">{{ $agencyTokens['name']}}</option>
                            @endforeach
                        </select>
                        <!-- <textarea name="notes" class="form-control" id="agency_update_notes_token"></textarea> -->
                        <span id="edit_agency_notes_token_error" class="error mt-2" for="document_type"></span>
                    </div>
                  
                </div>
                <div class="modal-footer">
                    <button type="button" id="update-agency-token" class="btn btn-success">Update</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                </div>
            </form>

        </div>
    </div>
</div>