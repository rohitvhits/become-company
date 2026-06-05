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
use App\LocationMaster;

use App\User;
use App\Helpers\Utility;

use App\Services\LocationMasterService;
use App\Services\PSEService;
use App\Services\ScheduleLocationDisableService;

class LocationMasterController extends BaseController
{
    protected $pseService,$LocationMasterService="",$scheduleLocationDisableService;
    public function __construct(LocationMasterService $LocationMasterService,PSEService $pseService,ScheduleLocationDisableService $scheduleLocationDisableService)
    {
        $this->middleware('permission:location-list|location-add|location-edit|location-delete|location-view', ['only' => ['index', 'save']]);
        $this->middleware('permission:location-list', ['only' => ['index']]);
        $this->middleware('permission:location-add', ['only' => ['add', 'save']]);
        $this->middleware('permission:location-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:location-delete', ['only' => ['delete']]);
        $this->middleware('permission:location-search-list', ['only' => ['searchLocation','searchLocationData']]);

        $this->middleware('auth');
        $this->LocationMasterService = $LocationMasterService;
        $this->pseService = $pseService;
        $this->scheduleLocationDisableService = $scheduleLocationDisableService;
    }

    public function index(Request $request)
    {
        $data['menu'] = "Location";
        $data['user'] = $user = auth()->user();
        if ($user['user_type_fk'] != 3 && $user['user_type_fk'] != 184) {
            return abort(404);
        }


        $data['query'] = $this->LocationMasterService->AllList();

        return view("location/location_list", $data);
    }

