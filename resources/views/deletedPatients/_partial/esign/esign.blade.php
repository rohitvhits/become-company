<div class="d-flex align-items-center justify-content-between mb-3">
    <p class="card-title mb-0">Esign</p>
    @can('esign-list')
        <p class="mb-0 tx-13 ">
            <a  class=" btn btn-primary btn-sm pull-right" onclick="esignResponseNew()"><i class="mdi mdi-refresh"></i> Refresh</a>    
            <a data-toggle="modal" class=" mr-3 btn btn-info btn-sm  d-none d-md-block pull-right" data-target="#exampleModal-esign" data-whatever="@mdo" style="color:#fff" onclick="loadTemplate();loadDoctorList();"><i class="mdi mdi-plus"></i> Add Esign</a>
            <a data-toggle="modal" class=" mr-3 btn btn-info btn-sm  d-none d-md-block pull-right" data-target="#exampleModal-upload-document" data-whatever="@mdo" style="color:#fff"><i class="mdi mdi-plus"></i>Upload Document</a>
        </p>
    @endcan
</div>
<div class="row">
    <div class="col-12">
        <div id="esign_reponse_id" class="table-responsive ">
            
            <table id="order-listing1" class="table table-bordered">
                <thead>
                    <tr>
                        <!--<th></th>-->
                        <th style="width:100px;">Record</th>

                        <th>Template Name</th>
                        <th>Status</th>
                        <th>Sender</th>
                       
                        <th>Completed Date</th>
                        <th>Created Date</th>
                        <th>Created By</th>
                        <th>Action</th>
                        
                    </tr>

                </thead>
                <tbody id="esign_resp_id">

                    <tr>
                        <td colspan="7">No record available</td>
                    </tr>
                </tbody>
            </table>

            <div class="pull-right pegination-margin" id="paginate_id">

            </div>
        </div>
    </div>
</div>
@include('patient._partial.esign.esign_modal')
@include('patient._partial.esign.upload_document_modal')
@include('patient._partial.esign.esign_sms_modal')