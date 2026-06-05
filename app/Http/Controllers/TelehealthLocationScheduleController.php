<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Services\LocationMasterService;
use App\Services\TelehealthLocationScheduleService;
use App\Services\LogsService;
use App\Services\TelehealthLocationScheduleSlotService;
use App\Services\TelehealthLocationScheduleDayService;
use App\Services\TelehealthLocationScheduleEventService;
use App\Services\DisableDateService;
use App\User;
use App\Model\PatientTelehealthSchedule;
use App\Helpers\Utility;
use App\Services\PatientService;
class TelehealthLocationScheduleController extends BaseController
{

    protected $telehealthLocationScheduleService,$locationMasterService,$logService,$telehealthLocationScheduleSlotService,$telehealthLocationScheduleDayService,$telehealthLocationScheduleEventService,$disableDateService,$patientService="";
    public function __construct(LocationMasterService $locationMasterService, TelehealthLocationScheduleService $telehealthLocationScheduleService, LogsService $logService, TelehealthLocationScheduleSlotService $telehealthLocationScheduleSlotService, TelehealthLocationScheduleDayService $telehealthLocationScheduleDayService, TelehealthLocationScheduleEventService $telehealthLocationScheduleEventService, DisableDateService $disableDateService, PatientService $patientService)
    {
        $this->middleware('permission:manage-telehealth-location', ['only' => ['index','teleHealthMange']]);
        $this->middleware('auth', ['except' => ['SearchByLocationIdAndDate']]);
        $this->telehealthLocationScheduleService = $telehealthLocationScheduleService;
        $this->locationMasterService = $locationMasterService;
        $this->logService = $logService;
        $this->telehealthLocationScheduleSlotService = $telehealthLocationScheduleSlotService;
        $this->telehealthLocationScheduleDayService = $telehealthLocationScheduleDayService;
        $this->telehealthLocationScheduleEventService = $telehealthLocationScheduleEventService;
        $this->disableDateService = $disableDateService;
        $this->patientService = $patientService;
    }


    public function index(Request $request)
    {
        $data['user'] = auth()->user();
        return view("telehealthLocationSchedule/location_schedule_list", $data);
    }

    public function telehealthLocationAjaxList(){
        $data['user'] = auth()->user();
        $data['query'] = $this->telehealthLocationScheduleService->getData();
        foreach ($data['query'] as $key => $value) {
            $days = array();
            foreach ($value->days as $day) {
                $days[] = date('l', strtotime($day->day));
            }
            $data['query'][$key]->days = $days;
        }
        return view("telehealthLocationSchedule/location_schedule_ajax_list", $data);
    }

    private function generateTimeSlots($startTime, $endTime, $slotDuration)
    {
        $slots = [];
        $start = strtotime($startTime);
        $end = strtotime($endTime);
        
        while ($start < $end) {
            $slotEnd = strtotime("+{$slotDuration} minutes", $start);
            if ($slotEnd > $end) {
                break;
            }
            
            $slots[] = [
                'start_time' => date('H:i:s', $start),
                'end_time' => date('H:i:s', $slotEnd)
            ];
            
            $start = $slotEnd;
        }
        
        return $slots;
    }

    public function save(Request $request)
    {
        $user = auth()->user(); 
        $validator = Validator::make($request->all(), [
            'days' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'slot' => ['required', 'integer', function ($attribute, $value, $fail) use ($request) {
                $start = strtotime($request->start_time);
                $end = strtotime($request->end_time);
                $diffInMinutes = ($end - $start) / 60;
        
                if ($value > $diffInMinutes) {
                    $fail('Slot time must not be greater than the time difference between start and end time.');
                }
            }],
        ]);
        
        if ($validator->fails()) {
			return response()->json([
				'error_msg' => $validator->errors()->all()[0],
				'status' => false,
			], 422);
		} else {
            $data = array(
                'start_time' => date('H:i:s', strtotime(request('start_time'))),
                'end_time' => date('H:i:s', strtotime(request('end_time'))),
                'location_id' => $request->input('location_id'),
                'slot' => $request->input('slot'),
                'title' => $request->input('title'),
                'tele_config_type' => $request->input('telehealth_config_type')
            );

            $insert = $this->telehealthLocationScheduleService->save($data);
            if ($insert) {
                // Store day wise data
                $day = $request->input('days');
                $dayData = array();
                foreach ($day as $value) {
                    $dayData = array(
                        'schedule_id' => $insert,
                        'day' => $value,
                    );
                    $this->telehealthLocationScheduleDayService->save($dayData);
                }

                // Generate and store slot wise data
                $slots = $this->generateTimeSlots($data['start_time'], $data['end_time'], $data['slot']);
                foreach ($slots as $slot) {
                    $slotData = [
                        'location_id' => $request->input('location_id'),
                        'loc_schedule_id' => $insert,
                        'slot_start_time' => $slot['start_time'],
                        'slot_end_time' => $slot['end_time'],
                    ];
                    $this->telehealthLocationScheduleSlotService->save($slotData);
                }
                
                // $ipaddress = request()->getClientIp();
                $ipaddress = Utility::getIP();
                $insertLog = [
                    'type' => 'Add Telehealth Location Schedule',
                    'link' => url('/telehealth-location-schedule/save'),
                    'module' => 'Telehealth Location Schedule',
                    'object_id' => $request->input('location_id'),
                    'message' => $user->first_name . ' ' . $user->last_name . ' has added Telehealth Location Schedule',
                    'new_response' => serialize($data),
                    'ip' => $ipaddress,
                ];
                $this->logService->save($insertLog);
                return response()->json(['status' => true, 'error_msg' => 'Telehealth Location schedule successfully added.'], 200);
            } else {
                return response()->json(['status' => true, 'error_msg' => 'Sorry, something went wrong. Please try again'], 500);
            }
        }
    }

