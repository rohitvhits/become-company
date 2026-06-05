<div class="modal fade" id="modal-default-stamp" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span></button>
                    <h4 class="modal-title">Stamp</h4>
                </div>
                <div class="modal-body">
                    <div id="signature-pad" class="signature-pad">
                        <div class="signature-pad--body">
                            <input type="hidden" id="stampId">
                            <div id="fileUploadBody">
                                <div class="row">
                                    <div class="col-md-12">
                                        <input type="hidden" name="type" id="type" value="stamp">
                                        <input type="hidden" name="login_id" id="loginId" value="{{$login_id}}">
                                        <input type="file" class="form-control" value="" name="file_upload"
                                            id="stamp_upload">
                                        <div id="error_message" style="color: red; margin-top: 5px;"></div>
                                    </div>
                                    <div class="col-md-1">

                                    </div>
                                </div>
                                <div id="show_existing_stamp"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left custom-margin-left"
                        data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary custom-margin-right" id="testingsSaveStamp">Save
                        changes</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>