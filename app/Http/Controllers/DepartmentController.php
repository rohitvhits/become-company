<?php

namespace App\Http\Controllers;

use App\Helpers\LogsHelper;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;
use App\Services\DepartmentTaskService;
use App\Services\DepartmentUserService;
use App\Services\PatientService;

class DepartmentController extends BaseController
{
    protected $departmentTaskService, $departmentUserService, $patientService = '';
    public function __construct(DepartmentTaskService $departmentTaskService, DepartmentUserService $departmentUserService, PatientService $patientService)
    {
        $this->middleware('auth');
        $this->middleware('permission:department-list',['only' => ['index','ajaxList','show','getUsers']]);
        $this->middleware('permission:department-add',['only' => ['store']]);
        $this->middleware('permission:department-edit',['only' => ['update']]);
        $this->middleware('permission:department-delete',['only' => ['destroy']]);

        $this->departmentTaskService = $departmentTaskService;
        $this->departmentUserService = $departmentUserService;
        $this->patientService = $patientService;
    }

    /**
     * Display listing of departments
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $users = User::select('id','active','first_name','last_name','email','agency_fk')->whereNull('agency_fk')->where('delete_flag', 'N')->orderBy('first_name')->get();
        $allUsers = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->full_name,
                'email' => $user->email != null ? $user->email : '' ,
                'status' => ucfirst($user->active),
            ];
        });
        return view('department.index', compact('users', 'search','allUsers'));
    }

    public function ajaxList(Request $request){
        $departments = $this->departmentTaskService->getList($request->all(),$paginate = true);
        return view('department.ajax_list', compact('departments'));
    }

    /**
     * Get department data for edit modal (AJAX)
     */
    public function show($id)
    {
        $department = $this->departmentTaskService->getDetailsById($id);

        if (!$department) {
            return response()->json([
                'status' => false,
                'message' => 'Department not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'id' => $department->id,
                'name' => $department->name,
                'user_ids' => $department->users->pluck('id')->toArray()
            ]
        ]);
    }

