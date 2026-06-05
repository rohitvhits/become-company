<?php

namespace App\Services;

use App\DocumentSentReport;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Model\DocumentPatient;
use App\Model\Patient;
use App\Model\WriteDocument;
use App\Template;
use App\Helpers\Utility;
use App\SiteSetting;
use App\Model\DocumentUploadHistory;
use App\Model\UserSendPatientDocumentLog;
class DocumentPatientService
{

	public  function save($data)
	{
		$auth = auth()->user();
		if(isset($data['flag']) && $data['flag'] ==1){
			$data['created_date'] = $data['created_date'];
			$data['created_by'] = $data['created_by'];
		}else{

			if(isset($auth['id'])){
				$data['created_date'] = date('Y-m-d H:i:s');
				$data['created_by'] = $auth['id'];
			}

		}
		// $data['created_date'] = date('Y-m-d H:i:s');
		// $data['created_by'] = $auth['id'];
		$data['deleted_flag'] = "N";

		$insert = new DocumentPatient($data);
		$insert->save();
		$insert_ids = $insert->id;

		$data['hha_document_id'] = $insert_ids;
		$data['document_type_flag'] = isset($data['document_type_flag'])?$data['document_type_flag']:1;
		$data['created_date'] = date('Y-m-d H:i:s');
		$saves = new DocumentUploadHistory($data);
		$saves->save();
		return $insert_ids;
	}

	public  function saveNew($data)
	{
		$auth = auth()->user();
		$data['created_date'] = date('Y-m-d H:i:s');

		$data['deleted_flag'] = "N";

		$insert = new DocumentPatient($data);
		$insert->save();
		$insert_ids = $insert->id;

		return $insert_ids;
	}

	public  function update($data, $where)
	{
		$auth = auth()->user();
		$data['updated_date'] = date('Y-m-d H:i:s');
		$data['updated_by'] = $auth['id'];

		$update = DocumentPatient::where($where)->update($data);

		$data['hha_document_id'] =$where['id'];
		$data['document_type_flag'] = isset($data['document_type_flag'])?$data['document_type_flag']:1;
		$data['created_date'] = date('Y-m-d H:i:s');
		$data['created_by'] = $data['updated_by'];
		$saves = new DocumentUploadHistory($data);
		$saves->save();

		return $update;
	}
	public  function SoftDelete($data, $where)
	{
		$auth = auth()->user();
		$data['deleted_date'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];

		$update = DocumentPatient::where($where)->update($data);
		return $update;
	}

	public function getAllDocumentByPatientId($id,$order='asc')
	{
		$update = DocumentPatient::select('document_patient.id','document_patient.send_third_party_date','document_patient.patient_id','document_patient.document_name','document_patient.attachment','document_patient.deleted_flag','document_patient.created_date','document_patient.created_by','document_patient.uploaded_to_hha','document_patient.uploaded_complience_hha','document_patient.request_service_id',
'document_patient.document_completed_date','document_patient.templete_id','document_patient.flag','document_patient.internal_use',
'document_patient.send_third_party','document_patient.document_review_by','document_patient.assign_document_review','document_patient.document_review_status','document_patient.document_review_date','document_patient.sign_stamp_status','document_patient.updated_date','document_patient.updated_by','users.first_name', 'users.last_name','uupdate.first_name as updated_first_name', 'uupdate.last_name as updated_last_name','document_patient.medication_list','document_patient.send_rnpad_document_date','document_patient.insurance_elg','document_patient.mdo_tag','document_patient.mdo_source','document_patient.info_only','document_patient.call_back_url')->with(['assignUserReviewDocument:id,first_name,last_name',
'reviewUserDetails:id,first_name,last_name'])->whereIn('patient_id', $id)->where('is_task_helath_dup',0);

		if(in_array('30589', $id)){
			$update->where('deleted_flag', 'N');
		}

		$update = $update->leftjoin('users', function ($join) {

			$join->on('users.id', '=', 'document_patient.created_by');
			$join->where('users.delete_flag', 'N');
		})
		->leftjoin('users as uupdate', function ($join) {
			$join->on('uupdate.id', '=', 'document_patient.updated_by');
			$join->where('uupdate.delete_flag', 'N');
		})->orderBy('id', $order)->paginate(20);
		return $update;
	}
	public function getAllDocumentByPatientIdAgency($id)
	{
		$update = DocumentPatient::select('document_patient.*','users.first_name', 'users.last_name','uupdate.first_name as updated_first_name', 'uupdate.last_name as updated_last_name')->where('patient_id', $id)
		->leftjoin('users', function ($join) {
			$join->on('users.id', '=', 'document_patient.created_by');
			$join->where('users.delete_flag', 'N');
		})
		->leftjoin('users as uupdate', function ($join) {
			$join->on('uupdate.id', '=', 'document_patient.updated_by');
			$join->where('uupdate.delete_flag', 'N');
		})->where('deleted_flag','N')->orderBy('id', 'asc')->paginate(20);
		return $update;
	}
	public function getDetailsById($id)
	{
		$update = DocumentPatient::with('templeteDetails')->where('id', $id)->where('deleted_flag', 'N')->first();
		return $update;
	}

	public function getDetailsByIdAll($id)
	{
		$update = DocumentPatient::select('id','patient_id','document_name','attachment','document_completed_date')->where('id', $id)->where('deleted_flag', 'N')->get();
		return $update;
	}

