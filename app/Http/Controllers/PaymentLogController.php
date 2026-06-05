<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Helpers\Utility;
use App\Services\PaymentLogService;
use App\Services\RateCardService;
use App\Services\PatientServicesRequest;
use App\Services\PaymentLogServiceWiseService;
use App\Services\PaymentReceivedLogService;
use App\Services\LogsService;
use App\Master;
use Illuminate\Support\Facades\Validator;

class PaymentLogController extends BaseController
{
	protected $paymentLogService,$rateCardService,$patientServicesRequest,$paymentLogServiceWiseService,$paymentReceivedLogService,$logsService = "";

	public function __construct(PaymentLogService $paymentLogService, RateCardService $rateCardService, PatientServicesRequest $patientServicesRequest, PaymentLogServiceWiseService $paymentLogServiceWiseService, PaymentReceivedLogService $paymentReceivedLogService, LogsService $logsService )
	{
        $this->middleware('permission:payment-log', ['only' => ['getPaymentData', 'getPaymentData','exportPaymentData','getPaymentDataById','addPaymentData','editPaymentData','genratePaymentDetails','genratePaymentHistroy','getServices']]);
        $this->middleware('auth');

		$this->paymentLogService = $paymentLogService;
		$this->rateCardService = $rateCardService;
		$this->patientServicesRequest = $patientServicesRequest;
		$this->paymentLogServiceWiseService = $paymentLogServiceWiseService;
		$this->paymentReceivedLogService = $paymentReceivedLogService;
		$this->logsService = $logsService;
	}


	public function getPaymentData(Request $request){
        
        $paginate = 1;
		$data['query'] = $this->paymentLogService->getAllPaymentLogPatientData($request->id,$paginate);
       
        foreach($data['query'] as $val){
            $totAmount = $totRemain = $totReceive = 0;
            if(isset($val->paymentLogDeatil)){
                $services = [];
                foreach($val->paymentLogDeatil as $valS){
                    $totAmount += $valS->total_amount;
                    $totReceive += $valS->received_amount;
                    $totRemain += $valS->remaining_amount;
                    if(isset($valS->serviceDetails) && !empty($valS->serviceDetails)){
                        $services[] = $valS->serviceDetails->name;
                    }
                }
                $val->services = $services;
            }
            $val->totAmount = $totAmount;
            $val->totReceive = $totReceive;
            $val->totRemain = $totRemain;
        }
        $data['color'] = array('success','info','danger','warning','primary');
		return view('patient._partial.payment-log.payment_log_ajax_list',$data);
	}

