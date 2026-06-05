<?php

namespace App\Http\Controllers;

use App\Agency;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Services\DocumentSendService;
use App\Services\LocationMasterService;
use App\Services\TemplateService;
use Illuminate\Support\Facades\Cache;

class EsignDashboardController extends BaseController{

    protected $documentSendService,$locationMasterService,$templateService ="";

    public function __construct(DocumentSendService $documentSendService, LocationMasterService $locationMasterService, TemplateService $templateService)
    {
        $this->middleware('permission:esign-dashboard', ['only' => ['index']]);
        $this->middleware('auth');
        $this->documentSendService = $documentSendService;
        $this->locationMasterService = $locationMasterService;
        $this->templateService = $templateService;
    }

    public function index(){
        $data['agencyList'] = Cache::get('patient_master_locations', function () {
			return Agency::getAgencyListWise();
		}, 10 * 60); 
                
        $data['location_list'] = Cache::get('patient_master_locations', function () {
			return $this->locationMasterService->AllListWithoutPaginate();
		}, 10 * 60);      
        return view('esignDashboard.index',$data);
    }

    public function totalCountForEsign(Request $request){

        $from_date = $to_date = '';         
        if($request->range_date != ''){
            $date = explode('-',$request->range_date);
            if(count($date) > 0){
                $from_date = date('Y-m-d 00:00:00',strtotime(trim($date[0])))??'';
                $to_date = date('Y-m-d 23:59:59',strtotime(trim($date[1])))??'';
            }
        }
        $agencyIds = Cache::remember('patient_agency',10 * 60, function () {
			return $this->getAgencyIds();
		}); 
        $totalCount = $this->documentSendService->gettotalCount('',$from_date,$to_date,$request->type,'',$agencyIds);        
        $totalCompletedCount = $totalRejectedCount = $toalApprovedCount = $totalPendingCount = 0;
        if (!empty($totalCount[0])) {
			foreach ($totalCount as $val) {
				$totalSigner = $this->documentSendService->TotalSignerCount($val->groupId);                
                if($totalSigner[0]->total == 0){
                    if($val->pdf_status == ''){
                        $totalCompletedCount++;
                    }else{
                        if($val->pdf_status == 0){
                            $totalRejectedCount++;
                        }else{
                            $toalApprovedCount++;
                        }
                    }
                }else{
                    if($val->pdf_status == ''){
                        $totalPendingCount++;
                    }else{
                        if($val->pdf_status == 0){
                            $totalRejectedCount++;
                        }else{
                            $toalApprovedCount++;
                        }
                    }
                }
			}
		}
        $data = ['totalEsign'=> count($totalCount),'totalPendingCount'=>$totalPendingCount,'totalCompletedCount' => $totalCompletedCount, 'toalApprovedCount' => $toalApprovedCount,'totalRejectedCount' => $totalRejectedCount];
        return response()->json(['success'=>true,'data'=>$data],200);
    }

    public function esignData(Request $request){
        $agencyIds = Cache::remember('patient_agency',10 * 60, function () {
			return $this->getAgencyIds();
		});
        $esignData = $this->documentSendService->getTodayEsignData($request->type_id,$agencyIds);
        foreach ($esignData as $val) {
            $totalSigner = $this->documentSendService->TotalSignerCount($val->groupId);
            $query = $this->documentSendService->GetDetailsbyGroupId($val->groupId);
            $sentOnCount = 0;
            $completedCount = 0;
            if (!empty($query)) {
                foreach ($query as $queryVal) {
                    if (!empty($queryVal->sent_on)) {
                        $sentOnCount++;
                    }
                    if ($queryVal->status == 'Completed') {
                        $completedCount++;
                    }
                }
            }
            $val->sentOnCount = $sentOnCount;
            $val->completedCount = $completedCount;
            $val->signerRemaining = $totalSigner[0]->total;

            $completed_on = "";
            if ($val->completed_on != "") {
                $completed_on = date('m/d/Y h:i A', strtotime($val->completed_on));
            }
            $val->completed_on = $completed_on;
            $val->created_date = date('m/d/Y h:i A', strtotime($val->created_date));
        }        
        return view('esignDashboard.esign_data',['esignData'=>$esignData]);
    }

    public function statusWiseGraphData(Request $request){
        $from_date = $to_date = '';         
        if($request->range_date != ''){
            $date = explode('-',$request->range_date);
            
            if(count($date) > 0){
                $from_date = date('Y-m-d 00:00:00',strtotime(trim($date[0])))??'';
                $to_date = date('Y-m-d 23:59:59',strtotime(trim($date[1])))??'';
            }
        }
        
        $agencyIds = $this->getAgencyIds($request->agency_id); 
        $totalCount = $this->documentSendService->gettotalCount('',$from_date,$to_date,$request->type,$request->location_id,$agencyIds);        
        $totalCompletedCount = $totalRejectedCount = $toalApprovedCount = $totalPendingCount = 0;
        if (!empty($totalCount[0])) {
			foreach ($totalCount as $val) {
				$totalSigner = $this->documentSendService->TotalSignerCount($val->groupId);                
                if($totalSigner[0]->total == 0){
                    if($val->pdf_status == ''){
                        $totalCompletedCount++;
                    }else{
                        if($val->pdf_status == 0){
                            $totalRejectedCount++;
                        }else{
                            $toalApprovedCount++;
                        }
                    }
                }else{
                    if($val->pdf_status == ''){
                        $totalPendingCount++;
                    }else{
                        if($val->pdf_status == 0){
                            $totalRejectedCount++;
                        }else{
                            $toalApprovedCount++;
                        }
                    }
                }
			}
		}       
        $data = array();
        if($totalPendingCount > 0 || $totalCompletedCount > 0 || $toalApprovedCount > 0 || $totalRejectedCount > 0){
            $data = array(
                array(
                        'name' => 'Pending',
                        'count' => $totalPendingCount
                    ),
                array(
                    'name' => 'Completed',
                    'count' => $totalCompletedCount
                ),
                array(
                    'name' => 'Approved',
                    'count' => $toalApprovedCount
                ),
                array(
                    'name' => 'Rejected',
                    'count' => $totalRejectedCount
                )
            );
        }
        
        return response()->json(['success'=>true,'data'=>$data],200);
    }

