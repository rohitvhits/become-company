<?php

namespace App\Services;
use App\Model\FlagMarked;
use App\Helpers\Utility;
class FlagMarkedService
{

    public static function save($data)
    {
        $auth = auth()->user();
        $data['created_at'] = date('Y-m-d H:i:s');
        if (isset($auth['id'])) {
            $data['created_by'] = $auth['id'];
        }
        $insert = new FlagMarked($data);
        $insert_id = $insert->save();

        return $insert_id;
    }

    public static function getALLAAppointmentData($search,$type){
        $auth = auth()->user();
		$where = 'patient_master.deleted_flag ="N"';


		if (isset($search['full_name']) && $search['full_name'] != '') {

			$where .= ' and CONCAT_WS("",patient_master.first_name," ",patient_master.last_name) LIKE "%' . $search['full_name'] . '%"';
		}
		if (isset($search['mobile']) && $search['mobile'] != '' ) {
			$where .= ' and patient_master.mobile = "' . $search['mobile'] . '"';
		}

		if (isset($search['type']) && $search['type'] != '') {
			$where .= ' and patient_master.type = "' . $search['type'] . '"';
		}

		if (isset($search['service_id']) && $search['service_id'] != '') {
			$explode = explode(',', implode(',',$search['service_id']));
			$final = '';
			foreach($explode as $key=>$vals){
				$or = '';
				if($key !=0){
					$or = ' OR ';
				}
				$final .= $or .' FIND_IN_SET("'.$vals.'",patient_master.service_id)';
			}
			$where .= ' and ('.$final.')';
		}
		if (isset($search['appointment_date']) && $search['appointment_date'] != '') {
			$explode = explode('-', $search['appointment_date']);

			$where .= ' and DATE_FORMAT(patient_master.appointment_date,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($explode[0])) . '" and DATE_FORMAT(patient_master.appointment_date,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($explode[1])) . '"';
		}
		if (isset($search['status'] ) && $search['status'] != '') {
			$status = implode(',',$search['status']);
			$stats = strtolower($status);
			$where .= ' and LOWER(patient_master.status) IN( "' . $stats . '")';
		}

		if(isset($search['patient_code']) && $search['patient_code'] !=""){
			$where .=' and patient_master.patient_code ="'.$search['patient_code'].'"';
		}

		if (isset($search['created_date']) && $search['created_date'] != '') {
			$exploder = explode('-', $search['created_date']);
			$where .= ' and DATE_FORMAT(flag_marked.created_at,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($exploder[0])) . '" and DATE_FORMAT(patient_master.created_date,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($exploder[1])) . '"';
		}

		if (isset($search['agency_fk']) && $search['agency_fk'] != '') {
			$agency_fk = implode(',',$search['agency_fk']);
			$where .= ' and patient_master.agency_id IN(' . $agency_fk . ')';
		}
	
		// print_r($auth);
		if (in_array($auth->agency_fk,array(5,6)) || $auth->login_type_fk == 2) {
			// if ($auth->agency_fk != "") {
			// 	$where .= ' and patient_master.agency_id = "' . $auth->agency_fk . '"';
			// }

			$agencyids = Utility::getUserWiseAgency();
			$agencyids[] = $auth['agency_fk'];

			if(!empty($agencyids)){
				$implodeIds = implode('","', $agencyids);
				$where .= ' and patient_master.agency_id IN("' . $implodeIds . '")';
			}
		}

		if (isset($search['created_by']) && $search['created_by'] != '') {
			if($search['created_by'] !='undefined'){
				$where .= ' and flag_marked.created_by = '.$search['created_by'];
			}

		}

		if (isset($search['dob']) && $search['dob'] != '') {
			$explode = explode('-', $search['dob']);

			$where .= ' and DATE_FORMAT(patient_master.dob,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($explode[0])) . '" and DATE_FORMAT(patient_master.dob,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($explode[1])) . '"';
		}

		$addCondition="";
		if($auth->record_access !='All'){
			$addCondition=" and patient_master.type='".$auth->record_access."'";
		}
		if ($auth['agency_fk'] != '') {
			$where = 'patient_master.deleted_flag ="N" and patient_master.agency_id="' . $auth['agency_fk'] . '"'.$addCondition.'';
		} else {
			$agencyids = Utility::getUserWiseAgency();
			if(!empty($agencyids)){
				$implodeIds = implode('","', $agencyids);
				$where .= ' and patient_master.agency_id IN("' . $implodeIds . '")';
			}
		}

		if($auth->restrict_user ==1){
			$where .=" and patient_master.created_by='".$auth->id."'";
		}
        $query = FlagMarked::select('patient_master.*','flag_marked.created_at','flag_marked.reason as reasonNotes','agency.agency_name','users.first_name as uFname','users.last_name as uLname','flag_marked.is_flag_read','flag_marked.id as flag_id')
            ->leftjoin('patient_master', function ($join) {
                $join->on('patient_master.id', '=', 'flag_marked.record_id');
                $join->where('patient_master.deleted_flag', 'N');
            })
            ->leftjoin('users', function ($join) {
                $join->on('users.id', '=', 'flag_marked.created_by');
             
            })
           
            ->leftjoin('agency', function ($join) {
                $join->on('agency.id', '=', 'patient_master.agency_id');
                $join->where('agency.delete_flag', 'N');
		    })->whereRaw($where)->where('flag_marked.type',$type)->where('agency.delete_flag', 'N')->orderBy('flag_marked.id', 'desc')->Paginate(20);
           
		return $query;
    }

