<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use App\Helpers\Utility;
use App\Master;
use App\Agency;
use App\Services\PatientService;
use Illuminate\Support\Facades\Cache;
use App\User;
use App\Services\TelehealthLocationScheduleEventService;
class SearchController extends BaseController
{
	
	protected $patientService = "";
	protected $telehealthLocationScheduleEventService = "";
    public function __construct(PatientService $patientService, TelehealthLocationScheduleEventService $telehealthLocationScheduleEventService)
    {
        $this->middleware('auth');
		$this->patientService = $patientService;
		$this->telehealthLocationScheduleEventService = $telehealthLocationScheduleEventService;
    }
	
	public function index(){
		$data['user']= $auth = auth()->user();
		if($data['user']->agency_fk ==""){
			if(!auth()->user()->can('appointments-list')){
				abort(404);
			}
		}
		$data['search'] = $search = addslashes(request('search_global'));
		$data['appointment_details'] = [];
		if($search !=""){
			$angecyList= Cache::get('patient_master_locations', function () {
				return Agency::where('delete_flag', 'N')->orderBy('agency_name', 'asc')->get();
			},10 * 60);
			$agencyIds = $angecyList->pluck('id');
			$query = $this->patientService->search($search,$agencyIds);
			
			$nurse= Cache::get('patient_master_nurse_user', function () {
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
	
				return $langArray;
			}, 10 * 60);
			$agencyIds=[];
			if (!empty($auth->agency_fk)) {
				if ($auth->login_type_fk == 2) {
					$agencyIds = Utility::getUserWiseAgency();
					$agencyIds[] = $auth->agency_fk;
				} else {
					$agencyIds = [$auth->agency_fk];
				}
			}
			
			$data['agencyIds'] = $agencyIds;
			
			foreach ($query as $vsl) {
				
				$assign_fname = '';
				$assign_lname = '';
				if($vsl->assign_user_id !=""){
					$getAssignNyUser = Cache::get('patient_get_user_' . $vsl->assign_user_id, function () use ($vsl) {
						return User::getDetailsById($vsl->assign_user_id);
					}, 10 * 60);

					if (isset($getAssignNyUser->first_name) && $getAssignNyUser->first_name != '') {
						$assign_fname = $getAssignNyUser->first_name;
					}
					if (isset($getAssignNyUser->last_name) && $getAssignNyUser->last_name != '') {
						$assign_lname = $getAssignNyUser->last_name;
					}
				}
				
			
				$agency=$angecyList->firstWhere('id', $vsl->agency_id);
				$vsl->agency_name=$agency?$agency->agency_name:'';
	
				$vsl->assign_user_name = $assign_fname . ' ' . $assign_lname;

				$lname = $vsl->users->last_name??"";
				$vsl->created_by_username = $vsl->users->first_name?? "" . ' ' . $lname;
				$explode = explode(',', $vsl->service_id);
				$newss = $vsl->service_id;
				if ($newss != '') {
					$sins = Cache::get('patient_master_' . implode(",", $explode), function () use ($explode) {
						return Master::select('name')->whereIn('id', $explode)->where('del_flag', 'N')->get();
					}, 10 * 60);
	
					$nrens = array();
					foreach ($sins as $names) {
						$nrens[$vsl->id][] = $names->name;
					}
				}
				$vsl->name = '';
				if (isset($nrens[$vsl->id]) && $nrens[$vsl->id] != '') {
					$vsl->name = implode(', ', $nrens[$vsl->id]);
				}
				
				if (!empty($vsl->telehealth_time_frame)) {
					// Patient type: nurse ID is stored directly in telehealth_nurse
					$rawNurseId = $vsl->telehealth_nurse;
					if (!empty($rawNurseId) && isset($nurse[$rawNurseId])) {
						$vsl->telehealth_nurse = 'C#' . $rawNurseId . '(' . $nurse[$rawNurseId]['language'] . ')';
					}
				} elseif (isset($vsl->telehealth_time_slot) && $vsl->telehealth_time_slot != "") {
					$telhealth = $this->telehealthLocationScheduleEventService->getTelehalthappointemntScheduledata($vsl->telehealth_time_slot);
					$vsl->telehealth_time_slot = isset($telhealth['start_time']) ? $telhealth['start_time'] . ' - ' . $telhealth['end_time'] : '';
					$nLanguage = "";
					if (!empty($telhealth['nurse_id']) && isset($nurse[$telhealth['nurse_id']]) && array_key_exists($telhealth['nurse_id'], $nurse)) {
						$nLanguage = 'C#' . $telhealth['nurse_id'] . '(' . $nurse[$telhealth['nurse_id']]['language'] . ')';
					}
					$vsl->telehealth_nurse = $nLanguage;
				}
			}
			$data['appointment_details'] =$query;
		}
		

		return view('globalsearch/global_search',$data);
	}
	
}