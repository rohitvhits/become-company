<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use App\Services\FeedbackQuestionFormService;
use App\Services\FeedbackAnswerFormService;
use App\Services\PatientServicesRequest;
use App\Services\PatientService;
use Illuminate\Http\Request;
use App\Master;
use Illuminate\Support\Facades\Cache;

class ReviewFeedbackFormController extends BaseController{
    protected $feedbackQuestionFormService,$feedbackAnswerFormService,$patientServicesRequest,$patientService,$locationMasterService,$locationScheduleService = "";
    public function __construct(FeedbackQuestionFormService $feedbackQuestionFormService,FeedbackAnswerFormService $feedbackAnswerFormService, PatientServicesRequest $patientServicesRequest, PatientService $patientService)
    { 
        $this->feedbackQuestionFormService = $feedbackQuestionFormService;
        $this->feedbackAnswerFormService = $feedbackAnswerFormService;
        $this->patientServicesRequest = $patientServicesRequest;
        $this->patientService = $patientService;
    }

    public function index($id){
        $serviceDetails = $this->patientServicesRequest->getByPatientDetailsWithSHA1($id);
        if(isset($serviceDetails->patient_id)){
            $data['service_request_id'] = $serviceDetails->id;
            $patientId = $serviceDetails->patient_id;
        
            $data['patientDetails'] = $this->patientService->getDetailByIdWithoutLogin($patientId);
            if (isset($data['patientDetails']->service_id) && $data['patientDetails']->service_id != '') {
                $explode = explode(',', $data['patientDetails']->service_id);
                $finalArray = [];
                foreach ($explode   as  $val) {
                    if ($val    != "") {
                        $finalArray[] = $val;
                    }
                }
    
                $services = Master::whereRaw('id IN (' . implode(',', $finalArray) . ')')->where('del_flag', 'N')->get();
    
                $newass  = array();
                foreach ($services as $kke) {
                    $newass[] = $kke->name;
                }
    
                if (!empty($newass)) {
                    $data['patientDetails']->service = implode(',', $newass);
                }
            }
            // Get Patient details 
            $data['details'] = $this->feedbackQuestionFormService->getAllQuestions();      
            return view('feedBackForm.index',$data);
        }else{
            abort(404);
        }
        
    }

    public function submitFeedback(Request $request){
        $data = $request->all();
        if(!empty($request->service_request_id)){
            $serviceDetails = $this->patientServicesRequest->getByPatientDetails($request->service_request_id);
        }
        $insert = array(
            'answer_response' => json_encode($data['question'])??'',
            'service_id' => $request->service_request_id??0,
            'patient_id' => $serviceDetails->patient_id??0,
            'ip_address' => $this->getUserIP()
        );            
        $this->feedbackAnswerFormService->save($insert);
        return redirect('/feedback-thank-you');
    }

    private function getUserIP()
    {
        // Get real visitor IP behind CloudFlare network
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
            $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
            $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        }
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = $_SERVER['REMOTE_ADDR'];

        if (filter_var($client, FILTER_VALIDATE_IP)) {
            $ip = $client;
        } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
            $ip = $forward;
        } else {
            $ip = $remote;
        }

        return $ip;
    }
}