<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StampService;
use App\Services\DynamicFormLogService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Storage;
class ApproveStampController extends BaseController
{
	protected $stampService, $dynamicFormLogService = '';

	public function __construct(StampService $stampService, DynamicFormLogService $dynamicFormLogService)
	{
		$this->middleware('auth');
		$this->middleware('permission:approve-stamp-list', ['only' => ['index', 'approveStampList','approveStampById']]);
		$this->stampService = $stampService;
		$this->dynamicFormLogService = $dynamicFormLogService;
	}

    public function index(){
        $data['menu'] = "";
        $data['user'] = $auth = auth()->user();
		if (!$auth || $auth == null) {
			return redirect('login');
		}
        return view('approve_stamp/approve_stamp_list', $data);        
    }

	public function store(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'title' => 'required',
		]);
		if ($validator->fails()) {
			return response()->json([
				'error_msg' => $validator->errors()->all()[0],
				'status' => false,
			], 422);
		} else {
			$name ="";
            if ($request->file('image') != '') {
			    $image = '';
				$stampImage = $request->file('image');
				$name = uniqid() . time() . '.' . $stampImage->getClientOriginalExtension();
				if (env('FILE_UPLOAD_PERMISSION')  == 'development') {
					$destination = public_path('stamp-image');
					$stampImage->move($destination, $name);
					
				} else {
					Storage::disk('s3')->putFileAs('stamp-image', $stampImage, $name);
				}
			}
			$data = array(
				'title' => $request->input('title'),
				'image'=>$name
			);
			
			$insert = $this->stampService->save($data);
			
			if ($insert) {

				$getNewData = $this->stampService->getDetailById($insert);				
				// Insert form Log into Dynamic form log table
				$insertLog = [
					'type' => 'Add',
					'link' => url('/approve-stamp'),
					'module' => 'Approve Stamp',
					'module_id' => $getNewData->id,
					'new_response' => serialize($getNewData->toArray())

				];
				$this->dynamicFormLogService->storeFormLog($insertLog);

                return response()->json(['status' => true, 'error_msg' => 'Stamp created successfully'], 200);
			} else {
				return response()->json(['status' => true, 'error_msg' => 'Sorry, something went wrong. Please try again'], 500);
			}
		}
	}

	public function update(Request $request,$id)
	{		
		$validator = Validator::make($request->all(), [
			'title' => 'required',
		]);
		if ($validator->fails()) {
			return response()->json([
				'error_msg' => $validator->errors()->all()[0],
				'status' => false,
			], 422);
		} else {
			$getExistingData = $this->stampService->getDetailById($id);
			if ($request->file('stamp_image') != '') {
				$image = '';
				$stampImage = $request->file('stamp_image');
				$name = uniqid() . time() . '.' . $stampImage->getClientOriginalExtension();
				if (env('FILE_UPLOAD_PERMISSION')  == 'development') {
					$destination = public_path('stamp-image');
					$stampImage->move($destination, $name);
				} else {
					$image = Storage::disk('s3')->putFileAs('stamp-image', $stampImage, $name);
				}

				$image = $name;
			}
			if ($id != '') {
				$data = array(
					'title' => $request->input('title'),
				);
				if (isset($image) && !empty($image)) {
					$data['image'] = $image;
				}
				$insert = $this->stampService->update($data, array('id' => $id));
			}
			if ($insert) {

				$getNewData = $this->stampService->getDetailById($id);

				// Insert form Log into Dynamic form log table
				$insertLog = [
					'type' => 'Update',
					'link' => url('/approve-stamp'),
					'module' => 'Approve Stamp',
					'module_id' => $getNewData->id,
					'new_response' => serialize($getNewData),
					'old_response' => serialize($getExistingData)
				];
				$this->dynamicFormLogService->storeFormLog($insertLog);

               	return response()->json(['status' => true, 'error_msg' => 'Stamp updated successfully'], 200);
			} else {
				return response()->json(['status' => true, 'error_msg' => 'Sorry, something went wrong. Please try again'], 500);
			}
		}
	}

	public function destroy($id)
	{
		$update = $this->stampService->SoftDelete(array('id' => $id));
		if ($update) {
			// Insert form Log into Dynamic form log table
			$insertLog = [
				'type' => 'Delete',
				'link' => url('/approve-stamp'),
				'module' => 'Approve Stamp',
				'module_id' => $id
			];
			$this->dynamicFormLogService->storeFormLog($insertLog);

			return response()->json(['status' => "1", 'error_msg' => "Stamp successfully deleted.", 'data' => array()], 200);
		} else {
			return response()->json(['status' => "0", 'error_msg' => "Sorry, something went wrong. Please try again.", 'data' => array()], 500);
		}
	}

	public function approveStampList(Request $request)
	{
		$data['query'] = $this->stampService->stampList();
		return view("approve_stamp.approve_stamp_ajax_list", $data);
	}

	public function approveStampById(Request $request)
	{
		$response = $this->stampService->getDetailById($request->id);
		return response()->json(['status' => true, 'data' => $response]);
	}

	
	public function stampStatus(Request $request){
		$checkDefault = $this->stampService->checkDefaultRecordExist($request->id);		
		if(isset($checkDefault[0]->id) && !empty($checkDefault[0]->id)){
			return response()->json(['status' => false, 'error_msg' => 'Stamp is already associated with '.$checkDefault[0]->title.'.'], 200);
		}else{
			$updateDefaultStatus = $this->stampService->updateStatus($request->id,$request->is_default);

			if ($updateDefaultStatus) {
				$isDefault = $request['is_default'] == 0 ? 'disabled' : 'enabled';
				return response()->json(['status' => true, 'error_msg' => 'Stamp ' . $isDefault . ' sucessfully'], 200);
			}
		}
	}
}
 
