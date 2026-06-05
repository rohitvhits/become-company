<?php

namespace App\Services;

use App\Model\Patient;
use App\Helpers\Utility;
use App\Model\ThirdPartyPatientMaster;
use App\Model\PatientNotes;
use App\Model\PatientServiceRequest;
use App\Model\DocumentPatient;

class AnalyticsDashboardService
{
	public function currentInprogressPatientData($agency_id,$type){
        $auth = auth()->user();
		$where = '';
		if (in_array($auth['user_type_fk'], array(184))) {
			$addCondition="";
			if($auth->record_access !='All'){
				$addCondition=" and patient_master.type='".$auth->record_access."'";
			}
			$where = 'patient_master.deleted_flag ="N"'. $addCondition;
			$agencyids = Utility::getUserWiseAgencyDashboard();
			if(!empty($agencyids)){
				$implodeIds = implode('","', $agencyids);
				$where .= ' and patient_master.agency_id IN("' . $implodeIds . '")';
			}
		} else {
			$addCondition="";
			if($auth->record_access !='All'){
				$addCondition=" and patient_master.type='".$auth->record_access."'";
			}
			$where = 'patient_master.deleted_flag ="N" '.$addCondition.'';
			
			$serviceIds = Utility::getServiceByAgency();
			$finalService = '';
			if(!empty($serviceIds[0])){
				foreach($serviceIds as $key=>$srv){
					$or = '';
					if($key !=0){
						$or = ' OR ';
					}
					$finalService .= $or .' FIND_IN_SET("'.$srv.'",patient_master.service_id)';
				}
				$where .= ' and ('.$finalService.')';
			}
		}
		if (in_array($auth->agency_fk,array(5,6)) || $auth->login_type_fk == 2) {
			
			$agencyids = Utility::getUserWiseAgencyDashboard();
			$agencyids[] = $auth['agency_fk'];
			
			if(!empty($agencyids)){
				$implodeIds = implode('","', $agencyids);
				$where .= ' and patient_master.agency_id IN("' . $implodeIds . '")';
			}
		}
		$query = PatientServiceRequest::with('patient.agencyDetail:id,agency_name', 'statusUserDetails','patientServiceRequestRelationShip.requestService','patient.locations:id,location_name')->where('del_flag','N');
		$query->whereHas('patientServiceRequestRelationShip',function($q) {
			$q->where('service_id','!=','')->where('del_flag','N');
		});
		$query = $query->whereHas('patient', function ($q) use($where) {
			$q->where('id', '!=', NULL);
			$q->where('archived_at', '=', NULL);
			$q->whereRaw($where);
		});
		if (!empty($agency_id)) {
			$query = $query->whereHas('patient', function ($q) use ($agency_id) {
				$q->whereIn('agency_id',$agency_id);
			});
		}
		$query = $query->whereHas('patient', function ($q){
			$q->where('deleted_flag','N');
		});
		if (!empty($type)) {
			$query = $query->whereHas('patient', function ($q) use ($type) {
				$q->where('type',$type);
			});
		}
		$query->whereHas('patient.agencyDetail', function ($q) {
			$q->where('agency.delete_flag', 'N');
		});
		$query->whereNotNull('status');
		$query->whereNotNull('last_status_update');
		$query = $query->where(function ($q) {
			$q->where('patient_service_requests.status', 'processing')
				  ->orWhere('patient_service_requests.status', 'arrived')
				  ->orWhere('patient_service_requests.status', 'checkin');
		})->orderByDesc('last_status_update')
        ->skip(0)->take(20)->get();
		return $query;
	}

    public function visitingAidTypeData($agency_id,$type){
		$where = '';
		$auth = auth()->user();
		if (in_array($auth['user_type_fk'], array(184))) {
			$where = 'deleted_flag ="N"';
			$agencyids = Utility::getUserWiseAgencyDashboard();
			if(!empty($agencyids)){
				$implodeIds = implode('","', $agencyids);
				$where .= ' and agency_id IN("' . $implodeIds . '")';
			}
		} else{
			$where .= " deleted_flag = 'N' " ;
		}
		if (in_array($auth->agency_fk,array(5,6)) || $auth->login_type_fk == 2) {
			
			$agencyids = Utility::getUserWiseAgencyDashboard();
			$agencyids[] = $auth['agency_fk'];
			
			if(!empty($agencyids)){
				$implodeIds = implode('","', $agencyids);
				$where .= ' and agency_id IN("' . $implodeIds . '")';
			}
		}
		$query = ThirdPartyPatientMaster::select('id','first_name','last_name','created_by','third_party_patient_master.created_date','updated_date','agency_id','mobile','location_id','status','type','patient_id')->with(['patientDetails:id,first_name,last_name','agencyDetails:id,agency_name','userDetails:id,first_name,last_name'])->where('deleted_flag','N')->whereRaw($where);
		$query->where('platform_type','!=','arla');
		if (!empty($agency_id)) {
			$query->whereIn('agency_id',$agency_id);
		}
		if (!empty($type)) {
			$query->where('type',ucfirst($type));
		}
		$query = $query->whereHas('patientDetails', function ($q){
			$q->where('deleted_flag','N');
		});
		$query->whereHas('patientDetails.agencyDetail', function ($q) {
			$q->where('agency.delete_flag', 'N');
		});
		$query->orderBy('third_party_patient_master.created_date','desc');
		return $query->skip(0)->take(20)->get();
	}

