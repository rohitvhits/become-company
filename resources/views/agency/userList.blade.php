<table id="" class="table">
    <thead>
        <tr>
        <th><input type="checkbox" id="cbox_user"> Select All
          
          </th>
            <th>Record #</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Ext No</th>
            <th>Permission Type</th>
            <th>Department</th>
            <th>Status</th>
            <th>Is Admin</th>
           
        </tr>
    </thead>
    <tbody>
        <?php
        $i = ($page !="")?($page *10)-9:1;
        foreach ($UsersList as $row) {  ?>
            <tr>
            <th ><input type="checkbox" class="cbox_user_id " value="{{ $row->id}}"></th>
                <th scope="row"><a href="/user-view/{{$row->id}}"><?= $i++ ?></a></th>
                <td><?= ucfirst($row->first_name) ?></td>
                <td><?= ucfirst($row->last_name) ?></td>
                <td><?= strtolower($row->email) ?></td>
                <td><?= $row->phone ?></td>
                <td><?= $row->ext ?></td>
                <td><?= $row->record_access ?></td>
                <td><?= $row->department ?></td>
                <td>
                @if($row->active =='active')
                    <span class="badge badge-success">Active</span>
                @else
                <span class="badge badge-danger"> {{ ucfirst($row->active)}}</span>
                @endif
               </td>
                <td>
                    <div class="col-md-12">
                        <input type="checkbox" onclick="changeRolePermission('{{$row->id}}','{{$row->role_access}}')" id="role_access{{$row->id}}" name="role_access" value="Send Note" title="Is Admin" @if($row->role_access == 1) checked @endif class="notification_checkbox patient_checkbox">
                    </div>
                </td>
            </tr>
        <?php }
        if (count($UsersList) == 0) {  ?>
            <tr>
                <td colspan="10"> No record available</td>
            </tr>
        <?php } ?>
    </tbody>
</table>
<div class="pull-right pegination-margin">
    {{$UsersList->appends(request()->query())->links('pagination::bootstrap-4')}}
</div>