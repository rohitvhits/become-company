<div class="tab-pane fade" id="user-email-notification-creator-1" role="tabpanel" aria-labelledby="user-email-notification-creator-tab">
<div class="row">
        <div class="col-sm-6 card-title">
            <h4 class="card-title">User Email Notification Creator List</h4>
        </div>
        <div class="col-sm-6">
            <a href="javascript:void(0)" onclick="refreshUserCreatorNotification()"  class="btn btn-success  btn-rounded btn-sm btn-fw pull-right mb-1 created-user-email-notification"  data-whatever="@mdo"><i class="mdi mdi-plus"></i>Add User Notification</a>
        </div>
    </div>
    <table id="order-listing1" class="table table-bordered users-email-loader" style="display:''">
        <thead>
            <tr>
                <th>No</th>
                <th>Email Notification Type</th>
                <th>Created Date/ Created By</th>
                <th>Updated Date/ Updated By</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <tr><td class="line loading-shimmer" colspan="5"></td></tr>
        </tbody>
    </table>
    <div class="table-responsive" id="user_email_notification_ajax_id">
    </div>
</div>