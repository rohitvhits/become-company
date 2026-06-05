<div class="modal fade" id="agency_web_hook" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">

        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabelNew">Add Webhook</h5>
                <button type="button" class="close" id="close_webhook" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="forms-sample" enctype="multipart/form-data" id="add_agency_webhook_form_id"  method="post">
                <div class="modal-body">

                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id" value="" id="webhook_id">
                    
                    <div class="form-group">
                        <label for="agency_app_name" class="col-form-label">Webhook URL</label><span style="color:red">*</span>
                        <input type="text" class="form-control form-control-lg" name="webhook" id="webhook"  placeholder="Webhook URL">
                        <span id="webhook_error" class="error mt-2" for="document_type"></span>
                    </div>
                    <div class="form-group">
                        <label for="agency_app_name" class="col-form-label">Authentication Type</label><span style="color:red">*</span>
                        <select name="authentication_type" onchange="ChangeAuthentication()" id="authentication_type" class="form-control">
                            <option value="">Select Authentication Type</option>
                            <option value="no_auth">No Auth</option>
                            <option value="basic_auth">Basic Auth</option>
                            <option value="bearer_token">Bearer Token</option>
                            <option value="api_key">Api Key</option>
                        </select>
                        <span id="authentication_type_error" class="error_web error mt-2" for="document_type"></span>
                    </div>
                    <div class=" hide divSectionId"  id="basic_auth">
                        <div class="form-group">
                            <label for="agency_app_name">Username</label><span style="color:red">*</span>
                            <input type="text" class="form-control form-reset" name="username" id="username" placeholder="Username">
                            <span id="username_error" class="error_web error mt-2" for="document_type"></span>
                        </div>
                        <div class="form-group">
                            <label for="agency_app_name">Password</label><span style="color:red">*</span>
                            <input type="text" class="form-control form-reset" name="password" id="password"  placeholder="Password">
                            <span id="password_error" class="error_web error mt-2" for="document_type"></span>
                        </div>
                    </div>
                    <div class=" hide divSectionId"  id="bearer_token">
                        <div class="form-group">
                            <label for="agency_app_name">Token</label><span style="color:red">*</span>
                            <input type="text" class="form-control form-reset" name="token" id="token" placeholder="Token">
                            <span id="token_error" class="error_web error mt-2" for="document_type"></span>
                        </div>
                    </div>
                    <div class=" hide divSectionId"  id="api_key">
                        <div class="form-group">
                            <label for="agency_app_name">Key</label><span style="color:red">*</span>
                            <input type="text" class="form-control form-reset" name="key" id="key" placeholder="Key">
                            <span id="key_error" class="error_web error mt-2" for="document_type"></span>
                        </div>
                        <div class="form-group">
                            <label for="agency_app_name">Value</label><span style="color:red">*</span>
                            <input type="text" class="form-control form-reset" name="value" id="value" placeholder="Value">
                            <span id="value_error" class="error_web error mt-2" for="document_type"></span>
                        </div>
                    </div>
                    
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="text_submit_button_web" onclick="saveWebHook()">Add</button>
                <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>