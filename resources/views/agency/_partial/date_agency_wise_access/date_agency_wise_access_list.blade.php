<div class="tab-pane fade" id="date-wise-agency-view-1" role="tabpanel" aria-labelledby="date-wise-agency-view-tab">
<div class="row">
        <div class="col-sm-6 card-title">
            <h4 class="card-title">Date Wise Agency View Access List</h4>
        </div>
        @can('add-date-wise-agency-view')
        <div class="col-sm-6">
            <a href="javascript:void(0)" data-toggle="modal" data-target="#add-date-wise-agency-view-use" data-whatever="@mdo"  onclick="refreshDateWiseAgencyAccess()"  class="btn btn-success  btn-rounded btn-sm btn-fw pull-right mb-1 add-date-wise-agency-view-use"><i class="mdi mdi-plus"></i>Add Date Wise Agency View Access</a>
        </div>
        @endcan
    </div>
    <table id="order-listing1" class="table table-bordered date-wise-agency-view-loader" style="display:''">
        <thead>
            <tr>
                <th>No</th>
                <th>Type</th>
                <th>Created Date/ Created By</th>
                <th>Updated Date/ Updated By</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <tr><td class="line loading-shimmer" colspan="5"></td></tr>
        </tbody>
    </table>
    <div class="table-responsive" id="date_wise_agency_ajax_id">
    </div>
</div>