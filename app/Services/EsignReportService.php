<?php

namespace App\Services;

use App\DocumentSentReport;
use App\Model\Patient;
use App\Agency;
use App\Template;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EsignReportService
{

	public function dataList($searchQuery)
	{
		$query = DocumentSentReport::with(['templateDetails.documentTypeDetails', 'userDetails:id,first_name,last_name', 'patient:id,first_name,last_name,email,mobile,type,agency_id','patient.agencyDetail:id,agency_name','writeDocumentDetails:id,document_patient_id,type,file_upload,response,docWidth,document_name']);

		if (!empty($searchQuery['patient_name'])) {
			$query = $query->where('main_intakeId', $searchQuery['patient_name']);
		}
		if (!empty($searchQuery['template_name'])) {
			$query = $query->whereIn('templete_id', $searchQuery['template_name']);
		}
		if (!empty($searchQuery['created_at'])) {
			$exploderDueDate = explode('-', $searchQuery['created_at']);
			if (count($exploderDueDate) == 2) {
				$startDate = Carbon::parse(trim($exploderDueDate[0]))->format('Y-m-d 00:00:00');
				$endDate = Carbon::parse(trim($exploderDueDate[1]))->format('Y-m-d 23:59:59');
				$query = $query->whereBetween('created_date', [$startDate, $endDate]);
			}
		}

		if (!empty($searchQuery['created_by'])) {
			$query = $query->where('created_by', $searchQuery['created_by']);
		}

		if (!empty($searchQuery['completed_on'])) {
			$exploderAppDate = explode('-', $searchQuery['completed_on']);
			if (count($exploderAppDate) == 2) {
				$startDate = Carbon::parse(trim($exploderAppDate[0]))->format('Y-m-d 00:00:00');
				$endDate = Carbon::parse(trim($exploderAppDate[1]))->format('Y-m-d 23:59:59');
				$query = $query->whereBetween('completed_on', [$startDate, $endDate]);
			}
		}

		if (!empty($searchQuery['sender_name'])) {
			$query = $query->where('sender_id', $searchQuery['sender_name']);
		}
		if (!empty($searchQuery['status']) && $searchQuery['status'] != 'all') {
			// $query = $query->where('status', $searchQuery['status']);
			if(strtolower($searchQuery['status']) == 'approved'){
				$query = $query->where('pdf_status','1');
			}else if(strtolower($searchQuery['status']) == 'rejected'){
				$query = $query->where('status','Completed')->where('pdf_status','0');
			}else{
				$query = $query->where('status', $searchQuery['status']);
			}
		}

		if($searchQuery['type'] != ''){
			$query->where('type',$searchQuery['type']);
		}
		if($searchQuery['location_id'] != ""){
			$location_id = $searchQuery['location_id'];
			$query->whereHas('patient', function($query) use ($location_id) {
				$query->where('location_id', $location_id);
			});
		}

		if (!empty($searchQuery['agency_fk'])) {
			$agency_id = $searchQuery['agency_fk'];
			$query->whereHas('patient', function($query) use ($agency_id) {
				$query->whereIn('agency_id', $agency_id);
			});
		}
		$query->whereHas('patient.agencyDetail', function ($q) {
			$q->where('agency.delete_flag', 'N');
		});
		return $query->orderBy('id', 'desc')->where('del_flag','N')->groupBy('document_sent_report.groupId')->paginate(50);
	}

	public function getAllTemplateList()
	{
		return Template::select('id', 'template_name')->orderBy('template_name', 'asc')->get();
	}

	public function getDataExport($searchQuery)
	{
		$query = DocumentSentReport::with(['templateDetails.documentTypeDetails', 'userDetails:id,first_name,last_name', 'patient:id,first_name,last_name,email,mobile,type,agency_id','patient.agencyDetail:id,agency_name','writeDocumentDetails:id,document_patient_id,type,file_upload,response,docWidth,document_name']);

		if (!empty($searchQuery['patient_name'])) {
			$query = $query->where('main_intakeId', $searchQuery['patient_name']);
		}
		if (!empty($searchQuery['template_name'])) {
			$query = $query->whereIn('templete_id', $searchQuery['template_name']);
		}
		if (!empty($searchQuery['created_at'])) {
			$exploderDueDate = explode('-', $searchQuery['created_at']);
			if (count($exploderDueDate) == 2) {
				$startDate = Carbon::parse(trim($exploderDueDate[0]))->format('Y-m-d 00:00:00');
				$endDate = Carbon::parse(trim($exploderDueDate[1]))->format('Y-m-d 23:59:59');
				$query = $query->whereBetween('created_date', [$startDate, $endDate]);
			}
		}

		if (!empty($searchQuery['created_by'])) {
			$query = $query->where('created_by', $searchQuery['created_by']);
		}

		if (!empty($searchQuery['completed_on'])) {
			$exploderAppDate = explode('-', $searchQuery['completed_on']);
			if (count($exploderAppDate) == 2) {
				$startDate = Carbon::parse(trim($exploderAppDate[0]))->format('Y-m-d 00:00:00');
				$endDate = Carbon::parse(trim($exploderAppDate[1]))->format('Y-m-d 23:59:59');
				$query = $query->whereBetween('completed_on', [$startDate, $endDate]);
			}
		}

		if (!empty($searchQuery['sender_name'])) {
			$query = $query->where('sender_id', $searchQuery['sender_name']);
		}

		if (!empty($searchQuery['status']) && $searchQuery['status'] != 'all') {
			//$query = $query->where('status', $searchQuery['status']);

			if(strtolower($searchQuery['status']) == 'approved'){
				$query = $query->where('pdf_status','1');
			}else if(strtolower($searchQuery['status']) == 'rejected'){
				$query = $query->where('status','Completed')->where('pdf_status','0');
			}else{
				$query = $query->where('status', $searchQuery['status']);
			}
		}
		if (!empty($searchQuery['agency_fk'])) {
			$agency_id = $searchQuery['agency_fk'];
			$query->whereHas('patient', function($query) use ($agency_id) {
				$query->whereIn('agency_id', $agency_id);
			});
		}
		return $query->orderBy('id', 'desc')->where('del_flag','N')->groupBy('document_sent_report.groupId')->get();
	}

	public function searchNybestPatient($search)
	{
		return Patient::selectRaw('id,first_name,last_name')->where('deleted_flag', 'N')->whereRaw('CONCAT(first_name," ",last_name) LIKE "%' . $search . '%"')->get();
	}

	public function searchNybestAllUser($search)
	{
		return User::selectRaw('id,first_name,last_name')->where('delete_flag', 'N')->whereRaw('CONCAT(first_name," ",last_name) LIKE "%' . $search . '%"')->get();
	}

	function TotalSignerCount($groupId)
	{
		$query = DocumentSentReport::select(DB::raw('COUNT(id) as total'))->where('del_flag', 'N')->whereNotIn('status', ['completed', 'Approved', 'Rejected'])->where('groupId', $groupId)->get();
		return $query;
	}

	public function esignTemplateUserList($templete_id)
	{
		$query = DocumentSentReport::with(['templateDetails.documentTypeDetails', 'userDetails:id,first_name,last_name', 'patient:id,first_name,last_name,email,mobile,type,agency_id','patient.agencyDetail:id,agency_name','writeDocumentDetails:id,document_patient_id,type,file_upload,response,docWidth,document_name']);

		return $query->orderBy('id', 'desc')->where('del_flag','N')->where('templete_id',$templete_id)->groupBy('document_sent_report.groupId')->get();
	}
}