<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\EbookService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Storage;
use App\Services\DynamicFormLogService;
use App\Helpers\Utility;
class EbookController extends BaseController
{
	protected $ebookService, $dynamicFormLogService ='';

	public function __construct(EbookService $ebookService, DynamicFormLogService $dynamicFormLogService)
	{
		$this->middleware('auth');
		$this->middleware('permission:ebook-list', ['only' => ['index', 'ebookList','ebookById']]);
		$this->ebookService = $ebookService;
		$this->dynamicFormLogService = $dynamicFormLogService;
	}

    public function index(){
        $data['menu'] = "";
        $data['user'] = $auth = auth()->user();
		if (!$auth || $auth == null) {
			return redirect('login');
		}
        return view('ebook/ebook_list', $data);
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
            if ($request->file('video') != '') {
			    $video = '';
				$video = $request->file('video');
				$name = uniqid() . time() . '.' . $video->getClientOriginalExtension();
				if (env('FILE_UPLOAD_PERMISSION')  == 'development') {
					$destination = public_path('ebook-video');
					$video->move($destination, $name);
				} else {
					Storage::disk('s3')->putFileAs('ebook-video', $video, $name);
				}
			}
	
			$data = array(
				'title' => $request->input('title'),
				'content' => $_REQUEST['content'],
				'type' => implode(',',$request->input('type')),
				'video'=>$name
			);
			
			$insert = $this->ebookService->save($data);
			
			if ($insert) {

				$getNewData = $this->ebookService->getDetailById($insert);
				// Insert form Log into Dynamic form log table
				$insertLog = [
					'type' => 'Add',
					'link' => url('/ebook'),
					'module' => 'Ebook',
					'module_id' => $getNewData->id,
					'new_response' => serialize($getNewData->toArray())

				];
				$this->dynamicFormLogService->storeFormLog($insertLog);

                return response()->json(['status' => true, 'error_msg' => 'Ebook created successfully'], 200);
			} else {
				return response()->json(['status' => true, 'error_msg' => 'Sorry, something went wrong. Please try again'], 500);
			}
		}
	}

	public function update(Request $request,$id)
	{
	
		$validator = Validator::make($request->all(), [
			'title' => 'required',
			'content' => 'required'
		]);
		if ($validator->fails()) {
			return response()->json([
				'error_msg' => $validator->errors()->all()[0],
				'status' => false,
			], 422);
		} else {
			
			$getExistingData = $this->ebookService->getDetailById($id);
			if ($request->file('video') != '') {
				$video = '';
				$ebook_Video = $request->file('video');
				$name = uniqid() . time() . '.' . $ebook_Video->getClientOriginalExtension();
				if (env('FILE_UPLOAD_PERMISSION')  == 'development') {
					$destination = public_path('ebook-video');
					$ebook_Video->move($destination, $name);
				} else {
					$video = Storage::disk('s3')->putFileAs('ebook-video', $ebook_Video, $name);
				}

				$video = $name;
			}
			$data = array(
				'title' => $request->input('title'),
				'content' => $_REQUEST['content'],
				'type' => implode(',',$request->input('type')),
			);
			
			if (isset($video) && !empty($video)) {
				$data['video'] = $video;
			}
			$this->ebookService->update($data, array('id' => $id));
			$getNewData = $this->ebookService->getDetailById($id);
			// Insert form Log into Dynamic form log table
			$insertLog = [
				'type' => 'Update',
				'link' => url('/ebook'),
				'module' => 'Ebook',
				'module_id' => $getNewData->id,
				'new_response' => serialize($getNewData),
				'old_response' => serialize($getExistingData)
			];
			$this->dynamicFormLogService->storeFormLog($insertLog);
			return response()->json(['status' => true, 'error_msg' => 'Ebook updated successfully'], 200);
		}
	}

	public function destroy($id)
	{
		$update = $this->ebookService->SoftDelete(array('id' => $id));
		if ($update) {
			// Insert form Log into Dynamic form log table
			$insertLog = [
				'type' => 'Delete',
				'link' => url('/ebook'),
				'module' => 'Ebook',
				'module_id' => $id
			];
			$this->dynamicFormLogService->storeFormLog($insertLog);

			return response()->json(['status' => "1", 'error_msg' => "Ebook successfully deleted.", 'data' => array()], 200);
		} else {
			return response()->json(['status' => "0", 'error_msg' => "Sorry, something went wrong. Please try again.", 'data' => array()], 500);
		}
	}

	public function ebookList(Request $request)
	{
		$data['query'] = $this->ebookService->ebookList();
		return view("ebook.ebook_ajax_list", $data);
	}

	public function ebookById(Request $request)
	{
		$response = $this->ebookService->getDetailById($request->id);
		return response()->json(['status' => true, 'data' => $response]);
	}

	public function ebookView(Request $request)
	{
		if(in_array(auth()->user()->id,Utility::agencyPortalRolePermission())){
            abort(404);
        }
		$data['ebookData'] = $this->ebookService->ebookAllDataTypeWise();
		return view('ebook/ebook_view', $data);
	}
}