<?php
namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Services\HubEligibilityLogService;

class HubRecordEligibilityController extends BaseController
{

	protected $hubEligibilityLogService="";
	public function __construct(HubEligibilityLogService $hubEligibilityLogService)
	{
        $this->middleware('permission:hub-record-eligibility', ['only' => ['hubRecordWiseEligibility']]);
        $this->middleware('auth');

        $this->hubEligibilityLogService = $hubEligibilityLogService;
	}

	public function hubRecordWiseEligibility(Request $request){
		$id = $request->hub_record_id;
		$agencyId = $request->hub_agency_id;
		$data['user'] = auth()->user();
		$data['logList'] = $this->hubEligibilityLogService->getAllHubLogs($id,$agencyId);
	
		return view("hubEligibility.hub_eligibility_ajax_list", $data);
	}


}