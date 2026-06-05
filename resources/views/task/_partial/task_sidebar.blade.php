<div id="right-sidebar" class="settings-panel">
    <i class="settings-close mdi mdi-close"></i>
    <button type="button" class="btn btn-light btn-xs" id="status_button" onclick="return getChangeStatusById('Completed');">
        <i class="fa fa-check" aria-hidden="true"></i> Mark Complate </button>

    <a href="javascript:void(0)" class="pull-right btn btn-danger btn-rounded clock_out btn-sm checkInCheckOutBtn" id="clock_out" onclick="clockIn('{{request()->segment(2)}}','clock_out')" title="Clock Out" style=""><i class="fa fa-clock-o"></i> Clock Out</a>
    <a href="javascript:void(0)" class="pull-right btn btn-success btn-rounded clock_in btn-sm checkInCheckOutBtn" id="clock_in" onclick="clockIn('{{request()->segment(2)}}','clock_in')" title="Clock In" style=""><i class="fa fa-clock-o"></i> Clock In</a>

    <div class="tab-content" id="setting-content">
        <!-----------------form----------------------->
        <div class="tab-pane fade show active scroll-wrapper ps ps--active-y" id="todo-section" role="tabpanel" aria-labelledby="todo-section">
            <div class="card">
                <div class="card-body">
                    <h2 id="project_title" class="card-title"></h2>
                    <form class="forms-sample">
                        <input type="hidden" name="assign_id" id="assign_id" value="">
                        <div class="form-group row">
                            <label for="assignee" class="col-sm-3 col-form-label">Assignee</label>
                            <div class="col-sm-9">
                                <select name="assign_to_user_select" id="assign_to_user_select" class="form-control select2" onchange="assignUserById(this.value);">
                                    @if(!empty($user_list[0])) @foreach($user_list as $va)
                                    <option value="{{$va->id}}">{{$va->name}}</option>
                                    @endforeach @endif
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="exampleInputEmail2" class="col-sm-3 col-form-label">Due Date</label>
                            <div class="col-sm-9">
                                <div class="input-group date" id="id_0">

                                    {{-- <input type="text" id="daterange" onChange="changeDate()" value="" class="form-control date_input" required/> --}}
                                    <input type="text" id="daterange" value="" class="form-control date_input" required />
                                    <div class="input-group-addon input-group-append">
                                        <div class="input-group-text">
                                            <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="status" class="col-sm-3 col-form-label">Status</label>
                            <div class="col-sm-9">
                                <select name="task_status" id="task_status_select" class="form-control" onchange="getChangeStatusById(this.value);">
                                    <option value="">Select Status</option>
                                    <option value="Pending">Pending</option>
                                    <option value="Urgent">Urgent</option>
                                    <option value="Outstanding">Outstanding</option>
                                    <option value="Completed">Completed</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="created_by" class="col-sm-3 col-form-label">Created By</label>
                            <div class="col-sm-9">
                                <label for="created_by" class="col-form-label" id="created_by"></label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="exampleInputConfirmPassword2" class="col-sm-12 col-form-label">Discription</label>
                            <input type="hidden" name="hdn_priority" id="hdn_priority" value="" />
                            <div class="col-sm-12">
                                <textarea class="form-control" id="task_discription" rows="10"></textarea>
                            </div>
                        </div>
                </div>
                </form>
            </div>
            <!----------------TAB----------------->
            <div class="row">
                <div class="col-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="comment-tab" data-toggle="tab" onclick="commentList();" href="#comment-log-1" role="tab" aria-controls="task-log-1" aria-selected="false">Comment List</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="activity-log-tab" data-toggle="tab" onclick="getLogList(1);" href="#activity-log-1" role="tab" aria-controls="activity-log-1" aria-selected="false">Activity Log</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="task-time-tab" data-toggle="tab" onclick="getTimeLogList(1);" href="#task-time-1" role="tab" aria-controls="task-time-1" aria-selected="false">Task Time Log</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade active show" id="comment-log-1" role="tabpanel" aria-labelledby="comment-tab">
                                    <div class="media">
                                        <div class="media-body">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="card">

                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <h6 class="card-title">Comment Section
                                                                    </h6>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-lg-12">
                                                                    <div class="profile-feed chat scrollchat min-height109" id="commentList">

                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade " id="activity-log-1" role="tabpanel" aria-labelledby="activity-log-tab">
                                    <div class="media">
                                        <div class="media-body">
                                            <div class="row">
                                                <div class="col-sm-6 card-title">
                                                    <h4 class="card-title">Activity Log</h4>
                                                </div>
       
                                            </div>
                                            <div class="table-responsive">
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="card">
                                                            <div class="card-body">
                                                               
                                                                <div class="row">
                                                                    <div class="col-lg-12">
                                                                        <div class="profile-feed chat scrollchat min-height109" id="logList">
                                                                            <img src="{{ asset('/ajax-loader.gif') }}" alt="loader" id="loadertag" style="display: none; ">
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade " id="task-time-1" role="tabpanel" aria-labelledby="task-time-tab">
                                    <div class="row">
                                        <div class="col-sm-6 card-title">
                                            <h4 class="card-title">Task Time Logs</h4>
                                        </div>
                                        <div class="col-sm-6">

                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <div class="col-12" id="task-time-log-list" style="display:flex;justify-content:center;">
                                            <img src="{{ asset('/ajax-loader.gif') }}" alt="loader" id="loadertag1" style="display: none; ">
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="card-footer sticky-top fixed-bottom border-none">
                            <div class="col-md-12 form-group">
                                <label for="comment" class="col-sm-3 col-form-label">Add Comment</label>
                                <textarea class="form-control" name="task_comment" placeholder="Type message..." id="task_comment" rows="5" cols="50"></textarea>
                                <span id="comment_error" style="color:red"></span>
                            </div>
                            <button type="submit" onclick="commentSave()" class="btn btn-primary">Save Comment</button>
                        </div>
                    </div>
                </div>
            </div>
            <!----------------end tab---------------->
        </div>
        <!-------------------------------------------->

        <div class="ps__rail-x" style="left: 0px; bottom: 0px;">
            <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
        </div>
        <div class="ps__rail-y" style="top: 0px; height: 192px; right: 0px;">
            <div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 57px;"></div>
        </div>
    </div>
    <!-- To do section tab ends -->
    <!-- chat tab ends -->
</div>

