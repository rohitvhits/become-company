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
use App\Helpers\Utility;
use App\Services\LocationMasterService;
use App\Services\LocationScheduleService;
use App\Model\LocationSchedule;
use App\Services\PatientService;
use App\Services\LogsService;
use Excel;
use URL;
use App\Services\AppointmentService;
use App\Services\DisableDateService;

class LocationScheduleController extends BaseController
{

    protected $appointmentService;
    protected $disableDateService;
    protected $locationMasterService;
    protected $locationScheduleService;
    protected $patientService;
    protected const CONVERT_TIME_TWELVE_HOUR_FORMAT = 'h:i A';
    protected const MODULE_NAME = 'Location Schedule';
    protected const ERROR_MSG = "Sorry, something went wrong. Please try again.";
    public function __construct(LocationMasterService $locationMasterService, LocationScheduleService $locationScheduleService, PatientService $patientService,AppointmentService $appointmentService,DisableDateService $disableDateService)
    {
        $this->middleware('permission:location-schedule', ['only' => ['index']]);

        $this->middleware('auth', ['except' => ['SearchByLocationIdAndDate']]);

        $this->locationMasterService = $locationMasterService;
        $this->locationScheduleService = $locationScheduleService;
        $this->patientService = $patientService;
        $this->appointmentService = $appointmentService;
        $this->disableDateService = $disableDateService;
    }


    public function index(Request $request, $id)
    {
        $data['menu'] = "user";
        $data['user'] = auth()->user();

        $data['location_id'] = request('location_id');
        $data['service_id'] = request('service_id');
        $data['day'] = $request->day;
        $data['query'] = $this->locationScheduleService->getData($id,$request->all());
        $data['id'] = $id;
        return view("locationSchedule/location_schedule_list", $data);
    }

    public function add($id)
    {
        $data['menu'] = "Add Doctor";
        $data['user'] = auth()->user();
        $data['location_id'] = $id;
        return view("locationSchedule/location_schedule_add", $data);
    }

    public function save(Request $request)
    {
        $user = auth()->user();

        $locationId = $request->input('location_id');
        $validator = Validator::make($request->all(), [
            'day' => 'required',
            'state_time' => 'required',
            'end_time' => 'required',
            'slot' => 'required'

        ]);
        if ($validator->fails()) {
            return redirect("/location-schedule/add/" . $locationId)
                ->withErrors($validator, 'add_agency')
                ->withInput();
        } else {

            $data = array(
                'day' => request('day'),
                'start_time' => date('H:i:s', strtotime(request('state_time'))),
                'end_time' => date('H:i:s', strtotime(request('end_time'))),
                'location_id' => $locationId,
                'slot' => $request->input('slot')
            );

            $insert = $this->locationScheduleService->save($data);

            if ($insert) {

                $ipaddress = Utility::getIP();
              
                $insertLog = [
                    'type' => 'Add Location Schedule',
                    'link' => url('/location-schedule/save'),
                    'module' => self::MODULE_NAME,
                    'object_id' => $locationId,
                    'message' => $user->first_name . ' ' . $user->last_name . ' has added Location Schedule',
                    'new_response' => serialize($data),
                    'ip' => $ipaddress,
                ];
                LogsService::save($insertLog);

                Session::flash('success', 'Location schedule successfully added.');
                return redirect()->back();
            }
            
            Session::flash('error', self::ERROR_MSG);
            return redirect('/location-schedule/add/' . $locationId);
        }
    }