    public function add()
    {
        $data['menu'] = "Add Location";
        $data['user'] = $user = auth()->user();
        if ($user['user_type_fk'] != 3 && $user['user_type_fk'] != 184) {
            return abort(404);
        }
        return view("location/location_add", $data);
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
            return redirect("/location/add")
                ->withErrors($validator, 'add_agency')
                ->withInput();
        } else {

            $address1 = request('address1');
            $address2 = request('address2');
            $state = request('state');
            $city = request('city');
            $zip_code = request('zip_code');
            $link = request('link');

            $data = array('address1' => $address1, 'address2' => $address2, 'city' => $city, 'state' => $state, 'zip_code' => $zip_code, 'link' => $link, 'location_name' => $request->input('short_name'),'walkin'=>$request->walkin,'latitude' => $request->latitude,'longitude' => $request->longitude,'telehealth_config' => $request->telehealth_config,'stop_date' => $request->stop_date != "" ? date('Y-m-d', strtotime($request->stop_date)) : NULL,
                'stop_time' => $request->stop_time != "" ? date('H:i:s', strtotime($request->stop_time)) : NULL);

            $ins_test = $this->LocationMasterService->save($data);

            if ($ins_test) {

                // $ipaddress = request()->getClientIp();
                $ipaddress = Utility::getIP();
                $insertLog = [
                    'type' => 'Add',
                    'link' => url('/location/save'),
                    'module' => 'Location',
                    'object_id' => $ins_test,
                    'message' => $user->first_name . ' ' . $user->last_name . ' has added Location',
                    'new_response' => serialize($data),
                    'ip' => $ipaddress,
                ];
                LogsService::save($insertLog);

                Session::flash('success', 'Location added successfully.');
                return redirect('/location');
            } else {
                Session::flash('error', 'Sorry, something went wrong. Please try again.');
                return redirect('/location/add');
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
        $data['agency'] = $this->LocationMasterService->getDetailbyId($id);

        return view('location/location_edit', $data);
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
            return redirect("/location/edit/$id")
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
                'longitude' => $request->longitude,
                'telehealth_config' => $request->telehealth_config,
                'stop_date' => $request->stop_date != "" ? date('Y-m-d', strtotime($request->stop_date)) : NULL,
                'stop_time' => $request->stop_time != "" ? date('H:i:s', strtotime($request->stop_time)) : NULL,
            );
            // echo "<pre>"; print_r($data); exit;
            $update = $this->LocationMasterService->update($data, array('id' => $id));

            // $ipaddress = request()->getClientIp();
            $ipaddress = Utility::getIP();
            $insertLog = [
                'type' => 'Update',
                'link' => url('/location/update/' . $id),
                'module' => 'Location',
                'object_id' => $id,
                'message' => $user->first_name . ' ' . $user->last_name . ' has updated Location',
                'new_response' => serialize($data),
                'ip' => $ipaddress,
            ];
            LogsService::save($insertLog);

            Session::flash('success', 'Location update successfully.');
            return redirect('/location');
        }
    }


    public function delete($id)
    {
        $user = auth()->user();
        if ($user['user_type_fk'] != 3  && $user['user_type_fk'] != 184) {
            return abort(404);
        }
        $data['id'] = $id;
        $update = $this->LocationMasterService->SoftDelete(array('delete_flag' => 'Y'), array('id' => $id));

        // $ipaddress = request()->getClientIp();
        $ipaddress = Utility::getIP();
        $insertLog = [
            'type' => 'Delete',
            'link' => url('/location/delete/' . $id),
            'module' => 'Location',
            'object_id' => $id,
            'message' => $user->first_name . ' ' . $user->last_name . ' has deleted Location',
            'new_response' => serialize($data),
            'ip' => $ipaddress,
        ];
        LogsService::save($insertLog);

        if ($update) {
            Session::flash('success', 'Location delete successfully.');
            return redirect('/location');
        } else {
            Session::flash('error', 'Sorry, something went wrong. Please try again.');
            return redirect('/location');
        }
    }
    public function getLocationLogPage(Request $request)
    {
        $id = request('id');
        $data['user'] = $authId = Auth::user();
        $data['logList'] = LogsService::getDatByAllLogNew($id,'Location');

        return view("user_log_ajax_list", $data);
    }
    public function locationLog($id)
    {
        $data['id'] = $id;
        $data['user'] = $user = auth()->user();
        if ($user->agency_fk !="") {
            return abort(404);
        }
        return view('location/location_log_list', $data);
    }

    public function searchLocation()
    {

        return view('location/search_view');

    }

    function searchLocationData(Request $request){
        $getData = $this->LocationMasterService->searchLocation();
        $getPSEData = $this->pseService->searchLocation();
        $finalLocationData = $finalPCEData = [];
        if(!empty($getData[0])){
            foreach($getData as $val){
                $distance = Utility::getDistanceByLatLong($request->latitude,$request->longitude,$val->latitude,$val->longitude);
                $val->name = $val->location_name;
                $val->address = trim($val->address1.','.$val->address2.','.$val->city.','.$val->state.','.$val->zip_code);
                $val->distance = $distance;
                $val->type = '<div class="badge badge-primary" style="float: inline-end;margin-right: 3px;">Our Location</div>';
                $finalLocationData[] = $val;
            }
        }
        // pse data
        if(!empty($getPSEData[0])){
            foreach($getPSEData as $val){
                $distance = Utility::getDistanceByLatLong($request->latitude,$request->longitude,$val->latitude,$val->longitude);
                $val->name = $val->location_name;
                $val->address = trim($val->address1.','.$val->address2.','.$val->city.','.$val->state.','.$val->zip_code);
                $val->distance = $distance;
                $val->type = '<div class="badge badge-info" style="float: inline-end;margin-right: 3px;">PSE Location</div>';
                $finalPCEData[] = $val;
            }
        }

       $finalData = array_merge($finalLocationData,$finalPCEData);

        usort($finalData, function ($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });

        $data['finalData'] = $finalData;

        return view('location.search_ajax_list',$data);
    }

    public function saveBlockDates(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'location_id' => 'required',
            'status' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'error_msg' => $validator->errors()->all()[0]], 422);
        }

        $save = $this->scheduleLocationDisableService->save($request->all());
        if(!$save){
            return response()->json(['status' => false, 'error_msg' => 'Sorry, something went wrong. Please try again.'], 500);
        }
        $ipaddress = Utility::getIP();
        $insertLog = [
            'type' => 'Location Block Dates',
            'link' => url('/location/save-block-dates'),
            'module' => 'Location',
            'object_id' => $request->location_id,
            'message' => $user->first_name . ' ' . $user->last_name . ' has saved block dates for location',
            'new_response' => serialize($request->except('_token')),
            'ip' => $ipaddress,
        ];
        LogsService::save($insertLog);

        return response()->json(['status' => true, 'error_msg' => 'Block dates saved successfully.']);
    }

    public function getBlockDates(Request $request)
    {
        $locationId = $request->location_id;
        $records = $this->scheduleLocationDisableService->getByLocationId($locationId);

        $dates = [];
        $status = '0';
        foreach ($records as $record) {
            $dates[] = Utility::convertMDY($record->disable_date);
            $status = $record->status;
        }

        return response()->json([
            'status' => true,
            'dates' => $dates,
            'disable_status' => $status,
        ]);
    }
}
