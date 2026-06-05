<div class="tab-pane fade" id="agency-wise-tele-services-1" role="tabpanel" aria-labelledby="agency-wise-tele-services-tab">
<div class="row">
        <div class="col-sm-6 card-title">
            <h4 class="card-title">Telehealth Service List</h4>
        </div>
        <div class="col-sm-6">
            @can('telehealth-service-add')
                <a href="javascript:void(0)" onclick="getTeleData()" class="btn btn-success  btn-rounded btn-sm btn-fw pull-right mb-1"><i class="mdi mdi-plus"></i>Add Service</a>
            @endcan
        </div>
    </div>
    <table id="order-listing1" class="table table-bordered teleLoader" style="display:''">
        <thead>
            <tr>
                <th>No</th>
                <th>Services</th>
                <th>Type</th>
                <th>Created Date/ Created By</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <tr><td class="line loading-shimmer" colspan="5"></td></tr>
        </tbody>
    </table>
    <div class="table-responsive" id="tele_ajax_id">
    </div>
</div>