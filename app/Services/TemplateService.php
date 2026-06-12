<?php

namespace App\Services;

use App\Template;
use App\TemplateLog;
use App\Agency;
class TemplateService
{
	public  function save($data){
		$userId = Auth()->user();
		$data['created_date']=date('Y-m-d H:i:s');
		if($userId){
		$data['created_by']=$userId['id'];
		}
		$insert = new Template($data);
		$insert->save();
		$insertId = $insert->id;
		return $insertId;
		
	}
	public  function update($data,$where){
		$userId = Auth()->user();
		$data['updated_date']=date('Y-m-d H:i:s');
		if($userId){
		$data['updated_by']=$userId['id'];
		}
		$update =Template::where($where)->update($data); 
		return $update;
	}
	public  function SoftDelete($data,$where){
		$userId = Auth()->user();
		$data['deleted_date']=date('Y-m-d H:i:s');
		$data['deleted_by']=$userId['id'];
		$update =Template::where($where)->update($data); 
		return $update;
	}

    public static function templateList($id, $template_name, $lookup_fields, $status,$agency_fk, $created_date = '', $updated_date = '')
	{
		$auth = auth()->user();
		if ($auth->user_type_fk == 184) {
			$temp = ' template_master.del_flag="N"';
		} else {
			$temp = ' template_master.del_flag="N" and lookup_fields IS NULL';
		}

		if ($id != '') {
			$temp .= ' and template_master.document_type ="' . $id . '"';
		}
		if ($template_name != '') {
			$temp .= ' and template_master.template_name LIKE "%' . $template_name . '%"';
		}
		if ($lookup_fields != '') {

			$temp .= ' and template_master.lookup_fields ="' . $lookup_fields . '"';
		}
		if ($status != '') {
			$temp .= ' and template_master.active_status ="' . $status . '"';
		}

		if ($agency_fk != '') {
			$agencyArray = explode(',', $agency_fk);
			$agencyConditions = [];
			foreach ($agencyArray as $agency) {
				$agency = trim($agency);
				$agencyConditions[] = "FIND_IN_SET('$agency', template_master.agency_id)";
			}
			if (!empty($agencyConditions)) {
				$temp .= ' and (' . implode(' OR ', $agencyConditions) . ')';
			}
		}

		if ($created_date != '') {
			$dates = explode(' - ', $created_date);
			if (count($dates) == 2) {
				$startDate = date('Y-m-d', strtotime(trim($dates[0])));
				$endDate = date('Y-m-d', strtotime(trim($dates[1])));
				$temp .= ' and DATE(template_master.created_date) BETWEEN "' . $startDate . '" AND "' . $endDate . '"';
			}
		}

		if ($updated_date != '') {
			$dates = explode(' - ', $updated_date);
			if (count($dates) == 2) {
				$startDate = date('Y-m-d', strtotime(trim($dates[0])));
				$endDate = date('Y-m-d', strtotime(trim($dates[1])));
				$temp .= ' and DATE(template_master.updated_date) BETWEEN "' . $startDate . '" AND "' . $endDate . '"';
			}
		}

		$query = Template::select('template_master.*', 'document_type_master.name', 'createdUser.first_name', 'createdUser.last_name', 'updatedUser.first_name as updated_first_name', 'updatedUser.last_name as updated_last_name')
			->leftjoin('document_type_master', function ($join) {
				$join->on('document_type_master.id', '=', 'template_master.document_type');
			})
			->leftjoin('users as createdUser', function ($join) {
				$join->on('createdUser.id', '=', 'template_master.created_by');
			})
			->leftjoin('users as updatedUser', function ($join) {
				$join->on('updatedUser.id', '=', 'template_master.updated_by');
			})
			->whereRaw($temp)
			->orderBy('template_master.id', 'desc')
			->paginate(20);
			
			foreach ($query as $template) {
				$agencyIds = explode(',', $template->agency_id);
				$agencyNames = Agency::whereIn('id', $agencyIds)->pluck('agency_name')->toArray();
				$template->agency_names = implode(', ', $agencyNames); 
			}
		return $query;
	}

	public function getDetailsById($id){
		$query =Template::where('del_flag','N')->where('id',$id)->first();
		return $query;

	}

	public function getListByLookupField($lookupfields, $templateType = null){
		$query = Template::where('del_flag','N')->where('lookup_fields',$lookupfields)->where('active_status','active')->where('custom_template', 1)
		->when($templateType, function ($q) use ($templateType) {
			$q->where('template_type', $templateType);
		})->orderBy('template_name','asc')->get();
		return $query;
	}
	
	public function getListWithoutPaginate(){
		$query =Template::where('del_flag','N')->where('active_status','active')->orderBy('template_name','asc')->get();
		return $query;
	}

	public function getTemplateLogData($id){
		return TemplateLog::where('template_id', $id)->orderBy('id','desc')->get();
	}

	public function getTemplateData(){
		$query =Template::select('id','template_name')->where('del_flag','N')->where('active_status','active')->orderBy('template_name','asc')->get()->toArray();
		return $query;
	}

	/******This is use for VNS Custom Esign Form */
	public function getTemplateListByVNS(){
		return  Template::select('id', 'template_name')
            ->where('del_flag', 'N')
            ->where('custom_template', 0)->where('active_status','Active')
            ->orderBy('template_name', 'asc')
            ->get();
	}

	public function getDetailsWithoutDelete($id){
		return Template::where('id', $id)->first();
	}

	public function getDetailsBySah1TemplateId($id){
		return Template::whereRaw('sha1(id)="' . $id . '"')->first();
	}

	public function loadEsignTemplateData($agency_id,$type,$templateType=null){
		
		return  Template::select('id','template_name','response')->where('active_status', 'Active')->where('del_flag', 'N')
		->where(function ($q) use ($agency_id) {
			$q->whereRaw('FIND_IN_SET(?, agency_id)', [$agency_id])
			->orWhereNull('agency_id'); })->where('lookup_fields', $type)->where('custom_template', 1)
		->when($templateType, function ($q) use ($templateType) {
			if($templateType !=null){
				$q->where('template_type', $templateType);
			}
			
		})->orderBy('template_name','asc')->get();
	}

	public function getListByLookupFieldWithSignerCaregiver($lookupfields, $templateType = null){
		return Template::select('template_master.id','template_master.template_name')->join('document_signer_master',function($join){
			$join->on('document_signer_master.template_id','=','template_master.id')->where('document_signer_master.del_flag','N');
		})
		->where('template_master.del_flag','N')->where('template_master.lookup_fields',$lookupfields)->where('template_master.active_status','active')->where('template_master.custom_template',1)
		->when($templateType, function ($q) use ($templateType) {
			$q->where('template_master.template_type', $templateType);
		})->where('document_signer_master.name','Caregiver')->groupBy('template_master.id')->orderBy('template_master.template_name','asc')->get();

	}
}
