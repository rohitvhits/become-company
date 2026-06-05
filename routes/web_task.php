<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\DepartmentController;

Route::group(['middleware' => 'auth'], function () {
    Route::controller(TaskController::class)
        ->prefix('tasks')
        ->name('tasks.')
        ->group(function () {
            Route::resource('task-list', TaskController::class);
            Route::post('task-list/update','update');
            Route::get('task-list-export','export');
            Route::get('task-change-status','changeStatus');
            Route::get('task-assign-to-user','taskAssignToUser');
            Route::post('bulk-assign-task','bulkAssignTask');
            Route::post('bulk-assign-portal','bulkAssignPortal');
            Route::post('task-comment-save','taskCommentSave');
            Route::get('task-comment-list','taskCommentList');
            Route::get('mytesk','mytesk');
            Route::get('mytesk/export','MyTaskExport');
            Route::post('patient/task-add','PatientTaskAdd');
            Route::get('patient/task-list','PatientTaskList');
            Route::get('patient/task-log-list','PatientTaskLogList');
            Route::get('patient/task-clock-in-out','PatientTaskClockInOut');
            Route::get('patient/task-time-log-list','PatientTaskTimeLogList');

            Route::get('patient/activity-log-list','ActivityLogList');
            /*****************************Task-list-ajax***************************************/
            Route::get('task-list-ajax','TaskListAjax');
            Route::post('task-discription-update','TaskDiscriptionUpdate');
            Route::get('my-due-task','myDueTask');
            Route::get('task-detail','taskDetails');
            /******************************************************************************/
            Route::post('task-due-date', 'taskDueDateUpdate');
            Route::post('task-title-update','taskTitleUpdate');
            Route::post('task-priority-update','taskPriorityUpdate');
            Route::post('patient/task-ajax-list','taskListPageAjax');
            Route::post('task-update-dept','taskUpdateDept');
        });

    Route::controller(DepartmentController::class)
        ->prefix('tasks')
        ->name('tasks.')
        ->group(function () {
            Route::resource('department-master', DepartmentController::class);
            Route::get('department/ajax-list', 'ajaxList');
            Route::post('department/status-update', 'changeStatus');
            Route::get('get-task-dept','deptList');
        });
});