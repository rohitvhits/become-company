<?php

namespace App\Services;

use App\Model\MultiplePatientDocApproval;

class MultiplePatientDocApprovalService
{

    public static function save($data)
    {
        $auth = auth()->user();
        $data['created_date'] = date('Y-m-d H:i:s');
        if (isset($auth['id'])) {
            $data['created_by'] = $auth['id'];
        }
        $insert = new MultiplePatientDocApproval($data);
        $insert_id = $insert->save();

        return $insert_id;
    }
    public static function update($data, $where)
    {
        $auth = auth()->user();
        $data['updated_date'] = date('Y-m-d H:i:s');
        $data['updated_by'] = $auth['id'];

        $update = MultiplePatientDocApproval::where($where)->update($data);
        return $update;
    }
    public static function SoftDelete($data, $where)
    {
        $auth = auth()->user();
        $data['deleted_date'] = date('Y-m-d H:i:s');
        $data['deleted_by'] = $auth['id'];

        $update = MultiplePatientDocApproval::where($where)->update($data);
        return $update;
    }

    public function getPendingData($search){
		$auth = auth()->user();
		$agencyids = Utility::getUserWiseAgency();
		if($auth->agency_fk !=""){
			$agencyids[] = $auth->agency_fk;
		}
		$query = MultiplePatientDocApproval::with(['documentDetails:id,document_completed_date,document_name,attachment,created_date,created_by,request_service_id,document_review_date,document_review_by,assign_document_review,internal_use,status_note,deleted_flag','documentDetails.userDetails:id,first_name,last_name','patientDetails:id,type,first_name,last_name,agency_id,status','patientDetails.agencyDetail:id,agency_name'])
        ->select('id','patient_id','user_id','document_id')->where('del_flag','N');
        $query->whereHas('patientDetails', function ($q)  use ($search,$agencyids,$auth) {
			if (in_array($auth['user_type_fk'], array(184))) {
				$where = 'patient_master.deleted_flag ="N"';
				if(!empty($agencyids)){
					$implodeIds = implode('","', $agencyids);
					$where .= ' and patient_master.agency_id IN("' . $implodeIds . '")';
				}
				$addCondition="";
				if($auth->record_access !='All'){
					$addCondition=" and patient_master.type='".$auth->record_access."'";
				}
				$where .= $addCondition;
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
				}
			}
			if(isset($search['agency_id']) && $search['agency_id'] !=""){
				$q->whereIn('agency_id',$search['agency_id']);
			}else{
				if(count($agencyids) >0){
					$q->whereIn('agency_id',$agencyids);
				}
			}
			if(isset($search['patient_type']) && $search['patient_type'] !=""){
				$q->where('type',$search['patient_type']);
			}
			$q->where('status','processing');
			$q->whereRaw($where);
		});
		if(isset($search['patient_id']) && $search['patient_id'] !=""){
			$query->where('patient_id',$search['patient_id']);
		}
		if(isset($search['created_date']) && $search['created_date'] !=""){
			$dExplode = explode('-',$search['created_date']);
            $query->whereHas('documentDetails', function($q) use($dExplode) {
				if(isset($dExplode[1])){
                    $q->whereDate('created_date','>=',date('Y-m-d',strtotime($dExplode[0])))->whereDate('created_date','<=',date('Y-m-d',strtotime($dExplode[1])));
                }else{
                    $q->whereDate('created_date','=',date('Y-m-d',strtotime('0000-00-00')));
                }
			});
		}
		if(isset($search['document_created_by']) && $search['document_created_by'] !=""){
            $created_by = $search['document_created_by'];
            $query->whereHas('documentDetails', function($q) use($created_by) {
                $q->where('document_patient.created_by',$created_by);
			});
		}
        $query->whereHas('documentDetails', function($q) {
            $q->where('document_review_status','Pending');
            $q->whereNotNull('attachment');
        });
        $serviceIds = $search['service_id']??'';
        $query->whereHas('documentDetails.documentUploadServiceDetails.masterDetails', function($q) use ($serviceIds) {
            $q->whereNotNull('service_id');
            if(isset($serviceIds) && !empty($serviceIds)){
                $q->whereIn('service_id',$serviceIds);
		    }
        });
		$service_id = ['181'];
		if(auth()->user()->id == 4611){ // Jada's user id
			$query->whereHas('documentDetails.documentUploadServiceDetails.masterDetails', function($q) use($service_id) {
				$q->whereIn('service_id',$service_id);
			});
		}
		$query->where('user_id', auth()->user()->id);
		return $query = $query->orderBy('id','desc')->paginate(50);
	}
}
