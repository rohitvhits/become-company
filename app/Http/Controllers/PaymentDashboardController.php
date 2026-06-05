<?php

namespace App\Http\Controllers;

use App\Agency;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Services\UserService;
use App\Services\LocationMasterService;
use App\Services\PaymentLogService;
use App\Services\PaymentLogServiceWiseService;
use App\Helpers\Utility;

class PaymentDashboardController extends BaseController{
    protected $userService,$locationMasterService,$paymentLogService,$paymentLogServiceWiseService="";
    public function __construct(UserService $userService, LocationMasterService $locationMasterService, PaymentLogService $paymentLogService, PaymentLogServiceWiseService $paymentLogServiceWiseService)
    {
        $this->middleware('permission:payment-dashboard', ['only' => ['index']]);
        $this->middleware('auth');
        $this->userService = $userService;
        $this->locationMasterService = $locationMasterService;
        $this->paymentLogService = $paymentLogService;
        $this->paymentLogServiceWiseService = $paymentLogServiceWiseService;
    }

    public function index(){
        $data['agencyList'] = Agency::getAllAgencyListWithoutAnyCondition();
        return view('paymentDashboard.index',$data);
    }

    public function getCountData(Request $request){
        $data = array();
        $countData = $this->paymentLogService->getCountData($request->agency_id);
        $total = $totalremainingPay = $totalreceivedPay = 0;
        foreach($countData as $d){
            if($d['payment_type'] == '866'){
                foreach($d['payment_log_deatil'] as $log){
                    $total += $log['total_amount'];
                    $totalremainingPay += $log['remaining_amount'];
                    $totalreceivedPay += $log['received_amount'];
                }
            }
        }
        $data['total'] = '$'.number_format(floor(floatval($total) * 100) / 100, 2);
        $data['remaining_amount'] = '$'.number_format(floor(floatval($totalremainingPay) * 100) / 100, 2);
        $data['received_amount'] = '$'.number_format(floor(floatval($totalreceivedPay) * 100) / 100, 2);
        return response()->json(['success'=>true,'data'=>$data],200);
    }

    public function locationWiseData(Request $request){
        $data = array();
        $locationWiseData = $this->paymentLogService->locationWiseData($request->agency_id);
        foreach($locationWiseData as $ldata){
            foreach($ldata['payment_log_deatil'] as $pay){
                if(isset($data[$ldata['location_id']]['total_amount'])){
                    $data[$ldata['location_id']]['total_amount'] = $pay['total_amount'] + $data[$ldata['location_id']]['total_amount'];
                }else{
                    $data[$ldata['location_id']]['total_amount'] = $pay['total_amount'];
                }   
                
                if(isset($data[$ldata['location_id']]['received_amount'])){
                    $data[$ldata['location_id']]['received_amount'] = $pay['received_amount'] + $data[$ldata['location_id']]['received_amount'];
                }else{
                    $data[$ldata['location_id']]['received_amount'] = $pay['received_amount'];
                } 

                if(isset($data[$ldata['location_id']]['remaining_amount'])){
                    $data[$ldata['location_id']]['remaining_amount'] = $pay['remaining_amount'] + $data[$ldata['location_id']]['remaining_amount'];
                }else{
                    $data[$ldata['location_id']]['remaining_amount'] = $pay['remaining_amount'];
                } 
            }
        }
        $locationdata = $this->locationMasterService->searchLocation();
        $ag_id = '';
        if(isset($request->agency_id )){
            foreach($request->agency_id as $id){
                $ag_id = $ag_id.'agency_fk[]='.$id.'&';
            }
        }        
        return view('paymentDashboard.location_data',['agency_id'=>$ag_id,'data'=>$data,'locationdata' => $locationdata]);
    }
    
    public function agencyWiseData(Request $request){
        $data = array();
        $agencyWiseData = $this->paymentLogService->agencyWiseData($request->agency_id);
        foreach($agencyWiseData as $ldata){
            foreach($ldata['payment_log_deatil'] as $pay){
                if(isset($data[$ldata['patient_details']['agency_detail']['id']]['total_amount'])){
                    $data[$ldata['patient_details']['agency_detail']['id']]['total_amount'] = $pay['total_amount'] + $data[$ldata['patient_details']['agency_detail']['id']]['total_amount'];
                }else{
                    $data[$ldata['patient_details']['agency_detail']['id']]['total_amount'] = $pay['total_amount'];
                }   
                
                if(isset($data[$ldata['patient_details']['agency_detail']['id']]['received_amount'])){
                    $data[$ldata['patient_details']['agency_detail']['id']]['received_amount'] = $pay['received_amount'] + $data[$ldata['patient_details']['agency_detail']['id']]['received_amount'];
                }else{
                    $data[$ldata['patient_details']['agency_detail']['id']]['received_amount'] = $pay['received_amount'];
                } 

                if(isset($data[$ldata['patient_details']['agency_detail']['id']]['remaining_amount'])){
                    $data[$ldata['patient_details']['agency_detail']['id']]['remaining_amount'] = $pay['remaining_amount'] + $data[$ldata['patient_details']['agency_detail']['id']]['remaining_amount'];
                }else{
                    $data[$ldata['patient_details']['agency_detail']['id']]['remaining_amount'] = $pay['remaining_amount'];
                } 
                $data[$ldata['patient_details']['agency_detail']['id']]['name'] = $ldata['patient_details']['agency_detail']['agency_name'];
            }
        }     
        $agencydata = array_values($data);
        return view('paymentDashboard.agency_data',['agencydata'=>$agencydata]);
    }

