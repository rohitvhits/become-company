<style>

</style>
<div class="tableData">
    <table id="order-listing1" class="table table-bordered table-head-fix recordtabletdwidth">
        <thead>
            <tr>
                <th nowrap>Id</th>
                <th nowrap>Task Name</th>
                <th nowrap>Priority</th>
                <th nowrap> Assign User </th>
                <th nowrap> Status </th>
                <th nowrap> Start Date </th>
                <th nowrap> Due Date </th>
                <th nowrap> Created Date/Created By </th>
                <th nowrap> Reason </th>
            </tr>
        </thead>
        <tbody>
            @php
            $i = 1 + ($query->currentPage() - 1) * $query->perPage();
            @endphp
            @if (count($query) > 0)
            @foreach ($query as $val)
            @if($val->is_flag_read == 0)
                @php
                    $clickHtml = "makeFlagRead('" . addslashes($val->flag_id) . "', '". addslashes($val->id) ."','Task');";
                    $href = "javascript:void(0)";
                    $target = '';
                @endphp
            @else
                @php
                    $clickHtml = "";
                    $href = url('tasks/task-list/' . $val->id);
                    $target = '_blank';
                @endphp
            @endif
            <tr>
                <td nowrap>
                    <a target="{{$target}}" onclick="{{$clickHtml}}" href="{{$href}}"><?= '#' . '' . $val->id ?></a>
                    @if($val->is_flag_read == 0)
                        <div style="position:relative"><span class="add_new_record left_record" >New</span></div>
                    @endif
                </td>
                <td nowrap>{{$val->task_name}}</td>
                <td nowrap>{{$val->priority}}</td>
                <td nowrap>{{$val->assignFname}} {{$val->assignLnamae}}</td>
                <td nowrap id="status{{$val->id}}">
                    @if(strtolower($val->task_status) =='pending')
                        <span class="badge badge-primary">Pending</span>
                    @elseif(strtolower($val->task_status) =='urgent')
                    <span class="badge badge-danger">Urgent</span>
                    @elseif(strtolower($val->task_status) =='outstanding')
                    <span class="badge badge-success">Outstanding</span>
                    @elseif(strtolower($val->task_status) =='completed')
                    <span class="badge badge-info">Completed</span>
                    @endif
                </td>
                <td nowrap>{{!empty($val->start_date) ? date('m/d/Y h:i A',strtotime($val->start_date)) : '-'}}</td>
                <td nowrap>{{!empty($val->due_date) ?date('m/d/Y h:i A',strtotime($val->due_date))  : '-'}}</td>
                <td nowrap>{{date('m/d/Y h:i A',strtotime($val->created_date))}} </br>{{$val->first_name}} {{$val->last_name}}</td>
                <td nowrap>{{$val->reason}}</td>
            </tr>
            @endforeach
            @endif
            @if (count($query) == 0)
            <tr>
                <td colspan="8">
                    <center><b>Data not found</b></center>
                </td>
            </tr>
            @endif
        </tbody>
    </table>
</div>
<div class="pull-right pegination-margin">
    {{ $query->appends(request()->input())->links('pagination::bootstrap-4') }}
</div>