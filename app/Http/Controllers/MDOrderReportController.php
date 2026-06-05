<?php

namespace App\Http\Controllers;

use App\Helpers\Utility;
use Illuminate\Routing\Controller as BaseController;
use App\Services\MDOrderService;
use Illuminate\Http\Request;
use App\Services\DocumentPatientService;
use App\Agency;
use App\Services\UserWiseAgencyService;
class MDOrderReportController extends BaseController
{
    protected $mdOrderService,$documentPatientService,$userWiseAgencyService="";
    public function __construct(MDOrderService $mdOrderService,DocumentPatientService $documentPatientService,UserWiseAgencyService $userWiseAgencyService)
    {
        //$this->middleware('permission:md-order-report', ['only' => ['index', 'ajaxList']]);
       // $this->middleware('permission:md-order-report-export', ['only' => ['exportcsv']]);

        $this->middleware('auth');
        $this->mdOrderService = $mdOrderService;
        $this->documentPatientService = $documentPatientService;
        $this->userWiseAgencyService = $userWiseAgencyService;

    }

    public function index(){
        $data['menu'] = "user";
        $data['user']= $user= auth()->user();
        
        $data['agency_list'] = Agency::getAgencyList();
        if(in_array(auth()->user()->id,Utility::agencyPortalRolePermission())){
            abort(404);
        }
        return view("mdOrder/index", $data);
    }
    public function ajaxList(Request $request){
       $data['query'] = $this->mdOrderService->getAllMDOrderList($request->all());
       return view('mdOrder.ajax_list',$data);
    }

    public function exportCsv(Request $request){
        $detail = $this->mdOrderService->getAllMDOrderList($request->all(),'export');
		
        $filename = 'Patient' . date("m-d-Y");
		$headers = array(
			"Content-type" => "text/csv",
			"Content-Disposition" => "attachment; filename=" . $filename . ".csv",
			"Pragma" => "no-cache",
			"Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
			"Expires" => "0",
		);

        $columns = array('Agency Name', 'Portal ID','Name', 'Portal Status','Document Name', 'Start Date', 'End Date', 'Created Date', 'Created By');
      
        $callback = function () use ($detail, $columns) {
            $file = fopen('php://output', 'w');
			fputcsv($file, $columns);
			$cnt = 1;
			foreach ($detail as $list) {

                $agencyName = "";
                if(isset($list->patientDetails->agencyDetail->id)){
                    $agencyName = $list->patientDetails->agencyDetail->agency_name;
                }

                $patientName = "";
                if(isset($list->patientDetails->id)){
                    $patientName = $list->patientDetails->first_name.' '.$list->patientDetails->last_name;
                }

                $status = "";
                if(isset($list->patientDetails->id)){
                    $status = ucfirst($list->patientDetails->status);
                }

                $documentName = "";
                if(isset($list->documentDetails->id)){
                    $documentName = $list->documentDetails->document_name.' '.$list->patientDetails->last_name;
                }

                $createdBy = "";
                if(isset($list->users->id)){
                    $createdBy = $list->users->first_name.' '.$list->users->last_name;
                }
                fputcsv($file, array($agencyName,$list->patient_id, $patientName,$status,  $documentName, Utility::convertMDY($list->start_date), Utility::convertMDY($list->end_date), Utility::convertMDYTime($list->created_date), $createdBy));
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }
    
}