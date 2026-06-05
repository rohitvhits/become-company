<style>
    #esignSectionLoader{
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100%;
        position: absolute;
        left: 50%;
        transform: translate(-50%);
    }
</style>
<div class="row">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <p class="card-title mb-0">Esign Section</p>
            @can('esign-list')
                <p class="mb-0 tx-13 ">
                    <a  class=" btn btn-primary btn-sm pull-right" onclick="esignResponseNew1()"><i class="mdi mdi-refresh"></i> Refresh</a>
                    @can('esign-add')
                    <a data-toggle="modal" class=" mr-3 btn btn-info btn-sm  d-none d-md-block pull-right" data-target="#exampleModal-esign-new" data-whatever="@mdo" style="color:#fff" onclick="loadTemplateNew();loadDoctorListNew();"><i class="mdi mdi-plus"></i> Add Esign</a>
                    @endcan
                    @can('esign-upload-document')
                    <a data-toggle="modal" class=" mr-3 btn btn-info btn-sm  d-none d-md-block pull-right" data-target="#exampleModal-upload-document-new" data-whatever="@mdo" style="color:#fff" onclick="refreshEsignUploadDocument()"><i class="mdi mdi-plus"></i>Upload Document</a>
                    @endcan
                </p>
            @endcan
        </div>
    </div>
    <div class="col-12">
        <div class="loader-main" id="esignSectionLoader" style="display:none">
            <div class="loader-inner">
                <img src="{{ asset('/ajax-loader.gif') }}" class=""
                    alt="loader">
            </div>
        </div>
        <div id="esign_reponse_id" class="table-responsive1">

            <table id="order-listing1" class="table table-bordered esign-shimmer"  style="display:none">
                <thead>
                    <tr>
                        <!--<th></th>-->
                        <th style="width:100px;">Record</th>

                        <th>Template Name</th>
                        <th>Status</th>
                        <th>Sender</th>
                        <th>Review By</th>
                        <th>Signers</th>
                        <th>Added By</th>
                        <th>Action</th>

                    </tr>

                </thead>
                <tbody class="shimmer-loader">

                    <tr>
                        <td colspan="8">Loading...</td>
                    </tr>
                </tbody>
            </table>
            <div id="esign_resp_id_new"></div>
            <div id="paginate_id"></div>
        </div>
    </div>
</div>

@include('patient._partial.esign.esign_modal_new')
@include('patient._partial.esign.upload_document_modal_new')
@include('patient._partial.esign.esign_sms_modal_new')
@include('patient._partial.esign.esign_log_modal')