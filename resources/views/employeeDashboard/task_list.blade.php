<table id="" class="table table-responsive">
    <thead>
        <tr>
            <th nowrap>ID</th>
            <th nowrap>Task Name</th>
            <th>Assigned Name</th>
            <th nowrap>status</th>
        </tr>
    </thead>
    <tbody>
        @if (count($taskData) > 0)
            @foreach($taskData as $task)
            <tr>
                <th scope="task"><?= '#' . '' . $task->id ?></th>
                <td>
                    {{$task->task_name}}
                </td>
                <td class="break-name">
                    @if(isset($task->assignUser->first_name))
                    {{$task->assignUser->first_name}} {{$task->assignUser->last_name }}
                    @endif
               </td>
                <td nowrap>
                <?php
                    if (strtolower($task->task_status) == 'pending') {
                    ?>
                        <label class='badge badge-primary'>Pending</label>
                    <?php } ?>
                    <?php

                    if (strtolower($task->task_status) == 'urgent') {
                    ?>
                        <label class='badge badge-danger'>Urgent</label>

                    <?php } ?>
                    <?php

                    if (strtolower($task->task_status) == 'outstanding') {
                    ?>
                        <label class='badge badge-success'>Outstanding</label>

                    <?php } ?>
                    <?php

                    if (strtolower($task->task_status) == 'completed') {
                    ?>
                        <label class='badge badge-info'>Completed</label>

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
<div class="pull-right pegination-margin static">
    {{ $taskData->appends(request()->input())->links('pagination::simple-bootstrap-4') }}
</div>