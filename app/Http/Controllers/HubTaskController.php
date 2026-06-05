<?php

namespace App\Http\Controllers;

use App\Helpers\Utility;
use App\Model\HubTask as Task;
use App\Model\HubTaskLog as TaskLog;
use App\Model\HubTaskTimer as TaskTimer;

use App\Services\HubLogsService as LogsService;

use Illuminate\Routing\Controller as BaseController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

use App\Services\HubTaskService as TaskService;
use App\Services\HubTaskStatusLogService as TaskStatusLogService;
use App\Services\PatientService;
use App\Services\HubTaskCommentService as TaskCommentService;
use App\Services\NotificationUserService;
use App\User;
use Carbon\Carbon;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;

class HubTaskController extends BaseController
{
    protected $PatientService, $taskCommentService, $taskService, $notificationUserService = "";
    public function __construct(PatientService $PatientService, TaskCommentService $taskCommentService, TaskService $taskService, NotificationUserService $notificationUserService)
    {
        $this->middleware('auth');
        $this->middleware('permission:hub-task-list|hub-task-add|hub-task-edit|hub-task-delete|hub-task-view', ['only' => ['index', 'store', 'show']]);
        $this->middleware('permission:hub-task-list', ['only' => ['index']]);
        $this->middleware('permission:hub-task-add', ['only' => ['create', 'store']]);
        $this->middleware('permission:hub-task-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:hub-task-delete', ['only' => ['delete']]);
        $this->middleware('permission:hub-task-view', ['only' => ['show']]);

        $this->PatientService = $PatientService;
        $this->taskCommentService = $taskCommentService;
        $this->taskService = $taskService;
        $this->notificationUserService = $notificationUserService;
    }

    public function index(Request $request)
    {
        $data['auth'] = $auth = auth()->user();
        $data['nyb_user_list'] = User::getNYBestUserData();
        return view('hubRecord.task_list', $data);
    }

    public function create(Request $request) {}

