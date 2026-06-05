<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\DynamicFormLogService;
use App\Services\AgencyTeleServicesService;
use App\Master;
use Illuminate\Support\Facades\Cache;


class TeleHealthServiceController extends BaseController
{
	protected $agencyTeleService, $dynamicFormLogService, $agencyWiseServiceService ='';

	public function __construct(AgencyTeleServicesService $agencyTeleService,DynamicFormLogService $dynamicFormLogService)
	{
		$this->middleware('auth');
		$this->middleware('permission:rate-card-list', ['only' => ['index']]);
		$this->agencyTeleService = $agencyTeleService;
		$this->dynamicFormLogService = $dynamicFormLogService;
	}

 

	public function store(Request $request)
	{        
		$validator = Validator::make($request->all(), [
			'add_tele_type' => 'required',
			'add_agency_tele_service' => 'required',
		]);
		if ($validator->fails()) {
			return response()->json([
				'error_msg' => $validator->errors()->all()[0],
				'status' => false,
			], 422);
		} else {
			$data = array(
				'type' => $request->add_tele_type,
                'service_id' => $request->add_agency_tele_service,
                'agency_id' => $request->agency_id,
			);
			$insert = $this->agencyTeleService->save($data);
			
			if ($insert) {
				$getNewData = $this->agencyTeleService->getDetailById($insert);				
				// Insert form Log into Dynamic form log table
				$insertLog = [
					'type' => 'Add',
					'link' => url('/agency-tele-service'),
					'module' => 'Agency Telehealth Service',
					'module_id' => $getNewData->id,
					'new_response' => serialize($getNewData->toArray())

				];
				$this->dynamicFormLogService->storeFormLog($insertLog);

                return response()->json(['status' => true, 'error_msg' => 'Telehealth Services created successfully'], 200);
			} else {
				return response()->json(['status' => true, 'error_msg' => 'Sorry, something went wrong. Please try again'], 500);
			}
		}
	}

	public function update(Request $request,$id)
	{		
		$validator = Validator::make($request->all(), [
			'edit_tele_type' => 'required',
			'edit_agency_tele_service' => 'required',
		]);
		if ($validator->fails()) {
			return response()->json([
				'error_msg' => $validator->errors()->all()[0],
				'status' => false,
			], 422);
		} else {
			
			$getExistingData = $this->agencyTeleService->getDetailById($id);
			$data = array(
				'service_id' => $request->edit_agency_tele_service,
                'type' => $request->edit_tele_type
			);
			$this->agencyTeleService->update($data, array('id' => $id));
			$getNewData = $this->agencyTeleService->getDetailById($id);
			// Insert form Log into Dynamic form log table
			$insertLog = [
				'type' => 'Update',
				'link' => url('/agency-tele-service'),
				'module' => 'Agency Telehealth Service',
				'module_id' => $getNewData->id,
				'new_response' => serialize($getNewData),
				'old_response' => serialize($getExistingData)
			];
			$this->dynamicFormLogService->storeFormLog($insertLog);
			return response()->json(['status' => true, 'error_msg' => 'Agency Telehealth Service updated successfully'], 200);
		}
	}

	public function destroy($id)
	{
		$update = $this->agencyTeleService->SoftDelete(array('id' => $id));
		if ($update) {
			// Insert form Log into Dynamic form log table
			$insertLog = [
				'type' => 'Delete',
				'link' => url('/agency-tele-service'),
				'module' => 'Agency Telehealth Service',
				'module_id' => $id
			];
			$this->dynamicFormLogService->storeFormLog($insertLog);

			return response()->json(['status' => "1", 'error_msg' => "Agency Telehealth Service successfully deleted.", 'data' => array()], 200);
		} else {
			return response()->json(['status' => "0", 'error_msg' => "Sorry, something went wrong. Please try again.", 'data' => array()], 500);
		}
	}

	public function getTelehealthServiceList(Request $request)
	{
		$agency_id = $request->agency_id;
		$data['query'] = $this->agencyTeleService->agencyTeleServiceList($agency_id);
		return view("agency._partial.agency_service_ajax_list", $data);
	}

	public function getTelehealthServiceListById(Request $request)
	{
		$response = $this->agencyTeleService->getDetailById($request->id);
		return response()->json(['status' => true, 'data' => $response]);
	}
}