<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;

use Illuminate\Routing\Controller as BaseController;
use App\Services\ThirdPartyPatientMasterService;
use App\Services\PatientService;
use App\Agency;
use App\Master;

class ThirdPartyReportController extends BaseController{

    protected $thirdPartyPatientMaster = "";
    public function __construct(ThirdPartyPatientMasterService $thirdPartyPatientMaster, PatientService $patientService)
    { 
   
        $this->middleware('auth');
        $this->middleware('permission:third-party-report-list', ['only' => ['reportList', 'reportAjaxList']]);
        $this->thirdPartyPatientMaster = $thirdPartyPatientMaster;
        $this->patientService = $patientService;
    }

    public function reportList(Request $request){
        $auth = auth()->user();
        $data['agencyList'] = Agency::getAgencyListByAgencyToken();
        return view('third_party_patient_report.list',$data);
    }

    public function reportAjaxList(Request $request){
        $data['searchData'] = $request->all();
    
        $patient_status_list= [];
        $query = $this->thirdPartyPatientMaster->getThirdPartyPatientReportList($request->all());
        
        if(!empty($query[0])){
            foreach($query as $val){
                $explodeService = explode(',',$val->service_id);

                $serviceArray = [];
                foreach($explodeService as $sd){
                    $srv = Master::getDetailsById($sd);
                    if(isset($srv->name)){
                        $serviceArray[] = $srv->name;
                    }
                  
                }
                $val->serviceName = implode(',',$serviceArray);
                $statusDetails = $this->patientService->getPatientDetailsByIdWhitoutAgency($val->patient_id);

                $val->status = isset($statusDetails->status)?$statusDetails->status:$val->status;

                if(in_array($val->status,$patient_status_list)){

                }else{
                    $patient_status_list[] = $val->status;
                }
            }
        }
        $data['query'] = $query;
        $data['patient_status_list'] = $patient_status_list;
        return view('third_party_patient_report.ajax_list',$data);
    }

    public function thirdPartyReportExport(Request $request){
		$data['searchData'] = $request->all();
        $query = $this->thirdPartyPatientMaster->getThirdPartyPatientReportList($request->all(),'export');

        $filename = 'Third-Party-Patient-Report' . date("m-d-Y");
		$headers = array(
			"Content-type" => "text/csv",
			"Content-Disposition" => "attachment; filename=" . $filename . ".csv",
			"Pragma" => "no-cache",
			"Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
			"Expires" => "0",
		);

        $columns = array('No', 'Agency Name', 'Patient ID', 'Requested ID', 'Name', 'Mobile', 'DOB', 'Services', 'Type', 'Discipline', 'Gender', 'Service Status',  'Portal', 'API Name', 'Due Date', "Created Date");

        $newass  = array();
        $callback = function () use ($query, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            $cnt = 1;
            foreach($query as $val){
            
                $dob = '';
                if(isset($val->dob)){
                $dob = date('m/d/Y', strtotime($val->dob));
                }

                $createdDate = '';
                if(isset($val->dob)){
                $createdDate = date('m/d/Y h:i A', strtotime($val->created_date));
                }
                $apiName = '';
                if(isset($val->agencyGenerateDetails->notes)){
                $apiName = $val->agencyGenerateDetails->notes;
                }
                                        
                $SName = "";
                if(isset($val->agencyGenerateDetails) && $val->agencyGenerateDetails->notes != ""){
                    $SName =$val->agencyGenerateDetails->notes;
                }

                $explodeService = explode(',',$val->service_id);

                $serviceArray = [];
                foreach($explodeService as $sd){
                    $srv = Master::getDetailsById($sd);
                    if(isset($srv->name)){
                        $serviceArray[] = $srv->name;
                    }
                  
                }
                $serviceName = implode(',',$serviceArray);
                $srvStatus="";
                 if(isset($val->serviceDetails) && $val->serviceDetails !=""){
                    $srvStatus =$val->serviceDetails->status;
                 }
                $due_date = isset($val->due_date) && $val->due_date != '0000-00-00' ?date('m/d/Y',strtotime($val->due_date)):'';
                fputcsv($file, array($val->id,$val->agencyDetails->agency_name,$val->patient_id,$val->requested_service_id,$val->first_name . ' ' . $val->last_name, $val->mobile,$dob,$serviceName,$val->type,$val->diciplin,$val->gender,$srvStatus,$val->platform_type,$SName,$due_date,$createdDate));

            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);    
    }
}