    public function templeteUseGraphData(Request $request){
        $from_date = $to_date = '';         
        if($request->range_date != ''){
            $date = explode('-',$request->range_date);
            if(count($date) > 0){
                $from_date = date('Y-m-d 00:00:00',strtotime(trim($date[0])))??'';
                $to_date = date('Y-m-d 23:59:59',strtotime(trim($date[1])))??'';
            }
        }
        $tempalteData = $this->templateService->getTemplateData();   
        foreach($tempalteData as $tdata){
            $tempData[$tdata['id']] = $tdata['template_name'];
        } 
        $agencyIds = Cache::remember('patient_agency',10 * 60, function () {
			return $this->getAgencyIds();
		});         
        $esignData = $this->documentSendService->getTempalteUsageData($from_date,$to_date,$request->type,$agencyIds);
        
        $tempalteUsageData = array();
        foreach($esignData as $data){
            if(array_key_exists($data['templete_id'],$tempData)){
                if(isset($tempalteUsageData[$data['templete_id']])){
                    $tempalteUsageData[$data['templete_id']] = $tempalteUsageData[$data['templete_id']] + 1; 
                }else{
                    $data['templete_id'];

                    $tempalteUsageData[$data['templete_id']] = 1;
                }
            }
        }     
        $datas = array();
        foreach($tempalteData as $tData){
            if(array_key_exists($tData['id'],$tempalteUsageData) && $tempalteUsageData[$tData['id']] > 0){
                $datas[] = array(
                    'name' => $tData['template_name'],
                    'count' => $tempalteUsageData[$tData['id']]
                );
            }
        }
        return response()->json(['success'=>true,'data'=>$datas],200);
    }

    public function reviewEsignGraphData(Request $request){
        $from_date = $to_date = '';         
        if($request->range_date != ''){
            $date = explode('-',$request->range_date);
            if(count($date) > 0){
                $from_date = date('Y-m-d 00:00:00',strtotime(trim($date[0])))??'';
                $to_date = date('Y-m-d 23:59:59',strtotime(trim($date[1])))??'';
            }
        }            
        $datas = $esignUsageData = $reviewData = array();
        $agencyIds = Cache::remember('patient_agency',10, function () {
			return $this->getAgencyIds();
		});
        $esignData = $this->documentSendService->getReviewByData($from_date,$to_date,$request->type,$agencyIds);
        foreach($esignData as $value){
            $reviewData[$value['review_by']] = array(
                'first_name' => $value['review_details']['first_name'],
                'last_name' => $value['review_details']['last_name']
            );
            if(isset($esignUsageData[$value['review_by']])){
                $esignUsageData[$value['review_by']] = $esignUsageData[$value['review_by']] + 1; 
            }else{
                $esignUsageData[$value['review_by']] = 1;
            }
        }
        
        $datas = array();
        foreach($reviewData as $key => $tData){
            $datas[] = array(
                'name' => $tData['first_name'].' '.$tData['last_name'],
                'count' => array_key_exists($key,$esignUsageData) ? $esignUsageData[$key] : 0 
            );
        }        
        return response()->json(['success'=>true,'data'=>$datas],200);
    }

    public function createEsignGraphData(Request $request){
        $from_date = $to_date = '';         
        if($request->range_date != ''){
            $date = explode('-',$request->range_date);
            if(count($date) > 0){
                $from_date = date('Y-m-d 00:00:00',strtotime(trim($date[0])))??'';
                $to_date = date('Y-m-d 23:59:59',strtotime(trim($date[1])))??'';
            }
        }            
        $datas = $esignUsageData = $reviewData = array();
        $agencyIds = Cache::remember('patient_agency',10 * 60, function () {
			return $this->getAgencyIds();
		});
        $esignData = $this->documentSendService->getCreatedByData($from_date,$to_date,$request->type,$agencyIds);
        
        foreach($esignData as $value){
            $reviewData[$value['created_by']] = array(
                'first_name' => $value['user_details']['first_name'],
                'last_name' => $value['user_details']['last_name']
            );
            if(isset($esignUsageData[$value['created_by']])){
                $esignUsageData[$value['created_by']] = $esignUsageData[$value['created_by']] + 1; 
            }else{
                $esignUsageData[$value['created_by']] = 1;
            }
        }
        
        $datas = array();
        foreach($reviewData as $key => $tData){
            $datas[] = array(
                'name' => $tData['first_name'].' '.$tData['last_name'],
                'count' => array_key_exists($key,$esignUsageData) ? $esignUsageData[$key] : 0 
            );
        }    
        return response()->json(['success'=>true,'data'=>$datas],200);
    }

    public function getAgencyIds($requestAgencyId = array()){
        $agencyList = Cache::remember('patient_master_locations', 10 ,function () {
			return Agency::getAgencyListWise();
		}); 
        $agencyIds = array();  
        if(isset($requestAgencyId) && !empty($requestAgencyId[0])){
            $agencyIds[] = $requestAgencyId;
        }else{
            foreach($agencyList as $list){
                $agencyIds[] = $list['id']; 
            }
        }
        return $agencyIds;
    }
}