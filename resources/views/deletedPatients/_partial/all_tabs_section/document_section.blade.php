<div class="tab-pane active" id="document-section">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <p class="card-title mb-0">Document</p>
        <?php if ($user['user_type_fk'] == 184 || ($user['user_type_fk'] == 2 || $user['user_type_fk'] == 6)) { ?>
        <p class="mb-0 tx-13">
            <a data-toggle="modal"
                class="pull-right btn btn-info btn-sm d-none d-md-block"
                data-target="#exampleModal-5" data-whatever="@mdo"
                onclick="viewServices(),requestsServices()"><i class="mdi mdi-plus"></i>
                Add</a>
        </p>
        <?php } ?>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="loader-main" id="loaderAlayaSkillLoaded"
                style="display:none">
                <div class="loader-inner">
                    <img src="{{ asset('/ajax-loader.gif') }}" class=""
                        alt="loader">
                </div>
            </div>
            <div id="document_response_list"></div>
        </div>
    </div>
</div>
