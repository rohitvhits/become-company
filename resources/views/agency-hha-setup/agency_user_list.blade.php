<table id="" class="table">
    <thead>
        <tr>
            <th>Record #</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Login Type</th>
            <th>User Type</th>
            <th>Last Login At</th>
            <th>NyBest Access</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $i = 1;
        foreach ($UsersList as $row) {  ?>
            <tr>
                <th scope="row"><?= $i++ ?></th>
                <td><?= ucfirst($row->first_name) ?></td>
                <td><?= ucfirst($row->last_name) ?></td>
                <td><?= $row->email ?></td>
                <td><?= $row->login_type_fk ?></td>
                <td><?= $row->user_type_fk ?></td>
                <td><?= $row->last_login_at ?></td>
                
                <td>
                    @if($row->hospital_flag == 1)
                    <span class="badge badge-success" onclick="HospitalChangeStatus(2,{{$row->id}})">Yes</span>
                    @else
                    <span class="badge badge-danger" onclick="HospitalChangeStatus(1,{{$row->id}})">No</span>
                    @endif

                </td>
            </tr>
        <?php }
        if (count($UsersList) == 0) {  ?>
            <tr>
                <td colspan="6"> No record available</td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<div class="pull-right pegination-margin">
    {{ $UsersList->appends(request()->input())->links('pagination::bootstrap-4') }}

</div>