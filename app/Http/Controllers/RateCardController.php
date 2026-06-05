<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Services\RateCardService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Services\DynamicFormLogService;
use App\Services\AgencyWiseServiceService;
use App\Master;
use Illuminate\Support\Facades\Cache;


class RateCardController extends BaseController
{
	protected $rateCardService, $dynamicFormLogService, $agencyWiseServiceService ='';

	public function __construct(RateCardService $rateCardService, DynamicFormLogService $dynamicFormLogService, AgencyWiseServiceService $agencyWiseServiceService)
	{
		$this->middleware('auth');
		$this->middleware('permission:rate-card-list', ['only' => ['index', 'rateCardList','rateCardById']]);
		$this->rateCardService = $rateCardService;
		$this->dynamicFormLogService = $dynamicFormLogService;
		$this->agencyWiseServiceService = $agencyWiseServiceService;
	}

    public function index(){
        $data['menu'] = "";
        $data['user'] = $user= $auth = auth()->user();		
		if (!$auth || $auth == null) {
			return redirect('login');
		}
        $data['serviceList'] = Cache::get('patient_master_services', function ()  use ($user) {
            $agencyId = $user->agency_fk;
			$getAgencyWiseList = $this->agencyWiseServiceService->getServiceNew($agencyId, "");
			if (!empty($getAgencyWiseList[0])) {
				return  $getAgencyWiseList;
			} else {
				return  Master::getServiceRequest(1)->whereIn('types',['Caregiver','Patient']);
			}
		}, 10 * 60);
        return view('rateCard/rate_card_list', $data);        
    }

	public function store(Request $request)
	{        
		$validator = Validator::make($request->all(), [
			'add_service_id' => ['required',
                            function ($attribute, $value, $fail) use ($request) {
								if(isset($request->agency_id)){
									$agency_id = $request->agency_id;
								}else{
									$agency_id =  '0';
								}
                                $exists = \DB::table('rate_card')
                                    ->where('service_id', $value)
                                    ->where('deleted_flag', 'N')
									->where('agency_id', '=', $agency_id)
                                    ->exists();

                                if ($exists) {
                                    $fail('This service has already been taken.');
                                }
                            },
                        ],
			'amount' => 'required',
		]);
		if ($validator->fails()) {
			return response()->json([
				'error_msg' => $validator->errors()->all()[0],
				'status' => false,
			], 422);
		} else {
			$data = array(
				'service_id' => $request->add_service_id,
				'amount' => round($request->amount,2),
			);
			if(isset($request->agency_id)){
                $data['agency_id'] = $request->agency_id;
            }
			$insert = $this->rateCardService->save($data);
			
			if ($insert) {

				$getNewData = $this->rateCardService->getDetailById($insert);				
				// Insert form Log into Dynamic form log table
				$insertLog = [
					'type' => 'Add',
					'link' => url('/rate-card'),
					'module' => 'Rate Card',
					'module_id' => $getNewData->id,
					'new_response' => serialize($getNewData->toArray())

				];
				$this->dynamicFormLogService->storeFormLog($insertLog);

                return response()->json(['status' => true, 'error_msg' => 'Rate Card created successfully'], 200);
			} else {
				return response()->json(['status' => true, 'error_msg' => 'Sorry, something went wrong. Please try again'], 500);
			}
		}
	}

	public function update(Request $request,$id)
	{		
		$validator = Validator::make($request->all(), [
			'edit_service_id' => ['required',
                    function ($attribute, $value, $fail) use ($id, $request){
						if(isset($request->agency_id)){
							$agency_id = $request->agency_id;
						}else{
							$agency_id =  '0';
						}
                        $exists = \DB::table('rate_card')
                                ->where('service_id', $value)
                                ->where('deleted_flag', 'N')
                                ->where('id', '!=', $id) // Ignore current ID
                                ->where('agency_id', '=', $agency_id) // Ignore current ID
                                ->exists();

                        if ($exists) {
                            $fail('This service has already been takend.');
                        }
                    },
                ],
			'amount' => 'required',
		]);
		if ($validator->fails()) {
			return response()->json([
				'error_msg' => $validator->errors()->all()[0],
				'status' => false,
			], 422);
		} else {
			
			$getExistingData = $this->rateCardService->getDetailById($id);
			$data = array(
				'service_id' => $request->edit_service_id,
				'amount' => round($request->amount,2),
			);
			if(isset($request->agency_id)){
                $data['agency_id'] = $request->agency_id;
            }
			$this->rateCardService->update($data, array('id' => $id));
			$getNewData = $this->rateCardService->getDetailById($id);
			// Insert form Log into Dynamic form log table
			$insertLog = [
				'type' => 'Update',
				'link' => url('/rate-card'),
				'module' => 'Rate Card',
				'module_id' => $getNewData->id,
				'new_response' => serialize($getNewData),
				'old_response' => serialize($getExistingData)
			];
			$this->dynamicFormLogService->storeFormLog($insertLog);
			return response()->json(['status' => true, 'error_msg' => 'Rate Card updated successfully'], 200);
		}
	}

	public function destroy($id)
	{
		$update = $this->rateCardService->SoftDelete(array('id' => $id));
		if ($update) {
			// Insert form Log into Dynamic form log table
			$insertLog = [
				'type' => 'Delete',
				'link' => url('/rate-card'),
				'module' => 'Rate Card',
				'module_id' => $id
			];
			$this->dynamicFormLogService->storeFormLog($insertLog);

			return response()->json(['status' => "1", 'error_msg' => "Rate Card successfully deleted.", 'data' => array()], 200);
		} else {
			return response()->json(['status' => "0", 'error_msg' => "Sorry, something went wrong. Please try again.", 'data' => array()], 500);
		}
	}

	public function rateCardList(Request $request)
	{
		$data['query'] = $this->rateCardService->rateCardList();
		return view("rateCard.rate_card_ajax_list", $data);
	}

	public function rateCardById(Request $request)
	{
		$response = $this->rateCardService->getDetailById($request->id);
		return response()->json(['status' => true, 'data' => $response]);
	}

    public function agencyWiseData(Request $request){
        $data['query'] = $this->rateCardService->getDetailAgencyWise($request->agency_id);
		return view("rateCard.rate_card_ajax_list", $data);
    }
}