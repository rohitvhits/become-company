<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CommunicationService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Storage;
use App\Services\DynamicFormLogService;
use Cookie;
use App\Jobs\AnnouncementJob;
class CommunicationController extends BaseController
{
	protected $communicationService, $dynamicFormLogService ='';

	public function __construct(CommunicationService $communicationService, DynamicFormLogService $dynamicFormLogService)
	{
		$this->middleware('auth');
		$this->middleware('permission:announcements-list', ['only' => ['index', 'eventList','eventById']]);
		$this->communicationService = $communicationService;
		$this->dynamicFormLogService = $dynamicFormLogService;
	}

    public function index(){
        $data['menu'] = "";
        $data['user'] = $auth = auth()->user();
		if (!$auth || $auth == null) {
			return redirect('login');
		}
        return view('communication/communication_list', $data);        
    }

	public function save(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'title' => 'required',
			'content' => 'required',
			'start_date' => 'required',
			'end_date' => 'required',
			
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
					$destination = public_path('announcements-image');
					$image->move($destination, $name);
					
				} else {
					Storage::disk('s3')->putFileAs('announcements-image', $image, $name);
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
		
			$insert = $this->communicationService->save($data);
			
			if ($insert) {

				$getNewData = $this->communicationService->getDetailById($insert);				
				// Insert form Log into Dynamic form log table
				$insertLog = [
					'type' => 'Add',
					'link' => url('/announcements-save'),
					'module' => 'Announcements Master',
					'module_id' => $getNewData->id,
					'new_response' => serialize($getNewData->toArray())

				];
				$this->dynamicFormLogService->storeFormLog($insertLog);

                return response()->json(['status' => true, 'error_msg' => 'Announcements created successfully'], 200);
			} else {
				return response()->json(['status' => true, 'error_msg' => 'Sorry, something went wrong. Please try again'], 500);
			}
		}
	}

	public function update(Request $request)
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
			$getExistingData = $this->communicationService->getDetailById($request->id);
			$image = '';
			if ($request->file('image') != '') {
				
				$eventImage = $request->file('image');
				$name = uniqid() . time() . '.' . $eventImage->getClientOriginalExtension();
				if (env('FILE_UPLOAD_PERMISSION')  == 'development') {
					$destination = public_path('announcements-image');
					$eventImage->move($destination, $name);
				} else {
					$image = Storage::disk('s3')->putFileAs('announcements-image', $eventImage, $name);
				}

				$image = $name;
			}

			$data = array(
				'title' => $request->title,
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

			

			$insert = $this->communicationService->update($data, array('id' => $request->id));
			$getNewData = $this->communicationService->getDetailById($request->id);

			// Insert form Log into Dynamic form log table
			$insertLog = [
				'type' => 'Update',
				'link' => url('/announcements/update'),
				'module' => 'Announcements Master',
				'module_id' => $getNewData->id,
				'new_response' => serialize($getNewData),
				'old_response' => serialize($getExistingData)
			];
			$this->dynamicFormLogService->storeFormLog($insertLog);

			return response()->json(['status' => true, 'error_msg' => 'Announcements updated successfully'], 200);
		}
	}

	public function destory(Request $request)
	{
		$update = $this->communicationService->SoftDelete(array('id' => $request->id));
		if ($update) {
			// Insert form Log into Dynamic form log table
			$insertLog = [
				'type' => 'Delete',
				'link' => url('/announcements-delete'),
				'module' => 'Announcements Master',
				'module_id' =>$request->id
			];
			$this->dynamicFormLogService->storeFormLog($insertLog);

			return response()->json(['status' => "1", 'error_msg' => "Announcements successfully deleted.", 'data' => array()], 200);
		} else {
			return response()->json(['status' => "0", 'error_msg' => "Sorry, something went wrong. Please try again.", 'data' => array()], 500);
		}
	}

	public function eventList(Request $request)
	{
		$query= $this->communicationService->eventList();
		if(!empty($query[0])){
			foreach($query as  $val){
				$val->contents = strlen($val->content) > 50 ? strip_tags(substr($val->content, 0, 50)) . '...' : $val->content;
			}
		}
		$data['query'] =  $query;
		return view("communication.communication_ajax_list", $data);
	}

	public function eventById(Request $request)
	{
		$response = $this->communicationService->getDetailById($request->id);
		return response()->json(['status' => true, 'data' => $response]);
	}

	public function changeStatus(Request $request){
		$response = $this->communicationService->getDetailById($request->id);
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
		$insert = $this->communicationService->update($data, array('id' => $request->id));
		return response()->json(['status' => true, 'data' => $data,'error_msg'=>'Status successfully updated']);

	}

	function eventImages(Request $request,$id){
		$query = $this->communicationService->getDetailById($id);
        if (isset($query->id)) {
            $file = public_path('/') . "/announcements-image/" . $query->image;
                if (file_exists($file)) {
                    $headers = [];
                    return response()->download($file, $query->image, $headers);
                }else{
                    return Storage::disk('s3')->download('/announcements-image/' . $query->image);
                    die();
                }
        } else {
            abort(404);
        }
	}

	public function announcementsMailAllUsers(Request $request){
		$getNewData = $this->communicationService->getDetailById($request->id);
		$data =['message'=>$getNewData->content,'id'=>$getNewData->id,'image'=>$getNewData->image,'title'=>$getNewData->title];
		AnnouncementJob::dispatch($data);
		return response()->json(['status' => true, 'data' => $data,'error_msg'=>'Announcements mail succssfully send']);
	}
}
 
