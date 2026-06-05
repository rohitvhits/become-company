<?php

namespace App\Services;
use Illuminate\Support\Facades\DB;
use App\Model\MDOrder;
use App\Helpers\Utility;
class MDOrderService
{

    public static function save($data)
    {
        $auth = auth()->user();
        $data['created_date'] = date('Y-m-d H:i:s');
        if (isset($auth['id'])) {
            $data['created_by'] = $auth['id'];
        }
        $insert = new MDOrder($data);
        $insert_id = $insert->save();
        return $insert_id;
    }

    public static function update($data, $where)
    {
        $auth = auth()->user();
        $data['updated_date'] = date('Y-m-d H:i:s');
        $data['updated_by'] = $auth['id'];

        $update = MDOrder::where($where)->update($data);
        return $update;
    }

    public static function SoftDelete($data, $where)
    {
        $auth = auth()->user();
        $data['deleted_date'] = date('Y-m-d H:i:s');
        $data['deleted_by'] = $auth['id'];

        $update = MDOrder::where($where)->update($data);
        return $update;
    }

    public function patientMDOrderList($search){
        return MDOrder::with(['users:id,first_name,last_name','documentDetails:id,document_name'])->where('del_flag','N')->where('patient_id',$search['id'])->orderBy('created_date','desc')->paginate(50);
    }

    public function getDetailsById($id){
        return MDOrder::where('del_flag','N')->where('id',$id)->first();
    }

    public function getAllMDOrderList($search,$paginate=""){
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
       
        $query = MDOrder::with(['users:id,first_name,last_name','patientDetails:id,first_name,last_name,agency_id,status','patientDetails.agencyDetail:id,agency_name','documentDetails:id,document_name'])->where('del_flag','N');
        $query->whereHas('patientDetails', function ($q) use($auth) {
            if($auth->record_access !="All"){
                $q->where('type',$auth->record_access);
            }
            if($auth->restrict_user ==1){
                $q->where('created_by',$auth->id);
            }
        });
        if(isset($search['id']) && $search['id'] !=""){
            $query->where('patient_id',$search['id']);
        }

        if($search['start_date'] !="" && $search['end_date'] !=""){
            $query->whereDate('start_date','>=',date('Y-m-d',strtotime($search['start_date'])))->whereDate('end_date','<=',date('Y-m-d',strtotime($search['end_date'])));
        }else{
            if($search['start_date'] !=""){
                $query->whereDate('start_date',date('Y-m-d',strtotime($search['start_date'])));
            }
            if($search['end_date'] !=""){
                $query->whereDate('end_date',date('Y-m-d',strtotime($search['end_date'])));
            }
        }

        if(count($agencyids) >0){
            $query->whereHas('patientDetails',function($sub) use($agencyids){
                $sub->whereIn('agency_id',$agencyids);
            });
        }
        $query->whereHas('patientDetails.agencyDetail', function ($q) {
			$q->where('agency.delete_flag', 'N');
		});
        if($paginate !=""){
            $query = $query->orderBy('created_date','desc')->get();
        }else{
            $query = $query->orderBy('created_date','desc')->paginate(50);
        }
        return $query;
    }
}