    public function getRecentNotesByAgencyUser($agency_id,$type){
        $where = '';
		// $auth = auth()->user();
		// if (in_array($auth['user_type_fk'], array(184))) {
		// 	$where = 'deleted_flag ="N"';
		// 	$agencyids = Utility::getUserWiseAgencyDashboard();
		// 	if(!empty($agencyids)){
		// 		$implodeIds = implode('","', $agencyids);
		// 		$where .= ' and agency_id IN("' . $implodeIds . '")';
		// 	}
		// } else{
		// 	$where .= " deleted_flag = 'N' " ;
		// }
		// if (in_array($auth->agency_fk,array(5,6)) || $auth->login_type_fk == 2) {
			
		// 	$agencyids = Utility::getUserWiseAgencyDashboard();
		// 	$agencyids[] = $auth['agency_fk'];
			
		// 	if(!empty($agencyids)){
		// 		$implodeIds = implode('","', $agencyids);
		// 		$where .= ' and agency_id IN("' . $implodeIds . '")';
		// 	}
		// }
		
		$query =  PatientNotes::with(['patient','userDetails:id,first_name,last_name,agency_fk','userDetails.agencyDetails:id,agency_name']);
		if (!empty($agency_id)) {
			$query = $query->whereHas('patient', function ($q) use ($agency_id) {
				$q->whereIn('agency_id',$agency_id);
			});
		}
		if (!empty($type)) {
			$query = $query->whereHas('patient', function ($q) use ($type) {
				$q->where('type',$type);
			});
		}
		$query = $query->whereHas('patient', function ($q){
			$q->where('deleted_flag','N');
		});
		$query->whereHas('patient.agencyDetail', function ($q) {
			$q->where('agency.delete_flag', 'N');
		});
		$query =  $query->orderBy('patient_notes.created_date','desc')->skip(0)->take(20)->get();
		return $query;
	}

    public function recentUpdateStatusData($agency_id,$type){
        $where = '';
		$auth = auth()->user();
		if (in_array($auth['user_type_fk'], array(184))) {
			$where = 'deleted_flag ="N"';
			$addCondition="";
			if($auth->record_access !='All'){
				$addCondition=" and patient_master.type='".$auth->record_access."'";
			}
			$agencyids = Utility::getUserWiseAgencyDashboard();
			if(!empty($agencyids)){
				$implodeIds = implode('","', $agencyids);
				$where .= ' and agency_id IN("' . $implodeIds . '")'. $addCondition;
			}
		} else{
			$addCondition="";
			if($auth->record_access !='All'){
				$addCondition=" and patient_master.type='".$auth->record_access."'";
			}
			$where .= " deleted_flag = 'N' ".$addCondition ;
		}
		if (in_array($auth->agency_fk,array(5,6)) || $auth->login_type_fk == 2) {
			
			$agencyids = Utility::getUserWiseAgencyDashboard();
			$agencyids[] = $auth['agency_fk'];
			
			if(!empty($agencyids)){
				$implodeIds = implode('","', $agencyids);
				$where .= ' and agency_id IN("' . $implodeIds . '")';
			}
		}
		$query = PatientServiceRequest::with('patient.agencyDetail:id,agency_name', 'statusUserDetails:id,first_name,last_name','patientServiceRequestRelationShip.requestService','patient.locations:id,location_name')->where('del_flag','N');
		$query->whereHas('patientServiceRequestRelationShip',function($q) {
			$q->where('service_id','!=','')->where('del_flag','N');
		});
		$query = $query->whereHas('patient', function ($q) use($where) {
			$q->where('id', '!=', NULL);
			$q->where('archived_at', '=', NULL);
			$q->whereRaw($where);
		});
		if (!empty($agency_id)) {
			$query = $query->whereHas('patient', function ($q) use ($agency_id) {
				$q->whereIn('agency_id',$agency_id);
			});
		}
		if (!empty($type)) {
			$query = $query->whereHas('patient', function ($q) use ($type) {
				$q->where('type',$type);
			});
		}
		$query = $query->whereHas('patient', function ($q){
			$q->where('deleted_flag','N');
		});
		$query->whereHas('patient.agencyDetail', function ($q) {
			$q->where('agency.delete_flag', 'N');
		});
		$query->whereNotNull('status');
		$query->whereNotNull('last_status_update');
		$query = $query->orderByDesc('last_status_update')
        ->skip(0)->take(20)->get();
		return $query;
    }

