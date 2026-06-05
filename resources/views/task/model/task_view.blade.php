<div class="modal fade modal-container" id="task_view" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <input type="hidden" name="hdn_priority" id="hdn_priority" value="" />
            <input type="hidden" name="hdn_task_id" id="hdn_task_id" value="" />
            <div class="modal-header">
                <div class="modal-header--left" id="modal-header--left">
                    <h4 class="modal-title">Task <span class="" style="font-size: 18px;"><span id="task_id"></span></h4>
                    <span class="modal-sub-title">Created by<span class="created-by mx-1 bold" id="created-by"></span><span class="created-at" id="created-at"></span></span>
                </div>
                <div class="modal-header--right" id="modal-header--right">
                    <div class="d-flex">
                        <div class="date-wrapper d-flex">
                            <div class="wrapper-content">
                                <div class="due-date-content">
                                    <span class="due-label content-label no-wrap">Due Date
                                        <span class="icon-box icon-white edit-due-date tippy-white" onclick="openDueDateModal();" data-theme="white" data-tippy-content="Edit" data-task-id="" data-time="" data-date="" data-follow-up="">
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M11.328 0.5C11.8191 0.5 12.2898 0.696065 12.6336 1.04257L14.9593 3.36824C15.3055 3.7145 15.5 4.18413 15.5 4.67381C15.5 5.16349 15.3055 5.63312 14.9593 5.97938L6.46813 14.4678C5.94427 15.0721 5.20167 15.4434 4.35095 15.5017H0.5V14.7517L0.502436 11.5905C0.566329 10.7996 0.934 10.0642 1.49487 9.57006L10.0215 1.04347C10.3673 0.695622 10.8375 0.5 11.328 0.5ZM4.29799 14.0036C4.69865 13.9753 5.07161 13.7888 5.37109 13.4462L11.0425 7.77479L8.22674 4.9589L2.52207 10.6622C2.21821 10.9309 2.03023 11.3069 2 11.6509V14.0022L4.29799 14.0036ZM9.28753 3.89836L12.1032 6.71413L13.8986 4.91872C13.9635 4.85376 14 4.76567 14 4.67381C14 4.58195 13.9635 4.49386 13.8986 4.4289L11.5708 2.10114C11.5066 2.03641 11.4192 2 11.328 2C11.2368 2 11.1494 2.03641 11.0852 2.10114L9.28753 3.89836Z" fill="#fff"></path>
                                            </svg>
                                        </span></span>
                                </div>
                                <span class="due-date  content-data no-wrap" id="due-date"> </span>
                            </div>
                            <div class="devider"></div>
                            <div class="wrapper-content">
                                <div class="due-date-content">
                                    <span class="due-label content-label no-wrap"><button type="button" class="btn cust-right-btn" id="" onclick="return getChangeStatusById('Completed');" style="border-radius: .25rem;border: 1px #ebebeb solid;color: #fff;margin-top: 10px;"><i class="fa fa-check" aria-hidden="true"></i> Mark Complete </button></span>
                                </div>
                            </div>
                            <div class="devider clock_in_div"></div>
                            <div class="wrapper-content">
                                <div class="due-date-content">
                                    <span class="due-label content-label no-wrap">
                                        <button href="javascript:void(0)" class="pull-right btn btn-danger clock_out btn-sm checkInCheckOutBtn" id="clock_out" onclick="clockIn('clock_out')" title="Clock Out" style=""><i class="fa fa-clock-o mr-1"></i> Clock Out</button>
                                        <button href="javascript:void(0)" class="pull-right btn btn-success clock_in btn-sm checkInCheckOutBtn" id="clock_in" onclick="clockIn('clock_in')" title="Clock In" style=""><i class="fa fa-clock-o mr-1"></i> Clock In</button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-close-wrapper ml-5">
                    <button type="button" class="close form-clear url-clear view_task_modal_close" data-dismiss="modal" aria-label="Close" style="margin: 0px 0px 36px 0px;">
                        <svg width="12" height="12" viewBox="0 0 14 14" fill="#fff" xmlns="http://www.w3.org/2000/svg">
                            <path d="M13.3 0.709956C13.1131 0.522704 12.8595 0.417471 12.595 0.417471C12.3305 0.417471 12.0768 0.522704 11.89 0.709956L6.99997 5.58996L2.10997 0.699956C1.92314 0.512704 1.66949 0.407471 1.40497 0.407471C1.14045 0.407471 0.886802 0.512704 0.699971 0.699956C0.309971 1.08996 0.309971 1.71996 0.699971 2.10996L5.58997 6.99996L0.699971 11.89C0.309971 12.28 0.309971 12.91 0.699971 13.3C1.08997 13.69 1.71997 13.69 2.10997 13.3L6.99997 8.40996L11.89 13.3C12.28 13.69 12.91 13.69 13.3 13.3C13.69 12.91 13.69 12.28 13.3 11.89L8.40997 6.99996L13.3 2.10996C13.68 1.72996 13.68 1.08996 13.3 0.709956Z"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="modal-body position-relative loader" style="display:block">
                <div class="row">
                    <div class="col-lg-8 mb-3">
                        <!-- Start : Task Details
                        ======================================================================= -->
                        <div class="card" id="task-info">
                        <div class="card-header">      
                            <div class="shimmer-loader"> <span>
                                <i class="fa fa-tasks mr-1"></i>
                            </span> <p></p></div>     
                        </div>
                        </div>
                        <!-- End : Task Details
                        ======================================================================= -->

                        <!-- Start : Task Description
                        ======================================================================= -->
                        <div class="card mt-2" id="task-description">
                            <div class="card-header">
                                <div class="d-flex justify-content-between">
                                    <div class="card-header--left">
                                        <h5 class="card-title"><b><i class="fa fa-align-left mr-1"></i>Description</b></h5>
                                    </div>
                                    <div class="card-header--right">
                                        <span class="icon-box edit-description tippy-black" style="padding:0px" data-theme="black" data-tippy-content="Edit Description">
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M11.328 0.5C11.8191 0.5 12.2898 0.696065 12.6336 1.04257L14.9593 3.36824C15.3055 3.7145 15.5 4.18413 15.5 4.67381C15.5 5.16349 15.3055 5.63312 14.9593 5.97938L6.46813 14.4678C5.94427 15.0721 5.20167 15.4434 4.35095 15.5017H0.5V14.7517L0.502436 11.5905C0.566329 10.7996 0.934 10.0642 1.49487 9.57006L10.0215 1.04347C10.3673 0.695622 10.8375 0.5 11.328 0.5ZM4.29799 14.0036C4.69865 13.9753 5.07161 13.7888 5.37109 13.4462L11.0425 7.77479L8.22674 4.9589L2.52207 10.6622C2.21821 10.9309 2.03023 11.3069 2 11.6509V14.0022L4.29799 14.0036ZM9.28753 3.89836L12.1032 6.71413L13.8986 4.91872C13.9635 4.85376 14 4.76567 14 4.67381C14 4.58195 13.9635 4.49386 13.8986 4.4289L11.5708 2.10114C11.5066 2.03641 11.4192 2 11.328 2C11.2368 2 11.1494 2.03641 11.0852 2.10114L9.28753 3.89836Z" fill="#526484"></path>
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body position-relative task-description-container shimmer-loader" style="min-height: 100px;">
                                <span class="card-header--right"></span>
                            </div>
                        </div>
                        <!-- End : Task Description
                        ======================================================================= -->

                        <div class="row">
                            <div class="col-md-6">
                                <div class="card mt-2">
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between">
                                            <div class="card-header--left">
                                                <h5 class="card-title bold"> <i class="mdi mdi-timetable mr-1"></i>Activity</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body position-relative activity-container shimmer-loader" id="" style="min-height: 100px;">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card mt-2">
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between">
                                            <div class="card-header--left">
                                                <h5 class="card-title"><i class="fa fa-clock-o mr-1"></i>Time Log</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body position-relative timeline-container shimmer-loader" id="" style="min-height: 100px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex justify-content-between">
                                    <div class="card-header--left">
                                        <h5 class="card-title"><i class="fa fa-user mr-1"></i>Assignee</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body py-2" id="assignee-card">
                                <div class="basic-wrapper" id="basic-wrapper">
                                    <div class="assignee mt-2 shimmer-loader" id="assignee-wrapper">
                                        <span class="assign_name bold" id=""> </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card mt-2">
                            <div class="card-header">
                                <div class="d-flex justify-content-between">
                                    <div class="card-header--left">
                                        <h5 class="card-title"><i class="fa fa-comment mr-1"></i>Comment</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body py-2" id="comment-card">
                                <div class="basic-wrapper" id="basic-wrapper">
                                    <div class="comment mt-2 chat shimmer-loader">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-body position-relative showResult" style="display:none">
                <input type="hidden" id="task_id" autocomplete="off" value="">
                <input type="hidden" id="show_type_id" autocomplete="off">
                
                <div class="row">
                    <div class="col-lg-8 mb-3">
                        <!-- Start : Task Details
                        ======================================================================= -->
                        <div class="card" id="task-info">
                            <div class="card-header d-flex justify-content-between flex-sm-wrap">
                                <span>
                                    <i class="mdi mdi-note mr-1"></i>
                                    <span id="task-title"></span>
                                        <span class="icon-box edit-title tippy-black" data-theme="black" data-tippy-content="Edit Title" onclick="openTitleModal();">
                                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M11.328 0.5C11.8191 0.5 12.2898 0.696065 12.6336 1.04257L14.9593 3.36824C15.3055 3.7145 15.5 4.18413 15.5 4.67381C15.5 5.16349 15.3055 5.63312 14.9593 5.97938L6.46813 14.4678C5.94427 15.0721 5.20167 15.4434 4.35095 15.5017H0.5V14.7517L0.502436 11.5905C0.566329 10.7996 0.934 10.0642 1.49487 9.57006L10.0215 1.04347C10.3673 0.695622 10.8375 0.5 11.328 0.5ZM4.29799 14.0036C4.69865 13.9753 5.07161 13.7888 5.37109 13.4462L11.0425 7.77479L8.22674 4.9589L2.52207 10.6622C2.21821 10.9309 2.03023 11.3069 2 11.6509V14.0022L4.29799 14.0036ZM9.28753 3.89836L12.1032 6.71413L13.8986 4.91872C13.9635 4.85376 14 4.76567 14 4.67381C14 4.58195 13.9635 4.49386 13.8986 4.4289L11.5708 2.10114C11.5066 2.03641 11.4192 2 11.328 2C11.2368 2 11.1494 2.03641 11.0852 2.10114L9.28753 3.89836Z" fill="#526484"></path>
                                                </svg>
                                            </span>
                                </span>
                                <div class="row">
                                    <div class="btn-group status-dropdoown mr-2">
                                        <button type="button" class="btn badge statusBtn" title="Status"><span class="status-name" id="status-name"></span></button>
                                        <button type="button" class="btn dropdown-toggle dropdown-toggle-split badge statusBtn"
                                            id="task-status" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                            <span class="sr-only">Toggle Dropdown</span>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="task-status">
                                            <a class="dropdown-item" href="javascript:void(0)" onclick="getChangeStatusById('Pending');" value="Pending">Pending</a>
                                            <a class="dropdown-item" href="javascript:void(0)" onclick="getChangeStatusById('Urgent');" value="Urgent">Urgent</a>
                                            <a class="dropdown-item" href="javascript:void(0)" onclick="getChangeStatusById('Outstanding');" value="Outstanding">Outstanding</a>
                                            <a class="dropdown-item" href="javascript:void(0)" onclick="getChangeStatusById('Completed');"value="Completed">Completed</a>
                                        </div>
                                    </div>
                                    <div class="btn-group status-dropdoown mr-2">
                                        <button type="button" class="btn badge prioBtn" title="Priority"><span class="priority-name" id="priority-name"></span></button>
                                        <button type="button" class="btn dropdown-toggle dropdown-toggle-split badge prioBtn"
                                            id="task-priority" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                            <span class="sr-only">Toggle Dropdown</span>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="task-priority">
                                            <a class="dropdown-item" href="javascript:void(0)" onclick="getChangePriorityById('High');" value="High">High</a>
                                            <a class="dropdown-item" href="javascript:void(0)" onclick="getChangePriorityById('Medium');" value="Medium">Medium</a>
                                            <a class="dropdown-item" href="javascript:void(0)" onclick="getChangePriorityById('Low');" value="Low">Low</a>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                        <!-- End : Task Details
                        ======================================================================= -->

                        <!-- Start : Task Description
                        ======================================================================= -->
                        <div class="card mt-2" id="task-description">
                            <div class="card-header">
                                <div class="d-flex justify-content-between">
                                    <div class="card-header--left">
                                        <h5 class="card-title"><b><i class="fa fa-align-left mr-1"></i>Description</b></h5>
                                    </div>
                                    <div class="card-header--right">
                                        <span class="icon-box edit-description tippy-black" style="padding:0px" data-theme="black" data-tippy-content="Edit Description" onclick="openDescriptionModal();">
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M11.328 0.5C11.8191 0.5 12.2898 0.696065 12.6336 1.04257L14.9593 3.36824C15.3055 3.7145 15.5 4.18413 15.5 4.67381C15.5 5.16349 15.3055 5.63312 14.9593 5.97938L6.46813 14.4678C5.94427 15.0721 5.20167 15.4434 4.35095 15.5017H0.5V14.7517L0.502436 11.5905C0.566329 10.7996 0.934 10.0642 1.49487 9.57006L10.0215 1.04347C10.3673 0.695622 10.8375 0.5 11.328 0.5ZM4.29799 14.0036C4.69865 13.9753 5.07161 13.7888 5.37109 13.4462L11.0425 7.77479L8.22674 4.9589L2.52207 10.6622C2.21821 10.9309 2.03023 11.3069 2 11.6509V14.0022L4.29799 14.0036ZM9.28753 3.89836L12.1032 6.71413L13.8986 4.91872C13.9635 4.85376 14 4.76567 14 4.67381C14 4.58195 13.9635 4.49386 13.8986 4.4289L11.5708 2.10114C11.5066 2.03641 11.4192 2 11.328 2C11.2368 2 11.1494 2.03641 11.0852 2.10114L9.28753 3.89836Z" fill="#526484"></path>
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body position-relative task-description-container" id="task-description-wrapper" style="min-height: 100px;">
                                <span class="card-header--right" id="task-description-text"></span>
                            </div>
                        </div>
                        <!-- End : Task Description
                        ======================================================================= -->

                        <div class="row">
                            <div class="col-md-6">
                                <div class="card mt-2">
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between">
                                            <div class="card-header--left">
                                                <h5 class="card-title bold"> <i class="mdi mdi-timetable mr-1"></i>Activity</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body position-relative activity-container" id="activity-loader-wrapper" style="min-height: 100px;">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card mt-2">
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between">
                                            <div class="card-header--left">
                                                <h5 class="card-title"><i class="fa fa-clock-o mr-1"></i>Time Log</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body position-relative timeline-container" id="task-time-log-list" style="min-height: 100px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex justify-content-between">
                                    <div class="card-header--left">
                                        <h5 class="card-title"><i class="fa fa-user mr-1"></i>Department</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body py-2" id="">
                                <div class="basic-wrapper" id="basic-wrapper">
                                    <div class="task-department mt-2" id="task-department-wrapper">
                                        <span class="bold" id="task-department"> </span>
                                        <input type="hidden" name="task-dept-id" id="task-dept-id" value="">
                                        <span class="icon-box edit-dept tippy-black" data-theme="black" data-tippy-content="Edit Department" onclick="openDeptModal();">
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M11.328 0.5C11.8191 0.5 12.2898 0.696065 12.6336 1.04257L14.9593 3.36824C15.3055 3.7145 15.5 4.18413 15.5 4.67381C15.5 5.16349 15.3055 5.63312 14.9593 5.97938L6.46813 14.4678C5.94427 15.0721 5.20167 15.4434 4.35095 15.5017H0.5V14.7517L0.502436 11.5905C0.566329 10.7996 0.934 10.0642 1.49487 9.57006L10.0215 1.04347C10.3673 0.695622 10.8375 0.5 11.328 0.5ZM4.29799 14.0036C4.69865 13.9753 5.07161 13.7888 5.37109 13.4462L11.0425 7.77479L8.22674 4.9589L2.52207 10.6622C2.21821 10.9309 2.03023 11.3069 2 11.6509V14.0022L4.29799 14.0036ZM9.28753 3.89836L12.1032 6.71413L13.8986 4.91872C13.9635 4.85376 14 4.76567 14 4.67381C14 4.58195 13.9635 4.49386 13.8986 4.4289L11.5708 2.10114C11.5066 2.03641 11.4192 2 11.328 2C11.2368 2 11.1494 2.03641 11.0852 2.10114L9.28753 3.89836Z" fill="#526484"></path>
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex justify-content-between">
                                    <div class="card-header--left">
                                        <h5 class="card-title"><i class="fa fa-user mr-1"></i>Assignee</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body py-2" id="assignee-card">
                                <div class="basic-wrapper" id="basic-wrapper">
                                    <div class="assignee mt-2" id="assignee-wrapper">
                                        <span class="assign_name bold" id="task-assign"> </span>
                                        <input type="hidden" name="task-assign-id" id="task-assign-id" value="">
                                        <span class="icon-box edit-assignee tippy-black" data-theme="black" data-tippy-content="Edit Assignee" onclick="openAssigneeModal();">
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M11.328 0.5C11.8191 0.5 12.2898 0.696065 12.6336 1.04257L14.9593 3.36824C15.3055 3.7145 15.5 4.18413 15.5 4.67381C15.5 5.16349 15.3055 5.63312 14.9593 5.97938L6.46813 14.4678C5.94427 15.0721 5.20167 15.4434 4.35095 15.5017H0.5V14.7517L0.502436 11.5905C0.566329 10.7996 0.934 10.0642 1.49487 9.57006L10.0215 1.04347C10.3673 0.695622 10.8375 0.5 11.328 0.5ZM4.29799 14.0036C4.69865 13.9753 5.07161 13.7888 5.37109 13.4462L11.0425 7.77479L8.22674 4.9589L2.52207 10.6622C2.21821 10.9309 2.03023 11.3069 2 11.6509V14.0022L4.29799 14.0036ZM9.28753 3.89836L12.1032 6.71413L13.8986 4.91872C13.9635 4.85376 14 4.76567 14 4.67381C14 4.58195 13.9635 4.49386 13.8986 4.4289L11.5708 2.10114C11.5066 2.03641 11.4192 2 11.328 2C11.2368 2 11.1494 2.03641 11.0852 2.10114L9.28753 3.89836Z" fill="#526484"></path>
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card mt-2">
                            <div class="card-header">
                                <div class="d-flex justify-content-between">
                                    <div class="card-header--left">
                                        <h5 class="card-title"><i class="fa fa-comment mr-1"></i>Comment</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body py-2" id="comment-card">
                                <div class="basic-wrapper" id="basic-wrapper">
                                    <div class="comment mt-2 chat" id="commentList">
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div>
                                    <div class="row col-md-12 form-group mt-2 mb-2" style="margin: 0px;padding:0px">
                                        <textarea class="form-control" name="task_comment" placeholder="Type message..." id="task_comment" rows="5" cols="50"></textarea>
                                        <span id="comment_error" style="color:red"></span>
                                    </div>
                                    <button type="submit" onclick="commentSave()" class="btn badge badge-info"><i class="fa fa-save mr-2"></i>Save Comment</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>