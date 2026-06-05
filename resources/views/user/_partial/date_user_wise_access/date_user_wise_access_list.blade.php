<div class="tab-pane fade" id="date-wise-user-view-1" role="tabpanel" aria-labelledby="date-wise-user-view-tab">
<div class="row">
        <div class="col-sm-6 card-title">
            <h4 class="card-title">Date Wise User View Access List</h4>
        </div>
        @can('add-date-wise-user-permission')
        <div class="col-sm-6 text-right">
            <button type="button" onclick="setPermanentRestriction()" class="btn btn-danger btn-rounded btn-sm btn-fw mb-1 mr-2" id="set-permanent-restriction-btn">
                <i class="mdi mdi-block-helper mr-1"></i>Set Permanent Restriction
            </button>
            <button type="button" onclick="removePermanentRestriction()" class="btn btn-warning btn-rounded btn-sm btn-fw mb-1 mr-2" id="remove-permanent-restriction-btn" style="display: none;">
                <i class="mdi mdi-lock-open"></i>Remove Restriction
            </button>
            <a href="javascript:void(0)" data-toggle="modal" data-target="#add-date-wise-user-view-use" data-whatever="@mdo"  onclick="refreshDateWiseUserAccess()"  class="btn btn-success  btn-rounded btn-sm btn-fw mb-1 add-date-wise-agency-view-use" id="add-user-access-btn">
                <i class="mdi mdi-plus"></i>Add Date Wise User View Access
            </a>
        </div>
        @endcan
    </div>
    <table id="order-listing1" class="table table-bordered date-wise-user-view-loader" style="display:''">
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
    <div class="table-responsive" id="date_wise_user_ajax_id">
    </div>
</div>