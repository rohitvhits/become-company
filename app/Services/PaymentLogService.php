<?php

namespace App\Services;

use App\Model\PaymentLog;
use App\Helpers\Utility;
class PaymentLogService
{

	public function save($data)
	{
		$auth = auth()->user();
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		$insert = new PaymentLog($data);
		$insert->save();
		$insertId = $insert->id;
		return $insertId;
	}

	public  function update($data, $where)
	{
		$auth = auth()->user();
		$data['updated_at'] = date('Y-m-d H:i:s');
		if(isset($auth['id'])){
			$data['updated_by'] = $auth['id'];
		}
		

		$update = PaymentLog::where($where)->update($data);
		return $update;
	}

	public  function SoftDelete($data, $where)
	{
		$auth = auth()->user();
		$data['deleted_date'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];

		$update = PaymentLog::where($where)->update($data);
		return $update;
	}

	public function getAllPaymentLogData($search,$paginate=""){
		$auth = auth()->user();
       
		if (in_array($auth['user_type_fk'], array(184))) {
		
			$agencyids = Utility::getUserWiseAgency();

        }else{
            $agencyids = Utility::getUserWiseAgency();
			$agencyids[] = $auth['agency_fk'];
        }
        
        if(isset($search['agency_fk'])){
            $agencyids = array_merge($agencyids,$search['agency_fk']); 
        }
        
        $query = PaymentLog::with(['users:id,first_name,last_name','patientDetails:id,first_name,last_name,agency_id','patientDetails.agencyDetail:id,agency_name','locationDetails:id,address1','paymentDeatil:id,name'])->where('delete_flag','N');

        if(isset($search['portal_id']) && $search['portal_id'] !=""){
            $query->where('patient_id',$search['portal_id']);
        }

        if(count($agencyids) >0){
            $query->whereHas('patientDetails',function($sub) use($agencyids){
                $sub->whereIn('agency_id',$agencyids);
            });
        }

		if(isset($search['status_payment_type']) && !empty($search['status_payment_type'])){
			$query->where('payment_type',$search['status_payment_type']);
		}

		if(isset($search['status_location_id']) && !empty($search['status_location_id'])){
			$query->where('location_id',$search['status_location_id']);
		}

		if(isset($search['services']) && !empty($search['services'])){
			$services = $search['services'];
			$query->whereHas('paymentLogDeatil.serviceDetails',function($sub) use($services){
                $sub->whereIn('service_id',$services);
            });
		}
        if(isset($search['created_date']) && $search['created_date'] !=""){
			$dExplode = explode('-',$search['created_date']);
			if(isset($dExplode[1])){
				$query->whereDate('created_at','>=',date('Y-m-d',strtotime($dExplode[0])))->whereDate('created_at','<=',date('Y-m-d',strtotime($dExplode[1])));
			}else{
				$query->whereDate('created_at','=',date('Y-m-d',strtotime('0000-00-00')));
			}
			
		}
        $query->whereHas('patientDetails.agencyDetail', function ($q) {
			$q->where('agency.delete_flag', 'N');
		});
        if($paginate !=""){
            $query = $query->orderBy('created_at','desc')->get();
        }else{
            $query = $query->orderBy('created_at','desc')->simplePaginate(50);
        }
        return $query;
	}

	public function getAllPaymentLogPatientData($id,$paginate=""){
		$auth = auth()->user();
		if (in_array($auth['user_type_fk'], array(184))) {
			$agencyids = Utility::getUserWiseAgency();
        }else{
            $agencyids = Utility::getUserWiseAgency();
			$agencyids[] = $auth['agency_fk'];
        }
        $query = PaymentLog::with(['users:id,first_name,last_name','patientDetails:id,first_name,last_name,agency_id','patientDetails.agencyDetail:id,agency_name','locationDetails:id,address1','paymentDeatil:id,name','paymentLogDeatil.serviceDetails:id,name'])->where('delete_flag','N');
        $query->where('patient_id',$id);
        if(count($agencyids) >0){
            $query->whereHas('patientDetails',function($sub) use($agencyids){
                $sub->whereIn('agency_id',$agencyids);
            });
        }
		if($paginate == ""){
			$query = $query->orderBy('created_at','desc')->get();
		}else{
			$query = $query->orderBy('created_at','desc')->paginate(50);
		}
        return $query;
	}

