<?php

namespace App\Http\Controllers;

use App\Helpers\Utility;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Agency;
use App\User;
use App\Services\TelehealthLocationScheduleEventService;
use App\Services\AppointmentService;
use App\Services\LocationMasterService;
use App\Services\LanguageService;
use App\Services\UserService;
use Illuminate\Support\Facades\Cache;

class TelehealthBookReportController extends BaseController
{
    protected $telehealthLocationScheduleEventService,$locationMasterService,$appointmentService,$languageService,$userService="";
    public function __construct(TelehealthLocationScheduleEventService $telehealthLocationScheduleEventService, LocationMasterService $locationMasterService, AppointmentService $appointmentService, LanguageService $languageService, UserService $userService)
    {
        $this->middleware('permission:telehealth-booking-report', ['only' => ['index', 'ajaxList']]);
        $this->middleware('permission:telehealth-booking-report-export', ['only' => ['exportcsv']]);

        $this->middleware('auth');
        $this->telehealthLocationScheduleEventService = $telehealthLocationScheduleEventService;
        $this->locationMasterService = $locationMasterService;
        $this->appointmentService = $appointmentService;
        $this->languageService = $languageService;
        $this->userService = $userService;
    }

    public function index(Request $request){
        $data['menu'] = "user";
        $data['user']= $user= auth()->user();
        $data['agency_list'] = Agency::getAgencyList();
        $data['location_list'] = Cache::get('patient_master_locations', function () {
			return $this->locationMasterService->getLocationsData();
		}, 10 * 60);
        $data['nurse_list'] = User::getNurses();
        $data['language_list'] = $this->languageService->getLanguageList();
        return view("telehealthBookReport/index", $data);
    }
    public function ajaxList(Request $request){
       $users = $this->userService->getAllNyUserList();
       foreach($users as $user){
            $userArray[$user->id] = $user->name;
       }
       $data['query'] = $this->appointmentService->getAllTelehealthAppointments($request->all());
       $nurse = User::getNurses();
        $langArray = array();
        foreach($nurse as $nurse){
        if(isset($nurse->nurseLanguages)){
            $languages = array();
            foreach($nurse->nurseLanguages as $nLang){
                if(isset($nLang->languages[0])){
                    $languages[] = $nLang->languages[0]['name'];
                }
            }
            $langArray[$nurse['id']]['language'] = implode(',', $languages);
        }
        }
        $nurse = $langArray;
       foreach($data['query'] as $query){
            $query->nurse_name = "-";
            $query->nurse_name = array_key_exists($query->nurse_id,$userArray)?$userArray[$query->nurse_id]:'-';
            if(array_key_exists($query->nurse_id,$nurse)){
                $query->nurse_name .= '('.$nurse[$query->nurse_id]['language'].')';
            }
            $query->created_by_name = array_key_exists($query->created_by,$userArray)?$userArray[$query->created_by]:'-';
       }
       return view('telehealthBookReport.ajax_list',$data);
    }

    public function exportCsv(Request $request){
        $detail = $this->appointmentService->getAllTelehealthAppointments($request->all(),'export');
		$filename = 'telehealth_book_report' . date("m-d-Y");
		$headers = array(
			"Content-type" => "text/csv",
			"Content-Disposition" => "attachment; filename=" . $filename . ".csv",
			"Pragma" => "no-cache",
			"Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
			"Expires" => "0",
		);

        if(count($detail) > 0){
            $columns = array('#','Agency Name', 'Type' , 'Portal Id', 'Portal Name','Appointment Date' , 'Appointment Time', 'Nurse', 'Language','Created at','Created By');
            $callback = function () use ($detail, $columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);
                $users = $this->userService->getAllNyUserList();
                $nurseLang = User::getNurses();
                $langArray = array();
                foreach($nurseLang as $nurse){
                if(isset($nurse->nurseLanguages)){
                    $languages = array();
                    foreach($nurse->nurseLanguages as $nLang){
                        if(isset($nLang->languages[0])){
                            $languages[] = $nLang->languages[0]['name'];
                        }
                    }
                    $langArray[$nurse['id']]['language'] = implode(',', $languages);
                }
                }
                $nurseLang = $langArray;
                foreach($users as $user){
                        $userArray[$user->id] = $user->name;
                }
                foreach ($detail as $key => $val) {
                    $agencyName = isset($val->patient->agencyDetail->agency_name) ? $val->patient->agencyDetail->agency_name : '';
                    $patientId = isset($val->patient->id) ? $val->patient->id : '';
                    $type = isset($val->patient->type) ? $val->patient->type : '';
                    $patientName = isset($val->patient->first_name) && isset($val->patient->last_name) ? $val->patient->first_name.' '.$val->patient->last_name : '';
                    $appointment_date = isset($val->telehealth_date) ? date('m/d/Y', strtotime($val->telehealth_date)) : '';
                    if(isset($val->start_time)){
                        $appointment_time = date('H:i A', strtotime($val->start_time)). '-' .date('H:i A', strtotime($val->end_time));
                    }
                    $language = isset($val->name) ? $val->name : '';
                    $nurse = $createdBy = "";
                    $nurse = array_key_exists($val->nurse_id,$userArray)?$userArray[$val->nurse_id]:'-';
                    if(array_key_exists($val->nurse_id,$nurseLang)){
                        $nurse .= '('.$nurseLang[$val->nurse_id]['language'].')';
                    }
                    $createdBy = array_key_exists($val->created_by,$userArray)?$userArray[$val->created_by]:'-';
                    fputcsv($file, array($key+1, $agencyName, $type,$patientId, $patientName ,$appointment_date, $appointment_time, $nurse, $language, Utility::convertMDYTime($val->created_at), $createdBy));
                }
                fclose($file);
            };
            return response()->stream($callback, 200, $headers);
        }else{
            return null;
        }
        
    }
    
}