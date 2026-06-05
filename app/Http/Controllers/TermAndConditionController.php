<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use App\Services\CMSService;
use App\User;
use Illuminate\Http\Request;
use App\Services\IpInfoService;
use App\Model\TermConditionLog;
class TermAndConditionController extends BaseController{
    protected $cmsService = "";
    public function __construct(CMSService $cmsService)
    { 
        $this->cmsService = $cmsService;
    }

    public function index(){
        $data['details'] = $this->cmsService->getDetailsById(2);
       
        return view('TermAndCondition.index_dynamic',$data);
    }

    public function privacyPolicy(){
        $data['details'] = $this->cmsService->getDetailsById(1);
       return view('privacyPolicy.index',$data);
    }

    public function loadTermAndCondition(){
        $details = $this->cmsService->getDetailsById(2);
        $privacy = $this->cmsService->getDetailsById(1);
        return response()->json(['error_msg'=>'success','data'=>array('term'=>$details,'privacy'=>$privacy)]);
    }

    public function saveTermCondition(Request $request){
        $auth = auth()->user();
        if(!$auth){
            return response()->json(['error_msg'=>'Unauthorization','data'=>[]],500);
        }
        $browserIP = $_SERVER['HTTP_USER_AGENT'];
        $ipaddress = request()->getClientIp();
        $ipAddress = $this->getUserIP();
        //$browserDetails = get_browser(null, true);
        $browserDetails = "";
        $ipDetails = IpInfoService::ipInfo($ipAddress);
  
        $dataUpdate = [];
        $terms ='';
        $privacy ='';
        if(isset($request->privacyChecked)){
            $dataUpdate['privacy_policy'] = 1;
            $privacy=1;
        }
        if(isset($request->termsChecked)){
            $dataUpdate['term_condition'] = 1;
            $terms =1;
        }
        $log = array('browser_ip' => $browserIP, 'browser_details' => $browserDetails, "ip_details" => $ipDetails);
        $update = User::where('id',$auth->id)->update($dataUpdate);
        $dataSave = ['user_id'=>$auth->id,'accepted_date'=>date('Y-m-d H:i:s'),'ip_address'=>$ipAddress,'broswer_details'=>json_encode($log),'privacy_policy_read'=>$privacy,'term_condition_read'=>$terms];
        $saveLog = new TermConditionLog($dataSave);
        $saveLog->save();
        
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