    public function edit($locationId, $id)
    {
        $data['menu'] = "user";
        $data['user'] = auth()->user();

        $data['id'] = $id;
        $data['locationId'] = $locationId;

        $data['location_schedule'] = $this->locationScheduleService->getDetailbyId($id);

        return view('locationSchedule/location_schedule_edit', $data);
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $data['id'] = $request->input('id');
        $data['location_id'] = $locationsId = $request->input('location_id');
        $validator = Validator::make($request->all(), [
            'day' => 'required',
            'state_time' => 'required',
            'end_time' => 'required',
            'slot' => 'required'
        ]);
        if ($validator->fails()) {
            return redirect("/location-schedule/edit/" . $request->input('location_id') . '/' . $request->input('id'))
                ->withErrors($validator, 'add_agency')
                ->withInput();
        } else {
            $oldDetails = $this->locationScheduleService->getDetailbyIdAll($request->input('id'));
   
            $data = array(
                'day' => request('day'),
                'start_time' => date('H:i:s', strtotime(request('state_time'))),
                'end_time' => date('H:i:s', strtotime(request('end_time'))),
                'slot' => $request->input('slot')

            );
            $this->locationScheduleService->update($data, array('id' => $request->input('id')));

            $ipaddress = Utility::getIP();
            $insertLog = [
                'type' => 'Update Location Schedule',
                'link' => url('/location-schedule/update'),
                'module' => self::MODULE_NAME,
                'object_id' => $locationsId,
                'message' => $user->first_name . ' ' . $user->last_name . ' has updated Location Schedule',
                'old_response' => serialize($oldDetails->toArray()),
                'new_response' => serialize($data),
                'ip' => $ipaddress,
            ];
            LogsService::save($insertLog);

            Session::flash('success', 'Location schedule successfully update.');
            return redirect('/location-schedule/' . $request->input('location_id'));
        }
    }

    public function delete($locationId, $id)
    {
        $user = auth()->user();

        $data['id'] = $id;
        $update = $this->locationScheduleService->SoftDelete(array('del_flag' => 'Y'), array('id' => $id));

        $ipaddress = Utility::getIP();
        $insertLog = [
            'type' => 'Delete Location Schedule',
            'link' => url('/location-schedule/delete/'.$locationId.'/'.$id),
            'module' => self::MODULE_NAME,
            'object_id' => $locationId,
            'message' => $user->first_name . ' ' . $user->last_name . ' has deleted Location Schedule',
            'new_response' => serialize($data),
            'ip' => $ipaddress,
        ];
        LogsService::save($insertLog);

        if ($update) {
            Session::flash('success', 'Location schedule successfully delete.');
            return redirect('/location-schedule/' . $locationId);
        }

        Session::flash('error', self::ERROR_MSG);
        return redirect('/location-schedule/' . $locationId);
    }

    public function SearchByLocationIdAndDate(Request $request) {
        $startDate = $start_time = $request->input('start_time');
        $location_id = $request->input('location_id');
        $timestamp = strtotime($start_time);
        $day = date('l', $timestamp);
        $query = $this->locationScheduleService->getSearchLocation($day, $location_id);
        $location = $this->locationMasterService->getDetailbyId($location_id);
        $dateArray = $this->disabledDate($startDate);
        
        $finalTimeArray = $dateArray['time'];
     
        $final = [];
        $checkStopTime = 0;
        $smallerTime = null;
        if(!empty($finalTimeArray)){
            foreach ($finalTimeArray as $time => $val) {
                $time1 = $time;
                $time2 = $location->stop_time;
                $currentSmall = strtotime($time1) < strtotime($time2) ? $time1 : $time2;
                if ($smallerTime === null || strtotime($currentSmall) < strtotime($smallerTime)) {
                    $smallerTime = $currentSmall;
                }
            }
        }
        if(isset($location->stop_date) && $timestamp == strtotime($location->stop_date)){
             $checkStopTime = 1; 
        }
        foreach ($query as $vs) {
            $subqye = $this->patientService->getCountByTimeScheduleNew($vs->id, $start_time);
            $vs->start_time = date(self::CONVERT_TIME_TWELVE_HOUR_FORMAT, strtotime($vs->start_time));
            $vs->end_time = date(self::CONVERT_TIME_TWELVE_HOUR_FORMAT, strtotime($vs->end_time));
            $slotRemaing = ($vs->slot - $subqye); $slotRemaings = 0;
            if ($slotRemaing > 0) {
                $slotRemaings = $slotRemaing;
            }
            $vs->slots = $slotRemaings;
           
            if($checkStopTime == 1){
                if(isset($smallerTime) && !empty($smallerTime)){
                    if (date('H:i A', strtotime($vs->start_time)) < date('H:i A',strtotime($smallerTime))) {
                        $final[] = $vs;
                    }
                }else{
                    if (date('H:i A', strtotime($vs->start_time)) < date('H:i A',strtotime($location->stop_time))) {
                        $final[] = $vs;
                    }
                }
            }else{
                if(!empty($finalTimeArray)){
                    foreach ($finalTimeArray as $time => $dates) {
                        if($startDate ==$dates){
                            if (date('H:i A', strtotime($vs->start_time)) < date('H:i A',strtotime($time))) {
                                $final[] = $vs;
                            }
                        }
                    }
                }else{
                    $final[] = $vs;
                }
            }
        }
        echo json_encode($final);
    }

