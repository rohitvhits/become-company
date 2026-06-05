<table id="order-listing1" class="table table-bordered table-width1">
    <thead>
        <tr>
            <th style="width:20px;">#</th>
            <th>Record #</th>
            <th nowrap>Agency Name</th>
            <th nowrap>Record Type</th>
            <th nowrap>Full Name</th>
            <th nowrap>Email</th>
            <th nowrap>Phone No</th>
            <th nowrap>EXT No</th>
            <th nowrap>Status</th>            
            <th nowrap>Last User Login<br>Last Ip Address</th>
        </tr>
    </thead>
    
    <tbody>
        <?php if (count($query) > 0) { ?>
        <?php $i = 1 + (($query->currentPage() - 1) * $query->perPage());
                        foreach ($query as $row) {  ?>
        <tr>
            <th><?= $i++ ?></th>
            <td nowrap><a
                    href="<?php echo URL::asset('/'); ?>user-view/<?= $row->id ?>"><?= '#' . ' ' . $row->id ?></a>
            <td nowrap><?= ucfirst($row->agency_name??'') ?></a>
            </td>
            </td>
            <td nowrap><?= ucfirst($row->record_access) ?></td>
            
            
            <td nowrap><?= ucfirst($row->first_name.' '.$row->last_name) ?></td>
        
            <td nowrap><?= $row->email ?></td>
            <td nowrap><?= $row->phone ?></td>
            <td nowrap><?= $row->ext ?></td>
            <td nowrap><?php if($row->active =='active'){ echo "<span class='badge badge-success'>Active</span>"; }else{ echo "<span class='badge badge-danger'>".ucfirst($row->active)."</span>";}  ?></td>
            <td><?php if ($row->last_login_at != '') {
                echo date('m-d-Y h:i:s', strtotime($row->last_login_at)).'<br>'.$row->last_login_ip;
            } ?></td>
            
        </tr>
        <?php }
                    } else { ?>
        <tr>
            <td colspan="12">
                <center><b>Data not found</b></center>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>

<div class="pull-right pegination-margin">
{{ $query->appends(request()->query())->links('pagination::bootstrap-4') }}
</div>