    /**
     * Store a new department
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ], [
            'name.required' => 'Department name is required.',
            'name.unique' => 'This department name already exists.',
            'name.max' => 'Department name cannot exceed 255 characters.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $id = $this->departmentTaskService->save([
            'name' => $request->input('name')
        ]);
        $department = $this->departmentTaskService->getDetailsById($id);
        // Sync users
        if ($request->has('user_ids') && is_array($request->input('user_ids'))) {
            foreach($request->input('user_ids') as $user){
                $userData = array(
                    'department_id' => $id,
                    'user_id' => $user 
                );
                $this->departmentUserService->save($userData);
            }
        }
        $logData = array(
            'type' => 'Add Department',
            'link' => url('tasks/department-master/store'),
            'module' => 'Task Department',
            'object_id' => '',
            'message' => auth()->user()->first_name .' '.auth()->user()->last_name . ' has added task department',
            'new_response' => $department,
        );
        LogsHelper::handleLogs($logData);
        return response()->json([
            'status' => true,
            'message' => 'Department created successfully.'
        ]);
    }

    /**
     * Update department
     */
    public function update(Request $request, $id)
    {
        $department = $this->departmentTaskService->getDetailsById($id);
        $old = $department;
        if (!$department) {
            return response()->json([
                'status' => false,
                'message' => 'Department not found.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ], [
            'name.required' => 'Department name is required.',
            'name.unique' => 'This department name already exists.',
            'name.max' => 'Department name cannot exceed 255 characters.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }
        $dp_data = array(
            'name' => $request->input('name')
        );
        $this->departmentTaskService->update($dp_data,['id' => $id]);

        $newUserIds = $request->input('user_ids', []);
        $existingUsers = $this->departmentUserService->getDepartment($id);
        $usersToDelete = array_diff($existingUsers, $newUserIds);
        $usersToAdd = array_diff($newUserIds, $existingUsers);
        if (!empty($usersToDelete)) {
            $this->departmentUserService->bulkUpdate($id,$usersToDelete);    
        }
        // Handle new users - check if they were previously soft deleted or truly new
        foreach ($usersToAdd as $userId) {
            $userData = [
                'department_id' => $id,
                'user_id' => $userId
            ];
            $this->departmentUserService->save($userData);
        }

        $new = $this->departmentTaskService->getDetailsById($id);
        $logData = array(
            'type' => 'Update Department',
            'link' => url('tasks/department-master/update'),
            'module' => 'Task Department',
            'object_id' => $id,
            'message' => auth()->user()->first_name .' '.auth()->user()->last_name . ' has updated task department',
            'old_response' => $old,
            'new_response' => $new,
        );
        LogsHelper::handleLogs($logData);
        return response()->json([
            'status' => true,
            'message' => 'Department updated successfully.'
        ]);
    }

    /**
     * Delete department
     */
    public function destroy($id)
    {
        $department = $this->departmentTaskService->getById($id);

        if (!$department) {
            return response()->json([
                'status' => false,
                'message' => 'Department not found.'
            ], 404);
        }
        // Detach all users first
        $this->departmentUserService->softDelete(['del_flag' => 'Y'],['department_id' => $id]);
        // Delete department
        $this->departmentTaskService->softDelete(['del_flag' => 'Y'],['id' => $id]);
        $logData = array(
            'type' => 'Delete Department',
            'link' => url('tasks/department-master/delete'),
            'module' => 'Task Department',
            'object_id' => $id,
            'message' => auth()->user()->first_name .' '.auth()->user()->last_name . ' has deleted task department',
            'old_response' => $department,
        );
        LogsHelper::handleLogs($logData);
        return response()->json([
            'status' => true,
            'message' => 'Department deleted successfully.'
        ]);
    }

    /**
     * Get users list for select (AJAX)
     */
    public function getUsers()
    {
        $users = User::where('delete_flag', 'N')
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name', 'email']);

        return response()->json([
            'status' => true,
            'data' => $users
        ]);
    }

    public function changeStatus(Request $request)
    {
        $id = $request->id;
        $department = $this->departmentTaskService->getById($id);
        if (!$department) {
            return response()->json([
                'status' => false,
                'message' => 'Department not found.'
            ], 404);
        }
        // Detach all users first
        $status = $request->status == 1 ? 0 : 1;
        $this->departmentTaskService->update(['status' => $status],['id' => $id]);
        $logData = array(
            'type' => 'Update Status Department',
            'link' => url('tasks/department/status-update'),
            'module' => 'Task Department',
            'object_id' => $id,
            'message' => auth()->user()->first_name .' '.auth()->user()->last_name . ' has updated status of task department',
            'old_response' => $department,
            'new_response' => ['status' => $status,'id'=>$id],
        );
        LogsHelper::handleLogs($logData);
        return response()->json([
            'status' => true,
            'message' => 'Department status updated successfully.'
        ]);
    }

    public function deptList(){
        $departments = $this->departmentTaskService->getAllDept();
        return response()->json([
            'status' => true,
            'data'   => $departments
        ]);
    }

    public function savePortalAssignDept(Request $request){
		$validator = Validator::make($request->all(), [
			'id' => 'required',
		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
		}
        $oldData = $this->patientService->getPatientDetailsByIdWhitoutAgency($request->id);
        $data=array('dept_id'=>$request->assign_dept);
        $this->patientService->update($data,array('id'=>$request->id));
        $logData = array(
            'type' => 'Assign Department',
            'link' => url('patient/assign-department'),
            'module' => 'Patient Appointment',
            'object_id' => $request->id,
            'message' => auth()->user()->first_name .' '.auth()->user()->last_name . ' has assigned department to portal.',
            'old_response' => $oldData,
            'new_response' => ['dept_id'=>$request->assign_dept,'id'=>$request->id],
        );
        $department = $this->departmentTaskService->getById($request->assign_dept);
        $data['department_name'] = $department->name??'';
        LogsHelper::handleLogs($logData);
        return response()->json(['status' => true, 'error_msg' => 'Assigned department has been successfully updated.','data'=>$data], 200);
    }
}
