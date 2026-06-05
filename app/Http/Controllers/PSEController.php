<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Services\LogsService;
use Illuminate\Support\Facades\Auth;

use App\User;
use App\Helpers\Utility;

use App\Services\PSEService;

class PSEController extends BaseController
{
    protected $pseService="";
    public function __construct(PSEService $pseService)
    {
        $this->middleware('permission:pse-list|pse-add|pse-edit|pse-delete', ['only' => ['index', 'save']]);
        $this->middleware('permission:pse-list', ['only' => ['index']]);
        $this->middleware('permission:pse-add', ['only' => ['add', 'save']]);
        $this->middleware('permission:pse-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:pse-delete', ['only' => ['delete']]);
       
        $this->middleware('auth');
        $this->pseService = $pseService;
    }

    public function index(Request $request)
    {
        $data['menu'] = "PSE";
        $data['user'] = $user = auth()->user();
        if ($user['user_type_fk'] != 3 && $user['user_type_fk'] != 184) {
            return abort(404);
        }


        $data['query'] = $this->pseService->AllList();

        return view("pse/pse_list", $data);
    }

    public function add()
    {
        $data['menu'] = "Add PSE";
        $data['user'] = $user = auth()->user();
        if ($user['user_type_fk'] != 3 && $user['user_type_fk'] != 184) {
            return abort(404);
        }
        return view("pse/pse_add", $data);
    }

    public function save(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [

            'address1' => 'required',
            'address2' => 'required',
            'state' => 'required',
            'city' => 'required',
            'zip_code' => 'required',
            'short_name' => 'required'

        ]);
        if ($validator->fails()) {
            return redirect("/pse-location/add")
                ->withErrors($validator, 'add_agency')
                ->withInput();
        } else {

            $address1 = request('address1');
            $address2 = request('address2');
            $state = request('state');
            $city = request('city');
            $zip_code = request('zip_code');
            $link = request('link');

            $data = array('address1' => $address1, 'address2' => $address2, 'city' => $city, 'state' => $state, 'zip_code' => $zip_code, 'link' => $link, 'location_name' => $request->input('short_name'),'walkin'=>$request->walkin,'latitude' => $request->latitude,'longitude' => $request->longitude);

            $ins_test = $this->pseService->save($data);

            if ($ins_test) {

                // $ipaddress = request()->getClientIp();
                $ipaddress = Utility::getIP();
                $insertLog = [
                    'type' => 'Add',
                    'link' => url('/pse-location/save'),
                    'module' => 'PSE',
                    'object_id' => $ins_test,
                    'message' => $user->first_name . ' ' . $user->last_name . ' has added pse',
                    'new_response' => serialize($data),
                    'ip' => $ipaddress,
                ];
                LogsService::save($insertLog);

                Session::flash('success', 'PSE added successfully.');
                return redirect('/pse-location');
            } else {
                Session::flash('error', 'Sorry, something went wrong. Please try again.');
                return redirect('/pse-location/add');
            }
        }
    }

    public function edit($id)
    {
        $data['menu'] = "user";
        $data['user'] = $user = auth()->user();
        if ($user['user_type_fk'] != 3  && $user['user_type_fk'] != 184) {
            return abort(404);
        }
        $data['id'] = $id;
        $data['agency'] = $this->pseService->getDetailbyId($id);

        return view('pse/pse_edit', $data);
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $data['id'] = $id;
        $validator = Validator::make($request->all(), [

            'address1' => 'required',
            'address2' => 'required',
            'state' => 'required',
            'city' => 'required',

            'zip_code' => 'required',
            'short_name' => 'required',

        ]);
        if ($validator->fails()) {
            return redirect("/pse-location/edit/$id")
                ->withErrors($validator, 'edit_agency')
                ->withInput();
        } else {
            $address1 = request('address1');
            $address2 = request('address2');
            $state = request('state');
            $city = request('city');
            $zip_code = request('zip_code');
            $link = request('link');
            $data = array(

                'address1' => $address1,
                'address2' => $address2,
                'state' => $state,
                'city' => $city,
                'zip_code' => $zip_code,
                'link' => $link, 
                'location_name' => $request->input('short_name'),
                'walkin'=>$request->walkin,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude

            );
            $update = $this->pseService->update($data, array('id' => $id));

            // $ipaddress = request()->getClientIp();
            $ipaddress = Utility::getIP();
            $insertLog = [
                'type' => 'Update',
                'link' => url('/pse-location/update/' . $id),
                'module' => 'PSE',
                'object_id' => $id,
                'message' => $user->first_name . ' ' . $user->last_name . ' has updated pse',
                'new_response' => serialize($data),
                'ip' => $ipaddress,
            ];
            LogsService::save($insertLog);

            Session::flash('success', 'PSE update successfully.');
            return redirect('/pse-location');
        }
    }


    public function delete($id)
    {
        $user = auth()->user();
        if ($user['user_type_fk'] != 3  && $user['user_type_fk'] != 184) {
            return abort(404);
        }
        $data['id'] = $id;
        $update = $this->pseService->SoftDelete(array('delete_flag' => 'Y'), array('id' => $id));

        // $ipaddress = request()->getClientIp();
        $ipaddress = Utility::getIP();
        $insertLog = [
            'type' => 'Delete',
            'link' => url('/pse-location/delete/' . $id),
            'module' => 'PSE',
            'object_id' => $id,
            'message' => $user->first_name . ' ' . $user->last_name . ' has deleted pse',
            'new_response' => serialize($data),
            'ip' => $ipaddress,
        ];
        LogsService::save($insertLog);

        if ($update) {
            Session::flash('success', 'PSE delete successfully.');
            return redirect('/pse-location');
        } else {
            Session::flash('error', 'Sorry, something went wrong. Please try again.');
            return redirect('/pse-location');
        }
    }
    public function getLocationLogPage(Request $request)
    {
        $id = request('id');
        $data['user'] = $authId = Auth::user();
        $data['logList'] = LogsService::getDatByAllLog($id,'Location');

        return view("user_log_ajax_list", $data);
    }
    public function locationLog($id)
    {
        $data['id'] = $id;
        return view('location/location_log_list', $data);
    }

}
