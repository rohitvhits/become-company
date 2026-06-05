<div class="tab-pane fade" id="agency-wise-webhook-list-1" role="tabpanel" aria-labelledby="1)agency_wise_webhook_list-tab">
    <div class="row">
        <div class="col-sm-6 card-title">
            <h4 class="card-title">Webhook Detail</h4>
        </div>
    </div>
    <div class="">
        <form class="forms-sample" enctype="multipart/form-data" action='' name="add-agency-wise-webhook-form" method="post" id="add-agency-wise-webhook-form">
            <input type="hidden" id="agency_id" name="agency_id" value="{{ $id }}">
            <div class="row">
                <div class="col-md-6">
                    <label class="col-form-label"><b>Webhook</b></label>
                    <input type="text" class="form-control form-control-lg" name="webhook" id="webhook" value="{{$agencyDetails->webhook}}">
                    <span id="agency_wise_webhook_message_error" class="error mt-2" for="document_type"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="agency-wise-webhook-saveId" class="btn btn-success">Update</button>
            </div>
        </form>
    </div>
</div>