<link href="<?php echo URL::to('/');?>/assets/sweetalert.min.css" rel="stylesheet" type="text/css" />
<script src="<?php echo URL::to('/');?>/assets/sweetalert.min.js"></script>
<style>
    .table-responsive12 {
        display: block;
        width: calc(100vh - -700px);
        -webkit-overflow-scrolling: touch;
        overflow: visible !important;
    }

    .table-responsive12 .dropdown-menu {
        position: absolute !important; /* Ensures proper rendering */
        will-change: transform; /* Fix dropdown positioning */
    }
</style>
<div class="table-responsive12">
    <table id="order-listing1" class="table table-bordered">
        <thead>
            <tr>
                <!--<th></th>-->
                <th style="white-space:nowrap">#</th>
                
                <th style="white-space:nowrap">Task Name</th>
                <th style="white-space:nowrap">Priority</th>
                <th style="white-space:nowrap">Assign User</th>
                <th style="white-space:nowrap">Status</th>
                <th style="white-space:nowrap">Start Date</th>
                <th style="white-space:nowrap">Due Date</th>
                <th style="white-space:nowrap">Department Name</th>
                <th style="white-space:nowrap">Created Date / Created By</th>
                @if(isset($patient_details->deleted_flag) && $patient_details->deleted_flag = 'N')
                <th style="white-space:nowrap">Action</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @php
            $i = 1 +(($query->currentPage()-1) * $query->perPage());
            @endphp
            @if(count($query) >0)
                @foreach($query as $val)
                    @php
                    $taskClass = '';
                    @endphp
                    @if($val->flag == 0)
                        @php 
                            $flag = 'Flag';
                            
                        @endphp
                    @else
                        @php 
                            $flag = 'Flagged'; 
                            $taskClass = 'pale-yellow-color';
                        @endphp
                    @endif
                    <tr class="{{$taskClass}}" id="{{$val->id}}">
                        <td  nowrap > @if($val->merge_flag ==1){{ $i++}} @else <a href="javascript:void(0)" onclick="return openModal('{{$val->id}}');"></i> {{$i++}} </a> @endif
                        @if(isset($val->task_label) && $val->task_label !='')
                        <br>
                        <div class="badge badge-danger" style="background-color:#FF474C">{{$val->task_label}}</div>
                        @endif
                        @if($val->merge_flag ==1)
                        <br>
                        <span class="badge badge-info">Merge</span>
                        @endif
                        </td>
                        <td id="task{{$val->id}}">{{$val->task_name}}</td>
                        <td id="priority{{$val->id}}">{{$val->priority}}</td>
                        <td id="assignee{{$val->id}}">{{$val->assignFname}} {{$val->assignLnamae}}</td>
                    
                        <td id="status{{$val->id}}">
                            
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
                        <td id="start_date{{$val->id}}">{{!empty($val->start_date) ? Common::convertMDY($val->start_date) : '-'}}</td>
                        <td id="due_date{{$val->id}}">{{!empty($val->due_date) ?Common::convertMDYTime($val->due_date) : '-'}}</td>
                        <td>{{$val->dep_name ?? '-'}}</td>
                        <td>{{ Common::convertMDYTime($val->created_date) }} <br> {{$val->first_name}} {{$val->last_name}}</td>
                        @if(isset($patient_details->deleted_flag) && $patient_details->deleted_flag = 'N')
                        <td  style="overflow: unset !important">
                            @if($val->merge_flag ==0)
                            <div class="btn-group pull-right status-dropdoown mr-2">
                                <button type="button" class="btn btn-warning" title="Status">Action</button>
                                <button type="button" class="btn btn-warning dropdown-toggle dropdown-toggle-split" id="dropdownMenuSplitButton6" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuSplitButton6">

                                    <a class="dropdown-item"  href="javascript:void(0)" onclick="return openModal('{{$val->id}}');"></i>View</a>
                            
                                    <a class="dropdown-item"  href="javascript:void(0)" onclick="getDeleteTask('{{$val->id}}')">Delete</a>
                            
                                    <a class="dropdown-item" href="javascript:void(0)" onclick="getModal('{{$val->id}}')" data-toggle="modal" data-target="#exampleModal-change-task-staus" data-whatever="@mdo">Change Status</a>

                                    @can('flag-task-change-status')
                                        <a onclick="flagTaskChange('{{$val->id}}');" class="dropdown-item" title="Flag">{{$flag}}</a>
                                    @endcan
                                    
                                </div>
                            </div>
                            @endif
                        </td>
                        @endif
                    </tr>
                @endforeach
            @endif
            
            @if(count($query) ==0)
                <tr>
                    <td colspan="10">No record available</td>
                </tr>
            @endif
        </tbody>
    </table>
                  		
    <div class="pull-right pegination-margin patient-task-list-pagination">
        {{$query->appends(request()->input())->links("pagination::bootstrap-4")}}
    </div>
</div>