	public function getDocumentIdServicesName($documentIds,$serviceNames,$agencyId){

		$query = DocumentPatient::where('deleted_flag', 'N')->whereHas('patientDetails',function($join) use($agencyId){
			$join->where('agency_id',$agencyId);
		})->whereNotIn('id',$documentIds);

		if(!empty($serviceNames[0])){
			$where = "(";
			$subQuery = "";
			foreach($serviceNames as $key=>$val){
				$condition =" OR ";
				if($key ==0){
					$condition = '';
				}

				$where .=  $condition .'( document_name LIKE "%'.trim($val).'%")';


			}
			$where .=')';
			$query->whereRaw($where);
		}




		$query = $query->pluck('id');

		return $query;
	}

	public function getAllPatientDetails($documentIds,$agencyId,$paginate){
		$query =  DocumentPatient::with(['patientDetails','documentUploadServiceDetails'])->where('deleted_flag', 'N')->whereHas('patientDetails',function($join) use($agencyId){
			$join->where('agency_id',$agencyId);
		})->whereIn('id',$documentIds);

		if($paginate !=""){
			return $query->paginate(50);
		}else{
			return $query->get();
		}
	}

	public function getDocumentWisePatientData($agencyId,$paginate=""){
		return  Patient::where('agency_id',$agencyId)->where('deleted_flag','N')->pluck('id');

	}

	public function getDetailsByPatientId($pids){
		return  DocumentPatient::select('id','patient_id','document_name','created_date')->with('patientDetails:id,first_name,last_name,email,phone,patient_code,completed_date,status,training_status,training_completed_date,diciplin,mobile')->whereIn('patient_id',$pids)->where('deleted_flag','N')->get();

	}

	public function getDetailsByIdAllWithRequestedServiceId($id,$request_service_id)
	{

		$update = DocumentPatient::select('id','patient_id','document_name','attachment','document_completed_date')->whereIn('id', $id)->where('request_service_id',$request_service_id)->where('deleted_flag', 'N')->get();
		return $update;
	}


	public function getTemplateData($template_id)
	{
		$data = Template::where('id', $template_id)->where('del_flag', 'N')->first();
		return $data;
	}

	public function getDocumentSentReportData($group_id)
	{
		$data = DocumentSentReport::where('groupId', $group_id)->where('del_flag', 'N')->whereNotNull('pdf_generate')->orderBy('id', 'DESC')->first();
		return $data;
	}

	public function getDetailsByIdAllWithRequestedServiceIdOnlyBackend($id,$request_service_id)
	{
		$update = DocumentPatient::select('id','patient_id','document_name','attachment','request_service_id')->where('id', $id)->where('request_service_id',$request_service_id)->where('deleted_flag', 'N')->get();
		return $update;
	}

	public function getAllDocumentListByServiceRequest($id,$requested_service_id)
	{
		$update = DocumentPatient::select('document_patient.*','users.first_name', 'users.last_name','uupdate.first_name as updated_first_name', 'uupdate.last_name as updated_last_name')->where('patient_id', $id)->where('request_service_id',$requested_service_id)
		->leftjoin('users', function ($join) {
			$join->on('users.id', '=', 'document_patient.created_by');
			$join->where('users.delete_flag', 'N');
		})
		->leftjoin('users as uupdate', function ($join) {
			$join->on('uupdate.id', '=', 'document_patient.updated_by');
			$join->where('uupdate.delete_flag', 'N');
		})->get();
		return $update;
	}