	public function visitingDueDateData($agency_id,$type){
		$where = '';
		$auth = auth()->user();
		if (in_array($auth['user_type_fk'], array(184))) {
			$where = 'deleted_flag ="N"';
			$agencyids = Utility::getUserWiseAgencyDashboard();
			if(!empty($agencyids)){
				$implodeIds = implode('","', $agencyids);
				$where .= ' and agency_id IN("' . $implodeIds . '")';
			}
		} else{
			$where .= " deleted_flag = 'N' " ;
		}
		if (in_array($auth->agency_fk,array(5,6)) || $auth->login_type_fk == 2) {
			
			$agencyids = Utility::getUserWiseAgencyDashboard();
			$agencyids[] = $auth['agency_fk'];
			
			if(!empty($agencyids)){
				$implodeIds = implode('","', $agencyids);
				$where .= ' and agency_id IN("' . $implodeIds . '")';
			}
		}
		$query = ThirdPartyPatientMaster::select('id','first_name','last_name','created_by','third_party_patient_master.created_date','updated_date','agency_id','mobile','location_id','status','type','patient_id','due_date')->with(['patientDetails:id,first_name,last_name','agencyDetails:id,agency_name','userDetails:id,first_name,last_name'])->where('deleted_flag','N')->whereRaw($where);
		$query->where('platform_type','!=','arla');
		if (!empty($agency_id)) {
			$query->whereIn('agency_id',$agency_id);
		}
		if (!empty($type)) {
			$query->where('type',ucfirst($type));
		}
		$query = $query->whereHas('patientDetails', function ($q){
			$q->where('deleted_flag','N');
		});
		$query->whereNotIn('agency_id',[2,424]);
		$query->whereHas('patientDetails.agencyDetail', function ($q) {
			$q->where('agency.delete_flag', 'N');
		});
		$query->orderBy('third_party_patient_master.due_date','asc');
		return $query->skip(0)->take(20)->get();
	}

	public function locationWiseStatusData($agency_id,$type){
        $auth = auth()->user();
		$where = '';
		if (in_array($auth['user_type_fk'], array(184))) {
			$where = 'patient_master.deleted_flag ="N"';
			$agencyids = Utility::getUserWiseAgencyDashboard();
			if(!empty($agencyids)){
				$implodeIds = implode('","', $agencyids);
				$where .= ' and patient_master.agency_id IN("' . $implodeIds . '")';
			}
		} else {
			$addCondition="";
			if($auth->record_access !='All'){
				$addCondition=" and patient_master.type='".$auth->record_access."'";
			}
			$where = 'patient_master.deleted_flag ="N" '.$addCondition.'';
			
			$serviceIds = Utility::getServiceByAgency();
			$finalService = '';
			if(!empty($serviceIds[0])){
				foreach($serviceIds as $key=>$srv){
					$or = '';
					if($key !=0){
						$or = ' OR ';
					}
					$finalService .= $or .' FIND_IN_SET("'.$srv.'",patient_master.service_id)';
				}
				$where .= ' and ('.$finalService.')';
			}
		}
		if (in_array($auth->agency_fk,array(5,6)) || $auth->login_type_fk == 2) {
			
			$agencyids = Utility::getUserWiseAgencyDashboard();
			$agencyids[] = $auth['agency_fk'];
			
			if(!empty($agencyids)){
				$implodeIds = implode('","', $agencyids);
				$where .= ' and patient_master.agency_id IN("' . $implodeIds . '")';
			}
		}
		$query = PatientServiceRequest::select(
			'patient_service_requests.*', 
			'patient_master.location_id',
		)
		->join('patient_master', 'patient_service_requests.patient_id', '=', 'patient_master.id')
		->whereHas('patientServiceRequestRelationShip',function($q) {
			$q->where('service_id','!=','')->where('del_flag','N');
		})
		->where('patient_service_requests.del_flag', 'N')
		->whereRaw($where)
		->whereNotNull('patient_service_requests.status')
		->whereNotNull('patient_master.location_id')
		->whereNull('patient_master.archived_at');
		if (!empty($agency_id)) {
			$query->whereIn('patient_master.agency_id',$agency_id);
		}
		if (!empty($type)) {
			$query->where('patient_master.type',$type);
		}
		$query->whereHas('patient.agencyDetail', function ($q) {
			$q->where('agency.delete_flag', 'N');
		});
		$query = $query->orderByDesc('patient_service_requests.last_status_update')
		->get();
		return $query;
	}

