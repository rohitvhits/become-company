@include('include/header')
 @include('include/sidebar')

 <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2/select2.min.css">
 <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
 <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
 <link href="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
 <link href="<?php echo URL::to('/'); ?>/assets/css/token-input.css" rel="stylesheet" type="text/css" />
 <link href="<?php echo URL::to('/'); ?>/assets/css/global.css" rel="stylesheet" type="text/css" />
 <link href="<?php echo URL::to('/'); ?>/assets/modulejs/css/task-module.css" rel="stylesheet" type="text/css" />
 <link href="{{ asset('/assets/bootstrap-datetimepicker.min.css')}}" type="text/css" media="all" rel="stylesheet" />
 <div class="main-panel main-page-box">
     <div class="content-wrapper content-wrapper-box">
         <div class="page-title-main">
             <h5 class="mb-0 font-weight-bold">Hub Record Task List <span id="total_task_id"></span></h5>
             <div class="page-rightbtns cust-page-rightbtns">
                 <div>
                    <a href="javascript:void(0)" id="searchbtns" class="btn cust-right-btn" style="background-color: #00879E;color:#fff;"><i
                        class="mdi mdi-filter-outline"></i>Filter <span class="active-filter"></span></a>
                    @can('hub-task-export')
                        <a href="javascript:void(0)" class="btn btn-success cust-right-btn" onclick="getExport();"><i class="mdi mdi-file"> </i>Export</a>
                    @endcan

                    @can('hub-task-add')
                        <a data-toggle="modal" href="javascript:void(0)" class="btn btn-primary cust-right-btn" data-target="#exampleModal-task"><i class="mdi mdi-plus"> </i>Add New Task</a>
                    @endcan
                 </div>
             </div>
         </div>
         <hr />
         <div class="row ">
             <div class="col-sm-12">
                <div id="search-filter-btn" style="display: none;">
                 <div class="card search-card1 cust-card-box" id="search-div">
                     <div class="card-body p-0 border-0">
                         <form method="get" id="formsubmit" class="form-patient-list-box" name="task-form">
                            @csrf
                            <div class="row form-row-gap">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <div class="row">
                                            <label class="col-sm-12 ">Task Name</label>
                                            <div class="col-sm-12">
                                                <input type="text" class="form-control" autocomplete="off" name="task_name" id="task_name" value="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <div class="row">
                                            <label class="col-sm-12 ">Assign User</label>
                                            <div class="col-sm-12">
                                                <select name="user_id" id="user_id" class="form-control">
                                                    <option value="all">All</option>
                                                    @if(!empty($nyb_user_list[0]))
                                                    @foreach($nyb_user_list as $va)
                                                    <option value="{{$va->id}}">{{$va->name}}</option>
                                                    @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <div class="row">
                                            <label class="col-sm-12 ">Due Date</label>
                                            <div class="col-sm-12">
                                                <input type="text" name="task_due_date" value="" autocomplete="off" class="form-control" id="task_due_date" placeholder="Select Task Due Date" value="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <div class="row">
                                            <label class="col-sm-12 ">Created Date</label>
                                            <div class="col-sm-12">
                                                <input type="text" name="created_task_date" autocomplete="off" value="" class="form-control" id="created_task_date" placeholder="Select Task Created Date" value="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <div class="row">
                                            <label class="col-sm-12 ">Created By</label>
                                            <div class="col-sm-12">
                                                <select name="created_user_id" id="created_user_id" class="form-control">
                                                    <option value="">All</option>
                                                    @if(!empty($nyb_user_list[0]))
                                                    @foreach($nyb_user_list as $va)
                                                    <option value="{{$va->id}}">{{$va->name}}</option>
                                                    @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <div class="row">
                                            <label class="col-sm-12 ">Priority</label>
                                            <div class="col-sm-12">
                                                <select name="priority" class="form-control" id="priority">
                                                    <option value="">Select Priority</option>
                                                    <option value="High">High</option>
                                                    <option value="Medium">Medium</option>
                                                    <option value="Low">Low</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <div class="row">
                                            <label class="col-sm-12 ">Status</label>
                                            <div class="col-sm-12">
                                                <select name="status" id="status" class="form-control">
                                                    <option value="all">All</option>
                                                    <option value="Urgent">Urgent</option>
                                                    <option value="Outstanding">Outstanding</option>
                                                    <option value="Pending">Pending</option>
                                                    <option value="Completed">Completed</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                     <div class="page-rightbtns cust-page-rightbtns">
                                         <a type="button" name="search" class="btn btn-primary searchTask cust-right-btn " id="search-data"
                                             value="Search"><i class="fa fa-search"></i>Search</a>
                                          <a class="btn btn-secondary cust-right-btn" href="javascript:void(0);" onclick="refresh();"><i class="fa fa-refresh"></i> Reset</a>  
                                     </div>
                                </div>
                            </div>
                         </form>
                     </div>
                 </div>
             </div>
             </div>
         </div>
    
        <div class="card common-card-box">
            <div class="card-body table-responsive location-wise-data-loader">
            <table id="" class="table table-bordered ">
                    <thead>
                        <th width="5">Task Id</th>
                        <th width="15%">Task Name</th>
                        <th width="5%">Priority</th>
                        <th width="5%">Status</th>
                        <th width="7%"># Record</th>
                        <th width="18%">Assign User</th>
                        <th width="7%">Start Date</th>
                        <th width="7%">Due Date</th>
                        <th width="14%">Created Date <br />Created By</th>
                        <th width="12%">Action</th>
                    </thead>
                    <tbody class="shimmer-loader">
                        <tr>
                            <td colspan="10"></td>
                        </tr>
                    </tbody>
                </table>
            </div> 
            <span id="task_list">
            </span>           
        </div>
    </div>
