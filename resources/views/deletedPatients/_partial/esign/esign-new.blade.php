<div class="d-flex align-items-center justify-content-between mb-3">
    <p class="card-title mb-0">Esign Section</p>
   
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
                        <th>Review By</th>
                        <th>Signers</th>
                        <th>Added By</th>
                        <th>Action</th>
                        
                    </tr>

                </thead>
                <tbody id="esign_resp_id_new">

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
@include('deletedPatients._partial.esign.esign_modal_new')
@include('deletedPatients._partial.esign.upload_document_modal_new')
@include('deletedPatients._partial.esign.esign_sms_modal_new')
@include('deletedPatients._partial.esign.esign_log_modal')