    public function edit($id)
    {
        $data = $this->telehealthLocationScheduleService->getDetailbyId($id);
        return response()->json(['status' => true, 'error_msg' => '','data' => $data], 200);
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $data['id'] = $request->input('edit_id');
        $data['location_id'] = $locationsId = $request->input('location_id');
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'slot' => ['required', 'integer', function ($attribute, $value, $fail) use ($request) {
                $start = strtotime($request->start_time);
                $end = strtotime($request->end_time);
                $diffInMinutes = ($end - $start) / 60;
        
                if ($value > $diffInMinutes) {
                    $fail('Slot time must not be greater than the time difference between start and end time.');
                }
            }],
        ]);
        if ($validator->fails()) {
            return response()->json([
				'error_msg' => $validator->errors()->all()[0],
				'status' => false,
			], 422);
        } else {
            $existSchedule = $this->telehealthLocationScheduleEventService->getDataByScheduleId($request->edit_id);
            if(empty($existSchedule) || (isset($existSchedule) && $existSchedule->count()) == 0){
                $updateData = array(
                    'title' => $request->input('title'),
                    'start_time' => date('H:i:s', strtotime(request('start_time'))),
                    'end_time' => date('H:i:s', strtotime(request('end_time'))),
                    'slot' => $request->input('slot')
                );
                $this->telehealthLocationScheduleService->update($updateData, array('id' => $request->input('edit_id')));
                $this->telehealthLocationScheduleSlotService->SoftDelete(array('del_flag' => 'Y'), array('loc_schedule_id' => $request->input('edit_id')));
                $this->telehealthLocationScheduleDayService->SoftDelete(array('del_flag' => 'Y'), array('schedule_id' => $request->input('edit_id')));
                // Store day wise data
                $day = $request->input('days');
                $dayData = array();
                foreach ($day as $value) {
                    $dayData = array(
                        'schedule_id' => $data['id'],
                        'day' => $value,
                    );
                    $this->telehealthLocationScheduleDayService->save($dayData);
                }

                // Generate and store slot wise data
                $slots = $this->generateTimeSlots($updateData['start_time'], $updateData['end_time'], $updateData['slot']);
                foreach ($slots as $slot) {
                    $slotData = [
                        'location_id' => $data['location_id'],
                        'loc_schedule_id' => $request->input('edit_id'),
                        'slot_start_time' => $slot['start_time'],
                        'slot_end_time' => $slot['end_time'],
                    ];
                    $this->telehealthLocationScheduleSlotService->save($slotData);
                }
                // $ipaddress = request()->getClientIp();
                $ipaddress = Utility::getIP();
                $insertLog = [
                    'type' => 'Update Telehealth Location Schedule',
                    'link' => url('/location-telehealth-schedule/update'),
                    'module' => 'Telehealth Location Schedule',
                    'object_id' => $locationsId,
                    'message' => $user->first_name . ' ' . $user->last_name . ' has updated Telehealth Location Schedule',
                    'new_response' => serialize($data),
                    'ip' => $ipaddress,
                ];
                $this->logService->save($insertLog);

                return response()->json(['status' => true, 'error_msg' => 'Telehealth Location schedule successfully updated.'], 200);
            }else{
                return response()->json(['status' => true, 'error_msg' => 'Sorry, You can not edit this schedule because this is used in the events.'], 500);
            }
        }
    }

    public function delete($id)
    {
        $user = auth()->user();
        $data['id'] = $id;
        //check if slot is used in telehealth patient do not delete
        $ids = $this->telehealthLocationScheduleEventService->getEventScheduleData($id);
        $existData = $this->patientService->getPatientScheduledData($ids);
        if($existData == 0){
            $update = $this->telehealthLocationScheduleService->SoftDelete(array('del_flag' => 'Y'), array('id' => $id));
            $this->telehealthLocationScheduleSlotService->SoftDelete(array('del_flag' => 'Y'), array('loc_schedule_id' => $id));
            $this->telehealthLocationScheduleEventService->SoftDelete(array('del_flag' => 'Y'), array('schedule_id' => $id));
            $this->telehealthLocationScheduleDayService->SoftDelete(array('del_flag' => 'Y'), array('schedule_id' => $id));
            $ipaddress = Utility::getIP();
            $insertLog = [
                'type' => 'Delete Telehealth Location Schedule',
                'link' => url('/telehealth-location-schedule/delete/'.$id),
                'module' => 'Telehealth Location Schedule',
                'object_id' => $id,
                'message' => $user->first_name . ' ' . $user->last_name . ' has deleted Telehealth Location Schedule',
                'new_response' => serialize($data),
                'ip' => $ipaddress,
            ];
            $this->logService->save($insertLog);

            if ($update) {
                return response()->json(['status' => true, 'error_msg' => 'Telehealth Location schedule successfully deleted.'], 200);
            } else {
                Session::flash('error', 'Sorry, something went wrong. Please try again.');
                return response()->json(['status' => true, 'error_msg' => 'Sorry, something went wrong. Please try again'], 500);
            }
        }else{
            return response()->json(['status' => true, 'error_msg' => 'Sorry, You can not edit this schedule because this is used in the events.'], 500);
        }
    }

    public function locationWiseScheduleLogs(Request $request)
    {
        $id = request('id');
        $data['user'] = auth()->user();
        $data['logList'] = LogsService::getDatByAllLog($id,'Telehealth Location Schedule');

        return view("user_log_ajax_list", $data);
    }

    public function scheduleEnabledDisabled(Request $request){
        $details = $this->telehealthLocationScheduleService->getDetailbyId($request->id);
        $status = "Y";
        $message ="Disabled";
        if($details->disable_status =='Y'){
            $status = "N";
            $message ="Enabled";
        }
        $existSchedule = $this->telehealthLocationScheduleEventService->getDataByScheduleId($request->id);
        if(empty($existSchedule) || (isset($existSchedule) && $existSchedule->count()) == 0){
            $this->telehealthLocationScheduleService->update(array('disable_status'=>$status),array('id'=>$request->id));
            return response()->json(['status'=>true,'message'=>'Status successfully '.$message,'data'=>array('status'=>$status) ]);
        }else{
            return response()->json(['status' => true, 'error_msg' => 'Sorry, You can not disable this schedule because this is used in the events.'], 500);
        }
    }

    public function teleHealthMange(Request $request){
        $data['locations'] = $this->locationMasterService->getLocationsData();
        $data['nurse'] = User::getNurses();
		$langArray = array();
		foreach($data['nurse'] as $nurse){
			if(isset($nurse->nurseLanguages)){
				$languages = array();
				foreach($nurse->nurseLanguages as $nLang){
                    if(isset($nLang->languages[0])){
					    $languages[] = $nLang->languages[0]['name'];  
                    }
				}
				$langArray[$nurse['id']]['language'] = implode(',', $languages);
				$langArray[$nurse['id']]['name'] = $nurse['name'];  
			}
		}
		$data['nurse'] = $langArray;
        $disable_date = $this->disableDateService->disableDateAllData()->toArray();
        $dateArray = explode(', ', implode(', ', $disable_date));
        $dateDetailArray = [];
        if (!empty($dateArray[0])) {
            foreach ($dateArray as $val) {
                $dateDetailArray[] = date('d-m-Y', strtotime($val));
            }
        }
        $slotsData = PatientTelehealthSchedule::where('del_flag', 'N')
            ->orderBy('start_time')
            ->get(['start_time', 'end_time', 'slot','id','day'])->groupBy('day')->toArray();
        $flg = 0 ;
        $slotCount = count($slotsData);
        foreach($slotsData as $slotd){
            $dataC = count($slotd); 
            if($dataC == 40){
                $flg++;
            }
        }
        if($slotCount > 0 && $slotCount == $flg){
            $data['showFlag'] = 1;
        }
        $data['disable_date']  = json_encode($dateDetailArray);
        return view("telehealthLocationSchedule/manage_telehealth",$data);
    }

    public function getLocationTypeWise(Request $request){
        $details = $this->locationMasterService->getLocationTypeWise($request->telehealth_config);
        return response()->json(['status'=>true,'message'=>'data get successfully','data'=>$details ]);
    }
}
