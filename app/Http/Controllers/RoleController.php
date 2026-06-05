<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Services\LogsService;
use Illuminate\Support\Facades\Auth;
use Session;
use App\Helpers\Utility;
class RoleController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:role-list|role-create|role-edit|role-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:role-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:role-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    }
    public function index(Request $request)
    {
        $data['user']  = $user =auth()->user();
        $page =$request->page;
        $roles = Role::orderBy('id', 'DESC')->paginate(10);
        return view('role.index', compact('roles','user','page'));
    }

    public function create()
    {
        $data['user']  = $user =auth()->user();
        $permission = Permission::GroupBy('module_name')->get();

        foreach ($permission as $rwpermission) {
            $permissionn = Permission::where('module_name', $rwpermission->module_name)->get();
            $array[] = array('module_name' => $rwpermission->module_name, 'value' => $permissionn);
        }
        $data['permission'] = $array;
        return view('role.create', $data);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $this->validate($request, [
            'name' => 'required|unique:roles,name',
            'permission' => 'required',
        ]);
        $role = Role::create(['name' => $request->input('name'), 'guard_name' => 'web']);
        $role->syncPermissions($request->input('permission'));

        // $ipaddress = request()->getClientIp();
        $ipaddress = Utility::getIP();

        $insertLog = [
            'type' => 'Add',
            'link' => url('roles'),
            'module' => 'Role',
            'object_id' => $role->id,
            'message' => $user->first_name . ' ' . $user->last_name . ' has added Role',
            'new_response' => serialize(['name' => $request->input('name'), 'guard_name' => 'web']),
            'ip' => $ipaddress,
        ];
        LogsService::save($insertLog);

        if ($role) {
            Session::flash('success', 'Role successfully inserted.');
            return redirect()->route('roles.index');
        } else {
            Session::flash('error', 'Sorry, something went wrong. Please try again.');
            return redirect()->route('roles.index');
        }
    }

    public function edit($id)
    {
        $data['user']  = $user =auth()->user();
        $data['role'] = Role::find($id);
        $permission = Permission::GroupBy('module_name')->get();
        foreach ($permission as $rwpermission) {
            $permissionn = Permission::where('module_name', $rwpermission->module_name)->get();
            $array[] = array('module_name' => $rwpermission->module_name, 'value' => $permissionn);
        }
        $data['permission'] = $array;
        $data['rolePermissions'] = DB::table("role_has_permissions")->where("role_has_permissions.role_id", $id)
            ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
            ->all();
        return view('role.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();

       
        $this->validate($request, [
            'name' => 'required',
            'permission' => 'required',
        ]);

        $role = Role::find($id);
        
        $role->name = $request->input('name');
        $role->save();
        $permissions = Permission::whereIn('id', $request->permission)->get();
        $role->syncPermissions($permissions); 

        // $ipaddress = request()->getClientIp();
        $ipaddress = Utility::getIP();
        $insertLog = [
            'type' => 'Update',
            'link' => url('roles'),
            'module' => 'Role',
            'object_id' => $id,
            'message' => $user->first_name . ' ' . $user->last_name . ' has updated Role',
            'new_response' => serialize($role),
            'ip' => $ipaddress,
        ];
        LogsService::save($insertLog);
        return redirect()->route('roles.index')->with('success', 'Role successfully updated ');
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $role = Role::where('id', $id)->delete();

        // $ipaddress = request()->getClientIp();
        $ipaddress = Utility::getIP();
        $insertLog = [
            'type' => 'Delete',
            'link' => url('roles'),
            'module' => 'Role',
            'object_id' => $id,
            'message' => $user->first_name . ' ' . $user->last_name . ' has deleted Role',
            'new_response' => serialize($role),
            'ip' => $ipaddress,
        ];
        LogsService::save($insertLog);
        return response()->json(['status' => true, 'msg' => 'Role successfully deleted ']);
    }
    public function getRoleLogShowPage(Request $request)
    {
        $id = request('id');
        $data['user'] = $authId = auth()->user();
        $data['logList'] = LogsService::getDatByAllLog($id, 'Role');
        return view("user_log_ajax_list", $data);
    }
    public function show($id)
    {
        $data['user']  = $user =auth()->user();
        $data['id'] = $id;
        $data['role'] = Role::find($id);
        $permission = Permission::GroupBy('module_name')->get();
        foreach ($permission as $rwpermission) {
            $permissionn = Permission::where('module_name', $rwpermission->module_name)->get();
            $array[] = array('module_name' => $rwpermission->module_name, 'value' => $permissionn);
        }
        $data['permission'] = $array;
        $data['rolePermissions'] = DB::table("role_has_permissions")->where("role_has_permissions.role_id", $id)
            ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
            ->all();
        return view("role/role_log_list", $data);
    }
}
