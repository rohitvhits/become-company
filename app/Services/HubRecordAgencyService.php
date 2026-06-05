<?php

namespace App\Services;

use App\Helpers\Utility;
use App\Model\HubRecordAgency;
class HubRecordAgencyService
{

	public  function save($data)
	{
		$auth = auth()->user();
		$data['created_date'] = date('Y-m-d H:i:s');
		
		if(isset($auth['id'])){
			$data['created_by'] = $auth['id'];
		}
		$data['deleted_flag'] = "N";

		$insert = new HubRecordAgency($data);
		$insert->save();
		$insertId = $insert->id;
		return $insertId;
	}
	
	public  function update($data, $where)
	{
		$auth = auth()->user();
		$data['updated_date'] = date('Y-m-d H:i:s');
		if(isset($auth['id'])){
			$data['updated_by'] = $auth['id'];
		}
		

	$update = HubRecordAgency::where($where)->update($data);
		return $update;
	}
	public  function SoftDelete($data, $where)
	{
		$auth = auth()->user();
		$data['deleted_date'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];

		$update = HubRecordAgency::where($where)->update($data);
		return $update;
	}

	public function getAgencyData($hub_id,$agency_id)
	{
		return HubRecordAgency::select('id','hub_record_id','agency_id','status','hire_date','work_contact','work_email','employee_code','last_worked_date')->where('hub_record_id',$hub_id)->where('agency_id',$agency_id)->first();
	}

    public function getAgencyListSearch(){
        return HubRecordAgency::with(['agencyDetail:id,agency_name'])->select('id','agency_id','hub_record_id')->get();
    }

	public function getDetailsByIdWhitoutAgency($id){
		return HubRecordAgency::with(['agencyDetail:id,agency_name'])->where('hub_record_id',$id)->get();
	}

	public function getAgencyDetails($hub_id,$agency_id)
	{
		return HubRecordAgency::with(['agencyDetail:id,agency_name'])->where('hub_record_id',$hub_id)->where('agency_id',$agency_id)->first();
	}

	public function getAllRecord($agency_id){
		$query = HubRecordAgency::where('del_flag', 'N')->where('status', '!=', 'deactivated')->where('agency_id',$agency_id);
		$query = $query->get();
		return $query;
	}

	public function fetchHubRecordsByAgency($agency_id,$first_name,$last_name,$mobile,$status,$dob,$offset="")
	{

		$where = 'hub_record.deleted_flag ="N" ';
		$query = HubRecordAgency::selectRaw('hub_record.id,hub_record.first_name,hub_record.middle_name,hub_record.last_name,hub_record.dob,hub_record.gender,hub_record.phone,hub_record.mobile,hub_record.address1,hub_record.address2,hub_record.state,hub_record.city,hub_record.zip_code,hub_record.county,hub_record.ssn,hub_record.email,hub_record.relation_ship,hub_record_agency.status,hub_record_agency.hire_date,hub_record_agency.work_contact,hub_record_agency.work_email,hub_record_agency.employee_code,hub_record_agency.last_worked_date,users.id as createdUserId,users.first_name as createdFirstName,users.last_name as createdLastName,hub_company.agency_name')
		->join('hub_record',function($join){
			$join->on('hub_record.id','=','hub_record_agency.hub_record_id');
			$join->where('hub_record.deleted_flag', 'N');
		})
		->join('users',function($join){
			$join->on('users.id','=','hub_record.created_by');
			$join->where('users.delete_flag', 'N');
		})->leftjoin('hub_company', function ($join) {
			$join->on('hub_company.id', '=', 'hub_record_agency.agency_id');
			$join->where('hub_company.delete_flag', 'N');
		})

		->where('hub_record_agency.agency_id',$agency_id);
		
        if(isset($first_name) && !empty($first_name) ){
			$query->where('hub_record.first_name', 'LIKE', '%' . $first_name . '%');
		}
		if(isset($last_name) && !empty($last_name) ){
			$query->where('hub_record.last_name', 'LIKE', '%' . $last_name . '%');
        }
        if(isset($mobile) && !empty($mobile) ){
            $query->where('hub_record.mobile', $mobile);
        }
		if (isset($dob) && $dob != '' && $dob !='undefined') {
			$query->where('hub_record.dob',date('Y-m-d', strtotime($dob)));
		}
		if (isset($status) && $status != '') {
			$query->where('hub_record_agency.status',$status);
		}
		$query->whereRaw($where);
		$query = $query->orderBy('hub_record.id', 'desc')->offset($offset)->limit(50)->get();
		return $query;
	}

	public function getBasicDetailsAPI($id,$agency_id)
	{
		
		$query =HubRecordAgency::selectRaw('hub_record.id,hub_record.first_name,hub_record.middle_name,hub_record.last_name,hub_record.dob,hub_record.gender,hub_record.phone,hub_record.mobile,hub_record.address1,hub_record.address2,hub_record.state,hub_record.city,hub_record.zip_code,hub_record.county,hub_record.ssn,hub_record.email,hub_record.relation_ship,hub_record_agency.status,hub_record_agency.hire_date,hub_record_agency.work_contact,hub_record_agency.work_email,hub_record_agency.employee_code,hub_record_agency.last_worked_date,users.id as createdUserId,users.first_name as createdFirstName,users.last_name as createdLastName,hub_company.agency_name')
		->join('hub_record',function($join){
			$join->on('hub_record.id','=','hub_record_agency.hub_record_id');
			$join->where('hub_record.deleted_flag', 'N');
		})
		->join('users',function($join){
			$join->on('users.id','=','hub_record.created_by');
			$join->where('users.delete_flag', 'N');
		})->leftjoin('hub_company', function ($join) {
			$join->on('hub_company.id', '=', 'hub_record_agency.agency_id');
			$join->where('hub_company.delete_flag', 'N');
		})->where('hub_record.id',$id)->where('hub_record_agency.agency_id',$agency_id)->where('hub_record_agency.del_flag',"N");
		$query = $query->first();
	
		return $query;
	}
}