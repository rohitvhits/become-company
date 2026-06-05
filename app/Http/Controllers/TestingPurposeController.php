<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Helpers\Utility;
use App\Agency;
use App\Model\HHACaregivers;
use Illuminate\Support\Facades\Validator;
use App\Services\PatientService;
use App\Services\RecordReportService;
use App\Services\LocationMasterService;
use App\Services\DocumentPatientService;
use App\Services\DoctorService;
use App\Services\LocationScheduleService;
use App\Services\PatientSMSLogService;
use App\Services\NyBestReminderNotificationService;
use App\Services\AssignNyBestUserService;
use App\Services\RequestService;
use App\Master;
use App\Record;
use App\Model\Patient;
use App\Model\PatientNotes;
use App\Model\HhaAppointment;
use App\Model\Language;
use App\User;

use URL;
use App\Services\AppointmentImportFileService;
use App\Services\CommonLogService;
use App\Services\PatientNotesService;
use Maatwebsite\Excel\Facades\Excel;
use Mail;
use App\Helpers\EsignHelper;
use App\Services\PatientDocumentSentReportService;
use App\Template;
use App\DocumentSignerMaster;
use App\Helpers\Common;
use App\Helpers\HHAAppointmentHelper;
use App\Helpers\HHACaregiversHelper;
use App\Model\Appointment;
use App\Model\AssignEMCRecord;
use App\Model\ScheduleAppointment;
use App\Notifications\MyFirstNotification;
use App\Services\LogsService;
use App\ZipCode;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use App\Services\HHACaregiverMedicalService;
use App\Helpers\HHAPatientHelper;
use App\Model\HHAPatient;
use App\Services\SendEmailNotificationSerivce;
use App\Model\SMSLogs;
use App\Services\SmsService;
use App\Model\AlayacareEmployee;
use App\Services\AgencyWiseServiceService;
use App\Services\AlayacareService;
use App\Helpers\AttachMailer;
use App\Model\PatientServiceRequest;
use Aws\S3\S3Client;
use Illuminate\Support\Facades\DB;
use App\Model\Logs;
use App\DocumentSentReport;

class TestingPurposeController extends BaseController
{
    protected $patientService,$DocumentPatientService="";
    public function __construct(PatientService $patientService,DocumentPatientService $DocumentPatientService)
    {
        $this->DocumentPatientService = $DocumentPatientService;
        $this->patientService = $patientService;
    }
    public function testing(){
        $query = Template::select('upload_document')->where('del_flag','N')->where('upload_document','20250115022651am.pdf')->get();

        $fileArray = [];
        $fileArray1 = [];
        if(count($query) !=0){
            foreach($query as $val){
                $outputPath = public_path()."/dosusinguploads/docusign/". $val->upload_document;
                $fileGetContain = file_get_contents($outputPath);
                $test =Storage::disk('s3')->put('dosusinguploads/docusign/'.$val->upload_document,$fileGetContain);

            }
         
        }
        echo "<pre>";print_r($fileArray1);
        echo "===========================";
        echo "<pre>";print_r($fileArray);die();
        // $insert = AttachMailer::sendEmail("notifications@nybestmedical.com", "vishal@yopmail.com", "Attachment Mailer", "Testing for Attachment Mailer Helper File");
        // $documents = HHACaregiversHelper::getCaregiverMedicalDocument(106, 241);
		// echo "<pre>";print_r($documents);die();
    }

    public function checkSendSMS(){
        $auth = auth()->user();
        echo "<pre>";print_r($auth->roles());die();
        return view("testing_sms.index");
    }

    public function checkPostSendSMS(Request $request){
        $agencyDetails = Agency::where('enable_hha',1)->where('id',43)->whereNotNull('app_name')->whereNotNull('app_key')->first();
        
        $query  = HHAPatient::whereNull('status')->where('agency_fk',43)->inrandomOrder()->limit(100)->get();
        foreach($query  as $val){
           $subquery =  HHAPatientHelper::getPatientDemographicDetails($val->patient_id,$agencyDetails);
            if(isset($subquery['admission_id'])){
                if($val->status ==""){
                    HHAPatient::where('id',$val->id)->update(array('status'=>$subquery['patientStatusName'],'EmploymentTypesDiscipline'=>$subquery['discipline_new']));
                }
            }
        }
       
    }


