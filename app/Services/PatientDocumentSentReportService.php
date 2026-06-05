<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\PatientDocumentSentReport;

class PatientDocumentSentReportService
{

	public static function save($data)
	{
		$auth = auth()->user();
		$data['created_date'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		$data['del_flag'] = "N";

		$insert = new PatientDocumentSentReport($data);
		$insert->save();
		$insert_id = $insert->id;


		return $insert_id;
	}
	public  function update($data, $where)
	{
		$auth = auth()->user();
		$data['updated_date'] = date('Y-m-d H:i:s');
		$data['updated_by'] = $auth['id'];

		$update = PatientDocumentSentReport::where($where)->update($data);
		return $update;
	}
	public static function updateNew($data, $where)
	{
		$auth = auth()->user();


		$update = PatientDocumentSentReport::where($where)->update($data);
		return $update;
	}
	public  function SoftDelete($data, $where)
	{
		$auth = auth()->user();
		$data['deleted_at'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];

		$update = PatientDocumentSentReport::where($where)->update($data);
		return $update;
	}
	public static function getResponseListNew($id)
	{
		$query = PatientDocumentSentReport::select('users.first_name', 'users.last_name', 'patient_document_sent_report.*', 'template_master.template_name', 'document_type_master.name')
			->leftjoin('template_master', function ($join) {
				$join->on('template_master.id', '=', 'patient_document_sent_report.templete_id');
				$join->where('template_master.del_flag', 'N');
			})
			->leftjoin('document_type_master', function ($join) {
				$join->on('document_type_master.id', '=', 'template_master.document_type');
				$join->where('document_type_master.del_flag', 'N');
			})
			->leftjoin('users', function ($join) {
				$join->on('users.id', '=', 'patient_document_sent_report.created_by');
				$join->where('users.delete_flag', 'N');
			})
			->where('patient_document_sent_report.del_flag', 'N')
			->where('patient_document_sent_report.main_intakeId', $id)
			->groupBy('patient_document_sent_report.groupId')
			->orderby('patient_document_sent_report.id', 'desc')
			->paginate(10);
		return $query;
	}

	public static function getPdfGenerate($id)
	{
		$query = PatientDocumentSentReport::select('pdf_generate', 'caregiver_code')->where('document_submit_status', '=', 1)->where('groupId', $id)->orderBy('id', 'desc')->first();
		return $query;
	}


	public static function pendingDocumentCount($groupid)
	{
		$query = PatientDocumentSentReport::where('document_submit_status', '=', 0)->where('groupId', $groupid)->count();
		return $query;
	}

	public static function getDocumentLsy($groupid)
	{
		$query = PatientDocumentSentReport::where('groupId', $groupid)->get();
		return $query;
	}
	public static function getDos($id)
	{
		$query = PatientDocumentSentReport::select('pdf_generate')->where('groupId', $id)->where('del_flag', 'N')->where('status', '!=', '')->first();
		return $query;
	}
	public static function getDetailsById($id)
	{
		$query = PatientDocumentSentReport::where('id', $id)->where('del_flag', 'N')->first();
		return $query;
	}
	public static function pendingDocument($id)
	{
		$query =
			PatientDocumentSentReport::select('sent_on')->where('groupId', $id)->where('status', 'Pending')->first();
		return $query;
	}

	public static function getCaregiverCodeByIdWithPending($documentId)
	{
		$query =
			PatientDocumentSentReport::select('caregiver_code')->where('id', $documentId)->where('status', 'Pending')->where('document_submit_status', 0)->first();
		return $query;
	}

	public static function nextEsign($tid, $groupId)
	{
		$query =
			PatientDocumentSentReport::select('id', 'sender_name', 'sent_on', 'mobile')->where('templete_id', $tid)->where('groupId', $groupId)->where('del_flag', 'N')->where('status', '=', '')->first();
		return $query;
	}
}
