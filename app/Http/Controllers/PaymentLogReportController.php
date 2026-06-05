<?php

namespace App\Http\Controllers;

use App\Helpers\Utility;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Agency;
use App\Master;
use App\Services\PaymentLogService;
use App\Services\LocationMasterService;
use App\Services\InsuranceMasterService;
use Illuminate\Support\Facades\Cache;

class PaymentLogReportController extends BaseController
{
    protected $paymentLogService,$locationMasterService,$insuranceMasterService="";
    public function __construct(PaymentLogService $paymentLogService, LocationMasterService $locationMasterService, InsuranceMasterService $insuranceMasterService)
    {
        $this->middleware('permission:payment-log-report', ['only' => ['index', 'ajaxList']]);
        $this->middleware('permission:payment-log-report-export', ['only' => ['exportcsv']]);

        $this->middleware('auth');
        $this->paymentLogService = $paymentLogService;
        $this->locationMasterService = $locationMasterService;
        $this->insuranceMasterService = $insuranceMasterService;
    }

    public function index(Request $request){
        $data['menu'] = "user";
        $data['user']= $user= auth()->user();
        $data['agency_list'] = Agency::getAgencyList();
        $data['location_list'] = Cache::get('patient_master_locations', function () {
			return $this->locationMasterService->AllListWithoutPaginate();
		}, 10 * 60);
        $data['masterData'] = Master::getAllDataByMasterTypeFk(array(12, 17, 25));
        $data['serviceList'] = Master::getServiceRequest();
        return view("paymentLogReport/index", $data);
    }
    public function ajaxList(Request $request){
       $data['query'] = $this->paymentLogService->getAllPaymentLogData($request->all());
       foreach($data['query'] as $val){
            $services = array();
            $totalAmount = $received_amount = $remaining_amount = "0";
            if(isset($val->paymentLogDeatil)){
                foreach($val->paymentLogDeatil as $plist){
                    if(isset($plist->serviceDetails) && !empty($plist->serviceDetails)){
                        $services[] = $plist->serviceDetails->name;
                    }
                    if(isset($plist->total_amount) && !empty($plist->total_amount)){
                        $totalAmount += $plist->total_amount;
                    }
                    if(isset($plist->received_amount) && !empty($plist->received_amount)){
                        $received_amount += $plist->received_amount;
                    }
                    if(isset($plist->remaining_amount) && !empty($plist->remaining_amount)){
                        $remaining_amount += $plist->remaining_amount;
                    }
                }
                $val->serviceArr = $services;
                $val->totalAmount = $totalAmount;
                $val->received_amount = $received_amount;
                $val->remaining_amount = $remaining_amount;
            }
       }
       $data['color'] = array('success','info','danger','warning','primary');
       return view('paymentLogReport.ajax_list',$data);
    }

    public function exportCsv(Request $request){
        $detail = $this->paymentLogService->getAllPaymentLogData($request->all(),'export');
		$filename = 'payment_log_report' . date("m-d-Y");
		$headers = array(
			"Content-type" => "text/csv",
			"Content-Disposition" => "attachment; filename=" . $filename . ".csv",
			"Pragma" => "no-cache",
			"Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
			"Expires" => "0",
		);

        if(count($detail) > 0){
            $columns = array('# NO','Agency Name', 'Portal Id', 'Patient Name','Payment Type' , 'Service', 'Total Service Amount', 'Received Amount', 'Remaining Amount', 'Location', 'Created Date', 'Created By');
            $callback = function () use ($detail, $columns) {
                $count = 0;
                $total_sum_amount = 0;
                $total_sum_remaining_amount = 0;
                $total_sum_received_amount = 0;
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);
                foreach ($detail as $key => $list) {
                    $payment_type = "";
                    if(isset($list->paymentDeatil->id)){
                        $payment_type = $list->paymentDeatil->name;
                    }
                    $locationName = "";
                    if(isset($list->locationDetails->id)){
                        $locationName = $list->locationDetails->address1;
                    }

                    $createdBy = "";
                    if(isset($list->users->id)){
                        $createdBy = $list->users->first_name.' '.$list->users->last_name;
                    }                
                    $agencyName = '';
                    if(isset($list->patientDetails->agencyDetail) && !empty($list->patientDetails->agencyDetail)){
                        $agencyName = $list->patientDetails->agencyDetail->agency_name;
                    }
                    $patientId = $list->patientDetails->id??'';
                    $patientName = $list->patientDetails->first_name.' '.$list->patientDetails->last_name??'';
                    $totalAmount = $received_amount = $remaining_amount = "0"; 
                    $service = array();
                    foreach($list->paymentLogDeatil as $plist){
                        if(isset($plist->serviceDetails) && !empty($plist->serviceDetails)){
                            $service[] = $plist->serviceDetails->name;
                        }
                        if(isset($plist->total_amount) && !empty($plist->total_amount)){
                            $totalAmount += $plist->total_amount;
                        }
                        if(isset($plist->received_amount) && !empty($plist->received_amount)){
                            $received_amount += $plist->received_amount;
                        }
                        if(isset($plist->remaining_amount) && !empty($plist->remaining_amount)){
                            $remaining_amount = $plist->remaining_amount;
                        }
                    }
                    $total_sum_amount += $totalAmount;
                    if($list->paymentDeatil->id != '866'){
                        $totalAmount = 'N/A';
                    }
                    $total_sum_remaining_amount += $remaining_amount;
                    $total_sum_received_amount += $received_amount;
                    fputcsv($file, array($key+1, $agencyName, $patientId, $patientName ,$payment_type, implode(',',$service),$totalAmount, $received_amount, $remaining_amount, $locationName, Utility::convertMDYTime($list->created_at), $createdBy));
                }
                fputcsv($file, array('', '', '', '', '', 'Total',$total_sum_amount, $total_sum_received_amount, $total_sum_remaining_amount, '','', ''));
                fclose($file);
            };
            return response()->stream($callback, 200, $headers);
        }else{
            return null;
        }
        
    }
    
}