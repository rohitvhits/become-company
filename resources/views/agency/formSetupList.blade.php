<table id="" class="table">
    <thead>
        <tr>
            <th>Record #</th>
            <th>Title</th>
            <th>Is Default</th>
            <th>Agency</th>
            <th>Action</th>
        </tr>
    </thead>
   
    <tbody id="refreshDiv">
        <?php
            $i = ($page != "") ? ($page * 10) - 9 : 1;
            foreach ($formSetupData as $key => $row) {  ?>
            <tr id="<?php echo $row->id; ?>">

                <td><?= $key+1 ?></td>
                <td><?php echo ucfirst($row->title); ?></td>
                <td><?php echo $row->is_default == 1 ? 'Yes' : 'No'; ?></td>
                <td>{{ $row->agencyValue->agency_name ?? '-' }}</td>
                <td>
                    @can('agency-form-setup-show')
                    <a href="{{ route('agency-master-list') }}?agency_id={{ $agency_id }}&form_id={{ $row->id }}"
                        class="pull-left" target="_blank" id="create-form-1">
                        <i class="fa fa-eye"></i>
                    </a>
                    @endcan
                </td>
            </tr>
        <?php } ?>

        <?php if (count($formSetupData) == 0) { ?>
            <tr>
                <td colspan="6"> No record available</td>
            </tr>
        <?php } ?>    
        
    </tbody>
</table>
<div class="pull-right pegination-margin"></div>
