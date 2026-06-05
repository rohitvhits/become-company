<div class="modal fade" id="exampleModal-link-alaycare-client-id" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Alaycare Client</h5>
                <button type="button" class="close" data-dismiss="modal" onclick="CloseClientPopup()" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="lnkhhx_alaycare_client_id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-10">
                                        <label for="recipient-name" class="mb-0">Search Client</label>
                                        <input type="text" class="form-control" name="search_alaya_client" id="search_alaya_client" data-gtm-form-interact-field-id="0">
                                    </div>
                                    <div class="col-md-2 mt-4">
                                        <a href="javascript:void(0)" onclick="searchAlayaClient()"><i class="fa fa-search" style="font-size:20px;"></i></a>
                                    </div>
                                </div>

                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-10">
                                        <label for="recipient-name"  class="mb-0">Client: <span class="error">*</span></label>
                                        <input type="text" name="hha_alaycare_client_id" class="form-control" value="" id="hha_alaycare_client_id">
                                        <input type="hidden" name="hha_alaycare_client_name" class="form-control" value="" id="hha_alaycare_client_name">
                                        <span class="error hha_alaycare_client_id_error"></span>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                        <div class="col-md-6 hide" id="alayacare_client_search_response">
                            <div class="form-group ">
                                    <div class="row">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr><th>#</th>
                                                <th nowrap="">Client ID</th>
                                                <th nowrap="">Client Name</th>
                                                <th nowrap="">Status</th>
                                                <th nowrap="">Action</th>
                                            </tr></thead>
                                            <tbody id="alaayaclients_id">

                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                        </div>
                    </div>
                    
                </form>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="update-alaycare-client-id">Save</button>
                <button type="button" class="btn btn-light" data-dismiss="modal" onclick="CloseClientPopup()">Close</button>
            </div>
        </div>
    </div>
</div>