    public function store(Request $request)
    {
        $user = $auth = auth()->user();
        $validator = Validator::make($request->all(), [
            'task_name' => 'required',
            'assign_to' => 'required',
            'priority' => 'required',
            'task_description' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error_msg' => $validator->errors()->all()[0],
                'status' => false,
            ], 422);
        } else {
            $dataTask = array(
                'user_id' => $auth['id'],
                'task_name' => $request->input('task_name'),
                'assign_id' => $request->input('assign_to'),
                'task_description' => $request->input('task_description'),
                'priority' => $request->input('priority'),
                'due_date' =>  date('Y-m-d H:i:s', strtotime($request->input('due_date'))),
                'start_date' =>  $request->input('start_date') != "" ? date('Y-m-d', strtotime($request->input('start_date'))) : null,
            );
            $insert = TaskService::save($dataTask);
            if ($insert) {
                $ipaddress = Utility::getIP();
                $serializedDataTask = TaskService::getDetailsById($insert);
                $assignUser = User::find($request->input('assign_to'));
                $taskLog = [
                    'hub_task_id' => $insert,
                    'created_by' => $user->id,
                    'description' => $user->first_name . ' ' . $user->last_name . ' is created new task and assign to ' . $assignUser->first_name . ' ' . $assignUser->last_name,

                    'new_response' =>  serialize($serializedDataTask),
                    'created_at' => date('Y-m-d H:i:s'),
                ];
                $insertTaskLog = TaskLog::create($taskLog);
                $insertLog = [
                    'type' => 'Hub Task Created',
                    'link' => url('hub-record/view/') . '/' . $serializedDataTask->hub_record_id,
                    'module' => 'Hub Record',
                    'object_id' => $serializedDataTask->hub_record_id,
                    'message' => $user->first_name . ' ' . $user->last_name . ' is created new task and assign to ' . $assignUser->first_name . ' ' . $assignUser->last_name,
                    'new_response' => serialize($serializedDataTask->toArray()),
                    'ip' => $ipaddress,
                ];
                LogsService::save($insertLog);

                $getUserDetails = User::getDetailsById($request->input('assign_to'));
                $allemails = isset($getUserDetails->email) ? $getUserDetails->email : "";
                $currentDate = date('Y-m-d', strtotime(now()));
                Cache::forget("task_list_user_{$user->id}");
                $subject = "New Task Assigned";
                $fname = '';
                $lname = '';
                if (isset($getUserDetails->first_name) && $getUserDetails->first_name != '') {
                    $fname = $getUserDetails->first_name;
                }
                if (isset($getUserDetails->last_name) && $getUserDetails->last_name != '') {
                    $lname = $getUserDetails->last_name;
                }

                $username = $user['first_name'] . ' ' . $user['last_name'];

                $emailData = array(
                    'username' => $username,
                    'fname' => $fname,
                    'lname' => $lname,
                    'task_name' => $request->input('task_name'),
                    'task_description' => $request->input('task_description')
                );
                /*$messages = Utility::getHtmlContent('email_template.email_task_create', $emailData);

                try {
                    $mail = Mail::mailer('second')->send([], [], function ($message) use ($allemails, $subject, $messages) {
                        $message->to($allemails, "EMC Rep")
                            ->subject($subject)->html($messages);
                    });
                } catch (\Throwable $th) {
                } */


                $notificationData = array(
                    'type' => 'Task',
                    'user_id' => $request->input('assign_to'),
                    'title' => 'New Task Assigned',
                    'message' => $request->input('task_name') . ' | <b> Assigned To</b>: ' . $username,
                );
                // $this->notificationUserService->save($notificationData);
                return response()->json(['status' => true, 'error_msg' => 'Task created successfully'], 200);
            } else {
                return response()->json(['status' => true, 'error_msg' => 'Sorry, something went wrong. Please try again'], 500);
            }
        }
    }

    public function edit(Request $request, $id)
    {
        $auth = auth()->user();
        $data['user_list'] = User::getHospitalUser();
        $data['task_details'] = TaskService::getDetailsById($id);
        return view('task.task_edit', $data);
    }

    public function update(Request $request)
    {
        $auth = auth()->user();

        $validator = Validator::make($request->all(), [
            'task_name' => 'required',
            'assign_to' => 'required',
            'priority' => 'required',
            'task_description' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect("task-list/" . $request->input('id') . '/edit')
                ->withErrors($validator, 'agency')
                ->withInput();
        } else {
            $TaskServiceDeatils = TaskService::getDetailsById($request->input('id'));

            $dataTask = array(
                'task_name' => $request->input('task_name'),
                'assign_id' => $request->input('assign_to'),
                'task_description' => $request->input('task_description'),
                'due_date' =>  date('Y-m-d H:i:s', strtotime($request->input('due_date'))),
                'start_date' =>  $request->input('start_date') != "" ? date('Y-m-d', strtotime($request->input('start_date'))) : null,
                'priority' => $request->input('priority'),
            );
            TaskService::update($dataTask, array('id' => $request->input('id')));

            $assignUser = User::find($request->input('assign_to'));

            $updateTaskDetails = TaskService::getDetailsById($request->input('id'));
            $user = $auth = auth()->user();
            $currentDate = date('Y-m-d', strtotime(now()));
            Cache::forget("task_list_user_{$user->id}");
            $taskLog = [
                'hub_task_id' => $request->input('id'),
                'created_by' => $auth->id,
                'description' => $auth->first_name . ' ' . $auth->last_name . ' is updated task and assign to ' . $assignUser->first_name . ' ' . $assignUser->last_name,
                'old_response' =>  serialize($TaskServiceDeatils),
                'new_response' =>  serialize($updateTaskDetails),
                'created_at' => date('Y-m-d H:i:s'),

            ];
            $insertTaskLog = TaskLog::create($taskLog);
            $ipaddress = Utility::getIP();
            $insertLog = [
                'type' => 'Hub Task Updated',
                'link' => url('hub-record/view/') . '/' . $TaskServiceDeatils->hub_record_id,
                'module' => 'Hub Record',
                'object_id' => $TaskServiceDeatils->hub_record_id,
                'message' => $auth->first_name . ' ' . $auth->last_name . ' is updated task and assign to ' . $assignUser->first_name . ' ' . $assignUser->last_name,
                'new_response' => serialize($TaskServiceDeatils->toArray()),
                'ip' => $ipaddress,
            ];
            LogsService::save($insertLog);
            Session::flash('success', 'Task successfully updated');
            return redirect('/task-list');
        }
    }

    protected function sentAssignToUserMail($assignTo, $taskId)
    {

        $getUserDetails = User::getDetailsById($assignTo);
        $taskData = TaskService::getDetailsById($taskId);
        $getCreatedByDetails = User::getDetailsById($taskData['created_by']);

        $taskStatus = $taskData['task_status'];
        $record = $this->PatientService->getDetailById($taskData['patient_id']);
        $statuss = 'Ny Best Medical Care';
        $email = isset($getUserDetails->email) ? $getUserDetails->email : "";
        $createdEmailBy = $getCreatedByDetails->email ?: "";

        $recordfname = isset($record->first_name) ? $record->first_name : "";
        $recordlname = isset($record->last_name) ? $record->last_name : "";
        $patientFullName = $recordfname . ' ' . $recordlname;
        $emailrecord = isset($record->email) ? $record->email : "";
        $phone = '';
        $address1 = '';
        $address2 = '';
        if (isset($record->phone) && $record->phone != '') {
            $phone = $record->phone;
        }
        if (isset($record->address1) && $record->address1 != '') {
            $address1 = $record->address1;
        }
        if (isset($record->address2) && $record->address2 != '') {
            $address2 = $record->address2;
        }

        $subject = "Update Task Assigned";

        $emailData = array(
            'full_name' => $getUserDetails->full_name,
            'task_id' => $taskId,
            'task_name' => $taskData->task_name,
            'task_description' => $taskData->task_description,
            'email' => $email,
            'taskStatus' => $taskStatus,
            'due_date' => $taskData->due_date,
            'patient_id' => $taskData['patient_id'],
            'patient_full_name' => $patientFullName,
            'patient_email' => $emailrecord,
            'phone' => $phone,
            'address1' => $address1,
            'address2' => $address2,
        );
        $messages  = Utility::getHtmlContent('email_template.email_sent_assign_task', $emailData);

        try {
            Mail::mailer('second')->send([], [], function ($message) use ($email, $subject, $messages, $createdEmailBy) {
                $message->to($email, $createdEmailBy, "EMC Rep")
                    ->subject($subject)->html($messages);
            });
        } catch (\Throwable $th) {
            //throw $th;
        }
        return true;
    }

    public function destroy($id)
    {
        $auth = auth()->user();
        $record = $this->taskService->getDetailsById($id);
        $dataTask = array(

            'del_flag' => "Y",

        );
        $insert = TaskService::SoftDelete($dataTask, array('id' => $id));

        if ($insert) {

            $taskLog = [
                'hub_task_id' => $id,
                'created_by' => $auth->id,
                'description' => $auth->first_name . ' ' . $auth->last_name . ' is deleted task',
                'created_at' => date('Y-m-d H:i:s')
            ];
            $deleteTaskLog =  TaskLog::create($taskLog);
            $ipaddress = Utility::getIP();
            $insertLog = [
                'type' => 'Hub Task Deleted',
                'link' => url('hub-record/view/') . '/' . $record->hub_record_id ?? request()->hub_record_id,
                'module' => 'Hub Record',
                'object_id' => $record->hub_record_id ?? request()->hub_record_id,
                'message' => $auth->first_name . ' ' . $auth->last_name . ' is deleted task',
                'new_response' => serialize($record->toArray()),
                'ip' => $ipaddress,
            ];
            LogsService::save($insertLog);
            $user = $auth = auth()->user();
            $currentDate = date('Y-m-d', strtotime(now()));
            Cache::forget("task_list_user_{$user->id}");

            $ipaddress = Utility::getIP();
            if ($record->record_id != "") {
                $module = "Patient Appointment";
                $objectId = $record->record_id;
            } else {
                $module = "Task";
                $objectId = $id;
            }

            return 1;
        } else {
            return 0;
        }
    }

    function export(Request $request)
    {
        $auth = auth()->user();
        $data['auth'] = $auth;
        $data['task_name'] = $task_name = $request->input('task_name');
        $data['user_id'] = $user_id = $request->input('user_id');
        $data['created_user_id'] = $created_user_id = $request->input('created_user_id');
        $data['created_task_date'] = $created_task_date = $request->input('created_task_date');
        $data['task_due_date'] = $task_due_date = $request->input('task_due_date');
        $data['priority'] = $priority = $request->input('priority');
        $data['status'] = $status = $request->input('status');
        $data['pendingTask'] = $pendingTask = $request->input('pending_task');

        $query = TaskService::getList($task_name, $user_id, $created_user_id, $created_task_date, $task_due_date, $priority, $status, $pendingTask, $paginate = false);
        if (!empty($query[0])) {
            $filename = 'Task' . date("m-d-Y");
            $headers = array(
                "Content-type" => "text/csv",
                "Content-Disposition" => "attachment; filename=" . $filename . ".csv",
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0",
            );
            $columns = array('No', 'Task Name', 'Priority', 'Status', '#Record', 'Assign User', 'Start Date', 'Due Date', 'Created Date', 'Creted By', 'Is Over Due?');

            $callback = function () use ($query, $columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);
                $cnt = 1;
                foreach ($query as $record) {
                    $userfname = '';
                    $userlname = '';
                    if (isset($record->createdFname) && $record->createdFname != '') {
                        $userfname = $record->createdFname;
                    }
                    if (isset($record->createdLname) && $record->createdLname != '') {
                        $userlname = $record->createdLname;
                    }

                    $currentDate = date('Y-m-d H:i:s', strtotime(now()));
                    if ((strtotime($record->due_date) < strtotime($currentDate)) && $record->task_status == 'Pending') {
                        $overDue = 'Yes';
                    } else {
                        $overDue = '';
                    }
                    if ($record->record_id != "") {
                        $patient_id = $record->record_id;
                    } else {
                        $patient_id = '-';
                    }
                    $userFullName = $record->assignUser != null ? $record->assignUser->full_name : '';
                    $final = array($cnt++, $record->task_name, $record->priority, $record->task_status, $patient_id, $userFullName, $record->start_date, date('m/d/Y h:i A', strtotime($record->due_date)), date('m/d/Y h:i A', strtotime($record->created_date)), $userfname . ' ' . $userlname, $overDue);
                    fputcsv($file, $final);
                }
                fclose($file);
            };
            return response()->stream($callback, 200, $headers);
        } else {
            return response()->json(['error_msg' => "No data found", 'status' => 0, 'data' => array()], 200);
        }
    }

    function changeStatus(Request $request)
    {
        $auth = auth()->user();

        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'status' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
        } else {
            $oldResponse = TaskService::getDetailsById($request->input('id'));
            $update = TaskService::update(array('task_status' => $request->input('status')), array('id' => $request->input('id')));
            $newResponse = TaskService::getDetailsById($request->input('id'));
            $data_array = array(
                'task_id' => $request->input('id'),
                'status' => $request->input('status'),
                'notes' => $request->input('task_description'),
            );
            $update = TaskStatusLogService::save($data_array);
            $taskLog = [
                'hub_task_id' => $request->input('id'),
                'created_by' => $auth->id,
                'description' => $auth->first_name . ' ' . $auth->last_name . ' is change status and status is ' . $request->input('status'),
                'created_at' => date('Y-m-d H:i:s'),
                'old_response' => serialize($oldResponse),
                'new_response' => serialize($newResponse)
            ];
            TaskLog::create($taskLog);
            $ipaddress = Utility::getIP();
            $insertLog = [
                'type' => 'Hub Task Status Changed',
                'link' => url('hub-record/view/') . '/' . $oldResponse->record_id,
                'module' => 'Hub Record',
                'object_id' => $oldResponse->record_id,
                'message' => $auth->first_name . ' ' . $auth->last_name . ' is change status and status is ' . $request->input('status'),
                'new_response' => serialize($newResponse->toArray()),
                'ip' => $ipaddress,
            ];
            LogsService::save($insertLog);
            // Store notification data for show
            if (isset($request->recordId) && !empty($request->recordId)) {
                $record_id = $request->recordId;
                $msg = $newResponse->task_name . ' <br/> <b>Status</b>: ' . $request->input('status');
            } else {
                $msg = $newResponse->task_name . ' | <b>Status</b>: ' . $request->input('status');
            }
            // Store notification data for show
            $patientData = $this->PatientService->getPatientDetailsByIdWhitoutAgency($request->recordId);

            Cache::forget("task_list_user_{$auth->id}");
            $users = [$newResponse->assign_id];
            if (isset($patientData->agency_id) && !empty($patientData->agency_id)) {
                $users = Utility::getGroupUsersData($patientData->agency_id, $patientData->type, 'Task', $users);
            }
            foreach ($users as $user) {
                // Store notification data for show
                $notificationData = array(
                    'type' => 'Task',
                    'user_id' => $user,
                    'record_id' => $record_id ?? NULL,
                    'title' => 'Task status changed',
                    'message' => $msg,
                );
                // $this->notificationUserService->save($notificationData);
            }

            return response()->json(['error_msg' => "Status successfully changed", 'status' => 1, 'data' => array('status' => $request->input('status'))], 200);
        }
    }
    public function show($id)
    {
        $auth = auth()->user();
        if (empty($auth)) {
            return redirect('/login');
        } else {
            $data['task_details'] = $task_details = TaskService::getDetailsByIdNew($id);
            $data['notes_details'] = TaskStatusLogService::getDetails($id);
            $data['user_list'] = User::getHospitalUser();
            return view('task.task_view', $data);
        }
    }

    function HubTaskAdd(Request $request)
    {
        $user = $auth = auth()->user();

        $validator = Validator::make($request->all(), [
            'task_name' => 'required',
            'assign_to' => 'required',
            'patient_id' => 'required',
            'priority' => 'required',


        ]);
        if ($validator->fails()) {
            return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 200);
        } else {
            $dataTask = array(
                'user_id' => $auth['id'],
                'task_name' => $request->input('task_name'),
                'assign_id' => $request->input('assign_to'),
                'record_id' => $request->input('patient_id'),
                'task_description' => $request->input('task_description'),
                'priority' => $request->input('priority'),
                'due_date' => date('Y-m-d H:i:s', strtotime($request->input('due_date'))),
                'start_date' => date('Y-m-d', strtotime($request->input('start_date')))
            );

            $insert = TaskService::save($dataTask);

            if ($insert) {
                $serializedDataTask = serialize($dataTask);
                $assignUser = User::find($request->input('assign_to'));
                $taskLog = [
                    'hub_task_id' => $insert,
                    'created_by' => $user->id,
                    'description' => $user->first_name . ' ' . $user->last_name . ' is created new task and assign to ' . $assignUser->first_name . ' ' . $assignUser->last_name,

                    'new_response' =>  $serializedDataTask,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $insertTaskLog = TaskLog::create($taskLog);

                $ipaddress = Utility::getIP();
                $insertLog = [
                    'type' => 'Hub Task Created',
                    'link' => url('hub-record/view/') . '/' . $request->patient_id,
                    'module' => 'Hub Record',
                    'object_id' => $request->patient_id,
                    'message' =>  $user->first_name . ' ' . $user->last_name . ' is created new task and assign to ' . $assignUser->first_name . ' ' . $assignUser->last_name,
                    'new_response' => $serializedDataTask,
                    'ip' => $ipaddress,
                ];
                LogsService::save($insertLog);

                $record = $this->PatientService->getDetailById($request->input('patient_id'));
                $currentDate = date('Y-m-d', strtotime(now()));
                Cache::forget("task_list_user_{$user->id}");
                $statuss = 'Ny Best Medical Care';
                if (isset($record->record_id) && $record->record_id != '') {
                    $statuss = 'NY Best Medicalss';
                }
                $getUserDetails = User::getDetailsById($request->input('assign_to'));
                $allemails = isset($getUserDetails->email) ? $getUserDetails->email : "";
                $subject = "New Record Assigned";
                $fname = '';
                $lname = '';
                if (isset($getUserDetails->first_name) && $getUserDetails->first_name != '') {
                    $fname = $getUserDetails->first_name;
                }
                if (isset($getUserDetails->last_name) && $getUserDetails->last_name != '') {
                    $lname = $getUserDetails->last_name;
                }

                $recordfname = isset($record->first_name) ? $record->first_name : "";
                $recordlname = isset($record->last_name) ? $record->last_name : "";
                $patientFullName = $recordfname . ' ' . $recordlname;
                $emailrecord = isset($record->email) ? $record->email : "";
                $phone = '';
                $address1 = '';
                $address2 = '';
                $city = '';
                $state = '';
                $zip = '';
                if (isset($record->phone) && $record->phone != '') {
                    $phone = $record->phone;
                }
                if (isset($record->address1) && $record->address1 != '') {
                    $address1 = $record->address1;
                }
                if (isset($record->address2) && $record->address2 != '') {
                    $address2 = $record->address2;
                }

                $emailData = array(
                    'full_name' => $fname . ' ' . $lname,
                    'task_id' => $insert,
                    'patient_id' => $request->input('patient_id'),
                    'patient_full_name' => $patientFullName,
                    'patient_email' => $emailrecord,
                    'phone' => $phone,
                    'address1' => $address1,
                    'address2' => $address2,
                    'task_status' => 'Pending',
                );
                /*  $messages  = Utility::getHtmlContent('email_template.email_patient_task_add', $emailData);

                try {

                    $mail = Mail::mailer('second')->send([], [], function ($message) use ($allemails, $subject, $messages) {
                        $message->to($allemails, "EMC Rep")
                            ->subject($subject)->html($messages);
                    });
                } catch (\Throwable $th) {
                } */

                // Get Group wise notification
                $users = [$request->input('assign_to')];
                $patientData = $this->PatientService->getPatientDetailsByIdWhitoutAgency($request->input('patient_id'));
                if (isset($patientData->agency_id) && !empty($patientData->agency_id)) {
                    $users = Utility::getGroupUsersData($patientData->agency_id, $patientData->type, 'Task', $users);
                }
                foreach ($users as $user) {
                    // Store notification data for show
                    $notificationData = array(
                        'type' => 'Task',
                        'user_id' => $user,
                        'record_id' => $request->input('patient_id'),
                        'title' => 'New Task Assigned',
                        'message' => $request->input('task_name') . ' <br/> <b> Assigned To</b>: ' . $fname . ' ' . $lname,
                    );

                    // $this->notificationUserService->save($notificationData);
                }
                return response()->json(['error_msg' => 'Task added successfully.', 'status' => 1, 'data' => array()], 200);
            } else {
                return response()->json(['error_msg' => "Sorry, something went wrong. Please try again.", 'status' => 0, 'data' => array()], 200);
            }
        }
    }

    function HubTaskList(Request $request)
    {
        $record_id = $request->id;

        $data['query'] = TaskService::getTaskByRecordIdArray($record_id);
        if (!empty($data['query'])) {
            foreach ($data['query'] as $va) {
                $userDetails = User::getDetailsById($va->assign_id);
                $fname = '';
                $lname = '';
                if (isset($userDetails->first_name) && $userDetails->first_name != '') {
                    $fname = $userDetails->first_name;
                }
                if (isset($userDetails->last_name) && $userDetails->last_name != '') {
                    $lname = $userDetails->last_name;
                }
                $va->user_name = $fname . ' ' . $lname;


                $createdDetails = User::getDetailsById($va->user_id);
                $userfname = '';
                $userlname = '';
                if (isset($createdDetails->first_name) && $createdDetails->first_name != '') {
                    $userfname = $createdDetails->first_name;
                }
                if (isset($createdDetails->last_name) && $createdDetails->last_name != '') {
                    $userlname = $createdDetails->last_name;
                }
                $va->created_by = $userfname . ' ' . $userlname;
                $currentDate = date('Y-m-d H:i:s', strtotime(now()));
                if ((strtotime($va->due_date) < strtotime($currentDate)) && $va->task_status == 'Pending') {
                    $va->task_label = 'Over due';
                } else {
                    $va->task_label = '';
                }
            }
        }
        return view('task.hub_task', $data);
    }
    public function mytesk(Request $request)
    {
        $data['auth'] = $auth = auth()->user();
        $data['task_name'] = $task_name = $request->input('task_name');
        $data['status'] = $status = $request->input('status');
        $data['created_date'] = $created_date = $request->input('created_date');
        $data['query'] = TaskService::getAssignTaskList($auth['id'], $task_name, $status, $created_date);
        return view('task.mytask', $data);
    }
    function MyTaskExport(Request $request)
    {
        $data['auth'] = $auth = auth()->user();
        $data['task_name'] = $task_name = $request->input('task_name');
        $data['created_date'] = $created_date = $request->input('created_date');
        $data['status'] = $status = $request->input('status');


        $query = TaskService::getAssignTaskListExport($auth['id'], $task_name, $status, $created_date);

        if (!empty($query[0])) {
            foreach ($query as $va) {
                $userDetails = User::getDetailsById($va->created_by);
                $fname = '';
                $lname = '';
                if (isset($userDetails->first_name) && $userDetails->first_name != '') {
                    $fname = $userDetails->first_name;
                }
                if (isset($userDetails->last_name) && $userDetails->last_name != '') {
                    $lname = $userDetails->last_name;
                }
                $va->user_name = $fname . ' ' . $lname;
            }
            $filename = 'Task' . date("m-d-Y");
            $headers = array(
                "Content-type" => "text/csv",
                "Content-Disposition" => "attachment; filename=" . $filename . ".csv",
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0",
            );
            //$columns = array('First Name','Middle Name','last Name','Email','Phone','Address1','Address2','State','City','Zip Code','Billing Email','Bill Date','Monthly Bill');
            $columns = array('No', 'Task Name', 'Status', 'Created Date', 'Created By');

            $callback = function () use ($query, $columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);
                $cnt = 1;
                foreach ($query as $record) {

                    $final = array($cnt++, $record->task_name, $record->task_status, date('m/d/Y h:i A', strtotime($record->created_date)), $record->user_name);
                    fputcsv($file, $final);
                }
                fclose($file);
            };
            return response()->stream($callback, 200, $headers);
        } else {
            return response()->json(['error_msg' => "No data found", 'status' => 0, 'data' => array()], 200);
        }
    }

    public function PatientTaskLogList(Request $request)
    {
        $query = TaskLog::getTaskLoglistByTaskId($request->id);
        return view('task.task_log', compact('query'));
    }

    public function hubRecordTaskTimeLogList(Request $request)
    {
        $query = TaskTimer::getTaskTimerlistByTaskId($request->id);
        return view('task.task_time_log', compact('query'));
    }
    public function taskListPageAjax(Request $request)
    {
        $auth = auth()->user();
        $data['auth'] = $auth;
        $data['task_name'] = $task_name = $request->input('task_name');
        $data['user_id'] = $user_id = $request->input('user_id');
        $data['created_user_id'] = $created_user_id = $request->input('created_user_id');
        $data['created_task_date'] = $created_task_date = $request->input('created_task_date');
        $data['task_due_date'] = $task_due_date = $request->input('task_due_date');
        $data['priority'] = $priority = $request->input('priority');
        $data['status'] = $status = $request->input('status');
        $data['pendingTask'] = $pendingTask = $request->input('pending_task');

        $query = TaskService::getList($task_name, $user_id, $created_user_id, $created_task_date, $task_due_date, $priority, $status, $pendingTask, $paginate = true);

        if (!empty($query[0])) {
            foreach ($query as $va) {
                $userfname = '';
                $userlname = '';
                if (isset($va->createdFname) && $va->createdFname != '') {
                    $userfname = $va->createdFname;
                }
                if (isset($va->createdLname) && $va->createdLname != '') {
                    $userlname = $va->createdLname;
                }
                $va->created_by = $userfname . ' ' . $userlname;


                $va->user_name = $va->assignUser != null  ? $va->assignUser->full_name : '';

                $va->created_by = $userfname . ' ' . $userlname;
                $currentDate = date('Y-m-d H:i:s');
                if ((strtotime($va->due_date) < strtotime($currentDate)) && $va->task_status == 'Pending') {
                    $va->task_label = 'Over due';
                } else {
                    $va->task_label = '';
                }
            }
        }
        $data['query'] = $query;
        return view('hubRecord.task_list_ajax', compact('data', 'query'));
    }
    public function PatientTaskClockInOut(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'auth_id' => 'required',
        ]);
        $getTaskDetailsById = Task::where('id', $request->id)->first();
        if ($validator->fails()) {
            return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 200);
        } else {
            $checkInOutArray = [];
            if ($request->type == 'clock_in') {
                $time = date('Y-m-d H:i:s');
                $checkInTimeArray = [
                    'hub_task_id' =>  $request->id,
                    'start_date_time' => $time,
                    'created_by' => auth()->user()->id,
                    'updated_at' => NULL,
                ];
                // task timer table store
                $storeTime = TaskService::timeStore($checkInTimeArray);
                //task master table store
                $checkInOutArray = ['clock_in' => $time];
            } else {
                $time = date('Y-m-d H:i:s');
                $lastRecord = TaskTimer::where('hub_task_id', $request->id)->where('end_date_time', NULL)->first();
                $timeDuration = gmdate('H:i:s', Carbon::createFromFormat('Y-m-d H:i:s', $lastRecord->start_date_time)->diffInSeconds(Carbon::createFromFormat('Y-m-d H:i:s', $time)));
                $checkOutTimeArray = [
                    'end_date_time' => $time,
                    'time_duration' => $timeDuration,
                    'updated_by' => auth()->user()->id,
                ];
                // task timer table store
                $storeTime = TaskService::updateTime($checkOutTimeArray, ['id' => $lastRecord->id]);

                if (isset($getTaskDetailsById) && $getTaskDetailsById->task_hour != NULL) {
                    $duration1 = $getTaskDetailsById->task_hour;
                    $duration2 = $timeDuration;
                    $time1 = Carbon::createFromFormat('H:i:s', $duration1);
                    $time2 = Carbon::createFromFormat('H:i:s', $duration2);
                    $sumInterval = $time1->addHours($time2->hour)->addMinutes($time2->minute)->addSeconds($time2->second);

                    $taskHour = $sumInterval->format('H:i:s');
                } else {
                    $taskHour = $timeDuration;
                }

                $checkInOutArray = ['clock_out' => $time, 'task_hour' => $taskHour];
            }

            //task master table store
            $query = TaskService::update($checkInOutArray, ['id' => $request->id]);
            $type = "Clock Out";
            if ($request->type == "clock_in") {
                $type = "Clock In";
            }

            if ($query) {
                $taskLog = [
                    'hub_task_id' => $request->id,
                    'created_by' => auth()->user()->id,
                    'description' => auth()->user()->first_name . ' ' . auth()->user()->last_name . ' is ' . $type . ' at ' . $time,
                    'old_response' =>  "",
                    'new_response' =>  "",
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $insertTaskLog = TaskLog::create($taskLog);
                $ipaddress = Utility::getIP();
                $insertLog = [
                    'type' => 'Hub Task ' . $type,
                    'link' => url('hub-record/view/') . '/' . $getTaskDetailsById->record_id ?? "",
                    'module' => 'Hub Record',
                    'object_id' => $getTaskDetailsById->record_id ?? "",
                    'message' => auth()->user()->first_name . ' ' . auth()->user()->last_name . ' is ' . $type . ' at ' . $time,
                    'new_response' => serialize($checkInOutArray),
                    'ip' => $ipaddress,
                ];
                LogsService::save($insertLog);
            }

            $data = TaskService::getDetailsById($request->id);
            $data['type'] = $request->type;

            if ($request->type == "clock_in") {
                $msg = 'Clock In Succesfully';
            } else {
                $msg = 'Clock Out Succesfully';
            }
            return response()->json(['error_msg' => $msg, 'status' => 1, 'data' => $data]);
        }
    }

    public function taskAssignToUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'task_id' => 'required',
            'assignUserId' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
        } else {

            $update = TaskService::update(array('assign_id' => $request->assignUserId), array('id' => $request->task_id));
            $taskDetail = $this->taskService->getDetailsById($request->task_id);
            $name = $request->selectedText;

            $user = $auth = auth()->user();
            $currentDate = date('Y-m-d', strtotime(now()));
            Cache::forget("task_list_user_{$user->id}");
            $getUserDetails = User::getDetailsById($request->assignUserId);
            if (isset($getUserDetails->first_name) && $getUserDetails->first_name != '') {
                $fname = $getUserDetails->first_name;
            }
            if (isset($getUserDetails->last_name) && $getUserDetails->last_name != '') {
                $lname = $getUserDetails->last_name;
            }

            if (isset($request->patient_id) && !empty($request->patient_id)) {
                $record_id = $request->patient_id;
                $msg = $taskDetail->task_name . ' <br/> <b> Assigned To</b>: ' . $fname . ' ' . $lname;
            } else {
                $msg = $taskDetail->task_name . ' | <b> Assigned To</b>: ' . $fname . ' ' . $lname;
            }
            $users = [$request->assignUserId];
            $patientData = $this->PatientService->getPatientDetailsByIdWhitoutAgency($taskDetail->record_id);
            if (isset($patientData->agency_id) && !empty($patientData->agency_id)) {
                $users = Utility::getGroupUsersData($patientData->agency_id, $patientData->type, 'Task', $users);
            }
            foreach ($users as $user) {
                // Store notification data for show
                $notificationData = array(
                    'type' => 'Task',
                    'user_id' => $user,
                    'title' => 'Task Assignee Updated',
                    'message' => $msg,
                );
                //  $this->notificationUserService->save($notificationData);
            }
            $updateTaskDetails = $this->taskService->getDetailsById($request->task_id);
            $taskLog = [
                'hub_task_id' => $request->task_id,
                'created_by' => $auth->id,
                'description' => $auth->first_name . ' ' . $auth->last_name . ' is updated assignee and assign to ' . $fname . ' ' . $lname,
                'old_response' =>  serialize($taskDetail),
                'new_response' =>  serialize($updateTaskDetails),
                'created_at' => date('Y-m-d H:i:s'),

            ];
            TaskLog::create($taskLog);
            $ipaddress = Utility::getIP();
            $insertLog = [
                'type' => 'Hub Task Assignee Updated',
                'link' => url('hub-record/view/') . '/' . $updateTaskDetails->record_id,
                'module' => 'Hub Record',
                'object_id' => $updateTaskDetails->record_id,
                'message' => $auth->first_name . ' ' . $auth->last_name . ' is updated assignee and assign to ' . $fname . ' ' . $lname,
                'new_response' => serialize($updateTaskDetails->toArray()),
                'ip' => $ipaddress,
            ];
            LogsService::save($insertLog);
            return response()->json(['error_msg' => "User Assign successfully", 'status' => 1, 'data' => $name], 200);
        }
    }

    public function taskCommentSave(Request $request)
    {
        $auth = auth()->user();
        $validator = Validator::make($request->all(), [
            'task_id' => 'required',
            'comment' => 'required',
        ]);
        $TaskServiceDeatils = TaskService::getDetailsById($request->task_id);
        if ($validator->fails()) {
            return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
        } else {
            $commentSave = [
                'task_id' => $request->task_id,
                'user_id' => auth()->user()->id,
                'comment' => $request->comment,
            ];
            $saveCommentData = $this->taskCommentService->save($commentSave);
            $getDetails = $this->taskCommentService->getDetailsByCommentId($saveCommentData);
            $updateTaskDetails = TaskService::getDetailsById($request->task_id);
            $taskLog = [
                'hub_task_id' => $request->task_id,
                'created_by' => $auth->id,
                'description' => 'Comment added by ' . $auth->first_name . ' ' . $auth->last_name,
                'old_response' =>  serialize($TaskServiceDeatils),
                'new_response' =>  serialize($updateTaskDetails),
                'created_at' => date('Y-m-d H:i:s'),

            ];
            TaskLog::create($taskLog);
            $ipaddress = Utility::getIP();
            $insertLog = [
                'type' => 'Hub Task Comment Added',
                'link' => url('hub-record/view/') . '/' . $updateTaskDetails->record_id,
                'module' => 'Hub Record',
                'object_id' => $updateTaskDetails->record_id,
                'message' => 'Comment added by ' . $auth->first_name . ' ' . $auth->last_name,
                'new_response' => serialize($updateTaskDetails->toArray()),
                'ip' => $ipaddress,
            ];
            LogsService::save($insertLog);
            return response()->json(['error_msg' => "Comment successfully store", 'status' => 1, 'data' => $getDetails], 200);
        }
    }

    public function taskCommentList(Request $request)
    {
        $commentListGet =  $this->taskCommentService->getCommentListByTaskId($request->task_id);

        if ($commentListGet) {
            return response()->json(['error_msg' => "", 'status' => 1, 'data' => $commentListGet], 200);
        } else {
            return response()->json(['error_msg' => "", 'status' => 0, 'data' => ""], 200);
        }
    }

    function TaskListAjax(Request $request)
    {
        $recordId = $request->input('id');
        $auth = auth()->user();
        $newResponse = TaskService::getDetailsByIdNew($recordId);
        $clockInFlag = 0;
        $clockinOut = 0;
        if ($auth->id == $newResponse->assign_id) {

            $getLastTime = TaskTimer::whereNotNull('start_date_time')->whereNull('end_date_time')->where('hub_task_id', $request->id)->orderBy('id', 'desc')->first();
            if (isset($getLastTime->id)) {
                $clockinOut = 1;
            }
            $clockInFlag = 1;
        }

        $newResponse->clockInFlag = $clockInFlag;
        $newResponse->clockinOut = $clockinOut;
        return response()->json(['error_msg' => "Status successfully changed", 'status' => 1, 'data' => $newResponse], 200);
    }

    function TaskDiscriptionUpdate(Request $request)
    {
        $auth = auth()->user();
        $TaskServiceDeatils = TaskService::getDetailsById($request->input('id'));
        $taskDiscription = $request->input('task_description');
        $dataTask = array(
            'task_description' => $taskDiscription,
        );

        TaskService::update($dataTask, array('id' => $request->input('id')));
        $updateTaskDetails = TaskService::getDetailsByIdNew($request->input('id'));
        $taskLog = [
            'hub_task_id' => $request->input('id'),
            'created_by' => $auth->id,
            'description' => $auth->first_name . ' ' . $auth->last_name . ' is updated task description',
            'old_response' =>  serialize($TaskServiceDeatils),
            'new_response' =>  serialize($updateTaskDetails),
            'created_at' => date('Y-m-d H:i:s'),

        ];
        $insertTaskLog = TaskLog::create($taskLog);
        $ipaddress = Utility::getIP();
        $insertLog = [
            'type' => 'Hub Task Description Updated',
            'link' => url('hub-record/view/') . '/' . $updateTaskDetails->record_id,
            'module' => 'Hub Record',
            'object_id' => $updateTaskDetails->record_id,
            'message' => $auth->first_name . ' ' . $auth->last_name . ' is updated task description',
            'new_response' => serialize($updateTaskDetails->toArray()),
            'ip' => $ipaddress,
        ];
        LogsService::save($insertLog);
        return response()->json(['error_msg' => "Description successfully updated", 'status' => 1, 'tasklogdata' =>  $insertTaskLog, 'TaskDetails' => $updateTaskDetails], 200);
    }
    public function ActivityLogList(Request $request)
    {
        $query = TaskLog::getActivityLoglistByTaskId($request->id);
        return response()->json(['error_msg' => "Activity Log get successfully", 'status' => 1, 'tasklogdata' =>  $query], 200);
    }


    function myDueTask(Request $request)
    {
        $query = $this->taskService->getMyDueTask();
        return response()->json(['success' => true, 'data' => $query]);
    }

    public function taskDetails(Request $request)
    {
        $auth = auth()->user();
        $query = $this->taskService->getDetailsByIdNew($request->id);

        $clockInFlag = 0;
        $clockinOut = 0;
        if ($auth->id == $query->assign_id) {

            $getLastTime = TaskTimer::whereNotNull('start_date_time')->whereNull('end_date_time')->where('hub_task_id', $request->id)->orderBy('id', 'desc')->first();
            if (isset($getLastTime->id)) {
                $clockinOut = 1;
            }
            $clockInFlag = 1;
        }

        $query->clockInFlag = $clockInFlag;
        $query->clockinOut = $clockinOut;
        return response()->json(['success' => true, 'data' => $query]);
    }

    function taskDueDateUpdate(Request $request)
    {
        $auth = auth()->user();
        $TaskServiceDeatils = TaskService::getDetailsById($request->input('id'));
        $dueDate = $request->input('due_date');
        $dataTask = array(
            'due_date' =>  date('Y-m-d H:i:s', strtotime($dueDate)),
        );
        TaskService::update($dataTask, array('id' => $request->input('id')));
        $updateTaskDetails = TaskService::getDetailsByIdNew($request->input('id'));
        $currentDate = date('Y-m-d', strtotime(now()));
        Cache::forget("task_list_user_{$auth->id}");
        $taskLog = [
            'hub_task_id' => $request->input('id'),
            'created_by' => $auth->id,
            'description' => $auth->first_name . ' ' . $auth->last_name . ' is updated task due date',
            'old_response' =>  serialize($TaskServiceDeatils),
            'new_response' =>  serialize($updateTaskDetails),
            'created_at' => date('Y-m-d H:i:s'),

        ];
        $insertTaskLog = TaskLog::create($taskLog);
        $ipaddress = Utility::getIP();
        $insertLog = [
            'type' => 'Hub Task Due Date Updated',
            'link' => url('hub-record/view/') . '/' . $updateTaskDetails->record_id,
            'module' => 'Hub Record',
            'object_id' => $updateTaskDetails->record_id,
            'message' => $auth->first_name . ' ' . $auth->last_name . ' is updated task due date',
            'new_response' => serialize($updateTaskDetails->toArray()),
            'ip' => $ipaddress,
        ];
        LogsService::save($insertLog);
        return response()->json(['error_msg' => "Due Date successfully updated", 'status' => 1, 'tasklogdata' =>  $insertTaskLog, 'TaskDetails' => $updateTaskDetails], 200);
    }

    function taskTitleUpdate(Request $request)
    {
        $auth = auth()->user();
        $TaskServiceDeatils = TaskService::getDetailsById($request->input('id'));
        $task_name = $request->input('task_name');
        $dataTask = array(
            'task_name' =>  $task_name,
        );
        TaskService::update($dataTask, array('id' => $request->input('id')));
        $updateTaskDetails = TaskService::getDetailsByIdNew($request->input('id'));
        $taskLog = [
            'hub_task_id' => $request->input('id'),
            'created_by' => $auth->id,
            'description' => $auth->first_name . ' ' . $auth->last_name . ' is updated task name',
            'old_response' =>  serialize($TaskServiceDeatils),
            'new_response' =>  serialize($updateTaskDetails),
            'created_at' => date('Y-m-d H:i:s'),

        ];
        $insertTaskLog = TaskLog::create($taskLog);
        $ipaddress = Utility::getIP();
        $insertLog = [
            'type' => 'Hub Task Title Updated',
            'link' => url('hub-record/view/') . '/' . $updateTaskDetails->record_id,
            'module' => 'Hub Record',
            'object_id' => $updateTaskDetails->record_id,
            'message' => $auth->first_name . ' ' . $auth->last_name . ' is updated task name',
            'new_response' => serialize($updateTaskDetails->toArray()),
            'ip' => $ipaddress,
        ];
        LogsService::save($insertLog);
        return response()->json(['error_msg' => "Task name successfully updated", 'status' => 1, 'tasklogdata' =>  $insertTaskLog, 'TaskDetails' => $updateTaskDetails], 200);
    }

    function taskPriorityUpdate(Request $request)
    {
        $auth = auth()->user();
        $TaskServiceDeatils = TaskService::getDetailsById($request->input('id'));
        $priority = $request->input('priority');
        $dataTask = array(
            'priority' =>  $priority,
        );
        TaskService::update($dataTask, array('id' => $request->input('id')));
        $updateTaskDetails = TaskService::getDetailsByIdNew($request->input('id'));
        $taskLog = [
            'hub_task_id' => $request->input('id'),
            'created_by' => $auth->id,
            'description' => $auth->first_name . ' ' . $auth->last_name . ' is updated task priority',
            'old_response' =>  serialize($TaskServiceDeatils),
            'new_response' =>  serialize($updateTaskDetails),
            'created_at' => date('Y-m-d H:i:s'),
        ];
        $insertTaskLog = TaskLog::create($taskLog);
        $ipaddress = Utility::getIP();
        $insertLog = [
            'type' => 'Hub Task Priority Updated',
            'link' => url('hub-record/view/') . '/' . $updateTaskDetails->record_id,
            'module' => 'Hub Record',
            'object_id' => $updateTaskDetails->record_id,
            'message' => $auth->first_name . ' ' . $auth->last_name . ' is updated task priority',
            'new_response' => serialize($updateTaskDetails->toArray()),
            'ip' => $ipaddress,
        ];
        LogsService::save($insertLog);
        return response()->json(['error_msg' => "Task Priority successfully updated", 'status' => 1, 'TaskDetails' => $updateTaskDetails], 200);
    }
}