	public function getPaymentLogData($id){
        $query = PaymentLog::with(['users:id,first_name,last_name','patientDetails:id,first_name,last_name,agency_id','patientDetails.agencyDetail:id,agency_name','locationDetails:id,address1','paymentDeatil:id,name','paymentLogDeatil.serviceDetails:id,name'])->where('delete_flag','N');
        $query->where('id',$id);
        $query = $query->first();
        return $query;
	}

	public function getPaymentHistroy($id){
        $query = PaymentLog::with(['users:id,first_name,last_name','paymentDeatil:id,name','paymentLogDeatil','paymentLogDeatil.serviceDetails:id,name','patientDetails:id,first_name,last_name,agency_id','patientDetails.agencyDetail:id,agency_name'])->where('delete_flag','N');
        $query->where('id',$id);
        $query = $query->first()->toArray();
        return $query;
	}

	public function getCountData($agency_id){
        $query = PaymentLog::with(['paymentLogDeatil:id,payment_log_id,total_amount,received_amount,remaining_amount'])->where('delete_flag','N');
        $query->whereHas('patientDetails',function($q) {
            $q->where('deleted_flag','N');
            $q->where('id', '!=', NULL);
            $q->where('archived_at', '=', NULL);
        });
        if (!empty($agency_id)) {
            $query = $query->whereHas('patientDetails', function ($q) use ($agency_id) {
                $q->whereIn('agency_id',$agency_id);
            });
        }
        return $query->get()->toArray();
    }
 
    public function locationWiseData($agency_id){
        $query = PaymentLog::with(['paymentLogDeatil:id,payment_log_id,total_amount,received_amount,remaining_amount','locationDetails:id,address1'])->where('delete_flag','N');
        $query->whereHas('patientDetails',function($q) {
            $q->where('deleted_flag','N');
            $q->where('id', '!=', NULL);
            $q->where('archived_at', '=', NULL);
        });
        if (!empty($agency_id)) {
            $query = $query->whereHas('patientDetails', function ($q) use ($agency_id) {
                $q->whereIn('agency_id',$agency_id);
            });
        }
        return $query->get()->toArray();
    }
 
    public function agencyWiseData($agency_id){
        $query = PaymentLog::with(['paymentLogDeatil:id,payment_log_id,total_amount,received_amount,remaining_amount','patientDetails.agencyDetail:id,agency_name'])->where('delete_flag','N');
        $query->whereHas('patientDetails',function($q) {
            $q->where('deleted_flag','N');
            $q->where('id', '!=', NULL);
            $q->where('archived_at', '=', NULL);
        });
        if (!empty($agency_id)) {
            $query = $query->whereHas('patientDetails', function ($q) use ($agency_id) {
                $q->whereIn('agency_id',$agency_id);
            });
        }
        return $query->get()->toArray();
    }
 
    public function servicesWiseData($agency_id){
        $query = PaymentLog::with(['paymentLogDeatil:id,payment_log_id,total_amount,received_amount,remaining_amount,service_id','paymentLogDeatil.serviceDetails:id,name'])->where('delete_flag','N');
        $query->whereHas('patientDetails',function($q) {
            $q->where('deleted_flag','N');
            $q->where('id', '!=', NULL);
            $q->where('archived_at', '=', NULL);
        });
        if (!empty($agency_id)) {
            $query = $query->whereHas('patientDetails', function ($q) use ($agency_id) {
                $q->whereIn('agency_id',$agency_id);
            });
        }
        return $query->get()->toArray();
    }
    
    public function getPaymentTypeWiseChartData($agency_id){
        $query = PaymentLog::with(['paymentLogDeatil:id,payment_log_id,total_amount,received_amount,remaining_amount,service_id','paymentLogDeatil'])->where('delete_flag','N');
        $query->whereHas('patientDetails',function($q) {
            $q->where('deleted_flag','N');
            $q->where('id', '!=', NULL);
            $q->where('archived_at', '=', NULL);
        });
        if (!empty($agency_id)) {
            $query = $query->whereHas('patientDetails', function ($q) use ($agency_id) {
                $q->whereIn('agency_id',$agency_id);
            });
        }
        return $query->get()->toArray();
    }
}