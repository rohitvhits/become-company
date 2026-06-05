<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TemplateService;
use App\Services\PatientService;
use App\Model\PDFVNS;
use App\Services\VnsEsignFormDataService;
use Illuminate\Support\Facades\View;
use App\Services\DocumentSendService;
use Storage;
use App\Services\VNSProcedureService;
use App\Services\VNSQuestionService;
use App\Services\DocumentSignerService;
use App\Helpers\Utility;
use App\Services\DynamicFormLogService;
use App\Services\LogsService;
use App\Model\VnsEsignFormData;
use App\Services\VNSSocialHistoryService;
class CustomVNSEsignController extends Controller
{
    protected $templateService;
    protected $patientService;
    protected  $vnsEsignFormDataService;
    protected $documentSentReport;
    protected $vnsProcedureService;
    protected $vnsQuestionService;
    protected $documentSignerService;
    protected $dynamicFormLogService;
    protected $vnsSocialHistoryService;
    public function __construct(TemplateService $templateService,PatientService $patientService, VnsEsignFormDataService $vnsEsignFormDataService,DocumentSendService $documentSentReport,VNSProcedureService $vnsProcedureService,VNSQuestionService $vnsQuestionService,DocumentSignerService $documentSignerService,DynamicFormLogService $dynamicFormLogService,VNSSocialHistoryService $vnsSocialHistoryService)
    {
        $this->middleware('auth');
        $this->templateService = $templateService;
        $this->patientService = $patientService;
        $this->vnsEsignFormDataService = $vnsEsignFormDataService;
        $this->documentSentReport = $documentSentReport;
        $this->vnsProcedureService = $vnsProcedureService;
        $this->vnsQuestionService = $vnsQuestionService;
        $this->documentSignerService = $documentSignerService;
        $this->dynamicFormLogService = $dynamicFormLogService;
        $this->vnsSocialHistoryService = $vnsSocialHistoryService;
    }

    public function index(Request $request){
        $patient_data = $this->patientService->getDetailByIdEncrypt($request->id);
       
        $template_id = $request->template_id;
        
        $templateProcedure = $this->vnsProcedureService->getProcedueListByTemplateTypeID();
        $vnsQuestionList = $this->vnsQuestionService->getQuestionsByTemplateType($request->template_id);
        $vnsSocialHistoryList = $this->vnsSocialHistoryService->getHistoryByTemplateId($request->template_id);
        return view('customVNSEsign.vns_pre_emp_quant_ds',['patient_data'=>$patient_data,'template_id'=>$template_id,'templateProcedure'=>$templateProcedure,'vnsQuestionList'=>$vnsQuestionList,'vnsSocialHistoryList'=>$vnsSocialHistoryList]);
    }

