<?php
namespace App\Services;

use App\Model\Robort;

class RobortService{

    public function save($data)
    {
        $auth = auth()->user();
        $data['created_date'] = date('Y-m-d H:i:s');
        $data['created_by'] = $auth['id'];
        $data['del_flag'] = "N";

        $insert = new Robort($data);
        $insert->save();
        return $insert->id;
    }

    public function update($data,$where){
        $auth = auth()->user();
		$data['updated_date'] = date('Y-m-d H:i:s');
		$data['updated_by'] = $auth['id'];

		$update = Robort::where($where)->update($data);
		return $update;
    }
	public function getRobortList($data){
        $query = Robort::select('robort_master.*','agn.agency_name')
        ->leftjoin('agency as agn',function($join){
            $join->on('agn.id','=','robort_master.agency_id');
        })
        ->where('agn.robort_status',1)
        ->where('robort_master.del_flag','N');
        if(isset($data['full_name']) && $data['full_name'] !=""){
            $query->whereRaw('CONCAT(robort_master.firstName," ",robort_master.lastName) LIKE "%'.$data['full_name'].'%"');
        }
        if(isset($data['dob']) && $data['dob'] !=""){
            $query->whereDate('robort_master.dob','=',date('Y-m-d',strtotime($data['dob'])));
        }
        if(isset($data['gender']) && $data['gender'] !=""){
            $query->where('robort_master.gender',$data['gender']);
        }
        if(isset($data['patient_status']) && $data['patient_status'] !=""){
            $query->where('robort_master.status',$data['patient_status']);
        }
        if(isset($data['agency_id']) && $data['agency_id'] !=""){
            $query->where('robort_master.agency_id',$data['agency_id']);
        }
        if(isset($data['status']) && $data['status'] !=""){
            if($data['status'] =='Booked'){
                $query->whereNotNull('robort_master.appointment_id');
            }else{
                $query->whereNull('robort_master.appointment_id');
            }
            
        }
        if(isset($data['created_date']) && $data['created_date'] !=""){
            $explode = explode('-',$data['created_date']);
            $query->whereDate('robort_master.created_date','>=',date('Y-m-d',strtotime($explode[0])))->whereDate('robort_master.created_date','<=',date('Y-m-d',strtotime($explode[1])));
        }
        $query = $query->orderBy('id','desc')->paginate(50);
        return $query;
    }

    public function getDetailsById($id){
        return Robort::find($id);
    }
    public function getDetailsByIdWithRelationShip($id){
        return Robort::with(['agencyDetails'])->where('id',$id)->first();
    }

    public function getRemoteDetails($data){
        $name =$data['q'];
        return Robort::whereRaw("CONCAT(firstName, ' ', lastName) LIKE ?", ["%$name%"])->where('del_flag','N')->where('agency_id',$data['agency_id'])
        ->get();
    }

    public function totalRemoteClientCount(){
        return Robort::where('del_flag','N')->count();
    }

    public function totalRemoteClientCountDateWise($from_date,$to_date){
        $query = Robort::where('del_flag','N');
        if(!empty($from_date) && !empty($to_date)){
			$query->whereBetween('created_date', [$from_date.' 00:00:00', $to_date.' 23:59:59']);
		}
		return $query->count();
    }

    public function getDetailsByIdWithAgencyId($id,$agencyId){
        return Robort::where('del_flag','N')->where('id',$id)->where('agency_id',$agencyId)->first();
    }

    public function getDetailsByPatientWithAgencyId($patientId,$agencyId){
        return Robort::where('del_flag','N')->where('patientId',$patientId)->where('agency_id',$agencyId)->first();
    }

    public function getDetailsByPatientWithRelationShip($id){
        return Robort::with(['agencyDetails'])->where('patientId',$id)->first();
    }
}