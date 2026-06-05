<div class="table-responsive">
    <div class="col-sm-12">
        <table id="order-listing1" class="table table-bordered table-width1">
            <thead>
                <tr class="">
                    <th width="5%">#</th>
                    <th width="15%" nowrap>Full Name</th>
                    <th width="15%" nowrap>Email</th>
                    <th width="15%" nowrap>Phone No</th>
                    <th width="10%" nowrap>EXT No</th>
                    <th width="10%" nowrap>Status</th>
                    <th width="5%" nowrap>Is Admin</th>
                    <th width="15%" nowrap>Last User Login <br/> Last Ip Address</th>
                    <th width="15%" nowrap>Created Date <br /> Created By</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($query) > 0) { ?>
                    <?php $i = 1;
                    foreach ($query as $row) {  ?>
                        <tr role="row" class=@if($i%2==0)"odd" @else "even" @endif>
                            <th><a href="{{url('agency-user-view')}}/{{ $row->id }}"><?= $i++ ?></a></th>
                            <td nowrap><?= ucfirst($row->first_name . ' ' . $row->last_name) ?></td>

                            <td nowrap><?= strtolower($row->email) ?></td>
                            <td nowrap><?= $row->phone ?></td>
                            <td nowrap><?= $row->ext ?></td>
                            <td nowrap><?php if ($row->active == 'active') {
                                            echo "<span class='badge badge-success'>Active</span>";
                                        } else {
                                            echo "<span class='badge badge-danger'>" . ucfirst($row->active) . "</span>";
                                        }  ?></td>
                            <td nowrap><?= $row->role_access == 1 ? 'Yes' : 'No'; ?></td>
                            <td><?php if ($row->last_login_at != '') {
                                    echo date('m-d-Y h:i:s', strtotime($row->last_login_at)) . '<br>' . $row->last_login_ip;
                                } ?></td>
                            <td><?php if ($row->created_at != '') {
                                    echo date('m-d-Y h:i:s', strtotime($row->created_at)) . '<br>' . $row->created_by_fname .' '.$row->created_by_lname;
                                } ?></td>

                        </tr>
                    <?php }
                } else { ?>
                    <tr>
                        <td colspan="9">
                            <center><b>Data not found</b></center>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>