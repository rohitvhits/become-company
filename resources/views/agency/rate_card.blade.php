<div class="tab-pane fade" id="agency_wise_rate_card-1" role="tabpanel" aria-labelledby="agency_wise_rate_card-tab">
<div class="row">
        <div class="col-sm-6 card-title">
            <h4 class="card-title">Rate Card List</h4>
        </div>
        <div class="col-sm-6">
            @can('rate-card-add')
                <a href="javascript:void(0)" onclick="getRateCard()" class="btn btn-success  btn-rounded btn-sm btn-fw pull-right mb-1"><i class="mdi mdi-plus"></i>Add Rate Card</a>
            @endcan
        </div>
    </div>
    <table id="order-listing1" class="table table-bordered rateCardLoader" style="display:''">
        <thead>
            <tr>
                <th>No</th>
                <th>Services</th>
                <th>Amount</th>
                <th>Created Date/ Created By</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <tr><td class="line loading-shimmer" colspan="5"></td></tr>
        </tbody>
    </table>
    <div class="table-responsive" id="rate_card_ajax_id">
    </div>
</div>