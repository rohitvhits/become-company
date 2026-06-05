<table id="" class="table table-responsive">
    <thead>
        <tr>
            <th width="10%">Appoitment ID</th>
            <th width="75%">Template Name</th>
            <th width="15%">Status</th>
        </tr>
    </thead>
    <tbody>
        @if (count($esignData) > 0)
            @foreach($esignData as $esign)
            <tr>
                <th scope="row"><a href="<?php echo URL::to('/'); ?>/patient/view/<?php echo $esign->main_intakeId; ?>" target="_blank"><?= '#' . '' . $esign->main_intakeId ?></a>
                </th>
                <td>{{$esign->templateDetails->template_name}}</td>
                <td>
                <?php
                    if (strtolower($esign->status) == 'pending') {
                    ?>
                        <label class='badge badge-warning'>Pending</label>
                    <?php } ?>
                    <?php

                    if (strtolower($esign->status) == 'booked') {
                    ?>
                        <label class='badge badge-info'>Booked</label>

                    <?php } ?>
                    <?php

                    if (strtolower($esign->status) == 'completed') {
                    ?>
                        <label class='badge badge-success'>Completed</label>

                    <?php } ?>
                    <?php

                    if (strtolower($esign->status) == 'cancelled') {
                    ?>
                        <label class='badge badge-danger'>Cancelled</label>

                    <?php } ?>
                    <?php

                    if (strtolower($esign->status) == 'noshow') {
                    ?>
                        <label class='badge badge-light'>No Show</label>

                    <?php } ?>
                    <?php

                    if (strtolower($esign->status) == 'refused') {
                    ?>
                        <label class='badge badge-danger'>Refused</label>

                    <?php } ?>
                    <?php

                    if (strtolower($esign->status) == 'processing') {
                    ?>
                        <label class='badge badge-info'>processing</label>

                    <?php } ?>
                    <?php

                    if (strtolower($esign->status) == 'arrived') {
                    ?>
                        <label class='badge badge-primary'>Arrived</label>

                    <?php } ?>
                    <?php

                    if (strtolower($esign->status) == 'checkin') {
                    ?>
                        <label class='badge badge-primary'>Mark as ClockIn</label>

                    <?php } ?>
                    <?php

                    if (strtolower($esign->status) == 'not interested') {
                    ?>
                        <label class='badge badge-primary'>Not Interested</label>

                    <?php }
                    if (strtolower($esign->status) == 'hospitalized/rehab') {
                    ?>
                        <label class='badge badge-secondary'>Hospitalized/Rehab</label>

                    <?php }
                    if (strtolower($esign->status) == 'unabletocontact') {
                    ?>
                        <label class='badge badge-primary'>Unable To Contact</label>

                    <?php } ?>
                </td>
            </tr>
            @endforeach
        @else
            <tr>
                <td colspan="7">
                    <center><b>Data not found</b></center>
                </td>
            </tr>
        @endif           
    </tbody>
</table>
<div class="pull-right pegination-margin">
{{ $esignData->appends(request()->input())->links('pagination::simple-bootstrap-4') }}
</div>