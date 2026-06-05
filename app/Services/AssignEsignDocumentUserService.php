<?php
namespace App\Services;
use App\Model\AssignEsignDocumentUser;
use Illuminate\Support\Facades\DB;

class AssignEsignDocumentUserService{

	public static function save($data){
		$auth = auth()->user();

		$data['created_at'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id']??"";
		$insert = new AssignEsignDocumentUser($data);
		$insert->save();
		return $insert->id;
	}

	public static function getDashboardList($userId, $templateName = null, $status = null, $createdDate = null)
	{
		$query = AssignEsignDocumentUser::select(
				'assign_esign_document_user.id',
				'assign_esign_document_user.assign_esign_document_id',
				'assign_esign_document.template_id',
				'assign_esign_document.esign_document_id',
				'template_master.template_name',
				'document_sent_report.status',
				'document_sent_report.created_date',
				'document_sent_report.main_intakeId as patient_id',
				'document_sent_report.id as document_report_id',
				'document_sent_report.completed_on',
				'document_sent_report.approved_date',
				'document_sent_report.groupId',
				'agency.agency_name',
				DB::raw("CONCAT(users.first_name, ' ', users.last_name) as created_by_name"),
				DB::raw("CONCAT(patient_master.first_name, ' ', IFNULL(patient_master.last_name,'')) as patient_name"),
				DB::raw("CONCAT(completed_user.first_name, ' ', completed_user.last_name) as completed_by_name"),
				DB::raw("CONCAT(approved_user.first_name, ' ', approved_user.last_name) as approved_by_name")
			)
			->join('assign_esign_document', 'assign_esign_document.id', '=', 'assign_esign_document_user.assign_esign_document_id')
			->join('document_sent_report', 'document_sent_report.id', '=', 'assign_esign_document.esign_document_id')
			->leftJoin('template_master', 'template_master.id', '=', 'assign_esign_document.template_id')
			->leftJoin('users', 'users.id', '=', 'document_sent_report.created_by')
			->leftJoin('users as completed_user', 'completed_user.id', '=', 'document_sent_report.updated_by')
			->leftJoin('users as approved_user', 'approved_user.id', '=', 'document_sent_report.approved_by')
			->leftJoin('patient_master', 'patient_master.id', '=', 'document_sent_report.main_intakeId')
			->leftJoin('agency', 'agency.id', '=', 'patient_master.agency_id')
			->where('assign_esign_document_user.user_id', $userId)
			->where('document_sent_report.del_flag', 'N');

		if ($templateName) {
			$query->where('template_master.template_name', 'LIKE', '%' . $templateName . '%');
		}

		if ($status) {
			$query->where('document_sent_report.status', $status);
		}

		if ($createdDate) {
			$dates = explode(' - ', $createdDate);
			if (count($dates) == 2) {
				$startDate = date('Y-m-d', strtotime(trim($dates[0])));
				$endDate = date('Y-m-d', strtotime(trim($dates[1])));
				$query->whereRaw('DATE(document_sent_report.created_date) BETWEEN ? AND ?', [$startDate, $endDate]);
			}
		}

		return $query->orderBy('document_sent_report.created_date', 'desc')
			->paginate(20);
	}
}