</div>
<div class="row" id="blank_div" style='margin-top: 15%;'></div>
    @include('hubRecord/modal/task_view')
    @include('hubRecord/modal/task_due_date')
    @include('hubRecord/modal/task_assignee_modal')
    @include('hubRecord/modal/task_description_modal')
    @include('hubRecord/modal/task_title_modal')
    @include('hubRecord/modal/task_add')

    @include('include/footer')
    <script>
        var _HUB_TASK_LIST = '{{ url("hub-record/task-list") }}';
        var TASK_STATUS_CHANGE = "{{url('hub-task-change-status')}}";
        var TASK_AJAX = "{{ url('hub-task-list-ajax')}}";
        var COMMENT_SAVE = "{{ url('hub-task-comment-save') }}";
        var TASK_TIME_LOG_LIST = "{{ url('hub-record/task-time-log-list') }}";
        var TASK_COMMENT_LIST = "{{ url('hub-task-comment-list') }}";
        var ACTIVITY_LOG = "{{ url('hub-task/activity-log-list') }}";
        var TASK_ASSIGN_USER = "{{ url('hub-task-assign-to-user') }}";
        var TASK_DESCRIPTION_UPDATE = "{{ url('hub-task-discription-update')}}";
        var FLAG_TASK = "{{ url('hub-flag-change-task-status')}}";
        var CSRF_TOKEN = "{{ csrf_token() }}";
        var TASK_DUE_DATE = "{{ url('hub-task-due-date') }}";
        var TASK_TITLE_UPDATE = "{{ url('hub-task-title-update') }}";
        var TASK_PRIORITY_CHANGE = "{{ url('hub-task-priority-update') }}";
        var AUTH = "{{auth()->user()->id}}";
        var CLOCK_IN_OUT = "{{ url('hub-record/task-clock-in-out') }}";
        var TASK_AJAX_LIST = "{{ url('hub-record/task-ajax-list') }}";
        var TASK_EXPORT  = "{{ url('hub-task-list-export') }}";
        var _DATE_TIME = "{{date('Y-m-d H:i:s')}}";
        var TASK_AJAX = "{{ url('hub-task-list-ajax')}}";
        var _TASK = "{{url('hub-record/task-record')}}";
        var _RECORD_ID="";
    </script>
    <script type="text/javascript" src="{{ asset('assets/vendors/summernote/dist/summernote-bs4.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('assets/vendors/tinymce/tinymce.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('assets/vendors/quill/quill.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/moment.min.js')}}"></script>
    <script src="{{ asset('/assets/js/tabs.js')}}"></script>
    <!-- Bootstrap DateTimePicker -->
    <script src="{{ asset('/assets/bootstrap-datetimepicker.min.js')}}"></script>
    
    <!-- SweetAlert -->
    <script src="{{ asset('assets/sweetalert.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/daterangepicker.min.js')}}"></script>
    <link rel="stylesheet" type="text/css" href="{{ asset('css/daterangepicker.css')}}" />
    <script type="text/javascript" src="{{ asset('assets/modulejs/hub_record/task_list.js')}}"></script>

    
