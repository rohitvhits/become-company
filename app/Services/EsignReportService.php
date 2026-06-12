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
		$query = DocumentSentReport::with(['templateDetails.documentTypeDetails', 'userDetails:id,first_name,last_name', 'patient:id,first_name,last_name,email,mobile,type,agency_id','patient.agencyDetail:id,agency_name','writeDocumentDetails:id,document_patient_id,type,file_upload,response,docWidth,document_name','reviewDetails:id,first_name,last_name']);

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

		if (!empty($searchQuery['agency_fk'])) {
			$agency_id = $searchQuery['agency_fk'];
			$query->whereHas('patient', function($query) use ($agency_id) {
				$query->whereIn('agency_id', $agency_id);
			});
		}

		if (!empty($searchQuery['type'])) {
			
			$type = $searchQuery['type'];
			$query->whereHas('patient', function($query) use ($type) {
				$query->where('type', $type);
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
		$query = DocumentSentReport::with(['templateDetails.documentTypeDetails', 'userDetails:id,first_name,last_name', 'patient:id,first_name,last_name,email,mobile,type,agency_id','patient.agencyDetail:id,agency_name','writeDocumentDetails:id,document_patient_id,type,file_upload,response,docWidth,document_name','reviewDetails:id,first_name,last_name']);

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

		if (!empty($searchQuery['type'])) {
			
			$type = $searchQuery['type'];
			$query->whereHas('patient', function($query) use ($type) {
				$query->where('type', $type);
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
		return User::selectRaw('id,first_name,last_name')->where('delete_flag', 'N')->whereNull('agency_fk')->whereRaw('CONCAT(first_name," ",last_name) LIKE "%' . $search . '%"')->get();
	}

	function TotalSignerCount($groupId)
	{
		$query = DocumentSentReport::select(DB::raw('COUNT(id) as total'))->where('del_flag', 'N')->whereNotIn('status', ['completed', 'Approved', 'Rejected'])->where('groupId', $groupId)->get();
		return $query;
	}

	public function isCaregiverSignPending($groupId)
	{
		return DocumentSentReport::where('groupId', $groupId)
			->where('del_flag', 'N')
			->whereRaw('LOWER(sent_on) = ?', ['caregiver'])
			->where('status', '!=', 'Completed')
			->exists();
	}

	public function getSignerCounts($groupId)
	{
		$total = DocumentSentReport::where('groupId', $groupId)->where('del_flag', 'N')->count();
		$completed = DocumentSentReport::where('groupId', $groupId)->where('del_flag', 'N')->where('status', 'Completed')->count();
		return ['completed' => $completed, 'total' => $total];
	}

	public function esignTemplateUserList($templete_id)
	{
		$query = DocumentSentReport::with(['templateDetails.documentTypeDetails', 'userDetails:id,first_name,last_name', 'patient:id,first_name,last_name,email,mobile,type,agency_id','patient.agencyDetail:id,agency_name','writeDocumentDetails:id,document_patient_id,type,file_upload,response,docWidth,document_name']);

		return $query->orderBy('id', 'desc')->where('del_flag','N')->where('templete_id',$templete_id)->groupBy('document_sent_report.groupId')->get();
	}

	public function getGroupPendingDocument($groupId, $type = null)
	{
		if ($type == 'single') {
			return DocumentSentReport::where('id', $groupId)->first();
		}
		return DocumentSentReport::where('groupId', $groupId)
			->where('status', '=', 'Pending')
			->where('del_flag', 'N')
			->first();
	}

	public function getDocumentWithRelations($document)
	{
		$document->load(['templateDetails', 'patient']);

		return [
			'templateName' => $document->templateDetails->template_name ?? '',
			'portalId' => $document->main_intakeId ?? '',
			'portalName' => $document->patient
				? trim(($document->patient->first_name ?? '') . ' ' . ($document->patient->last_name ?? ''))
				: '',
		];
	}

	public function dataListNew($searchQuery,$pagination="")
	{
		$query = DocumentSentReport::with([
		'templateDetails.documentTypeDetails',
		'userDetails:id,first_name,last_name',
		'patient:id,first_name,last_name,email,mobile,type,agency_id',
		'patient.agencyDetail:id,agency_name',
		'writeDocumentDetails:id,document_patient_id,type,file_upload,response,docWidth,document_name',
		'reviewDetails:id,first_name,last_name'
	]);

	// Patient Name
	if (!empty($searchQuery['patient_name'])) {
		$query->where('main_intakeId', $searchQuery['patient_name']);
	}

	// Template Name
	if (!empty($searchQuery['template_name'])) {
		$query->whereIn('templete_id', $searchQuery['template_name']);
	}

	// Created Date Range
	if (!empty($searchQuery['created_at'])) {
		$exploderDueDate = explode('-', $searchQuery['created_at']);
		if (count($exploderDueDate) == 2) {
			$startDate = Carbon::parse(trim($exploderDueDate[0]))->format('Y-m-d 00:00:00');
			$endDate   = Carbon::parse(trim($exploderDueDate[1]))->format('Y-m-d 23:59:59');

			$query->whereBetween('created_date', [$startDate, $endDate]);
		}
	}

	// Created By
	if (!empty($searchQuery['created_by'])) {
		$query->where('created_by', $searchQuery['created_by']);
	}

	// Completed Date Range
	if (!empty($searchQuery['completed_on'])) {
		$exploderAppDate = explode('-', $searchQuery['completed_on']);
		if (count($exploderAppDate) == 2) {
			$startDate = Carbon::parse(trim($exploderAppDate[0]))->format('Y-m-d 00:00:00');
			$endDate   = Carbon::parse(trim($exploderAppDate[1]))->format('Y-m-d 23:59:59');

			$query->whereBetween('completed_on', [$startDate, $endDate]);
		}
	}

	// Sender Name
	if (!empty($searchQuery['sender_name'])) {
		$query->where('sender_id', $searchQuery['sender_name']);
	}

	// ✅ STATUS FILTER (FINAL)
	if (!empty($searchQuery['status']) && $searchQuery['status'] != 'all') {

		$status = strtolower($searchQuery['status']);

		if ($status == 'completed') {

			$query->where('status', 'Completed')->whereNull('pdf_status')
				->whereNotExists(function ($q) {
					$q->select(\DB::raw(1))
						->from('document_sent_report as dsr2')
						->whereRaw('dsr2.groupId = document_sent_report.groupId')
						->where('dsr2.status', '!=', 'Completed');
				});

		} elseif ($status == 'approved') {

			$query->where('pdf_status', '1')->where('status', 'Completed');

		} elseif ($status == 'rejected') {

			$query->where('status', 'Completed')
				->where('pdf_status', '0');
		} else{
			$query->where('status', $searchQuery['status']);
		}
	}

	// Agency Filter
	if (!empty($searchQuery['agency_fk'])) {
		$agency_id = $searchQuery['agency_fk'];

		$query->whereHas('patient', function ($q) use ($agency_id) {
			$q->whereIn('agency_id', $agency_id);
		});
	}

	// Patient Type Filter
	if (!empty($searchQuery['type'])) {
		$type = $searchQuery['type'];

		$query->whereHas('patient', function ($q) use ($type) {
			$q->where('type', $type);
		});
	}

	if($searchQuery['template_type'] != ""){
		$template_type = $searchQuery['template_type'];
		$query->whereHas('templateDetails', function($query) use ($template_type) {
			$query->where('template_type', $template_type);
		});
	}
	// Agency Delete Flag Check
	$query->whereHas('patient.agencyDetail', function ($q) {
		$q->where('agency.delete_flag', 'N');
	});

	// Final Result
	if($pagination !=""){
		return $query->where('del_flag', 'N')
				->orderBy('id', 'desc')
				->groupBy('document_sent_report.groupId')
				->get();
	}
	return $query->where('del_flag', 'N')
				->orderBy('id', 'desc')
				->groupBy('document_sent_report.groupId')
				->paginate(50);
	}
}