    public function saveData(Request $request){
        $auth = auth()->user();
        $data['newform'] = $request->all();
        $save = $this->vnsEsignFormDataService->save([
            'patient_id' => $data['newform']['patient_id'],
            'template_id' => $data['newform']['template_id']??'',
            'req_data' => serialize($data['newform']),
        ]);
        if($save){

            $html = trim(View::make('customVNSEsign.regenerate_pre_emp_pdf', $data)->render());
            // Add a page
            $pdf = new PDFVNS();

            $pdf->SetMargins(15, 35, 15);
            $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
            $pdf->SetFont('helvetica', '', 9);

            // Optional: header/footer text
            $pdf->headerTextName = "ID : " . $data['newform']['patient_id'];
            $pdf->footerTextName = "Name : " . $data['newform']['footer_name'];
            $pdf->footerTextDOB  = "DOB : " . $data['newform']['footer_dob'];

            $pdf->AddPage();

            $pdf->writeHTML($html, true, false, true, false, '');
            
            $pdfName = uniqid().'-'.time().'-'.$data['newform']['patient_id'].'.pdf';
            $public_path = public_path('/customVNSEsign').'/'.$pdfName;
            
            if(env('FILE_UPLOAD_PERMISSION') !="development"){
                $pdfContain = $pdf->Output("", 'S');
				Storage::disk('s3')->put('dosusinguploads/docusign/'.$pdfName,$pdfContain);
            }else{
                $pdf->Output($public_path, 'F');
            }

            $getTemplateDetails = $this->templateService->getDetailsById($data['newform']['template_id']);
           
            $this->vnsEsignFormDataService->update(array('pdf'=>$pdfName,'main_template_id'=>$getTemplateDetails->dependent_id),array('id'=>$save));
          
            $rand = uniqid();
            $data_array = array(
                'sender_name' => $auth['first_name'] . ' ' . $auth['last_name'],
                'caregiver_code' =>$data['newform']['patient_id'],
                'status' => "Completed",
                'sender_id' => $auth['id'],
                'receipt_name' => $data['newform']['footer_name'],
                'templete_id' => $request->template_id,

                'type' => $request->input('type'),
                'sourceFile' => $pdfName,
                'main_intakeId' => $data['newform']['patient_id'],
                'sent_on' => 'formFill',
                'groupId' => $rand,
                'pdf_generate' => $pdfName,
                'document_submit_status'=>1,
                'vns_id'=>$save,
                'completed_on'=>date('Y-m-d H:i:s'),

            );
            $this->documentSentReport->save($data_array);

            $documentRes = $this->documentSentReport->getAllDetailsByGroupId($rand);
            $logDetails = [];
            $groupId = "";
            if(count($documentRes) >0){
                foreach($documentRes as $val){
                    $val->template_name = $val->templateDetails->template_name;
                    $val->added_by_name = $val->userDetails->first_name.' '.$val->userDetails->last_name;

                    unset($val->templateDetails, $val->userDetails);
                    $logDetails[] = $val;
                    $groupId = $val->groupId;
                }
            }
            
            
            // Insert form Log into Dynamic form log table
            $ipaddress = Utility::getIP();
            $message = auth()->user()->first_name . ' ' . auth()->user()->last_name . ' has Completed a new VNS Form';
            $insertLog = [
                'type' => 'Completed',
                'link' => url('/save_response_data_vns'),
                'module' => 'Esign Section',
                'module_id' => $groupId,
                'new_response' => serialize($logDetails),
                'old_response' => '',
                'is_status' => 'Completed',
                'ip_address'=>$ipaddress,
                'message'=>$message,
                'esign_new_response'=> serialize($data['newform'])
            ];
            $this->dynamicFormLogService->storeFormLog($insertLog);
            
            $insertLog = [
                'type' => 'Completed VNS Form',
                'link' => url('/save_response_data_vns'),
                'module' => 'Patient Appointment',
                'object_id' =>  $data['newform']['patient_id'],
                'message' =>$message,
                'new_response' => serialize($data_array),
                'ip' => $ipaddress,
                
            ];
            LogsService::save($insertLog);
            return response()->json(['status' => "1", 'error_msg' => "Success.", 'data' => array()],200);
        }else{
            return response()->json(['status' => "1", 'error_msg' => "Success.", 'data' => array()],500);
        }
    }

    public  function orientation($width, $height) {
        if ($width > $height) {
            return "L";
        }else{
            return "P";
        }
    }

    public function tempRegeneratePDF(Request $request){
        $query = VnsEsignFormData::where('id',$request->id)->first();
       
        $data['newform'] = unserialize($query->req_data);
     
        $html = trim(View::make('customVNSEsign.regenerate_pre_emp_pdf', $data)->render());
        // Add a page
        $pdf = new PDFVNS();

        $pdf->SetMargins(15, 31, 15);
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $pdf->SetFont('helvetica', '', 9);

        // Optional: header/footer text
        $pdf->headerTextName = "ID : " . $data['newform']['patient_id'];
        $pdf->footerTextName = "Name : " . $data['newform']['footer_name'];
        $pdf->footerTextDOB  = "DOB : " . $data['newform']['footer_dob'];

        $pdf->AddPage();

        $pdf->writeHTML($html, true, false, true, false, '');
        
        $pdfName = uniqid().'-'.time().'-'.$data['newform']['patient_id'].'.pdf';
        $public_path = public_path('/customVNSEsign').'/'.$pdfName;
        
        if(env('FILE_UPLOAD_PERMISSION') !="development"){
            $pdfContain = $pdf->Output("", 'S');
            Storage::disk('s3')->put('dosusinguploads/docusign/'.$pdfName,$pdfContain);
            $public_path = Storage::disk('s3')->temporaryUrl(
                'dosusinguploads/docusign/'.$pdfName,
                now()->addMinutes(5)
            );
        }else{
            $pdf->Output($public_path, 'I');
        }

        echo $public_path;die();
    }
	
}
