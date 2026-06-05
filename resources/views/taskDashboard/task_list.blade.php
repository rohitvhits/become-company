<table id="" class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Task Name</th>
            <th>Priority</th>
            <th>Status</th>
            <th>Assign User</th>
            <th>Due Date</th>
            <th>Created Date / Created By </th>
        </tr>
    </thead>
    <tbody>
        @if (count($taskData) > 0)
            @foreach($taskData as $data)
            <tr>
                <th scope="row">{{$data->id}}</th>
                <td>{{$data->task_name}}</td>
                <td>
                    @if($data->priority == 'High')
                        <label class='badge badge-danger'>High</label>
                    @elseif($data->priority == 'Medium')
                        <label class='badge badge-warning'>Medium</label>
                    @elseif($data->priority == 'Low')
                        <label class='badge badge-info'>Low</label>
                    @endif
                </td>
                <td>
                    @if($data->task_status == 'Pending')
                        <label class='badge badge-primary'>Pending</label>
                    @elseif($data->task_status == 'Outstanding')
                        <label class='badge badge-success'>Outstanding</label>
                    @elseif($data->task_status == 'Completed')
                        <label class='badge badge-info'>Completed</label>
                    @elseif($data->task_status == 'Urgent')
                        <label class='badge badge-danger'>Urgent</label>
                    @endif
                </td>
                <td>{{$data->assignFname}}{{$data->assignLname}}</td>
                <td>{{date('m/d/Y  h:i A',strtotime($data->due_date))}}</td>
                <td>{{date('m/d/Y  h:i A',strtotime($data->created_date))}}
                    <br/>{{$data->first_name}}{{$data->last_name}}</td>
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
{{ $taskData->appends(request()->query())->links() }}
</div>