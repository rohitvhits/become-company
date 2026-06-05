<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\EventService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Storage;
use App\Services\DynamicFormLogService;
use Cookie;
class EventMasterController extends BaseController
{
	protected $eventService, $dynamicFormLogService ='';

	public function __construct(EventService $eventService, DynamicFormLogService $dynamicFormLogService)
	{
		$this->middleware('auth');
		$this->middleware('permission:event-master-list', ['only' => ['index', 'eventList','eventById']]);
		$this->eventService = $eventService;
		$this->dynamicFormLogService = $dynamicFormLogService;
	}

    public function index(){
        $data['menu'] = "";
        $data['user'] = $auth = auth()->user();
		if (!$auth || $auth == null) {
			return redirect('login');
		}
        return view('event_master/event_master_list', $data);        
    }

	public function store(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'title' => 'required',
			'content' => 'required',
			
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
				$image = $request->file('image');
				$name = uniqid() . time() . '.' . $image->getClientOriginalExtension();
				if (env('FILE_UPLOAD_PERMISSION')  == 'development') {
					$destination = public_path('event-image');
					$image->move($destination, $name);
					
				} else {
					Storage::disk('s3')->putFileAs('event-image', $image, $name);
				}
			}
			$auth = auth()->user();
			$data = array(
				'title' => $request->input('title'),
				'content' => $_REQUEST['content'],
				'status' => 1,
				
				'image'=>$name
			);
			if($request->input('start_date') !=""){
				$data['start_date'] =date('Y-m-d',strtotime($request->input('start_date')));
			}

			if($request->input('end_date') !=""){
				$data['end_date'] = date('Y-m-d',strtotime($request->input('end_date')));
			}
			$insert = $this->eventService->save($data);
			
			if ($insert) {

				$getNewData = $this->eventService->getDetailById($insert);				
				// Insert form Log into Dynamic form log table
				$insertLog = [
					'type' => 'Add',
					'link' => url('/event-master'),
					'module' => 'Popup Master',
					'module_id' => $getNewData->id,
					'new_response' => serialize($getNewData->toArray())

				];
				$this->dynamicFormLogService->storeFormLog($insertLog);

                return response()->json(['status' => true, 'error_msg' => 'Popup created successfully'], 200);
			} else {
				return response()->json(['status' => true, 'error_msg' => 'Sorry, something went wrong. Please try again'], 500);
			}
		}
	}

	public function update(Request $request,$id)
	{		
		$auth = auth()->user();
		$validator = Validator::make($request->all(), [
			'title' => 'required',
			'content' => 'required',
			
		]);
		if ($validator->fails()) {
			return response()->json([
				'error_msg' => $validator->errors()->all()[0],
				'status' => false,
			], 422);
		} else {
			$getExistingData = $this->eventService->getDetailById($id);
			$image = '';
			if ($request->file('image') != '') {
				
				$eventImage = $request->file('image');
				$name = uniqid() . time() . '.' . $eventImage->getClientOriginalExtension();
				if (env('FILE_UPLOAD_PERMISSION')  == 'development') {
					$destination = public_path('event-image');
					$eventImage->move($destination, $name);
				} else {
					$image = Storage::disk('s3')->putFileAs('event-image', $eventImage, $name);
				}

				$image = $name;
			}

			$data = array(
				'title' => $request->input('title'),
				'content' => $_REQUEST['content'],
				
			);

			if($request->input('start_date') !=""){
				$data['start_date'] =date('Y-m-d',strtotime($request->input('start_date')));
			}

			if($request->input('end_date') !=""){
				$data['end_date'] = date('Y-m-d',strtotime($request->input('end_date')));
			}
			if ($image !="") {
				$data['image'] = $image;
			}

			if($request->input('start_date') !=""){
				$data['start_date'] =date('Y-m-d',strtotime($request->input('start_date')));
			}

			if($request->input('end_date') !=""){
				$data['end_date'] = date('Y-m-d',strtotime($request->input('end_date')));
			}

			$insert = $this->eventService->update($data, array('id' => $id));
			$getNewData = $this->eventService->getDetailById($id);

			// Insert form Log into Dynamic form log table
			$insertLog = [
				'type' => 'Update',
				'link' => url('/event-master'),
				'module' => 'Popup Master',
				'module_id' => $getNewData->id,
				'new_response' => serialize($getNewData),
				'old_response' => serialize($getExistingData)
			];
			$this->dynamicFormLogService->storeFormLog($insertLog);

			   return response()->json(['status' => true, 'error_msg' => 'Popup updated successfully'], 200);
		}
	}

	public function destroy($id)
	{
		$update = $this->eventService->SoftDelete(array('id' => $id));
		if ($update) {
			// Insert form Log into Dynamic form log table
			$insertLog = [
				'type' => 'Delete',
				'link' => url('/event-master'),
				'module' => 'Popup Master',
				'module_id' => $id
			];
			$this->dynamicFormLogService->storeFormLog($insertLog);

			return response()->json(['status' => "1", 'error_msg' => "Popup successfully deleted.", 'data' => array()], 200);
		} else {
			return response()->json(['status' => "0", 'error_msg' => "Sorry, something went wrong. Please try again.", 'data' => array()], 500);
		}
	}

	public function eventList(Request $request)
	{
		$data['query'] = $this->eventService->eventList();
		return view("event_master.event_master_ajax_list", $data);
	}

	public function eventById(Request $request)
	{
		$response = $this->eventService->getDetailById($request->id);
		return response()->json(['status' => true, 'data' => $response]);
	}

	public function activeEvents(){
		$auth = auth()->user();
		
		$date = "";
		$cookieKey = 'userLogin' . $auth->id;
$date = Cookie::get($cookieKey);

		$response = $this->eventService->getActiveEvent();

		return response()->json(['status' => true, 'data' => $response,'date'=>$date,'cookie_date'=>""]);
	}

	public function changeStatus(Request $request){
		$response = $this->eventService->getDetailById($request->id);
		$data = [];
		if($response->status ==1){
			$status = 0;	
			$data['deactivated_datetime'] = date('Y-m-d H:i:s');
			$data['deactivated_by'] = auth()->user()->id;
		}else{
			$status = 1;
			if($request->input('start_date') !=""){
				$data['start_date'] =date('Y-m-d',strtotime($request->input('start_date')));
			}

			if($request->input('end_date') !=""){
				$data['end_date'] = date('Y-m-d',strtotime($request->input('end_date')));
			}
			$data['deactivated_datetime'] = null;
			$data['deactivated_by'] = null;

		}

		$data['status'] = $status;
		$insert = $this->eventService->update($data, array('id' => $request->id));
		return response()->json(['status' => true, 'data' => $data,'error_msg'=>'Status successfully updated']);

	}
}
 
