<div class="card-body table-responsive">
    <table id="order-listing1" class="table table-bordered table-width1">
        <thead>
            <tr>
                <th width="3%"><input type="checkbox" id="main_checkBox1" /></th>
                <th width="5">Task Id</th>
                <th width="15%">Task Name</th>
                <th width="5%">Priority</th>
                <th width="5%">Status</th>
                <th width="7%"># Record</th>
                <th width="8%">Assign User</th>
                <th width="8%">Start Date</th>
                <th width="8%">Due Date</th>
                <th width="8%">Department</th>
                <th width="14%">Created Date <br />Created By</th>
                <th width="12%">Action</th>
            </tr>

        </thead>
        <tbody>
            @php

            $i = 1 +(($query->currentPage()-1) * $query->perPage());

            @endphp
            @if(count($query) >0)

            @foreach($query as $val)
            @php $taskClass = ''; @endphp
            @if($val->flag == 0)
            @php $flag = 'Flag'; @endphp
            @php $color = 'secondary'; @endphp
            @else
            @php $flag = 'Flagged';
            $color = 'success';
            $taskClass = 'pale-yellow-color';
            @endphp

            @endif
            <tr id="{{$val->id}}" class="taskBgHide">
                <td><input type="checkbox" class="cbox_id" value="{{$val->id}}" /></td>
                <td>
                    <a target="_blank" style="color:#005dc1;font-weight:bold;" onclick="return openModal('{{$val->id}}');"># {{$val->id}}</a>
                    @if(isset($val->task_label) && $val->task_label !='')
                    <br>
                    <div class="badge badge-danger" style="background-color:#FF474C">{{$val->task_label}}</div>
                    @endif
                </td>
                <td id="task{{$val->id}}">{{$val->task_name}}</td>
                <td id="priority{{$val->id}}">
                    @if(strtolower($val->priority) =='high')
                    <span class="badge badge-outline-danger">High</span>
                    @elseif(strtolower($val->priority) =='low')
                    <span class="badge badge-outline-success">Low</span>
                    @elseif(strtolower($val->priority) =='medium')
                    <span class="badge badge-outline-info">Medium</span>
                    @endif
                </td>
                <td id="status{{$val->id}}">
                    @if(strtolower($val->task_status) =='pending')
                    <span class="badge badge-warning">Pending</span>
                    @elseif(strtolower($val->task_status) =='urgent')
                    <span class="badge badge-danger">Urgent</span>
                    @elseif(strtolower($val->task_status) =='outstanding')
                    <span class="badge badge-success">Outstanding</span>
                    @elseif(strtolower($val->task_status) =='completed')
                    <span class="badge badge-info">Completed</span>
                    @endif
                </td>
                <td>
                    @if($val->record_id!='')
                    <a style="color:#01c100;font-weight:bold;" href="{{url('/patient/view/')}}/{{$val->record_id}}"># {{ $val->record_id}}</a>
                    @else
                    <span style="font-weight:bold;"> -</span>
                    @endif
                </td>
                <td id="assignee{{$val->id}}">{{!empty($val->assignUser) ? $val->assignUser->full_name : '-'}}</td>
                <td id="start_date{{$val->id}}">{{!empty($val->start_date)? date('m/d/Y',strtotime($val->start_date)):'-'}}</td>
                <td id="due_date{{$val->id}}">{{!empty($val->due_date)? date('m/d/Y h:i A',strtotime($val->due_date)):'-'}}</td>
                <td id="department{{$val->id}}">{{ $val->dep_name??'-' }}</td>
                <td>{{date('m/d/Y h:i A',strtotime($val->created_date))}}<br />{{$val->created_by}}</td>

                <td>
                    @can('flag-task-change-status')
                    <a onclick="flagTaskChange('{{$val->id}}');" class="btn btn-{{$color}} mr-2 badge badge-{{$color}}" title="{{$flag}}" style="margin-top: 4px;"><i class="fa fa-flag"></i></a>
                    @endcan
                    @can('task-delete')
                        <a class="btn btn-danger mr-2 badge badge-danger" style="background-color:#cb0b0b" href="javascript:void(0)" onclick="deleteTask('{{$val->id}}')"><i class="fa fa-trash"></i></a>
                    @endcan
                </td>

            </tr>
            @endforeach
            @endif

            @if(count($query) == 0)
            <tr>
                <td colspan="12" style="text-align:center">No record available</td>
            </tr>
            @endif
        </tbody>
    </table>

    <div class="pull-right pegination-margin task-list-pagination">
        {{$query->appends(request()->input())->links("pagination::bootstrap-4")}}
    </div>


</div>