    public function servicesWiseData(Request $request){
        $data = array();
        $serviceWiseData = $this->paymentLogService->servicesWiseData($request->agency_id);
        
        foreach($serviceWiseData as $ldata){
            foreach($ldata['payment_log_deatil'] as $pay){
                if(isset($pay['service_details']))
                {
                    if(isset($data[$pay['service_details']['id']]['total_amount'])){
                        $data[$pay['service_details']['id']]['total_amount'] = $pay['total_amount'] + $data[$pay['service_details']['id']]['total_amount'];
                    }else{
                        $data[$pay['service_details']['id']]['total_amount'] = $pay['total_amount'];
                    }   
                    if(isset($data[$pay['service_details']['id']]['received_amount'])){
                        $data[$pay['service_details']['id']]['received_amount'] = $pay['received_amount'] + $data[$pay['service_details']['id']]['received_amount'];
                    }else{
                        $data[$pay['service_details']['id']]['received_amount'] = $pay['received_amount'];
                    } 
                    if(isset($data[$pay['service_details']['id']]['remaining_amount'])){
                        $data[$pay['service_details']['id']]['remaining_amount'] = $pay['remaining_amount'] + $data[$pay['service_details']['id']]['remaining_amount'];
                    }else{
                        $data[$pay['service_details']['id']]['remaining_amount'] = $pay['remaining_amount'];
                    } 
                    
                    $data[$pay['service_details']['id']]['name'] = $pay['service_details']['name'];
                }
            }
        }     
        $servicesdata = array_values($data);
        return view('paymentDashboard.service_data',['servicesdata'=>$servicesdata]);
    }

    public function paymentTypeWiseChartData(Request $request){
        $data = $finaldata = array();
        $payData = $this->paymentLogService->getPaymentTypeWiseChartData($request->agency_id);
        foreach($payData as $d){
            if($d['payment_type'] == 865){
                if(isset($data[$d['payment_type']]) && !empty($data[$d['payment_type']])){
                    $data[$d['payment_type']]++;
                }else{
                    $data[$d['payment_type']] = 1;
                }
            }else if($d['payment_type'] == 866){
                if(isset($data[$d['payment_type']]) && !empty($data[$d['payment_type']])){
                    $data[$d['payment_type']]++;
                }else{
                    $data[$d['payment_type']] = 1;
                }
            }else if($d['payment_type'] == 867){
                if(isset($data[$d['payment_type']]) && !empty($data[$d['payment_type']])){
                    $data[$d['payment_type']]++;
                }else{
                    $data[$d['payment_type']] = 1;
                }
            }
        }
        foreach($data as $key => $d){
            if($key == 865){
                $finaldata[] = array(
                    'count' => (int)$d,
                    'type' => 'Agency Pay'
                );
            }else if($key == 866){
                $finaldata[] = array(
                    'count' => (int)$d,
                    'type' => 'Caregiver Pay'
                );
            }else if($key == 867){
                $finaldata[] = array(
                    'count' => (int)$d,
                    'type' => 'Caregiver Insurance Pay'
                );
            }
        }
        return response()->json(['error_msg' => "Success", 'data' => $finaldata], 200);
    }

    public function monthlyPaymentChartData(Request $request){
        $finaldata = $payData = array();
        $data = array(
                'type' => 'monthly',
                'year' => date('Y')
        );
        $search_data = self::getSearchData($data);
        $payData = $this->paymentLogServiceWiseService->getMonthlyWisePaymentChartData($request->agency_id,$search_data);
        $finaldata = $payData;
        return response()->json(['error_msg' => "Success", 'data' => $finaldata], 200);
    }

    public function getSearchData($data){
        $year = $data['year'];
        // $month = $data['month'];
        // $week = $data['week'];
        $search_data = [];
        if($data['type'] == 'monthly'){
            $group_by = 'month';
            if(empty($year)){
                $search_data = array();
            }else if(empty($month)){
                $search_data = array(
                    'start_date' => $year.'-01-01',
                    'end_date' => $year.'-12-31'
                );     
            }else{
                $search_data = array(
                    'start_date' => $year.'-'.$month.'-01',
                    'end_date' => $year.'-'.$month.'-31'
                );
            }
        }else if($data['type'] == 'weekly'){
            $group_by = 'day';
            if(empty($week) && empty($year) && empty($month)){
                $search_data = array();
            }else{
                if(empty($week)){
                    if(empty($month)){
                        $search_data = array(
                            'start_date' => $year.'-01-01',
                            'end_date' => $year.'-12-31'
                        );     
                    }else{
                        $search_data = array(
                            'start_date' => $year.'-'.$month.'-01',
                            'end_date' => $year.'-'.$month.'-31'
                        ); 
                    }  
                }else{
                    $weeks = explode('-',$week);
                    $search_data = array(
                        'start_date' => $year.'-'.$month.'-'.$weeks[0],
                        'end_date' => $year.'-'.$month.'-'.$weeks[1]
                    );
                }
            }
        }else{
            $group_by = 'year';
            if(!empty($year)){
                $search_data = array(
                    'start_date' => $year.'-01-01',
                    'end_date' => $year.'-12-31'
                );
            }
        } 
        $searchData = array(
                'search_data' => $search_data,
                'group_by' => $group_by
            );
        return $searchData;     
    }
}