<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\EbookService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Storage;
use App\Services\EmmacareReferalTableService;
use App\Helpers\Utility;
use App\Services\InsuranceMasterService;
use Illuminate\Support\Facades\Cache;
use App\Model\Language;
class EmmacareReferalTableController extends BaseController
{
	protected $emmacareReferalTableService,$insuranceMasterService ='';

	public function __construct(EmmacareReferalTableService $emmacareReferalTableService,InsuranceMasterService $insuranceMasterService)
	{
		$this->middleware('auth');
        $this->middleware('permission:emmacare-referal-report', ['only' => ['index', 'ajaxList']]);
        $this->middleware('permission:emmacare-referal-report-export', ['only' => ['exportCsv']]);
		$this->emmacareReferalTableService = $emmacareReferalTableService;
		$this->insuranceMasterService = $insuranceMasterService;
	}

    public function index(){
        $data['menu'] = "";
        $data['user'] = $auth = auth()->user();		
		if (!$auth || $auth == null) {
			return redirect('login');
		}

        $data['insuranceList'] = $this->insuranceMasterService->getInsuranceMasterList();

        $data['language_list'] =  Cache::get('language_list', function ()  use ($auth) {
            return Language::getLanguageList();
        }, 10 * 60);

        return view('emmacareReferalTable/list', $data);        
    }

	public function ajaxList(Request $request){
      
        $query = $this->emmacareReferalTableService->getList($request->all());
        $getReferraks = Utility::getRemoteReferralSourceId();

        $referrs = [];
        foreach($getReferraks as $key=>$vas){
            $referrs[$key] = $vas;
        }
        if(!empty($query[0])){
            foreach($query as $val){
                $val->dob = date('m/d/Y',strtotime($val->dob));
                $val->referral_uid = $referrs[$val->referral_uid]??"";
            }
        }

        $data['query'] = $query;
        return view('emmacareReferalTable/ajax_list', $data);    
    }

    public function exportCsv(Request $request){
        $query = $this->emmacareReferalTableService->getList($request->all());
        $getReferraks = Utility::getRemoteReferralSourceId();
 
        $referrs = [];
        foreach($getReferraks as $key=>$vas){
            $referrs[$key] = $vas;
        }
        if(!empty($query[0])){
            foreach($query as $val){
              
                $val->referral_uid = $referrs[$val->referral_uid];
            }
        }

        $filename = 'emmacare' . date("m-d-Y");
        $headers = array(
			"Content-type" => "text/csv",
			"Content-Disposition" => "attachment; filename=" . $filename . ".csv",
			"Pragma" => "no-cache",
			"Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
			"Expires" => "0",
		);
        $columns = array('Record Id', 'Full Name', 'Date of Birth', 'Gender', 'Language', 'Mobile', 'Insurance', 'Referral Uid','Emmacare Response','Created Date', 'Created By');

        $callback = function () use ($query, $columns) {
			$file = fopen('php://output', 'w');
			fputcsv($file, $columns);

            foreach($query as $list){
                $createdDate  = '';
				if ($list->created_at != '' && $list->created_at !="0000-00-00 00:00:00") {
					$createdDate = date('m/d/Y h:i A', strtotime($list->created_at));
				}

                $firstName = "";
                $lastName = "";
                if(isset($list->userDetaials->first_name)){
                    $firstName = $list->userDetaials->first_name;
                    $lastName = $list->userDetaials->last_name;
                }

                $response  = unserialize($list->return_response);
                $emmacareData = "";
                if(isset($response['uuid'])){
                    $emmacareData =$response['uuid'];
                }
                fputcsv($file, array($list->record_id, $list->first_name.' '.$list->last_name,date('m/d/Y',strtotime($list->dob)), $list->gender, $list->language, $list->phones, $list->insurance, $list->referral_uid,$emmacareData,$createdDate, $firstName.' '.$lastName));
              
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }
}