	public function exportPaymentData(Request $request){
		$detail = $this->paymentLogService->getAllPaymentLogPatientData($request->id,$paginate="");
		$filename = 'payment_log_patient_wise' . date("m-d-Y");
		$headers = array(
			"Content-type" => "text/csv",
			"Content-Disposition" => "attachment; filename=" . $filename . ".csv",
			"Pragma" => "no-cache",
			"Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
			"Expires" => "0",
		);

        if(count($detail) > 0){
            $columns = array('# NO','Agency Name', 'Payment Type' , 'Service', 'Total Service Amount', 'Received Amount', 'Remaining Amount', 'Location', 'Created Date', 'Created By');

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
                    $services = array();
                    if(isset($list->serviceDetails->patientServiceRequestRelationShip)){
                        foreach($list->serviceDetails->patientServiceRequestRelationShip as $valS){
                            $services[] = $valS->services[0]->name;
                        }
                    }
                    $agencyName = '';
                    if(isset($list->patientDetails->agencyDetail) && !empty($list->patientDetails->agencyDetail)){
                        $agencyName = $list->patientDetails->agencyDetail->agency_name;
                    }
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
                            $remaining_amount += $plist->remaining_amount;
                        }
                    }
                    $total_sum_amount += $totalAmount;
                    if($list->paymentDeatil->id != '866'){
                        $totalAmount = 'N/A';
                    }
                    $total_sum_remaining_amount += $remaining_amount;
                    $total_sum_received_amount += $received_amount;
                    fputcsv($file, array($key+1, $agencyName, $payment_type, implode(', ', $service),$totalAmount, $received_amount, $remaining_amount, $locationName, Utility::convertMDYTime($list->created_at), $createdBy));
                }
                fputcsv($file, array('', '', '', 'Total',$total_sum_amount, $total_sum_received_amount, $total_sum_remaining_amount, '','', ''));
                fclose($file);
            };
            return response()->stream($callback, 200, $headers);
        }else{
            return null;
        }
	}

	public function getPaymentDataById(Request $request){
		$data = $this->paymentLogService->getPaymentLogData($request->id);
        
		return response()->json([ 'status' => 0, 'data' => $data], 200);
	}

	public function addPaymentData(Request $request){
        $validator = Validator::make($request->all(), [
			'payment_type' => 'required',
			'patient_id' => 'required',
			'location' => 'required',
		]);
		if ($validator->fails()) {
			return response()->json([
				'error_msg' => $validator->errors()->all()[0],
				'status' => false,
			], 422);
		} else {
            $payment_data = array(
                'payment_type' => $request->payment_type,
                'patient_id' => $request->patient_id,
                'location_id' => $request->location??'',
                'service_requested_id' => $request->service_requested_id??'',
            );
            $insert_id = $this->paymentLogService->save($payment_data);
            // Store data into Payment log services
            if($insert_id){
                if($insert_id && isset($request->payment_service['service_id'][0])){
                    foreach($request->payment_service['service_id'] as $key => $pData){
                        $payment_log_service_data = array(
                            'payment_log_id' => $insert_id,
                            'service_id' => $request->payment_service['service_id'][$key]??'',
                            'total_amount' => isset($request->payment_service['service_amount'])?round($request->payment_service['service_amount'][$key],2):0,
                            'received_amount' => isset($request->payment_service['recive_amount'])?round($request->payment_service['recive_amount'][$key],2):0,
                            'remaining_amount' => isset($request->payment_service['remain_amount']) ? round($request->payment_service['remain_amount'][$key],2):0,
                        );
                        $insert_service_id = $this->paymentLogServiceWiseService->save($payment_log_service_data);
                        if(!empty($insert_service_id)){
                            $payment_receive_log_data = array(
                                'payment_log_id' => $insert_id,
                                'payment_log_services_id' => $insert_service_id,
                                'received_amount' => round($request->payment_service['recive_amount'][$key],2)??0,
                            );
                            $this->paymentReceivedLogService->save($payment_receive_log_data);
                        }
                    }
                }
                $user = auth()->user();
                $ipaddress = Utility::getIP();
                $data = $this->paymentLogService->getPaymentLogData($insert_id);
                $insertLog = [
                    'type' => 'Add Payment Details',
                    'link' => url('/patient-log'),
                    'module' => 'Patient Appointment',
                    'object_id' => $request->patient_id,
                    'message' => $user->first_name . ' ' . $user->last_name . ' has added Appointment',
                    'new_response' => serialize($data),
                    'ip' => $ipaddress,
                ];
                LogsService::save($insertLog);
                return response()->json([ 'status' => 1, 'error_msg' => 'Payment deatils added successfully.'], 200); 
            }else{
                return response()->json(['status' => true, 'error_msg' => 'Sorry, something went wrong. Please try again'], 500);
            }
        }
	}

	public function editPaymentData(Request $request){
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
			'payment_type' => 'required',
			'patient_id' => 'required',
			'location' => 'required',
		]);
		if ($validator->fails()) {
			return response()->json([
				'error_msg' => $validator->errors()->all()[0],
				'status' => false,
			], 422);
		} else {
            $payment_data = array(
                'payment_type' => $request->payment_type,
                'patient_id' => $request->patient_id,
                'location_id' => $request->location??'',
                'service_requested_id' => $request->service_requested_id??'',
            );
            $olddata = $this->paymentLogService->getPaymentLogData($request->id);
            $this->paymentLogService->update($payment_data,array('id' => $request->id));
            $deleteData = array('delete_flag' => 'Y');
            $this->paymentLogServiceWiseService->SoftDelete($deleteData,array('payment_log_id' => $request->id));
            $this->paymentReceivedLogService->SoftDelete($deleteData,array('payment_log_id' => $request->id));
            if(isset($request->payment_service['service_id'][0])){
                //delete the payment log services data
                foreach($request->payment_service['service_id'] as $key => $pData){
                    $payment_log_service_data = array(
                        'payment_log_id' => $request->id,
                        'service_id' => $request->payment_service['service_id'][$key]??'',
                        'total_amount' => round($request->payment_service['service_amount'][$key],2)??0,
                        'received_amount' => round($request->payment_service['recive_amount'][$key],2)??0,
                        'remaining_amount' => round($request->payment_service['remain_amount'][$key],2)??0,
                    );
                    $insert_service_id = $this->paymentLogServiceWiseService->save($payment_log_service_data);
                    if(!empty($insert_service_id)){
                        $payment_receive_log_data = array(
                            'payment_log_id' => $request->id,
                            'payment_log_services_id' => $insert_service_id,
                            'received_amount' => round($request->payment_service['recive_amount'][$key],2)??0,
                        );
                        $this->paymentReceivedLogService->save($payment_receive_log_data);
                    }
                }
            }
            $ipaddress = Utility::getIP();
            
            $newdata = $this->paymentLogService->getPaymentLogData($request->id);
            $insertLog = [
               'type' => 'Update Payment Details',
                'link' => url('/edit-patient-log'),
                'module' => 'Patient Appointment',
                'object_id' => $request->patient_id,
                'message' => $user->first_name . ' ' . $user->last_name . ' has added Appointment',
                'old_response' => serialize($olddata),
                'new_response' => serialize($newdata),
                'ip' => $ipaddress,
            ];
            LogsService::save($insertLog);
            return response()->json([ 'status' => 1, 'error_msg' => 'Payment details updated successfully.'], 200);
        }
	}

    public function genratePaymentDetails(Request $request){
        $data = array();
        $services = $request->pay_service_id;
        $servicePaymentLog = Master::getServicePaymentData($services,$request->agency_id);
        $service_ids = array_unique(array_column($servicePaymentLog, 'service_id'));
        if($request['debug']){
            echo "<pre>"; print_r($servicePaymentLog); 
            echo "<pre>"; print_r($services); 
            echo "<pre>"; print_r($service_ids); 
            echo "<pre>"; print_r($request->agency_id); 
        }
        foreach($servicePaymentLog as $key => $service){
            if(in_array($service['service_id'],$service_ids)){
                if($service['agency_id'] == $request->agency_id){
                    if($request['debug']){
                        echo "Hello";
                    }
                    $data[$service['service_id']] = $service;
                }elseif($service['agency_id'] == 0 || $service['agency_id'] == ""){   
                    if($request['debug']){
                        echo "Hello1";
                    } 
                    if(!array_key_exists($service['service_id'],$data) && empty($data[$service['service_id']])){
                        $data[$service['service_id']] = $service;
                    }
                }
                elseif(!array_key_exists($service['service_id'],$data) && empty($data[$service['service_id']])) {
                    $service = Master::getRecordById([$service['service_id']]);
                    foreach($service as $ser){
                        $data[$ser['id']] = array(
                            'service_id' => $ser['id'],
                            'name' => $ser['name'],
                            'agency_id' => null,
                            'amount' => null
                        );
                    }
                }
            }
        }
        foreach($services as $ser){
            if(!array_key_exists($ser,$data) && empty($data[$ser])) 
            {
                if($request['debug']){
                    echo "<pre>"; print_r($ser); 
                }
                $service = Master::getRecordById([$ser]);
                foreach($service as $ser){
                    $data[$ser['id']] = array(
                        'service_id' => $ser['id'],
                        'name' => $ser['name'],
                        'agency_id' => null,
                        'amount' => null
                    );
                }
            }
        }
        return response()->json([ 'status' => 0, 'error_msg' => '', 'data' => $data], 200);
    }

    public function genratePaymentHistroy(Request $request){
        $data = $paymentLogData = array();
        $logdata = $this->paymentLogService->getPaymentHistroy($request->id);  
        foreach($logdata['payment_log_deatil'] as $log){
            $paymentLogData[] = array(
                                        'total_amount'  => $log['total_amount'],
                                        'received_amount'  => $log['received_amount'],
                                        'remaining_amount'  => $log['remaining_amount'],
                                        'service_name'  => $log['service_details']['name'],
                                    ); 
        }
        $data = array(
            'payment_type' => $logdata['payment_deatil']['name'],
            'patient_id' => $logdata['patient_id'],
            'patient_name' => $logdata['patient_details']['first_name'].' '.$logdata['patient_details']['last_name'],
            'agency_name' => $logdata['patient_details']['agency_detail']['agency_name'],
            'created_at' => date('m-d-Y H:i A' , strtotime($logdata['created_at'])),
            'created_by' => $logdata['users']['full_name'],
            'paymentLogData' => $paymentLogData,
        );
        return response()->json([ 'status' => 0, 'error_msg' => '', 'data' => $data], 200);
    }

    public function getServices(Request $request){
        $services = Master::getServiceRequestNewWithCondition($request->type);
        foreach($services as $ser){
            $data[$ser['id']] = $ser['name'];
        }
        return response()->json([ 'status' => 0, 'error_msg' => '', 'data' => $data], 200);
    }
}
