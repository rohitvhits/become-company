<div class="tab-pane fade" id="agency-wise-webhook-list-1" role="tabpanel" aria-labelledby="1)agency_wise_webhook_list-tab">
    <div class="row">
        <div class="col-sm-6 card-title">
            <h4 class="card-title">Webhook</h4>
        </div>
        <div class="col-sm-6">
            @can('add-webhook-form')
            <a href="javascript:void(0)" id="view_web_hook_modal_id" data-toggle="modal" onclick="showWebHook()" data-target="#agency_web_hook" class="btn btn-success  btn-rounded btn-sm btn-fw pull-right"><i class="mdi mdi-plus"></i>Add Webhook</a>
            @endcan
        </div>

    </div>
    <div class="table-responsive" id="web_ajax_id">



    </div>
</div>