    public function getFlagAllDocumentByPatientIdAgency($type)
	{
	
		$auth = auth()->user();
		$query = FlagMarked::select('document_patient.*','flag_marked.created_at','flag_marked.reason as reasonNotes','users.first_name', 'users.last_name','flag_marked.is_flag_read','flag_marked.id as flag_id')
		->leftjoin('document_patient', function ($join) {
			$join->on('document_patient.id', '=', 'flag_marked.record_id');
			$join->where('document_patient.deleted_flag', 'N');
		})
        ->leftjoin('users', function ($join) {
			$join->on('users.id', '=', 'flag_marked.created_by');
			$join->where('users.delete_flag', 'N');
		})
        
        ->leftjoin('patient_master', function ($join) {
			$join->on('patient_master.id', '=', 'document_patient.patient_id');
			$join->where('patient_master.deleted_flag', 'N');
		});
		$where = $addCondition="";
		if($auth->record_access !='All'){
			$addCondition=" and patient_master.type='".$auth->record_access."'";
		}
		if ($auth['agency_fk'] != '') {
			$where = 'patient_master.deleted_flag ="N" and patient_master.agency_id="' . $auth['agency_fk'] . '"'.$addCondition.'';
		} else {
			$agencyids = Utility::getUserWiseAgency();
			if(!empty($agencyids)){
				$implodeIds = implode('","', $agencyids);
				$where .= ' and patient_master.agency_id IN("' . $implodeIds . '")';
			}
		}
		if($auth->restrict_user ==1){
			$query->where('patient_master.created_by',$auth->id);
		}
		$query->where('document_review_status','Approved');
		$query = $query->where('flag_marked.delete_flag','N')->where('document_patient.deleted_flag','N')->where('flag_marked.type',$type)->whereRaw($where)->orderBy('flag_marked.created_at','desc')->paginate(50);
	
		return $query;
	}

    public function getFlagAllDocumentByPatientId($type)
	{
	
		$auth = auth()->user();
		$query = FlagMarked::select('document_patient.*','flag_marked.created_at','flag_marked.reason as reasonNotes','users.first_name', 'users.last_name','flag_marked.is_flag_read','flag_marked.id as flag_id')
		->leftjoin('document_patient', function ($join) {
			$join->on('document_patient.id', '=', 'flag_marked.record_id');
			$join->where('document_patient.deleted_flag', 'N');
		})
        ->leftjoin('users', function ($join) {
			$join->on('users.id', '=', 'flag_marked.created_by');
			$join->where('users.delete_flag', 'N');
		})->where('flag_marked.type',$type)
		->leftjoin('patient_master', function ($join) {
			$join->on('patient_master.id', '=', 'document_patient.patient_id');
			$join->where('patient_master.deleted_flag', 'N');
		})
		->leftjoin('agency', function ($join) {
			$join->on('agency.id', '=', 'patient_master.agency_id');
		});
		$where = $addCondition="";
		if($auth->record_access !='All'){
			$addCondition=" and patient_master.type='".$auth->record_access."'";
		}
		if ($auth['agency_fk'] != '') {
			$where = 'patient_master.deleted_flag ="N" and patient_master.agency_id="' . $auth['agency_fk'] . '"'.$addCondition.'';
		} else {
			$agencyids = Utility::getUserWiseAgency();
			if(!empty($agencyids)){
				$implodeIds = implode('","', $agencyids);
				$where .= ' and patient_master.agency_id IN("' . $implodeIds . '")';
			}
		}
		$query->where('document_patient.deleted_flag','N')->where('agency.delete_flag','N');
		if($auth->restrict_user ==1){
			$query->where('patient_master.created_by',$auth->id);
		}
		$query = $query->orderBy('flag_marked.created_at','desc')->paginate(20);
		return $query;
	}

