<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\UserDocApprovalService;
use App\Services\UserService;
use App\Services\LogsService;
use App\Helpers\Utility;
use App\User;

class UserDocApprovalController extends Controller
{
    protected $userDocApprovalService;

    public function __construct(UserDocApprovalService $userDocApprovalService,UserService $userService)
    {
        $this->middleware('auth');
        $this->middleware('permission:user-doc-approval-list|user-doc-approval-create|user-doc-approval-edit|user-doc-approval-delete', ['only' => ['index']]);
        $this->middleware('permission:user-doc-approval-list', ['only' => ['ajaxList', 'show']]);
        $this->middleware('permission:user-doc-approval-create', ['only' => ['store']]);
        $this->middleware('permission:user-doc-approval-edit', ['only' => ['update']]);
        $this->middleware('permission:user-doc-approval-delete', ['only' => ['destroy']]);
        $this->userDocApprovalService = $userDocApprovalService;
        $this->userService = $userService;
    }

    public function index()
    {
        $data['menu'] = 'User Doc Approval';
        $data['user'] = auth()->user();
        $data['userList'] = $this->userService->getAllUserList();
        return view('user_doc_approval.index', $data);
    }

    public function ajaxList(Request $request)
    {
        $userId = $request->user_id ?? '';
        $key    = $request->key ?? '';
        $data['query'] = $this->userDocApprovalService->getAll($userId, $key);
        return view('user_doc_approval.ajax_list', $data);
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'user_id' => 'required',
                    'key'     => 'required',
                ],
                [
                    'user_id.required' => 'Please select a user.',
                    'key.required'     => 'Please select a key type.',
                ]
            );

            if ($validator->fails()) {
                return response()->json(['status' => false, 'error_msg' => $validator->errors()->all()[0]], 422);
            }

            $exitUser = $this->getExistingUsers($request->user_id);
            if(!empty($exitUser[0]->id)){
                return response()->json(['status' => false, 'error_msg' => 'A record for this user already exists.'], 422);
            }

            $user = $this->userService->getUserDetailsById($request->user_id);
            $name = $user ? trim($user->first_name . ' ' . $user->last_name) : '';

            $data = [
                'user_id'      => $request->user_id,
                'type'         => "patient",
                'key'          => $request->key,
                'name'         => $name,
            ];

            $this->userDocApprovalService->save($data);

            $ipaddress = Utility::getIP();
            $insertLog = [
                'type'         => 'Add',
                'link'         => url('/user-doc-approval'),
                'module'       => 'User Doc Approval',
                'object_id'    => $request->user_id,
                'message'      => auth()->user()->first_name . ' ' . auth()->user()->last_name . ' has added User Doc Approval',
                'new_response' => serialize($data),
                'ip'           => $ipaddress,
            ];
            LogsService::save($insertLog);

            return response()->json(['status' => true, 'error_msg' => 'Record saved successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'error_msg' => 'Something went wrong. Please try again.'], 500);
        }
    }

    public function show($id)
    {
        $record = $this->userDocApprovalService->getById($id);
        if ($record) {
            return response()->json(['status' => true, 'data' => $record], 200);
        }
        return response()->json(['status' => false, 'error_msg' => 'Record not found.'], 404);
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'user_id' => 'required',
                    'key'     => 'required',
                ],
                [
                    'user_id.required' => 'Please select a user.',
                    'key.required'     => 'Please select a key type.',
                ]
            );

            if ($validator->fails()) {
                return response()->json(['status' => false, 'error_msg' => $validator->errors()->all()[0]], 422);
            }

            $exitUser = $this->getExistingUsers($request->user_id, $id);
            if (!empty($exitUser[0]->id)) {
                return response()->json(['status' => false, 'error_msg' => 'A record for this user already exists.'], 422);
            }

            $oldRecord = $this->userDocApprovalService->getById($id);

            $user = $this->userService->getUserDetailsById($request->user_id);
            $name = $user ? trim($user->first_name . ' ' . $user->last_name) : '';

            $data = [
                'user_id'      => $request->user_id,
                'key'          => $request->key,
                'name'         => $name,
            ];

            $this->userDocApprovalService->update($data, ['id' => $id]);

            $ipaddress = Utility::getIP();
            $insertLog = [
                'type'         => 'Update',
                'link'         => url('/user-doc-approval'),
                'module'       => 'User Doc Approval',
                'object_id'    => $id,
                'message'      => auth()->user()->first_name . ' ' . auth()->user()->last_name . ' has updated User Doc Approval',
                'old_response' => serialize($oldRecord),
                'new_response' => serialize($data),
                'ip'           => $ipaddress,
            ];
            LogsService::save($insertLog);

            return response()->json(['status' => true, 'error_msg' => 'Record updated successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'error_msg' => 'Something went wrong. Please try again.'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $oldRecord = $this->userDocApprovalService->getById($id);

            $data = ['del_flag' => 'Y'];
            $this->userDocApprovalService->SoftDelete($data, ['id' => $id]);

            $ipaddress = Utility::getIP();
            $insertLog = [
                'type'         => 'Delete',
                'link'         => url('/user-doc-approval'),
                'module'       => 'User Doc Approval',
                'object_id'    => $id,
                'message'      => auth()->user()->first_name . ' ' . auth()->user()->last_name . ' has deleted User Doc Approval',
                'old_response' => serialize($oldRecord),
                'new_response' => serialize($data),
                'ip'           => $ipaddress,
            ];
            LogsService::save($insertLog);

            return response()->json(['status' => true, 'error_msg' => 'Record deleted successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'error_msg' => 'Something went wrong. Please try again.'], 500);
        }
    }

    private function getExistingUsers($userId, $excludeId = null){
        return $this->userDocApprovalService->countByUser($userId, $excludeId);
    }
}