	public function countStatusData($agency_id,$type){
		$where = '';
		$auth = auth()->user();
		if (in_array($auth['user_type_fk'], array(184))) {
			$where = 'deleted_flag ="N"';
			$agencyids = Utility::getUserWiseAgencyDashboard();
			if(!empty($agencyids)){
				$implodeIds = implode('","', $agencyids);
				$where .= ' and agency_id IN("' . $implodeIds . '")';
			}
		} else{
			$where .= " deleted_flag = 'N' " ;
		}
		if (in_array($auth->agency_fk,array(5,6)) || $auth->login_type_fk == 2) {
			
			$agencyids = Utility::getUserWiseAgencyDashboard();
			$agencyids[] = $auth['agency_fk'];
			
			if(!empty($agencyids)){
				$implodeIds = implode('","', $agencyids);
				$where .= ' and agency_id IN("' . $implodeIds . '")';
			}
		}
		$query = PatientServiceRequest::select(\DB::raw("COUNT(id) as count"),"status")->where('del_flag','N');
		$query->whereHas('patient',function($q) {
			$q->where('deleted_flag','N');
			$q->where('id', '!=', NULL);
			$q->where('archived_at', '=', NULL);
		});
		$query->whereHas('patientServiceRequestRelationShip',function($q) {
			$q->where('service_id','!=','')->where('del_flag','N');
		});
		if (!empty($agency_id)) {
			$query = $query->whereHas('patient', function ($q) use ($agency_id) {
				$q->whereIn('agency_id',$agency_id);
			});
		}
		if (!empty($type)) {
			$query = $query->whereHas('patient', function ($q) use ($type) {
				$q->where('type',$type);
			});
		}
		$query = $query->whereHas('patient', function ($q){
			$q->where('deleted_flag','N');
		});
		$query->whereHas('patient.agencyDetail', function ($q) {
			$q->where('agency.delete_flag', 'N');
		});
		$query =$query->whereIn('status',['processing','arrived','checkin'])->groupBy('status');

		return $query->get()->toArray();
		// $where = '';
		// $auth = auth()->user();
		
		// if (in_array($auth['user_type_fk'], array(184))) {
		// 	$where = 'deleted_flag ="N"';
		// 	$agencyids = Utility::getUserWiseAgencyDashboard();
		// 	if(!empty($agencyids)){
		// 		$implodeIds = implode('","', $agencyids);
		// 		$where .= ' and agency_id IN("' . $implodeIds . '")';
		// 	}
		// } else{
		// 	$where .= " deleted_flag = 'N' " ;
		// }
		// if (in_array($auth->agency_fk,array(5,6)) || $auth->login_type_fk == 2) {
			
		// 	$agencyids = Utility::getUserWiseAgencyDashboard();
		// 	$agencyids[] = $auth['agency_fk'];
			
		// 	if(!empty($agencyids)){
		// 		$implodeIds = implode('","', $agencyids);
		// 		$where .= ' and agency_id IN("' . $implodeIds . '")';
		// 	}
		// }
		// $query = PatientServiceRequest::with(['patient:id,deleted_flag'])->where('del_flag','N');
		// $query = $query->whereHas('patient', function ($q) use($where) {
		
		// 	$q->whereNull('archived_at');
		// 	$q->whereRaw($where);
		// });
		// if (!empty($agency_id)) {
		// 	$query = $query->whereHas('patient', function ($q) use ($agency_id) {
		// 		$q->whereIn('agency_id',$agency_id);
		// 	});
		// }
		// $query = $query->where(function ($q) {
		// 	$q->where('patient_service_requests.status', 'processing')
		// 		  ->orWhere('patient_service_requests.status', 'arrived')
		// 		  ->orWhere('patient_service_requests.status', 'checkin');
		// });
		// $query->where('del_flag','N');
		
		// return $query->get()->toArray();
	}

