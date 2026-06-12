<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\DocumentSentReport;
use App\Model\AgencyForm;
use App\Model\WriteDocument;
use App\Model\DocumentPatient;
use App\Template;

class DocumentSendService
{
	public  function save($data)
	{
		$userId = Auth()->user();
		$data['created_date'] = date('Y-m-d h:i:s');
		$data['created_by'] = $userId['id'];

		$insert = new DocumentSentReport($data);
		$insert->save();
		$insertId = $insert->id;
		return $insertId;
	}
	public static function saveesign($data)
	{
		$data['created_date'] = date('Y-m-d H:i:s');
		$insert = new DocumentSentReport($data);
		$insert->save();
		return $insert->id;
	}
	public  function update($data, $where)
	{
		$userId = Auth()->user();
		$data['updated_date'] = date('Y-m-d H:i:s');
		if (isset($userId['id']) && $userId['id'] != '') {
			$data['updated_by'] = $userId['id'];
		}
		$update = DocumentSentReport::where($where)->update($data);
		return $update;
	}

	public static function updateEsign($data, $where)
	{
		$userId = Auth()->user();
		$data['updated_date'] = date('Y-m-d H:i:s');
		if (isset($userId['id']) && $userId['id'] != '') {
			$data['updated_by'] = $userId['id'];
		}
		$update = DocumentSentReport::where($where)->update($data);
		return $update;
	}
	public  function SoftDelete($data, $where)
	{
		$userId = Auth()->user();
		$data['deleted_date'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $userId['id'];
		$update = DocumentSentReport::where($where)->update($data);
		return $update;
	}

	function TotalSignerCount($groupId)
	{
		$query = DocumentSentReport::select(DB::raw('COUNT(id) as total'))->where('del_flag', 'N')->whereNotIn('status', ['completed', 'Approved', 'Rejected'])->where('groupId', $groupId)->get();
		return $query;
	}
	function GetGeneratePDF($groupId)
	{
		$query = DocumentSentReport::select('pdf_generate')->where('del_flag', 'N')->where('groupId', $groupId)->where('pdf_generate', '!=', '')->orderBy('id', 'desc')->get();
		return $query;
	}

	function GetDetailsbyGroupId($groupId)
	{
		return DocumentSentReport::where('groupId', $groupId)->get();
	}

	function AssignTemplateList($id, $type)
	{
		$query = DocumentSentReport::select('document_sent_report.groupId', 'document_sent_report.status', 'document_sent_report.created_date', 'tm.template_name')
			->join('template_master as tm', function ($join) {
				$join->on('tm.id', '=', 'document_sent_report.templete_id');
			})->where('document_sent_report.caregiver_code', $id)->where('tm.del_flag', 'N')->where('document_sent_report.del_flag', 'N')->where('document_sent_report.singstatus', '=', 'No')->where('document_sent_report.status', '!=', 'Completed')->where('type', $type)->get();
		return $query;
	}

	function getAllDetails($id)
	{
		$query = DocumentSentReport::select('document_sent_report.*', 'tm.docWidth', 'tm.response', 'tm.template_name', 'tm.upload_document')
			->join('template_master as tm', function ($join) {
				$join->on('tm.id', '=', 'document_sent_report.templete_id');
			})->where('document_sent_report.id', $id)->first();
		return $query;
	}
	function pdfGenerateOrNot($id)
	{
		$query = DocumentSentReport::select('pdf_generate')->where('groupId', $id)->where('pdf_generate', '!=', null)->first();
		return $query;
	}

	function getDocumentServiceInApi($document_report_id)
	{
		$query = DocumentSentReport::select('sourceFile', 'type','sent_on','created_by','doctor_id')->where('id', $document_report_id)->where('del_flag', 'N')->where('sourceFile', '!=', '')->first();
		return $query;
	}

	function GetNextSignerDetails($id, $groupId)
	{
		$query = DocumentSentReport::where('templete_id', $id)->where('groupId', $groupId)->where('del_flag', 'N')->where('status', '=', '')->first();
		return $query;
	}

	function getTotalSigner($groupId)
	{
		$query = DocumentSentReport::select('id as MainTotal')->where('groupId', $groupId)->count();
		return $query;
	}

	function getTotalComplete($groupId)
	{
		$query = DocumentSentReport::select('id as CompletedTotal')->where('groupId', $groupId)->where('status', 'completed')->count();
		return $query;
	}

	public function getGroupPending($id)
	{
		return DocumentSentReport::select('*')->where('groupId', $id)->where('status', '=', 'Pending')->where('del_flag', 'N')->first();
	}

	public function CompleteEmail($esign_pdf_id)
	{
		$explode = explode(',', $esign_pdf_id);
		$result = "'" . implode("', '", $explode) . "'";

		$query = DocumentSentReport::select('document_sent_report.pdf_generate', 'document_sent_report.status', 'tmp.template_name')
			->leftjoin('template_master as tmp', function ($join) {
				$join->on('tmp.id', '=', 'document_sent_report.templete_id');
			})->whereRaw('document_sent_report.del_flag ="N" and document_sent_report.status ="Completed"  and document_sent_report.pdf_generate !="" and  document_sent_report.groupId IN(' . $result . ')')->groupBy('document_sent_report.groupId')->orderBy('document_sent_report.id', 'desc')->get();

		return $query;
	}
	function getDetailsById($groupId)
	{
		$query = DocumentSentReport::with('doctors:id,full_name,phone,signature_upload,stamp_upload,is_signature_stamp_active')->where('id', $groupId)->first();
		return $query;
	}
	public static function getDetailsNew($templateId, $main_intakeId)
	{

		$query = DocumentSentReport::whereRaw('caregiver_code ="' . $main_intakeId . '"  and del_flag="N" and templete_id="' . $templateId . '" and status="Pending"')->first();


		return $query;
	}

	public static function totalPendingEsign($main_intakeId)
	{

		$query = DocumentSentReport::whereRaw('caregiver_code ="' . $main_intakeId . '"  and del_flag="N"  and status="Pending"')->count();


		return $query;
	}

	public function caregiverWiseEsignTemplateList($type, $eid, $sortColumn, $sortOrder)
	{
		$query1 = DocumentSentReport::with(['templateDetails:id,template_name,document_type,upload_document,del_flag,created_date,created_by,updated_date,updated_by,remark,docWidth,lookup_fields,esign,active_status,email_notification,resouce_tab,agency_id,custom_form_id','templateDetails.documentTypeDetails', 'userDetails:id,first_name,last_name','writeDocumentDetails:id,document_patient_id,type,file_upload,docWidth,document_name','reviewDetails:id,first_name,last_name'])
		->where('del_flag','N');
		if(!empty($eid)){
			$query1->whereIn('main_intakeId',$eid);
		}
		
		$query = $query1->groupBy('document_sent_report.groupId')->orderBy($sortColumn, $sortOrder)->paginate(50);
		return $query;
	}

	public static function getMedicalDocumentById($medicalId)
	{
		$query = DocumentSentReport::where('status', 'Pending')->where('del_flag', 'N')->where('caregiver_medical_id', $medicalId)->first();
		return $query;
	}

	public static function getPendingCaregiverDocument($templateId, $caregiverId)
	{
		$query = DocumentSentReport::where('status', 'Pending')->where('del_flag', 'N')->where('templete_id', $templateId)->where('main_intakeId', $caregiverId)->first();
		return $query;
	}

	public function caregiverWiseEsignReport($type="", $cid="", $tid="",$status="",$hr_status="",$completed_date="",$created_date="",$export="")
	{
	
		$temp = 'document_sent_report.del_flag ="N" and document_sent_report.type ="caregiver" and document_sent_report.status ="Completed"';
		if($cid !=""){
			$temp .=' and document_sent_report.main_intakeId ="'.$cid.'"';
		}

		if($tid !=""){
			$temp .=' and document_sent_report.templete_id ="'.$tid.'"';
		}
		if($status !=""){
			$temp .=' and document_sent_report.status ="'.$status.'"';
		}
		if($hr_status !=""){
			$temp .=' and document_sent_report.hr_approval_status ="'.$hr_status.'"';
		}
		if($completed_date !=""){
			$explode= explode('-',$completed_date);
			
			$temp .=' and document_sent_report.completed_on >="'.date('Y-m-d',strtotime($explode[0])).'" and document_sent_report.completed_on <="'.date('Y-m-d',strtotime($explode[1])).'"';
		}

		if($created_date !=""){
			$explodes= explode('-',$created_date);
			$temp .=' and document_sent_report.created_date >="'.date('Y-m-d',strtotime($explodes[0])).'" and document_sent_report.created_date <="'.date('Y-m-d',strtotime($explodes[1])).'"';
		}

		$query1 = DocumentSentReport::with(['templateDetails.documentTypeDetails', 'userDetails:id,first_name,last_name','caregiverDetails']
		)->whereHas('caregiverDetails')->whereRaw($temp)
			->groupBy('document_sent_report.groupId')->orderBy('id', 'desc');
			if($export !=""){
				$query = $query1->get();
			}else{
				$query = $query1->paginate(50);
			}
			
		return $query;
	}

	public function findEsignDocumentById($groupId,$id){
		return DocumentSentReport::where('groupId', $groupId)->where('id',$id)->first();
	}

	public function getGroupPendingNew($groupId){
		return DocumentSentReport::where('id',$groupId)->first();
	}

	public function EsignTemplateList()
	{
		$temp = 'document_sent_report.del_flag ="N" AND main_intakeId IS NOT NULL';
		$query1 = DocumentSentReport::with(['templateDetails.documentTypeDetails', 'userDetails','reviewDetails'])->whereRaw($temp)
			->groupBy('document_sent_report.groupId')->orderBy('id', 'desc');
		return $query1;
	}

	public function saveWriteDocumentData(array $data)
	{
		$data['old_file_upload'] = $data['file_upload'];
		WriteDocument::create($data);
	}

	public function getWriteDataByID($id,$type="")
	{
		$query =  WriteDocument::where('document_patient_id', $id);
		if($type !=""){
			$query->where('type','Document');
		}else{
			$query->where('type','Esign');
		}
		return $query->first();
	}


	public function getWriteDataByUniqueId($id)
	{
		return WriteDocument::where('id', $id)->first();
		
	}

	public function markDocumentAsCompleted($documentKey,$file)
    {
        $updated = DocumentSentReport::where('id', $documentKey)
            ->update([
                'status' => 'Completed',
                'completed_on' => date('Y-m-d H:i:s'),
				'pdf_generate'=>$file
            ]);

        return $updated;
    }

	public function updateDocumentResponse($documentKey,$response, $docWidth,$fileName="",$isSubmit=0)
    {
		$finalArray = [
			'response' => $response,
			'docWidth' => $docWidth,
		];

		if($fileName !=""){
			$finalArray['file_upload'] = $fileName;
		}

		if($isSubmit ==1){
			$finalArray['is_submit'] = 1;
		}
        $updated = WriteDocument::where('document_patient_id', $documentKey)
            ->update($finalArray);

        return $updated;
    }
	
	function getDocumentServiceData($document_report_id)
	{
		$query = DocumentSentReport::with('templateDetails')->where('id', $document_report_id)->where('del_flag', 'N')->where('sourceFile', '!=', '')->first();
		return $query;
	}
	function getDetailsByIdNew($groupId)
	{
		$query = DocumentSentReport::with('templateDetails','userDetails:id,first_name,last_name')->where('id', $groupId)->first();
		return $query;
	}

	function getDetailsByGroup($groupId)
	{
		$query  = DocumentSentReport::with('templateDetails','userDetails:id,first_name,last_name')->where('document_submit_status', '=', 1)->where('groupId', $groupId)->orderBy('id', 'desc')->first();
		return $query;
	}

	function detailsByGroupId($groupId)
	{
		$query = DocumentSentReport::with('templateDetails','userDetails:id,first_name,last_name')->where('groupId', $groupId)->first();
		return $query;
	}

	public function gettotalCount($status="",$from_date="",$to_date="",$type="",$location_id="",$agency_id="")
	{
		$query  = DocumentSentReport::select('document_sent_report.id','main_intakeId','groupId','pdf_status')->with(['patient:id,agency_id,location_id'])->where('del_flag', '=', 'N');
		if($status == 'Pending'){
			$query->where('status','Pending');
		}else if($status == 'Completed'){
			$query->where('status','Completed');
		}else if($status == 'Approved'){
			$query->where('pdf_status','1');
		}else if($status == 'Rejected'){
			$query->where('status','Completed')->where('pdf_status','0');
		}
		if ($from_date != "" && $to_date != "") {
			$query = $query->whereBetween('created_date', [$from_date, $to_date]);
		}
		if($type != ''){
			$query->where('type',$type);
		}
		if($location_id != ""){
			$query->whereHas('patient', function($query) use ($location_id) {
				$query->where('location_id', $location_id);
			});
		}
		if (!empty($agency_id[0])) {
			$query->whereHas('patient', function($query) use ($agency_id) {
				$query->whereIn('agency_id', $agency_id);
			});
		}
		$query->where('status','!=','');
		return $query->groupBy('document_sent_report.groupId')->get();
	}

	public function getTodayEsignData($type,$agency_id = array())
	{
		$from_date = date('Y-m-d 00:00:00');
		$to_date = date('Y-m-d 23:59:59');
		$query  = DocumentSentReport::select('id','status','sender_name','review_by','created_by','created_date','templete_id','groupId','completed_on','main_intakeId')->with(['patient:id,agency_id','templateDetails:id,template_name','userDetails:id,first_name,last_name','writeDocumentDetails:id,document_patient_id,type,file_upload,response,docWidth,document_name'])->where('del_flag', '=', 'N');
		if ($type != "") {
			$query = $query->where('type', $type);
		}
		if (!empty($agency_id[0])) {
			$query->whereHas('patient', function($query) use ($agency_id) {
				$query->whereIn('agency_id', $agency_id);
			});
		}
		$query->where('status','!=','');
		return $query->whereBetween('created_date', [$from_date, $to_date])->groupBy('document_sent_report.groupId')->simplePaginate(20);
	}

	public function getTempalteUsageData($from_date, $to_date,$type="",$agency_id = array()){
		$query  = DocumentSentReport::selectRaw('templete_id,main_intakeId')->with(['patient:id,agency_id'])->where('del_flag', '=', 'N');
		if ($from_date != "" && $to_date) {
			$query = $query->whereBetween('created_date', [$from_date, $to_date]);
		}
		if ($type != "") {
			$query = $query->where('type', $type);
		}
		if (!empty($agency_id[0])) {
			$query->whereHas('patient', function($query) use ($agency_id) {
				$query->whereIn('agency_id', $agency_id);
			});
		}
		$query->where('status','!=','');
		return $query->groupBy('document_sent_report.groupId')->get();
	}

	public function getReviewByData($from_date="",$to_date="",$type="",$agency_id = array())
	{
		$query  = DocumentSentReport::select('review_by')->with(['patient:id,agency_id','reviewDetails:id,first_name,last_name'])->where('del_flag', '=', 'N')->where('pdf_status',1);
		$query->whereHas('reviewDetails', function($query) {
			$query->whereNotNull('id');
		});
		if ($from_date != "" && $to_date != "") {
			$query = $query->whereBetween('created_date', [$from_date, $to_date]);
		}
		if($type != ''){
			$query->where('type',$type);
		}
		if (!empty($agency_id[0])) {
			$query->whereHas('patient', function($query) use ($agency_id) {
				$query->whereIn('agency_id', $agency_id);
			});
		}
		$query->where('status','!=','');
		return $query->groupBy('document_sent_report.groupId')->get()->toArray();
	}

	public function getCreatedByData($from_date="",$to_date="",$type="",$agency_id = array())
	{
		$query  = DocumentSentReport::select('created_by')->with(['patient:id,agency_id','userDetails:id,first_name,last_name'])->where('del_flag', '=', 'N');
		$query->whereHas('userDetails', function($query) {
			$query->whereNotNull('id');
		});
		if ($from_date != "" && $to_date != "") {
			$query = $query->whereBetween('created_date', [$from_date, $to_date]);
		}
		if($type != ''){
			$query->where('type',$type);
		}
		if (!empty($agency_id[0])) {
			$query->whereHas('patient', function($query) use ($agency_id) {
				$query->whereIn('agency_id', $agency_id);
			});
		}
		$query->where('status','!=','');
		return $query->groupBy('document_sent_report.groupId')->get()->toArray();
	}

	function getAllGroupIdData($groupId)
	{
		$query = DocumentSentReport::select('document_sent_report.*', 'tm.docWidth', 'tm.response', 'tm.template_name', 'tm.upload_document')
			->join('template_master as tm', function ($join) {
				$join->on('tm.id', '=', 'document_sent_report.templete_id');
			})->where('document_sent_report.groupId', $groupId)->get();
		return $query;
	}

	function getAllDetailsByGroup($id)
	{
		$query = DocumentSentReport::select('document_sent_report.*', 'tm.docWidth', 'tm.response', 'tm.template_name', 'tm.upload_document')
			->join('template_master as tm', function ($join) {
				$join->on('tm.id', '=', 'document_sent_report.templete_id');
			})->where('document_sent_report.groupId', $id)->first();
		return $query;
	}

	function getById($groupId)
	{
		$query = DocumentSentReport::where('groupId', $groupId)->orderBy('id', 'desc')->first();
		return $query;
	}

	public function updateDocumentPatient($documentKey,$attachment)
    {
        $updated = DocumentPatient::where('id', $documentKey)
            ->update([
                'attachment' => $attachment,
				'sign_stamp_status'=> 1,
            ]);

        return $updated;
    }

	public function getAllDetailsByGroupId($groupId)
	{
		return DocumentSentReport::select('id','caregiver_code','status','created_date','created_by','templete_id','sourceFile','pdf_generate','type','main_intakeId','sent_on','completed_on','completion_time','groupId','doctor_id')->with('templateDetails:id,template_name','userDetails:id,first_name,last_name')->where('groupId', $groupId)->get();
		
	}
	
	function getDetailsByIdAllLimited($docId)
	{
		$query = DocumentSentReport::select('id','caregiver_code','subject','status','sender_name','receipt_name','created_date','created_by','templete_id','del_flag','document_submit_status','pdf_generate','type','latitude','longitude','mobileinfo','sourceFile','referral_id','main_intakeId','final_document','sent_on','last_activity','completed_on','completion_time','groupId','approved_date','approved_by','mobile','updated_date','updated_by','caregiver_medical_id','pdf_status','pdf_status_reason','review_date','review_by','is_undo','is_undo_date','doctor_id','sms','email','document_response_id','rejected_date','rejected_by')->where('id', $docId)->first();
		return $query;
	}

	public function getWriteDataByIDWithoutCondition($id)
	{
		return WriteDocument::where('document_patient_id', $id)->first();
	}

	public function getCompletedDocumentSignersExcludingCurrentId($id,$groupId){
		return DocumentSentReport::selectRaw('*,LOWER(sent_on)')->where('id','!=',$id)->where('status','Completed')->where('groupId',$groupId)->get();
	}
}