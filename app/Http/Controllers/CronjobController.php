<?php

namespace App\Http\Controllers;

use App\Services\PatientService;
use App\Services\TaskService;
use App\User;
use Illuminate\Support\Facades\Mail;
use App\Helpers\Utility;
class CronjobController extends Controller
{
    protected $taskService,$PatientService="";
    public function __construct(TaskService $taskService,PatientService $PatientService)
    {
        $this->taskService = $taskService;
        $this->PatientService = $PatientService;
    }

    public function sentMailOutstandingTask(){
        $outstandingTask = $this->taskService->getOutstandingTaskList();
        if(count($outstandingTask)>0){
            foreach($outstandingTask as $outstanding){
                $this->sentAssignToUserMail($outstanding->assign_id,$outstanding->id);
            }
        }
    }

    protected function sentAssignToUserMail($assignTo,$taskId){
       
        $getUserDetails = User::getDetailsById($assignTo);
        $taskData = TaskService::getDetailsById($taskId);

        $taskStatus = $taskData['task_status'];
        $record = $this->PatientService->getDetailById($taskData['patient_id']);
        $statuss = 'Ny Best Medical Care';
        $email = $getUserDetails->email ?? "";

        $recordfname = $record->first_name ?? "";
        $recordlname = $record->last_name ?? "";
        $patientFullName = $recordfname . ' ' . $recordlname;
        $emailrecord = $record->email ?? "";
        $phone = $record->phone ?? "";
        $address1 = $record->address1 ?? "";
        $address2 = $record->address2 ?? "";
        

        $subject = "Update Task Assigned";
    
        // $messages = 'Hello ' . $getUserDetails->full_name . ',<br>';
        // $messages .= 'Below new record has been assigned to you.<br>';
        // $messages .= 'Task Information.<br>';
        // $messages .= 'Task Id :' . $taskId . '<br>';
        // $messages .= 'Task Name :' . $taskData->task_name . '<br>';
        // $messages .= 'Task Description :' . $taskData->task_description . '<br>';
        // $messages .= 'Name : ' . $getUserDetails->full_name . '<br>';
        // $messages .= 'Email : ' . $email . '<br>';
        // $messages .= 'Task Status : '.$taskStatus.'<br>';
        // $messages .= 'Task Due Date : '.$taskData->due_date.'<br>'; 

        // $messages .= 'Record Information.<br>';

        // $messages .= 'Record Id :' . $taskData['patient_id'] . '<br>';
        // $messages .= 'Name : ' . $patientFullName . '<br>';
        // $messages .= 'Email : ' . $emailrecord . '<br>';
        // $messages .= 'Phone : ' . $phone . '<br>';
        // $messages .= 'Address : ' . $address1 . "," . $address2 . '<br>';
        // $messages .= 'Record From : ' . $statuss . '<br>';
        // $messages .= 'Thank you!';

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
        $messages  = Utility::getHtmlContent('email_template.email_sent_assign_task',$emailData);
        

          try {
            Mail::mailer('second')->send([], [], function ($message) use ($email, $subject, $messages) {
                $message->to($email, "EMC Rep")
                    ->subject($subject)->html($messages);
            });
          } catch (\Throwable $th) {
            //throw $th;
          }
        return true;
        
    }
}
