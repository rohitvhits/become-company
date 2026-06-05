<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Model\PaymentLogServiceWise;
use App\Helpers\Utility;
class PaymentLogServiceWiseService
{

	public function save($data)
	{
		$auth = auth()->user();
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		$insert = new PaymentLogServiceWise($data);
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
		

		$update = PaymentLogServiceWise::where($where)->update($data);
		return $update;
	}

	public  function SoftDelete($data, $where)
	{
		$auth = auth()->user();
		$data['deleted_at'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];

		$update = PaymentLogServiceWise::where($where)->update($data);
		return $update;
	}

	public function getMonthlyWisePaymentChartData($agency_id,$search_data){

		$where = "";
        $query = PaymentLogServiceWise::select(DB::raw("SUM(received_amount) as sum"),"payment_log_id","created_at",\DB::raw("MONTH(created_at) as month"),\DB::raw("MONTHNAME(created_at) as month_name"))->where('delete_flag','N');
		if(isset($agency_id) && !empty($agency_id)){
            $query->whereHas('paymentLogDetails.patientDetails',function($sub) use($agency_id){
                $sub->whereIn('agency_id',$agency_id);
            });
        }
		if(!empty($search_data)){
			$where .=' DATE_FORMAT(created_at,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($search_data['search_data']['start_date'])) . '" and DATE_FORMAT(created_at,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($search_data['search_data']['end_date'])) . '"';
		}
		$query->whereRaw($where);
		$query->groupBy($search_data['group_by']);
        return $query->get()->toArray();
    }
}