    public function getSlotRemaining(Request $request)
    {
        $time = $request->input('time');
        $query = $this->locationScheduleService->getDetailbyId($time);
        $subqye = $this->patientService->getCountByTimeScheduleNewWithLocation($time,$request->input('date'),$request->location_id);

        $total = ($query->slot - $subqye);
        if ($total > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    public function locationWiseScheduleLogs(Request $request)
    {
        $id = request('id');
        $data['user'] = auth()->user();
        $data['logList'] = LogsService::getDatByAllLogNew($id,'Location Schedule');
   
        return view("locationSchedule.location_schedule_log_ajax_list", $data);
    }

    public function copySchedule(Request $request){
        
        $user = auth()->user();
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];

        $time_slots = [
            ['09:00:00', '09:15:00'], ['09:15:00', '09:30:00'], ['09:30:00', '09:45:00'], ['09:45:00', '10:00:00'],
            ['10:00:00', '10:15:00'], ['10:15:00', '10:30:00'], ['10:30:00', '10:45:00'], ['10:45:00', '11:00:00'],
            ['11:00:00', '11:15:00'], ['11:15:00', '11:30:00'], ['11:30:00', '11:45:00'], ['11:45:00', '12:00:00'],
            ['12:00:00', '12:15:00'], ['12:15:00', '12:30:00'], ['12:30:00', '12:45:00'], ['12:45:00', '13:00:00'],
            ['13:00:00', '13:15:00'], ['13:15:00', '13:30:00'], ['13:30:00', '13:45:00'], ['13:45:00', '14:00:00'],
            ['14:00:00', '14:15:00'], ['14:15:00', '14:30:00'], ['14:30:00', '14:45:00'], ['14:45:00', '15:00:00'],
            ['15:00:00', '15:15:00'], ['15:15:00', '15:30:00'], ['15:30:00', '15:45:00'], ['15:45:00', '16:00:00'],
            ['16:00:00', '16:15:00'], ['16:15:00', '16:30:00'], ['16:30:00', '16:45:00'], ['16:45:00', '17:00:00']
        ];
        $insert =0;
        $allData = [];
        foreach ($days as $day) {
            foreach ($time_slots as $slot) {
                $start_time = $slot[0];
                $end_time = $slot[1];
               $finalArray = [
                'location_id'=>$request->id,
                'day'=>$day,
                'start_time'=>$start_time,
                'end_time'=>$end_time,
                'slot'=>2
               ];
               $insert =  LocationSchedule::updateOrCreate([
               
                        'location_id'=>$request->id,
                        'day'=>$day,
                        'start_time'=>$start_time,
                        'end_time'=>$end_time,
                    ],[
                        'slot'=>2,
                        'created_date'=>date('Y-m-d H:i:s'),
                        'created_by'=>$user->id
                    ]);
              
               $allData[] = $finalArray;
            }
        }

        if ($insert) {

            $ipaddress = Utility::getIP();
            $insertLog = [
                'type' => 'Add Location Schedule',
                'link' => url('/copy-schedule'),
                'module' => self::MODULE_NAME,
                'object_id' => $request->id,
                'message' => $user->first_name . ' ' . $user->last_name . ' has added Location Schedule',
                'new_response' => serialize($allData),
                'ip' => $ipaddress,
            ];
            LogsService::save($insertLog);

            return response()->json(['error_msg' => "Location schedule successfully added", 'status' => 1, 'data' => array()], 200);
        } 
        return response()->json(['error_msg' => self::ERROR_MSG, 'status' => 1, 'data' => array()], 500);
    }

    public function scheduleEnabledDisabled(Request $request){
        $user = auth()->user();
        $oldDetails = $this->locationScheduleService->getDetailbyIdAll($request->id);
        $details = $this->locationScheduleService->getDetailbyId($request->id);
        $status = "Y";
        $message ="Disabled";
        if($details->disable_status =='Y'){
            $status = "N";
            $message ="Enabled";
        }

        $this->locationScheduleService->update(array('disable_status'=>$status),array('id'=>$request->id));
        $ipaddress = Utility::getIP();
        $insertLog = [
            'type' => $message.' Location Schedule',
            'link' => url('/schedule-enabled-disabled'),
            'module' => self::MODULE_NAME,
            'object_id' => $oldDetails->location_id,
            'message' => $user->first_name . ' ' . $user->last_name . ' has '.$message.' Location Schedule',
            'new_response' => serialize(array('disable_status'=>$status)),
            'old_response' => serialize($oldDetails),
            'ip' => $ipaddress,
        ];
        LogsService::save($insertLog);
        return response()->json(['status'=>true,'message'=>'Status successfully '.$message,'data'=>array('status'=>$status) ]);
    }

    public function totalCountByLocationIdAndDate(Request $request)
    {
        $startDate = $start_time = $request->input('start_time');
        $location_id = $request->input('location_id');
        $timestamp = strtotime($start_time);
        $day = date('l', $timestamp);
        
        $query = $this->locationScheduleService->getSearchLocation($day, $location_id);
        $location = $this->locationMasterService->getDetailbyId($location_id);
        $final = [];
        $totalSloat = 0;
        $totalBokked = 0;
    
        $dateArray =  $this->disabledDate($startDate);
       
        $finalTimeArray = $dateArray['time'];

        $checkStopTime = 0;
        $smallerTime = null;
        if(!empty($finalTimeArray)){
            foreach ($finalTimeArray as $time => $val) {
                $time1 = $time;
                $time2 = $location->stop_time;
                $currentSmall = strtotime($time1) < strtotime($time2) ? $time1 : $time2;
                if ($smallerTime === null || strtotime($currentSmall) < strtotime($smallerTime)) {
                    $smallerTime = $currentSmall;
                }
            }
        }
        if(isset($location->stop_date) && $timestamp == strtotime($location->stop_date)){
             $checkStopTime = 1; 
        }
        foreach ($query as $vs) {
            if($checkStopTime == 1){
                if(isset($smallerTime) && !empty($smallerTime)){
                    if (date('H:i A', strtotime($vs->start_time)) < date('H:i A',strtotime($smallerTime))) {
                        $totalSloat += $vs['slot'];
                    }
                }else{
                    if (date('H:i A', strtotime($vs->start_time)) < date('H:i A',strtotime($location->stop_time))) {
                        $totalSloat += $vs['slot'];
                    }
                }
            }else{
                if(!empty($finalTimeArray)){
                    foreach ($finalTimeArray as $time => $dates) {
                    
                        if($startDate ==$dates){
                        
                            if (date('H:i A', strtotime($vs->start_time)) < date('H:i A',strtotime($time))) {
                                $totalSloat += $vs['slot'];
                            }
                        }
                    }
                    
                }else{
                    $totalSloat += $vs['slot'];
                }
            }
          
        }
        $subqye = $this->appointmentService->getCountByTimeScheduleNewWithLocation($startDate,$location_id);
        $totalBokked  = $subqye;

        $totalRemaining = $totalSloat - $totalBokked;
        $final['totalSloat'] = $totalSloat;
        $final['totalBokked'] = $totalBokked;
        $final['totalRemaining'] = $totalRemaining;
        echo json_encode($final);
    }

    public function totalCountByLocationIdAndDateTime(Request $request)
    {
        $start_time = $request->input('start_time');
        $appointmentId = $request->input('timeId');
       
        $query = $this->locationScheduleService->getDetailbyIdAll($appointmentId);
        $final = [];
        $totalSloat = 0;
        $totalBokked = 0;
        $subqye = $this->patientService->getCountByTimeScheduleNew($appointmentId, $start_time);
        $totalSloat = $query->slot??0;
        $totalBokked = $subqye;
        $totalRemaining = $totalSloat - $totalBokked;
        $final['totalSloat'] = $totalSloat;
        $final['totalBokked'] = $totalBokked;
        $final['totalRemaining'] = $totalRemaining;
        echo json_encode($final);
    }

    protected function disabledDate($startDate){
        $dateArray =  $this->disableDateService->disableDateAllDataWithTime();
        $finalTimeArray = [];
        if(!empty($dateArray)){
            foreach($dateArray as $key=>$vals){
                $explode = explode(',',$vals);
               
                if(!empty($explode[0])){
                    foreach($explode as $dats){
                        if(trim($dats) == $startDate){
                            $finalTimeArray[date('H:i', strtotime($key))]=trim($dats);
                        }
                    }
                }
                
            }
        }

        return ['time'=>$finalTimeArray];
    }

}