    public function sendDocumentMail(Request $request){
        $ids = ['280851','280850','280849','280844','280843','280842','280840','280838','280837','281066','281065','281063','280968','280967','280965','280964','280962','280957','280955','280945','280936','280917','280912','280904','280899','280853','280854','280855','280889','280891','280892'];
		$query = HHACaregivers::where('hha_delete_flag','N')->whereIn('id',$ids)->whereNotNull('officeId')->groupBy('agency_fk')->groupBy('officeId')->inRandomOrder()->get();
        $finals = [];
        foreach($query as $vc){
            $finals[] = $vc;
        }
     
        foreach($finals as $val){
     
            $agenctDetails =Agency::where('id',$val['agency_fk'])->where('delete_flag','N')->first();
           
            if(isset($agenctDetails->app_name)){
            
                $data = HHACaregiversHelper::searchCaregiverForHHA($val['agency_fk'],$val['caregiver_code']);
               
                if(isset($data[0]['officeId'])){
                    if($val['officeId'] != $data[0]['officeId']){
                      
                        echo "sad";
                        echo "<pre>";print_r($data[0]);
    
                        HHACaregivers::where('hha_delete_flag','N')->where('id',$val['id'])->update(array('officeId'=>$data[0]['officeId']));
                    }
                }
            }
            
           
        }

        die();
	}

    public function checkTimeOut1(){
        $query = DB::select('SELECT count(caregiver_id) as total,caregiver_id,agency_fk,caregiver_code,officeId FROM `hha_caregivers` WHERE hha_delete_flag="N" and agency_fk=37 GROUP BY caregiver_id HAVING total >1 order BY total DESC;');
       
       if(!empty($query[0])){
        foreach($query as $val){
            if($val->total >1){
               
                $subQuery = HHACaregivers::where('hha_delete_flag','N')->where('caregiver_id',$val->caregiver_id)->where('caregiver_code',$val->caregiver_code)->where('officeId',$val->officeId)->where('agency_fk',$val->agency_fk)->orderBy('id','desc')->get();
                if(count($subQuery) >0){
                    foreach($subQuery as $key =>$vas){
                        if($key !=0){
                            HHACaregivers::where('hha_delete_flag','N')->where('id',$vas->id)->update(array('hha_delete_flag'=>'Y','deleted_at'=>date('Y-m-d H:i:s'),'remove_flag'=>2));
                        }
                    }
                }
            }
            
            
        //    if(!empty($subQuery[0])){
        //     
        //    }else{
        //     HHACaregivers::where('hha_delete_flag','N')->where('id',$val->id)->update(array('remove_flag'=>2));
        //    }
        }
       }

       die();
    }
    
    public function updateDatePerform(Request $request){
       $query = HhaAppointment::with(['agencyDetails'])->where('del_flag','N')->where('agency_id',$request->id)->whereNull('date_perform')->inRandomOrder()->limit(300)->get();
       if(!empty($query[0])){
        foreach($query as $val){
          $data =   $this->datePerformUpdate($val);
          if($data !=""){
            HhaAppointment::where('agency_id',$val->agency_id)->where('caregiver_id',$val->caregiver_id)->where('caregiver_medical_id',$val->caregiver_medical_id)->update(array('date_perform'=>date('Y-m-d',strtotime($data))));
          }
        }
       }
    }

