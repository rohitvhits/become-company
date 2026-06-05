<?php
namespace App\Services;
use App\Model\FeedBackAnswer;

class FeedbackAnswerFormService{

	public static function save($data){ 
		$auth = auth()->user();
		$data['created_at'] = date('Y-m-d H:i:s');		
		$insert = new FeedBackAnswer($data);
		$insert->save();
		$insert_id =$insert->id;
		
		
		return $insert_id;
		
	}
	public static function update($data,$where){
		$auth = auth()->user();
		$data['updated_at'] = date('Y-m-d H:i:s');
		$data['updated_by'] = $auth['id'];
		
		$update =FeedBackAnswer::where($where)->update($data); 
		return $update;
	}
	
    public static function getList(){
        return FeedBackAnswer::select('id','type','message','created_at','updated_at','created_by','updated_by')->with(['updatedUsers:id,first_name,last_name','createdUser:id,first_name,last_name'])->where('delete_flag','N')->paginate(10);
    }

	public static function getDetailsByid($id){
	
		return FeedBackAnswer::select('id','type','message','updated_at','send_mail')->where('delete_flag','N')->where('id',$id)->first();
	}

	public static function dataList($search){
		$name = $search['name'] ?? null;
		$createdDate = $search['created_date'] ?? null;
		$service_id = $search['service_id'] ?? null;

		$query = FeedBackAnswer::select('id', 'answer_response', 'patient_id', 'service_id', 'created_at', 'ip_address')->with(['patientDetail:id,first_name,last_name,agency_id,status','serviceRequestDetail:id,completed_date','serviceRequestDetail.patientServiceRequestRelationShip','patientDetail.agencyDetail:id,agency_name','serviceRequestDetail.patientServiceRequestRelationShip.services:id,name'])->where('delete_flag', 'N');

		// Handle patient name search
		if (!empty($name)) {
			$query->whereHas('patientDetail', function ($subQuery) use ($name) {
				// $name = str_replace(" ", "", $name); // Remove spaces
				$subQuery->whereRaw("CONCAT(TRIM(first_name), ' ', TRIM(last_name)) LIKE ?", ["%$name%"]);
			});
		}

		// Handle created date range search
		if (!empty($createdDate)) {
			$exploder = explode('-', $createdDate);
			if (isset($exploder[0]) && isset($exploder[1])) {
				$startDate = date('Y-m-d', strtotime(trim($exploder[0])));
				$endDate = date('Y-m-d', strtotime(trim($exploder[1])));
				$query->whereBetween('created_at', [$startDate. ' 00:00:00', $endDate.' 23:59:59']);
			}
		}
		$query->orderBy('client_review_feedback_answer.id','desc');
		return $query->paginate(10);
	}
}