	public function getFlagTaskData($type)
	{
		$auth = auth()->user();
		$query = FlagMarked::select('task_master.*', 'users.first_name', 'users.last_name', 'us.first_name as assignFname', 'us.last_name as assignLnamae','task_master.priority','task_master.flag','flag_marked.reason','flag_marked.is_flag_read','flag_marked.id as flag_id')
			->leftjoin('task_master', function ($join) {
				$join->on('task_master.id', '=', 'flag_marked.record_id');
				$join->where('task_master.del_flag', 'N');
			})
			->leftjoin('users', function ($join) {
				$join->on('users.id', '=', 'flag_marked.created_by');
				$join->where('users.delete_flag', 'N');
			})->where('flag_marked.type',$type)
			->leftjoin('users as us', function ($join) {
				$join->on('us.id', '=', 'task_master.assign_id');
				$join->where('us.delete_flag', 'N');
			})
			->where('task_master.del_flag', 'N');
		if(auth()->user()->id != 482){
			$query->whereRaw('(task_master.assign_id="'.auth()->user()->id.'" OR task_master.created_by="'.auth()->user()->id.'")');
		}
		$query = $query->orderBy('task_master.id', 'desc')->paginate(50);
		return $query;
	}

	public function getAllFlagData($type){
		$auth = auth()->user();
		$query = FlagMarked::select('patient_notes.*','users.id as uid','users.first_name','users.last_name','users.name','flag_marked.is_flag_read','flag_marked.id as flag_id')
			->leftjoin('patient_notes', function ($join) {
				$join->on('patient_notes.patient_id', '=', 'flag_marked.record_id');
			})->leftjoin('patient_master', function ($join) {
				$join->on('patient_master.id', '=', 'patient_notes.patient_id');
				$join->where('patient_master.deleted_flag', 'N');
			})->leftjoin('agency', function ($join) {
				$join->on('agency.id', '=', 'patient_master.agency_id');
			})
			->leftjoin('users', function ($join) {
				$join->on('users.id', '=', 'flag_marked.created_by');
				$join->where('users.delete_flag', 'N');
			})->where('flag_marked.type',$type);

			$addCondition="";
			if($auth->record_access !='All'){
				$addCondition=" and  patient_master.type='".$auth->record_access."'";
			}

			$where = "flag_marked.type ='".$type."' and flag_marked.delete_flag ='N' ".$addCondition;
			
			if ($auth['agency_fk'] != '') {
				$where = ' patient_master.deleted_flag ="N"';
				$agencyids = Utility::getUserWiseAgency();
				$agencyids[] = $auth['agency_fk'];
				if(!empty($agencyids)){
					$implodeIds = implode('","', $agencyids);
					$where .= ' and patient_master.agency_id IN("' . $implodeIds . '")';
				}
				$where .= " and patient_notes.type = 'agency' ";
			} else {
				$agencyids = Utility::getUserWiseAgency();
				if(!empty($agencyids)){
					$implodeIds = implode('","', $agencyids);
					$where .= ' and patient_master.agency_id IN("' . $implodeIds . '")';
				}
			}
			if($auth->restrict_user ==1){
				$query->where('patient_master.created_by',$auth->id);
			}
			$query = $query->whereRaw('patient_notes.delete_flag ="N" and '.$where)->where('agency.delete_flag','N')->orderBy('flag_marked.id', 'desc');
			
			
			return $query->paginate(50);
	}

	public static  function update($data, $where)
	{
		$auth = auth()->user();
		$data['updated_at'] = date('Y-m-d H:i:s');
		$data['updated_by'] = $auth['id'];

		$update = FlagMarked::where($where)->update($data);
		return $update;
	}

	public static function getDetailsById($id)
	{
		$query = FlagMarked::where('id', $id)->where('delete_flag', 'N')->first();
		return $query;
	}
}
