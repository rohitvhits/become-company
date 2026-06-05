<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Services\AgencyNotificationService;
use App\Services\PatientV2Service;

class MyDashboardController extends BaseController
{
	protected $agencyNotificationService;
	protected $patientV2Service;

	public function __construct(AgencyNotificationService $agencyNotificationService, PatientV2Service $patientV2Service)
	{
		$this->middleware('auth');
		$this->middleware('permission:my-dashboard', ['only' => ['index', 'getActivityFeedData', 'lastStatusUpdatedData','getActivityFeedUserData']]);
		$this->agencyNotificationService = $agencyNotificationService;
		$this->patientV2Service = $patientV2Service;
	}

	public function index(Request $request)
	{
		return view('myDashboard.index');
	}

	public function getActivityFeedData(Request $request)
	{
		$page = $request->page??1;
		$result = $this->agencyNotificationService->getAgencyActivityFeed($page);
		return response()->json(['status' => true, 'data' => $result]);
	}

	public function lastStatusUpdatedData(Request $request)
	{
		$page = $request->page??1;
		$result = $this->patientV2Service->getLastUpdatedData($page);
		return response()->json(['status' => true, 'data' => $result]);
	}

	public function getActivityFeedUserData(Request $request)
	{
		$page = $request->page??1;
		$result = $this->agencyNotificationService->getAgencyActivityUserFeed($page);
		return response()->json(['status' => true, 'data' => $result]);
	}
}