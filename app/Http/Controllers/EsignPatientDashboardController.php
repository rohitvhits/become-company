<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\AssignEsignDocumentUserService;
use App\Model\DefualtAssignEsignUser;
use App\Services\DefualtAssignEsignUserService;
use App\Services\DocumentSendService;
use App\Helpers\Utility;
use App\Services\UserService;
use App\User;
use App\Services\LogsService;
use Illuminate\Validation\Rule;

class EsignPatientDashboardController extends Controller
{
	protected $documentSendService;
	protected $userService;

	public function __construct(DocumentSendService $documentSendService,UserService $userService)
	{
		$this->middleware('auth');
		$this->middleware('permission:esign-patient-dashboard', ['only' => ['index','ajaxList','storeDefaultAssignUser']]);
		$this->documentSendService = $documentSendService;
		$this->userService = $userService;
	}

	public function index()
	{
		return view('esign-dashboard.index');
	}

	public function ajaxList(Request $request)
	{
		$list = AssignEsignDocumentUserService::getDashboardList(
			auth()->user()->id,
			$request->template_name,
			$request->status,
			$request->created_date
		);

		if(!empty($list[0])){
			foreach($list as $val){
				$query = $this->documentSendService->GetDetailsbyGroupId($val->groupId);
				if(isset($query[0]['pdf_status']) && $query[0]['pdf_status'] !=""){
					if($query[0]['pdf_status'] ==1){
						$val->status = "Approved";
					}
					if($query[0]['pdf_status'] ==0){
						$val->status = "Rejected";
					}
				}

				if(isset($query[0]['review_date']) && $query[0]['review_date'] !=""){
					$val->review_date = Utility::convertMDYTime($query[0]['review_date']);
				}
				
				$reviewerFirstName = "";
				$reviewerLastName = "";
				if(isset($query[0]['review_by']) && $query[0]['review_by'] !=""){
					$reviewDetails = $this->userService->getUserDetails([$query[0]['review_by']]);
					$reviewerFirstName = $reviewDetails[0]->first_name??"";
					$reviewerLastName = $reviewDetails[0]->last_name??"";
				}

				$val->review_by = $reviewerFirstName.' '.$reviewerLastName;
			}
		}

		$data['list'] = $list;
		return view('esign-dashboard.ajax_list', $data);
	}

	public function storeDefaultAssignUser(Request $request)
	{
		$auth = auth()->user();
		$validator = Validator::make($request->all(), [
			'user_id' => [
				'required',
			],
		], [
			'user_id.required' => 'Please select a user.',
			
		]);

		if ($validator->fails()) {
			return response()->json(['status' => 'error', 'error_msg' => $validator->errors()->first()], 422);
		}

		if (DefualtAssignEsignUserService::exists($request->user_id)) {
			return response()->json(['status' => 'error', 'error_msg' => 'User is already assigned.'], 422);
		}

		DefualtAssignEsignUserService::save(['user_id' => $request->user_id]);

		$user = User::find($request->user_id);
		if ($user && !$user->hasRole('Esign Patient Dashboard')) {
			$user->assignRole('Esign Patient Dashboard');
		}

		$ipaddress = Utility::getIP();
		$insertLog = [
			'type' => 'Assign Esign User',
			'link' => url('/esign/esign-patient-dashboard/default-assign-esign-user/store'),
			'module' => 'User',
			'object_id' => $request->user_id,
			'message' => $auth->first_name . ' ' . $auth->last_name . ' has assigned an eSign user',
			'new_response' => serialize($request->except('_token')),
			'ip' => $ipaddress,
		];
		LogsService::save($insertLog);
		return response()->json(['status' => 'success', 'error_msg' => 'User assigned successfully.']);
	}

	public function defaultAssignUserList()
	{
		$data['list'] = DefualtAssignEsignUserService::getList();
		return view('esign-dashboard.default_assign_user_list', $data);
	}

	public function deleteDefaultAssignUser(Request $request)
	{
		$auth = auth()->user();
		$record = DefualtAssignEsignUser::where('id', $request->id)->where('del_flag', 'N')->first();

		if (!$record) {
			return response()->json(['status' => 'error', 'error_msg' => 'Record not found.'], 422);
		}

		DefualtAssignEsignUserService::softDelete($request->id);

		$user = User::find($record->user_id);
		if ($user && $user->hasRole('Esign Patient Dashboard')) {
			$user->removeRole('Esign Patient Dashboard');
		}

		$ipaddress = Utility::getIP();
		$insertLog = [
			'type' => 'Delete Assign Esign User',
			'link' => url('/esign/esign-patient-dashboard/default-assign-esign-user/delete'),
			'module' => 'User',
			'object_id' => $request->user_id,
			'message' => "{$auth->first_name} {$auth->last_name} has deleted an eSign user",
			'old_response' => serialize($record->toArray()),
			'new_response' => serialize(['del_flag'=>'Y','deleted_at'=>date('Y-m-d H:i:s'),'deleted_by'=>$auth->id]),
			'ip' => $ipaddress,
		];
		LogsService::save($insertLog);
		return response()->json(['status' => 'success', 'error_msg' => 'User removed successfully.']);
	}
}