    public function datePerformUpdate($response){
        $agencyHHADetail  =$response->agencyDetails;
        $caregiver = $response;
        $datePerform = NULL;
        $xml = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
            <soap:Body>
                <GetCaregiverMedicalDetails
                    xmlns="https://www.hhaexchange.com/apis/hhaws.integration">
                    <Authentication>
                        <AppName>'.$agencyHHADetail->app_name.'</AppName>
                        <AppSecret>'.$agencyHHADetail->app_key.'</AppSecret>
                        <AppKey>'.$agencyHHADetail->app_token.'</AppKey>
                    </Authentication>
                    <SearchFilter>
                        <CaregiverID>'.$caregiver->caregiver_id.'</CaregiverID>
                        
                        <CaregiverComplianceExpItemID>-1</CaregiverComplianceExpItemID>
                        <ComplianceStatus>All</ComplianceStatus>
                    </SearchFilter>
                </GetCaregiverMedicalDetails>
            </soap:Body>
        </soap:Envelope>';
        
               
        $json = SELF::getData($xml, 'GetCaregiverMedicalDetails');
        if ($json === false) {
            // Avoid echo of empty string (which is invalid JSON), and
            // JSONify the error message instead:
            $json = json_encode(array("jsonError", json_last_error_msg()));
            if ($json === false) {
                // This should not happen, but we go all the way now:
                $json = '{"jsonError": "unknown"}';
            }
            // Set HTTP response status code to: 500 - Internal Server Error
            http_response_code(500);
        } else {
            $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $json);
            $xml = simplexml_load_string($clean_xml);
            //echo "<pre>";print_r($xml);die();
            if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->Result->ErrorInfo->ErrorID) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->Result->ErrorInfo->ErrorID == 0) {
                if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails)) {
                    
                    $respoe = count($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails);
                    for ($i = 0; $i < $respoe; $i++) {
                        
                        $datePerform = NULL;
                        if (isset($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->DatePerformed) && $xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->DatePerformed != '') {
                         
                            $datePerform = addslashes($xml->Body->GetCaregiverMedicalDetailsResponse->GetCaregiverMedicalDetailsResult->CaregiverMedicalDetails[$i]->DatePerformed);
                        }
                      
                    }
                    
                }
            }

        }
       return $datePerform;
    }

    public static function getData($xml, $action)
    {
        $headers = array(
            "POST /Integration/ENT/V1.8/ws.asmx HTTP/1.1",
            "Host: app.hhaexchange.com",
            "Content-Type: text/xml;charset=utf-8",
            "Content-Length: " . strlen($xml),
            "SOAPAction: https://www.hhaexchange.com/apis/hhaws.integration/" . $action
        );

        $url = "https://app.hhaexchange.com/Integration/ENT/V1.8/ws.asmx?op=" . $action;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
        $json = curl_exec($ch);

        return $json;
    }

    public function rolesCheckings(){
        $query = DocumentSentReport::select('send_sms_id','id')->whereNotNull('send_sms_id')->where('send_sms_status','Pending')->limit(1000)->get();
       
        foreach($query as $val){
            $subQuery = Common::fetchSingleMessage($val->send_sms_id);
            $json = json_decode($subQuery,true);
            if(isset($json['status'])){
                DocumentSentReport::where('id',$val->id)->update(array('send_sms_status'=>$json['status'],'send_sms_updated_date'=>date('Y-m-d H:i:s',strtotime($json['date_updated']))));
            }
        }
        /***********Today's Task ***********/
       // $ids = DB::table('hha_patients')->where('agency_fk',225)->where('hha_delete_flag','N')->pluck('patient_id');
        // foreach($ids as $val){
          
        //     DB::table('hha_patients')->where('agency_fk',225)->where('hha_delete_flag','Y')->where('patient_id',$val)->update(array('deleted_at'=>date('Y-m-d H:i:s')));

        // }

        /***********End Today's Task ***********/

    //    $query = DB::table('hha_patients')->selectRaw('COUNT(patient_id) as total,patient_id')->where('agency_fk',225)->where('hha_delete_flag','Y')->whereNotIn('patient_id',$ids)->groupBy('patient_id')->orderBy('total','desc')->inRandomOrder()->limit(200)->get();
    //    foreach($query as $val){
    //     $subQuery = DB::table('hha_patients')->where('agency_fk',225)->where('patient_id',$val->patient_id)->where('hha_delete_flag','N')->get();
    //     if(count($subQuery) ==0){
    //         $allQuery = DB::table('hha_patients')->where('agency_fk',225)->where('patient_id',$val->patient_id)->where('hha_delete_flag','Y')->orderBy('id','desc')->get();
    //         foreach($allQuery as $key=>$vs){
    //             if($vs->patient_record_id !=""){
    //                 DB::table('hha_patients')->where('agency_fk',225)->where('id',$vs->id)->update(array('hha_delete_flag'=>'N'));
    //             }else{
    //                 if($key ==0){
    //                     DB::table('hha_patients')->where('agency_fk',225)->where('id',$vs->id)->update(array('hha_delete_flag'=>'N'));
    //                 }
    //             }
    //         }
    //     }
    //    }
    }

    public function existingRecordServices(){
       $query = PatientServiceRequest::where('del_flag','N')->groupBy('patient_id')->pluck('patient_id');
       
       $patientList = Patient::whereNotIn('id',$query->toArray())->where('deleted_flag','N')->get();
       echo "<pre>";print_r($patientList);die();
       echo "<pre>";print_r($query);die();
    }

    public function getListing(){
   
        $query = PatientServiceRequest::whereNull('last_status_update')->whereDate('created_at','>=','2025-02-01')->whereDate('created_at','<=','2025-02-25')->orderBy('id','desc')->inRandomorder()->limit(1000)->get();
       echo count($query);
        if(!empty($query[0])){
            foreach($query as $va){
                $getPatientDetails = Patient::where('id',$va->patient_id)->whereNull('last_status_update')->first();
                if(isset($getPatientDetails->id)){
                    $subQuery = Logs::where('object_id',$getPatientDetails->id)->where('type','Status Appointment')->orderBy('id','desc')->first();
                    if(isset($subQuery->id)){
                        Patient::where('id',$getPatientDetails->id)->update(array('last_status_update'=>$subQuery->created_at,'last_status_update_by'=>$subQuery->created_by));
                        PatientServiceRequest::where('id',$va->id)->update(array('last_status_update'=>$subQuery->created_at,'last_status_update_by'=>$subQuery->created_by));
                    }
                    
                }
            }
            
        }
    }
}