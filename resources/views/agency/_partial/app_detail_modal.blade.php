<div class="modal fade" id="agency_add_app_detail" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">

        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">HHA Office</h5>
                <button type="button" class="close" id="close_office" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="forms-sample" enctype="multipart/form-data" action='{{ url("agency/app-details-add")}}' id="appDetail" name="appDetail" method="post">
                <div class="modal-body">

                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" id="agency_id" name="agency_id" val="">
                    <div class="form-group">
                        <label for="agency_app_name" class="col-form-label">HHA Offices</label>
                        <select name="office_id" id="office_id" class="form-control">
                            <option value="">Select HHA Office</option>
                        </select>
                        <span id="agency_app_name_error" class="error mt-2" for="document_type"></span>
                    </div>
                    
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick="saveOfficeDetails()">Add</button>
                <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>