	public function documentRecentData($agency_id,$type){
		$where = '';
		$auth = auth()->user();
		if (in_array($auth['user_type_fk'], array(184))) {
			$where = 'deleted_flag ="N"';
			$agencyids = Utility::getUserWiseAgencyDashboard();
			if(!empty($agencyids)){
				$implodeIds = implode('","', $agencyids);
				$where .= ' and agency_id IN("' . $implodeIds . '")';
			}
		} else{
			$where .= " deleted_flag = 'N' " ;
		}
		if (in_array($auth->agency_fk,array(5,6)) || $auth->login_type_fk == 2) {
			
			$agencyids = Utility::getUserWiseAgencyDashboard();
			$agencyids[] = $auth['agency_fk'];
			
			if(!empty($agencyids)){
				$implodeIds = implode('","', $agencyids);
				$where .= ' and agency_id IN("' . $implodeIds . '")';
			}
		}

		$auth = auth()->user();
		$query = DocumentPatient::with(['userDetails:id,first_name,last_name','patientDetails:id,type,first_name,last_name,agency_id','patientDetails.agencyDetail:id,agency_name,app_name'])->where('deleted_flag','N')->whereNotNull('attachment');
		if (!empty($agency_id)) {
			$query = $query->whereHas('patientDetails', function ($q) use ($agency_id) {
				$q->whereIn('agency_id',$agency_id);
			});
		}
		if (!empty($type)) {
			$query = $query->whereHas('patientDetails', function ($q) use ($type) {
				$q->where('type',$type);
			});
		}
		$query = $query->whereHas('patientDetails', function ($q) {
			$q->where('deleted_flag','N');
		});
		$query->whereHas('patientDetails.agencyDetail', function ($q) {
			$q->where('agency.delete_flag', 'N');
		});
		$query->orderBy('created_date','desc');
		return $query->skip(0)->take(20)->get();
	}

	public function agencyWiseStatusData($agency_id,$type){
        $auth = auth()->user();
		$where = '';
		if (in_array($auth['user_type_fk'], array(184))) {
			$where = 'patient_master.deleted_flag ="N"';
			$agencyids = Utility::getUserWiseAgencyDashboard();
			if(!empty($agencyids)){
				$implodeIds = implode('","', $agencyids);
				$where .= ' and patient_master.agency_id IN("' . $implodeIds . '")';
			}
		} else {
			$addCondition="";
			if($auth->record_access !='All'){
				$addCondition=" and patient_master.type='".$auth->record_access."'";
			}
			$where = 'patient_master.deleted_flag ="N" '.$addCondition.'';
			
			$serviceIds = Utility::getServiceByAgency();
			$finalService = '';
			if(!empty($serviceIds[0])){
				foreach($serviceIds as $key=>$srv){
					$or = '';
					if($key !=0){
						$or = ' OR ';
					}
					$finalService .= $or .' FIND_IN_SET("'.$srv.'",patient_master.service_id)';
				}
				$where .= ' and ('.$finalService.')';
			}
		}
		if (in_array($auth->agency_fk,array(5,6)) || $auth->login_type_fk == 2) {
			
			$agencyids = Utility::getUserWiseAgencyDashboard();
			$agencyids[] = $auth['agency_fk'];
			
			if(!empty($agencyids)){
				$implodeIds = implode('","', $agencyids);
				$where .= ' and patient_master.agency_id IN("' . $implodeIds . '")';
			}
		}
		$query = PatientServiceRequest::select(
			'patient_service_requests.*', 
			'patient_master.agency_id',
		)
		->join('patient_master', 'patient_service_requests.patient_id', '=', 'patient_master.id')
		->whereHas('patientServiceRequestRelationShip',function($q) {
			$q->where('service_id','!=','')->where('del_flag','N');
		})
		->where('patient_service_requests.del_flag', 'N')
		->whereRaw($where)
		->whereNotNull('patient_service_requests.status')
		->whereNotNull('patient_master.agency_id')
		->whereNull('patient_master.archived_at');
		if (!empty($agency_id)) {
			$query->whereIn('patient_master.agency_id',$agency_id);
		}
		if (!empty($type)) {
			$query->where('patient_master.type',$type);
		}
		$query->whereHas('patient.agencyDetail', function ($q) {
			$q->where('agency.delete_flag', 'N');
		});
		$query = $query->orderByDesc('patient_service_requests.last_status_update')
		->get();
		return $query;
	}
}