	public function getPatientDocumentList($search,$patientIds,$paginate=""){
		$auth = auth()->user();
		$agencyids = Utility::getUserWiseAgency();
		if($auth->agency_fk !=""){
			$agencyids[] = $auth->agency_fk;
		}

		$query = DocumentPatient::with(['userDetails:id,first_name,last_name','updatedUserDetails:id,first_name,last_name','reviewUserDetails:id,first_name,last_name','assignUserReviewDocument:id,first_name,last_name'])->select('id','patient_id','document_completed_date','document_name','attachment','created_date','created_by','updated_date','updated_by','request_service_id','templete_id','is_checked','flag','reason','document_review_status','document_review_date','document_review_by','assign_document_review','internal_use','status_note','deleted_flag')->where('deleted_flag','N')->whereNotNull('attachment');
		if($auth->agency_fk !=""){
			$query->where('internal_use',0)->where('document_review_status','Approved');
		}
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
				//and patient_master.archived_at IS NULL
			} else {
				$addCondition="";
				if($auth->record_access !='All'){
					$addCondition=" and patient_master.type='".$auth->record_access."'";
				}
				$where = 'patient_master.deleted_flag ="N" '.$addCondition.'';
				//$where = 'patient_master.deleted_flag ="N" and patient_master.agency_id="' . $auth['agency_fk'] . '"'.$addCondition.'';



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

			if($auth->restrict_user ==1){
				$q->where('created_by',$auth->id);

			}

			if (isset($search['branch_id']) && !empty($search['branch_id'])) {
				$q->where('branch_id',$search['branch_id']);
			}
			$q->whereRaw($where);

		});

		if(isset($search['service_id']) && !empty($search['service_id'])){
			$serviceIds = $search['service_id'];
			$query->whereHas('documentUploadServiceDetails.masterDetails', function($q) use($serviceIds) {
				$q->whereIn('service_id',$serviceIds);
			});
		}

		if(isset($search['patient_id']) && $search['patient_id'] !=""){

			$query->where('patient_id',$search['patient_id']);
		}
		if(isset($search['created_date']) && $search['created_date'] !=""){

			$dExplode = explode('-',$search['created_date']);

			if(isset($dExplode[1])){
				$query->whereDate('created_date','>=',date('Y-m-d',strtotime($dExplode[0])))->whereDate('created_date','<=',date('Y-m-d',strtotime($dExplode[1])));
			}else{
				$query->whereDate('created_date','=',date('Y-m-d',strtotime('0000-00-00')));
			}

		}

		if(isset($search['completion_date']) && $search['completion_date'] !=""){

			$dExplode = explode('-',$search['completion_date']);
			if(isset($dExplode[1])){
				$query->whereDate('document_completed_date','>=',date('Y-m-d',strtotime($dExplode[0])))->whereDate('document_completed_date','<=',date('Y-m-d',strtotime($dExplode[1])));
			}else{
				$query->whereDate('document_completed_date','=',date('Y-m-d',strtotime('0000-00-00')));
			}

		}

		if(isset($search['document_review_date']) && $search['document_review_date'] !=""){

			$dExplode = explode('-',$search['document_review_date']);
			if(isset($dExplode[1])){
				$query->whereDate('document_review_date','>=',date('Y-m-d',strtotime($dExplode[0])))->whereDate('document_review_date','<=',date('Y-m-d',strtotime($dExplode[1])));
			}else{
				$query->whereDate('document_review_date','=',date('Y-m-d',strtotime('0000-00-00')));
			}
		}

		if(isset($search['document_created_by']) && $search['document_created_by'] !=""){

			$query->where('created_by',$search['document_created_by']);
		}

		if(isset($search['assign_document_user']) && $search['assign_document_user'] !=""){

			$query->where('assign_document_review',$search['assign_document_user']);
		}

		if(isset($search['review_document_user']) && $search['review_document_user'] !=""){

			$query->where('document_review_by',$search['review_document_user']);
		}
		if(isset($search['internal_use']) && $search['internal_use'] !=""){

			$query->where('internal_use',$search['internal_use']);
		}

		if(isset($search['document_status']) && $search['document_status'] ==1){
			// $query->whereNull('document_review_status')->whereNotNull('assign_document_review');
			$query->whereNull('document_review_status');
		}
		if($auth->agency_fk !=""){
			$query->where('document_review_status','Approved');
		}

		if (!empty($search['mdo_tag'])) {
			if ($search['mdo_tag'] == 'Yes') {
				$query->where('mdo_tag','=',1);
			} else {
				$query->where('mdo_tag','=',0);
			}
		}

		if (!empty($search['insurance_elg'])) {
			if ($search['insurance_elg'] == 'Yes') {
				$query->where('insurance_elg','=',1);
			} else {
				$query->where('insurance_elg','=',0);
			}
		}

		if (!empty($search['medication_list'])) {
			if ($search['medication_list'] == 'Yes') {
				$query->where('medication_list','=',1);
			} else {
				$query->where('medication_list','=',0);
			}
		}

		if (isset($search['mdo_source']) && !empty($search['mdo_source'])) {
			$query->where('mdo_source', $search['mdo_source']);
		}

		if (!empty($search['send_email'])) {
			if ($search['send_email'] == 'Yes') {
				$query->whereHas('sendBackToAgencyLogs');
			} else {
				$query->whereDoesntHave('sendBackToAgencyLogs');
			}
		}

		$query->whereHas('patientDetails.agencyDetail', function ($q) {
			$q->where('agency.delete_flag', 'N');
		});
		$sortBy = (isset($search['sort_by']) && $search['sort_by'] == 'updated_date') ? 'updated_date' : 'id';
		$sortOrder = (isset($search['sort_order']) && $search['sort_order'] == 'asc') ? 'asc' : 'desc';

		if($paginate !=""){
			return $query = $query->orderBy($sortBy, $sortOrder)->get();
		}else{
			return $query = $query->orderBy($sortBy, $sortOrder)->paginate(50);

		}

	}

	public function getDocumentsIds($search,$patientIds=[],$paginate=""){
		$auth = auth()->user();
		$agencyids = Utility::getUserWiseAgency();
		$query = DocumentPatient::with(['userDetails:id,first_name,last_name','patientDetails:id,inflowcare_id,type,first_name,last_name,agency_id,hha_id,link_hha_caregiver','patientDetails.agencyDetail:id,agency_name,app_name','reviewUserDetails:id,first_name,last_name','assignUserReviewDocument:id,first_name,last_name'])->where('deleted_flag','N')->whereNotNull('attachment');
		$query->whereHas('patientDetails.agencyDetail');

		if(isset($search['patient_id']) && $search['patient_id'] !=""){

			$query->where('patient_id',$search['patient_id']);
		}
		if(isset($search['internal_use']) && $search['internal_use'] ==1){
			$query->where('internal_use',1);
		}
		if(isset($search['created_date']) && $search['created_date'] !=""){
			$dExplode = explode('-',$search['created_date']);
			if(isset($dExplode[1])){
				$query->whereDate('created_date','>=',date('Y-m-d',strtotime($dExplode[0])))->whereDate('created_date','<=',date('Y-m-d',strtotime($dExplode[1])));
			}else{
				$query->whereDate('created_date','=',date('Y-m-d',strtotime('0000-00-00')));
			}

		}

		if(isset($search['completion_date']) && $search['completion_date'] !=""){
			$dExplode = explode('-',$search['completion_date']);
			if(isset($dExplode[1])){
				$query->whereDate('document_completed_date','>=',date('Y-m-d',strtotime($dExplode[0])))->whereDate('document_completed_date','<=',date('Y-m-d',strtotime($dExplode[1])));
			}else{
				$query->whereDate('document_completed_date','=',date('Y-m-d',strtotime('0000-00-00')));
			}

		}

		if(isset($search['document_review_date']) && $search['document_review_date'] !=""){
			$dExplode = explode('-',$search['document_review_date']);
			if(isset($dExplode[1])){
				$query->whereDate('document_review_date','>=',date('Y-m-d',strtotime($dExplode[0])))->whereDate('document_review_date','<=',date('Y-m-d',strtotime($dExplode[1])));
			}else{
				$query->whereDate('document_review_date','=',date('Y-m-d',strtotime('0000-00-000')));
			}

		}

		if(isset($search['document_created_by']) && $search['document_created_by'] !=""){
			$query->where('created_by',$search['document_created_by']);
		}

		if(isset($search['assign_document_user']) && $search['assign_document_user'] !=""){
			$query->where('assign_document_review',$search['assign_document_user']);
		}

		if(isset($search['review_document_user']) && $search['review_document_user'] !=""){
			$query->where('document_review_by',$search['review_document_user']);
		}
		if(isset($search['internal_use']) && $search['internal_use'] !=""){
			$query->where('internal_use',$search['internal_use']);
		}
		if(isset($search['document_status']) && $search['document_status'] ==1){
			$query->whereNull('document_review_status')->whereNotNull('assign_document_review');
		}

		if (!empty($search['send_email'])) {
			if ($search['send_email'] == 'Yes') {
				$query->whereHas('sendBackToAgencyLogs');
			} else {
				$query->whereDoesntHave('sendBackToAgencyLogs');
			}
		}

		$query->whereHas('patientDetails',function($subQuery) use($search,$agencyids){
			$subQuery->where('deleted_flag','N');


			if(isset($search['agency_id']) && $search['agency_id'] !=""){

				$subQuery->whereIn('agency_id',$search['agency_id']);
			}else{
				if(count($agencyids) >0){
					$subQuery->whereIn('agency_id',$agencyids);
				}
			}


			if(isset($search['patient_type']) && $search['patient_type'] !=""){

				$subQuery->where('type',$search['patient_type']);
			}
		});

		return $query = $query->get();

	}

	public function getDocumentsWithAllDatas($docIds){
		$auth = auth()->user();
		$query = DocumentPatient::with(['userDetails:id,first_name,last_name','patientDetails:id,inflowcare_id,type,first_name,last_name,agency_id,hha_id,link_hha_caregiver','patientDetails.agencyDetail:id,agency_name,app_name','documentUploadServiceDetails'])->where('deleted_flag','N')->whereNotNull('attachment')->whereIn('id',$docIds);
		$query->whereHas('patientDetails',function($subQuery){
			$subQuery->where('deleted_flag','N');

		});
		return $query = $query->groupBy('id')->orderBy('id','desc')->get();

	}

	public function getDataForHamaspik($docIds,$agencyId,$created_date,$paginate=""){
		$auth = auth()->user();
		$query = DocumentPatient::with(['userDetails:id,first_name,last_name','patientDetails:id,inflowcare_id,patient_code,type,mobile,email,first_name,last_name,agency_id,hha_id,link_hha_caregiver','patientDetails.agencyDetail:id,agency_name,app_name'])->where('deleted_flag','N')->whereIn('id',$docIds);
		$query->whereHas('patientDetails',function($subQuery) use($agencyId){
			$subQuery->where('deleted_flag','N')->where('agency_id',$agencyId);

		});
		if($created_date !=""){
			$explode = explode('-',$created_date);
			$query->whereDate('document_completed_date','>=',date('Y-m-d',strtotime($explode[0])))->whereDate('document_completed_date','<=',date('Y-m-d',strtotime($explode[1])));
		}

		if($paginate !=""){
			return $query = $query->orderBy('id','desc')->get();
		}else{
			return $query = $query->orderBy('id','desc')->paginate(50);
		}
	}

	public function getDocumentAttach($pid,$requested_service_id){
		return DocumentPatient::select('attachment','document_completed_date','document_name','id')->where('patient_id',$pid)->where('request_service_id',$requested_service_id)->whereNotNull('attachment')->first();
	}

	public function getDocumentByPatientId($id)
	{
		$query = DocumentPatient::select('document_patient.id','document_patient.document_name','document_completed_date','document_patient.created_date')->where('patient_id', $id)->where('deleted_flag','N')->orderBy('created_date','desc')->get();
		return $query;
	}

	public function getWriteDocData($esign_doc_id)
	{
		$data = WriteDocument::where('document_patient_id', $esign_doc_id)->first();
		return $data;
	}

	public function getFlagAllDocumentByPatientId()
	{
		$query = DocumentPatient::select('document_patient.*','users.first_name', 'users.last_name','uupdate.first_name as updated_first_name', 'uupdate.last_name as updated_last_name')->where('flag', 1)
		->leftjoin('users', function ($join) {
			$join->on('users.id', '=', 'document_patient.created_by');
			$join->where('users.delete_flag', 'N');
		})
		->leftjoin('users as uupdate', function ($join) {
			$join->on('uupdate.id', '=', 'document_patient.updated_by');
			$join->where('uupdate.delete_flag', 'N');
		})->paginate(50);
		return $query;
	}
	public function getFlagAllDocumentByPatientIdAgency()
	{
		$query = DocumentPatient::select('document_patient.*','users.first_name', 'users.last_name','uupdate.first_name as updated_first_name', 'uupdate.last_name as updated_last_name')->where('flag', 1)
		->leftjoin('users', function ($join) {
			$join->on('users.id', '=', 'document_patient.created_by');
			$join->where('users.delete_flag', 'N');
		})
		->leftjoin('users as uupdate', function ($join) {
			$join->on('uupdate.id', '=', 'document_patient.updated_by');
			$join->where('uupdate.delete_flag', 'N');
		})->where('deleted_flag','N')->paginate(50);
		return $query;
	}

	public function getAllDocumentSentReportData($group_id)
	{
		$data = DocumentSentReport::where('groupId', $group_id)->where('del_flag', 'N')->orderBy('id', 'DESC')->first();
		return $data;
	}

	public function mergeAppointmentAppointmentId($patientId){
		return DocumentPatient::select('id','patient_id')->where('deleted_flag','N')->where('patient_id',$patientId)->get();
	}

	public function getDocumentNameByServiceId($id){
		return DocumentPatient::where('deleted_flag','N')->where('request_service_id',$id)->get();
	}

	public function getDocumentListByPatientId($pid){
		return  DocumentPatient::select('id','document_name')->where('patient_id',$pid)->where('deleted_flag','N')->get();

	}

	public function getDocumentDetailsByIdOrPatientId($documentId,$patientId=""){
		$query =  DocumentPatient::with(['userDetails:id,first_name,last_name','reviewUserDetails:id,first_name,last_name','assignUserReviewDocument:id,first_name,last_name','patientDetails:id,first_name,last_name,agency_id,ssn,gender,type,dob','patientDetails.agencyDetail:id,agency_name'])->where('id',$documentId);
		if($patientId !=""){
			$query->where('patient_id',$patientId);
		}
		$query = $query->where('deleted_flag','N')->first();

	return $query;
	}

	public function getAllDocumentByPatientIdsAgency($id,$order='asc')
	{

		$update = DocumentPatient::select('document_patient.*','users.first_name', 'users.last_name','uupdate.first_name as updated_first_name', 'uupdate.last_name as updated_last_name')->whereIn('patient_id', $id)
		->leftjoin('users', function ($join) {
			$join->on('users.id', '=', 'document_patient.created_by');
			$join->where('users.delete_flag', 'N');
		})
		->leftjoin('users as uupdate', function ($join) {
			$join->on('uupdate.id', '=', 'document_patient.updated_by');
			$join->where('uupdate.delete_flag', 'N');
		})->where('deleted_flag','N')->where('internal_use',0)->where('is_task_helath_dup',0)->where('document_review_status','Approved')->orderBy('id', $order)->paginate(20);
		return $update;
	}

	public function getDocumentDetailsById($id)
	{
		$data = DocumentPatient::where('id', $id)->where('deleted_flag', 'N')->first();
		return $data;
	}

	public function getPatientPendingDocumentList($search,$paginate=""){
		$auth = auth()->user();
		$query = DocumentPatient::with(['userDetails:id,first_name,last_name','patientDetails:id,inflowcare_id,type,first_name,last_name,agency_id,hha_id,link_hha_caregiver','patientDetails.agencyDetail:id,agency_name,app_name','documentUploadServiceDetails','reviewUserDetails:id,first_name,last_name','assignUserReviewDocument:id,first_name,last_name'])->where('deleted_flag','N')->whereNotNull('attachment')->whereNotIn('document_review_status',['Approved','Rejected']);
		$query->whereHas('patientDetails.agencyDetail');
		if(isset($search['patient_id']) && $search['patient_id'] !=""){

			$query->where('patient_id',$search['patient_id']);
		}
		if(isset($search['created_date']) && $search['created_date'] !=""){
			$dExplode = explode('-',$search['created_date']);

			if(isset($dExplode[1])){
				$query->whereDate('created_date','>=',date('Y-m-d',strtotime($dExplode[0])))->whereDate('created_date','<=',date('Y-m-d',strtotime($dExplode[1])));
			}else{
				$query->whereDate('created_date','=',date('Y-m-d',strtotime('0000-00-00')));
			}

		}

		if(isset($search['completion_date']) && $search['completion_date'] !=""){
			$dExplode = explode('-',$search['completion_date']);
			if(isset($dExplode[1])){
				$query->whereDate('document_completed_date','>=',date('Y-m-d',strtotime($dExplode[0])))->whereDate('document_completed_date','<=',date('Y-m-d',strtotime($dExplode[1])));
			}else{
				$query->whereDate('document_completed_date','=',date('Y-m-d',strtotime('0000-00-00')));
			}

		}

		if(isset($search['document_review_date']) && $search['document_review_date'] !=""){
			$dExplode = explode('-',$search['document_review_date']);
			if(isset($dExplode[1])){
				$query->whereDate('document_review_date','>=',date('Y-m-d',strtotime($dExplode[0])))->whereDate('document_review_date','<=',date('Y-m-d',strtotime($dExplode[1])));
			}else{
				$query->whereDate('document_review_date','=',date('Y-m-d',strtotime('0000-00-00')));
			}
		}

		if(isset($search['document_created_by']) && $search['document_created_by'] !=""){
			$query->where('created_by',$search['document_created_by']);
		}

		if(isset($search['assign_document_user']) && $search['assign_document_user'] !=""){
			$query->where('assign_document_review',$search['assign_document_user']);
		}

		if(isset($search['review_document_user']) && $search['review_document_user'] !=""){
			$query->where('document_review_by',$search['review_document_user']);
		}
		if(isset($search['internal_use']) && $search['internal_use'] !=""){
			$query->where('internal_use',$search['internal_use']);
		}

		if(isset($search['document_status']) && $search['document_status'] ==1){
			$query->whereNull('document_review_status')->whereNotNull('assign_document_review');
		}
		$query->whereHas('patientDetails',function($subQuery) use($search){
			$subQuery->where('deleted_flag','N');
			if(isset($search['agency_id']) && $search['agency_id'] !=""){

				$subQuery->whereIn('agency_id',$search['agency_id']);
			}
			if(isset($search['patient_type']) && $search['patient_type'] !=""){

				$subQuery->where('type',$search['patient_type']);
			}

		});

		if($paginate !=""){
			return $query = $query->orderBy('id','desc')->get();
		}else{
			return $query = $query->orderBy('id','desc')->simplePaginate(50);

		}

	}

	public function getDocumentsIdsNew($search,$patientIds=[],$paginate=""){

		$agencyids = Utility::getUserWiseAgency();
		$query = DocumentPatient::select('document_patient.id','document_patient.document_name','document_patient.patient_id','document_patient.document_completed_date','document_patient.document_review_date','document_patient.document_review_status','document_patient.request_service_id','document_patient.created_date','document_patient.internal_use','users.first_name as createdByFname','users.last_name as createdByLname','pt.first_name as pFname','pt.last_name as pLname','pt.type','pt.hha_id','pt.link_hha_caregiver','ag.agency_name','drw.first_name as docReviewFirstName','drw.last_name as docReviewLastName','asu.first_name as assignFirstName','asu.last_name as assignLastName')
		->leftjoin('users',function($join){
			$join->on('users.id',"=",'document_patient.created_by');
			$join->where('users.delete_flag','N');
		})
		->join('patient_master as pt',function($join){
			$join->on('pt.id',"=",'document_patient.patient_id');
			$join->where('pt.deleted_flag','N');
		})
		->join('agency as ag',function($join){
			$join->on('ag.id',"=",'pt.agency_id');
			$join->where('ag.delete_flag','N');
		})
		->leftjoin('users as drw',function($join){
			$join->on('drw.id',"=",'document_patient.document_review_by');
			$join->where('ag.delete_flag','N');
		})
		->leftjoin('users as asu',function($join){
			$join->on('asu.id',"=",'document_patient.assign_document_review');
			$join->where('asu.delete_flag','N');
		})
		->where('document_patient.deleted_flag','N')->whereNotNull('document_patient.attachment')->whereNotNull('document_patient.patient_id');


		if(isset($search['patient_id']) && $search['patient_id'] !=""){

			$query->where('document_patient.patient_id',$search['patient_id']);
		}

		if(isset($search['created_date']) && $search['created_date'] !=""){
			$dExplode = explode('-',$search['created_date']);
			if(isset($dExplode[1])){
				$query->whereDate('document_patient.created_date','>=',date('Y-m-d',strtotime($dExplode[0])))->whereDate('document_patient.created_date','<=',date('Y-m-d',strtotime($dExplode[1])));
			}else{
				$query->whereDate('document_patient.created_date','=',date('Y-m-d',strtotime('0000-00-00')));
			}

		}

		if(isset($search['completion_date']) && $search['completion_date'] !=""){
			$dExplode = explode('-',$search['completion_date']);
			if(isset($dExplode[1])){
				$query->whereDate('document_patient.document_completed_date','>=',date('Y-m-d',strtotime($dExplode[0])))->whereDate('document_patient.document_completed_date','<=',date('Y-m-d',strtotime($dExplode[1])));
			}else{
				$query->whereDate('document_patient.document_completed_date','=',date('Y-m-d',strtotime('0000-00-00')));
			}

		}

		if(isset($search['document_review_date']) && $search['document_review_date'] !=""){
			$dExplode = explode('-',$search['document_review_date']);
			if(isset($dExplode[1])){
				$query->whereDate('document_patient.document_review_date','>=',date('Y-m-d',strtotime($dExplode[0])))->whereDate('document_patient.document_review_date','<=',date('Y-m-d',strtotime($dExplode[1])));
			}else{
				$query->whereDate('document_patient.document_review_date','=',date('Y-m-d',strtotime('0000-00-000')));
			}

		}

		if(isset($search['document_created_by']) && $search['document_created_by'] !=""){
			$query->where('document_patient.created_by',$search['document_created_by']);
		}

		if(isset($search['assign_document_user']) && $search['assign_document_user'] !=""){
			$query->where('document_patient.assign_document_review',$search['assign_document_user']);
		}

		if(isset($search['review_document_user']) && $search['review_document_user'] !=""){
			$query->where('document_patient.document_review_by',$search['review_document_user']);
		}
		if(isset($search['internal_use']) && $search['internal_use'] !=""){
			$query->where('document_patient.internal_use',$search['internal_use']);
		}
		if(isset($search['document_status']) && $search['document_status'] ==1){
			$query->whereNull('document_patient.document_review_status')->whereNotNull('assign_document_review');
		}

		if(isset($search['agency_id']) && $search['agency_id'] !=""){

			$query->whereIn('pt.agency_id',$search['agency_id']);
		}else{
			if(count($agencyids) >0){
				$query->whereIn('pt.agency_id',$agencyids);
			}
		}

		if(isset($search['patient_type']) && $search['patient_type'] !=""){

			$query->where('pt.type',$search['patient_type']);
		}


		return $query = $query->get();

	}

	public function getPendingData($search){
		$auth = auth()->user();
		$agencyids = Utility::getUserWiseAgency();
		$statusInclude = SiteSetting::select('document_dashboard_status')->first();
		if($auth->agency_fk !=""){
			$agencyids[] = $auth->agency_fk;
		}

		$query = DocumentPatient::with(['userDetails:id,first_name,last_name','patientDetails:id,type,first_name,last_name,agency_id,status','patientDetails.agencyDetail:id,agency_name'])->select('id','patient_id','document_completed_date','document_name','attachment','created_date','created_by','request_service_id','templete_id','is_checked','flag','reason','document_review_status','document_review_date','document_review_by','assign_document_review','internal_use','status_note','deleted_flag')->where('deleted_flag','N')->whereNotNull('attachment');
		$query->whereHas('patientDetails', function ($q)  use ($search,$agencyids,$auth,$statusInclude) {
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
				//and patient_master.archived_at IS NULL
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
			if(isset($statusInclude->document_dashboard_status) && !empty($statusInclude->document_dashboard_status)){
				$explode = explode(',',$statusInclude->document_dashboard_status);
					$final = [];
					foreach($explode as $vsl){
						if($vsl =='Signed-SentBacktotheAgency'){
							$vsl = 'Signed & Sent Back to the Agency';
						}
						if($vsl =='TelehealthCompleted-Pending Forms'){
							$vsl = 'Telehealth Completed , Pending Forms';
						}
						$final[] = $vsl;
					}
					$q->whereIn('status',$final);
			}else{
				$q->where('status','processing');
			}
			$q->whereRaw($where);
		});
		if(isset($search['patient_id']) && $search['patient_id'] !=""){
			$query->where('document_patient.patient_id',$search['patient_id']);
		}
		if(isset($search['created_date']) && $search['created_date'] !=""){
			$dExplode = explode('-',$search['created_date']);
			if(isset($dExplode[1])){
				$query->whereDate('created_date','>=',date('Y-m-d',strtotime($dExplode[0])))->whereDate('created_date','<=',date('Y-m-d',strtotime($dExplode[1])));
			}else{
				$query->whereDate('created_date','=',date('Y-m-d',strtotime('0000-00-00')));
			}
		}
		if(isset($search['document_created_by']) && $search['document_created_by'] !=""){
			$query->where('document_patient.created_by',$search['document_created_by']);
		}
		if(isset($search['assign_document_user']) && $search['assign_document_user'] !=""){
			$query->where('document_patient.assign_document_review',$search['assign_document_user']);
		}
		if(isset($search['service_id']) && !empty($search['service_id'])){
			$serviceIds = $search['service_id'];
			$query->whereHas('documentUploadServiceDetails', function($q) use($serviceIds) {
				$q->whereIn('service_id',$serviceIds);
			});
		}

		$service_id = ['181'];
		$getApprovedUserId = Utility::dynamicDocumentApproved();
		$jadaArray = $tilinArray = [];
		foreach($getApprovedUserId as $key => $userIds){
			if($key !="All"){
				if($key == '181'){
					foreach($userIds as $key => $u){
						$jadaArray = array_keys($u);
					}
				}else{

					foreach($userIds as $key => $u){
						$tilinArray = array_keys($u);
					}
				}
			}

		}

		if(in_array(auth()->user()->id,$jadaArray)){
			$query->whereHas('documentUploadServiceDetails', function($q) use($service_id) {
				$q->whereIn('service_id',$service_id);
			});
		}else if(in_array(auth()->user()->id,$tilinArray)){

			$query->where(function($q) use($service_id) {
				$q->whereHas('documentUploadServiceDetails', function($subQuery) use($service_id) {
					$subQuery->whereNotIn('service_id', $service_id);
				})->orWhereDoesntHave('documentUploadServiceDetails');
			})->where('document_review_status', 'Pending');

		}else{
			$query->whereRaw("FIND_IN_SET(?, assign_document_review)", [auth()->user()->id]);
		}
		$query->whereHas('patientDetails.agencyDetail', function ($q) {
			$q->where('agency.delete_flag', 'N');
		});
		$query->where('document_review_status','Pending');
		return $query = $query->orderBy('id','desc')->paginate(50);
	}

	public function getPendingDocData($patientId){
		$query = DocumentPatient::select('id','assign_document_review','document_name','created_date','created_by')->with(['documentUploadServiceDetailsMany:id,document_id,service_id'])
		->where('deleted_flag','N')->whereNotNull('attachment');
		$query->where('document_patient.patient_id',$patientId);
		$query->where('document_review_status','Pending');
		return $query = $query->get();
	}

	public function getInternalUseDocData($patientId){
		$query = DocumentPatient::select('id','assign_document_review','document_name','created_date','created_by','document_completed_date','request_service_id')->with(['userDetails:id,first_name,last_name'])
		->where('deleted_flag','N')->whereNotNull('attachment');
		$query->where('document_patient.patient_id',$patientId);
		$query->where('document_review_status','Pending');
		$query->where('internal_use','1');
		return $query = $query->get();
	}

	public function getLastDocumentNameByServiceId($id,$pid){
		return DocumentPatient::where('deleted_flag','N')->where('internal_use',0)->where('request_service_id',$id)->where('patient_id',$pid)->orderBy('created_date','desc')->first();
	}

	public function getDocumentByDocIdAndPatientId($id,$pid){
		return DocumentPatient::select('patient_id','document_name','attachment','created_date','created_by','hha_document_id','hha_medical_doc_id','uploaded_to_hha','uploaded_complience_hha','request_service_id','document_completed_date','templete_id','is_checked','agency_form_id','flag','reason','internal_use','send_third_party','send_third_party_date','send_third_party_by','document_review_status','document_review_date','document_review_by','assign_document_review','status_note')->where('deleted_flag','N')->where('id',$id)->where('patient_id',$pid)->first();
	}

	public function getAllDocumentListByApi($patientId,$search){
		$query = DocumentPatient::select('id','patient_id','document_name','created_date','created_by','document_completed_date')->with('patientDetails:id,first_name,last_name,email,phone,patient_code,mobile,dob')->whereIn('patient_id',$patientId)->where('deleted_flag','N');
		$startDate = "";
		$endDate = "";

		if(isset($search['start_date']) && $search['start_date'] !=""){
			$startDate = date('Y-m-d',strtotime($search['start_date'])).' 00:00:00';
		}

		if(isset($search['end_date']) && $search['end_date'] !=""){
			$endDate = date('Y-m-d',strtotime($search['end_date'])).' 23:59:59';
		}

		$query = $query->where('created_date','>=',$startDate)->where('created_date','<=',$endDate)->orderBy('id','desc')->get();
		return $query;
	}

	public function getAllDocumentByPatientIdApiSide($id)
	{
		$update = DocumentPatient::select('document_patient.id','document_patient.patient_id','document_patient.document_name','document_patient.attachment','document_patient.deleted_flag','document_patient.created_date','document_patient.created_by','document_patient.uploaded_to_hha','document_patient.uploaded_complience_hha','document_patient.request_service_id',
'document_patient.document_completed_date','document_patient.templete_id','document_patient.flag','document_patient.internal_use',
'document_patient.send_third_party','document_patient.document_review_by','document_patient.assign_document_review','document_patient.document_review_status','document_patient.document_review_date','document_patient.sign_stamp_status','document_patient.updated_date','document_patient.updated_by','users.first_name', 'users.last_name','uupdate.first_name as updated_first_name', 'uupdate.last_name as updated_last_name')->with(['assignUserReviewDocument:id,first_name,last_name',
'reviewUserDetails:id,first_name,last_name'])->whereIn('patient_id', $id)
		->leftjoin('users', function ($join) {

			$join->on('users.id', '=', 'document_patient.created_by');
			$join->where('users.delete_flag', 'N');
		})
		->leftjoin('users as uupdate', function ($join) {
			$join->on('uupdate.id', '=', 'document_patient.updated_by');
			$join->where('uupdate.delete_flag', 'N');
		})->where('internal_use',0)->orderBy('id', 'asc')->paginate(20);
		return $update;
	}

	public function getDetailsBydocId($documentId){
		return DocumentPatient::where('id',$documentId)->where('deleted_flag','N')->first();
	}

	public function getAllDocumentListById($doc_id)
	{
		$update = DocumentPatient::select('document_patient.*','users.first_name', 'users.last_name','uupdate.first_name as updated_first_name', 'uupdate.last_name as updated_last_name')->whereIn('document_patient.id', $doc_id)
		->leftjoin('users', function ($join) {
			$join->on('users.id', '=', 'document_patient.created_by');
			$join->where('users.delete_flag', 'N');
		})
		->leftjoin('users as uupdate', function ($join) {
			$join->on('uupdate.id', '=', 'document_patient.updated_by');
			$join->where('uupdate.delete_flag', 'N');
		})->get();
		return $update;
	}

	public function getLastDocumentByPatientId($patientId){
		return DocumentPatient::select('id','document_name','document_name','attachment')->where('internal_use',0)->where('patient_id',$patientId)->orderBy('id','desc')->first();
	}

	public function findById($id)
	{
		return DocumentPatient::find($id);
	}

}