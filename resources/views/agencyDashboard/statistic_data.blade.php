<table id="" class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Agency Name</th>
            <th>Type</th>
            <th>Name</th>
            <th>Assign Status</th>
        </tr>
    </thead>
    <tbody>
        @if (count($statisticData) > 0)
            @foreach($statisticData as $statistic)
            <tr>
                <th scope="statistic"><a href="<?php echo URL::to('/'); ?>/patient/view/<?php echo $statistic->id; ?>" target="_blank"><?= '#' . '' . $statistic->id ?></a>
                </th>
                <td>
                {{$statistic->agencyDetail->agency_name}}
                </td>
                <td>{{$statistic->type}}</td>
                <td>{{$statistic->first_name}} {{$statistic->middle_name}}  {{$statistic->last_name }}</td>
                <td>
                <?php
                    if (strtolower($statistic->status) == 'pending') {
                    ?>
                        <label class='badge badge-warning'>Pending</label>
                    <?php } ?>
                    <?php

                    if (strtolower($statistic->status) == 'booked') {
                    ?>
                        <label class='badge badge-info'>Booked</label>

                    <?php } ?>
                    <?php

                    if (strtolower($statistic->status) == 'completed') {
                    ?>
                        <label class='badge badge-success'>Completed</label>

                    <?php } ?>
                    <?php

                    if (strtolower($statistic->status) == 'cancelled') {
                    ?>
                        <label class='badge badge-danger'>Cancelled</label>

                    <?php } ?>
                    <?php

                    if (strtolower($statistic->status) == 'noshow') {
                    ?>
                        <label class='badge badge-light'>No Show</label>

                    <?php } ?>
                    <?php

                    if (strtolower($statistic->status) == 'refused') {
                    ?>
                        <label class='badge badge-danger'>Refused</label>

                    <?php } ?>
                    <?php

                    if (strtolower($statistic->status) == 'processing') {
                    ?>
                        <label class='badge badge-info'>processing</label>

                    <?php } ?>
                    <?php

                    if (strtolower($statistic->status) == 'arrived') {
                    ?>
                        <label class='badge badge-primary'>Arrived</label>

                    <?php } ?>
                    <?php

                    if (strtolower($statistic->status) == 'checkin') {
                    ?>
                        <label class='badge badge-primary'>Mark as ClockIn</label>

                    <?php } ?>
                    <?php

                    if (strtolower($statistic->status) == 'not interested') {
                    ?>
                        <label class='badge badge-primary'>Not Interested</label>

                    <?php }
                    if (strtolower($statistic->status) == 'hospitalized/rehab') {
                    ?>
                        <label class='badge badge-secondary'>Hospitalized/Rehab</label>

                    <?php }
                    if (strtolower($statistic->status) == 'unabletocontact') {
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

    </div>