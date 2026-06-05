<?php

namespace App;

use App\Model\Doctor;
use App\Model\Patient;
use App\Model\WriteDocument;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use App\Template;
use App\User;
use App\DocusignDetail;
class DocumentSentReport extends Model
{

	use Notifiable;

	public $timestamps = false;
	protected $table = 'document_sent_report';
	protected $fillable = ['id', 'caregiver_code', 'subject', 'status', 'sender_id', 'sender_name', 'receipt_name', 'sent_on', 'created_date', 'created_by', 'templete_id', 'pdf_generate', 'document_submit_status', 'del_flag', 'type', 'latitude', 'longitude', 'mobileinfo', 'sourceFile', 'referral_id', 'main_intakeId', 'final_document', 'receipted', 'groupId', 'singstatus', 'approved_date', 'approved_by', 'mobile','agency_form_id','pdf_status','pdf_status_reason','streamlined_action','auto_notified','auto_notified_at','review_date','review_by','is_undo','is_undo_date','doctor_id','Sms','email','document_response_id','vns_id','completed_on'];

	public function templateDetails(){
        return $this->belongsTo(Template::class,'templete_id','id')->select('id','template_name','document_type','upload_document','response')->orderBy('template_name');
    }

    public function userDetails(){
        return $this->hasOne(User::class,"id","created_by");
    }

	public static function getResponseList($id)
	{
		$query = DocumentSentReport::select('users.first_name', 'users.last_name', 'document_sent_report.*', 'template_master.template_name', 'document_type_master.name')
			->leftjoin('template_master', function ($join) {
				$join->on('template_master.id', '=', 'document_sent_report.templete_id');
				$join->where('template_master.del_flag', 'N');
			})
			->leftjoin('document_type_master', function ($join) {
				$join->on('document_type_master.id', '=', 'template_master.document_type');
				$join->where('document_type_master.del_flag', 'N');
			})
			->leftjoin('users', function ($join) {
				$join->on('users.id', '=', 'document_sent_report.created_by');
				$join->where('users.delete_flag', 'N');
			})
			->where('document_sent_report.del_flag', 'N')
			->where('document_sent_report.caregiver_code', $id)
			->simplePaginate(50);
		return $query;
	}
	public static function getResponseListNew($id)
	{
		$query = DocumentSentReport::select('users.first_name', 'users.last_name', 'document_sent_report.*', 'template_master.template_name', 'document_type_master.name')
			->leftjoin('template_master', function ($join) {
				$join->on('template_master.id', '=', 'document_sent_report.templete_id');
				$join->where('template_master.del_flag', 'N');
			})
			->leftjoin('document_type_master', function ($join) {
				$join->on('document_type_master.id', '=', 'template_master.document_type');
				$join->where('document_type_master.del_flag', 'N');
			})
			->leftjoin('users', function ($join) {
				$join->on('users.id', '=', 'document_sent_report.created_by');
				$join->where('users.delete_flag', 'N');
			})
			->where('document_sent_report.del_flag', 'N')
			->where('document_sent_report.main_intakeId', $id)
			->groupBy('document_sent_report.groupId')
			->orderby('document_sent_report.id', 'desc')
			->get();
		return $query;
	}

	public static function getDetails($groupId)
	{
		$query  = DocumentSentReport::select('pdf_generate', 'main_intakeId','templete_id','id','agency_form_id')->where('document_submit_status', '=', 1)->where('groupId', $groupId)->orderBy('id', 'desc')->first();
		return $query;
	}

	public function patient()
    {
        return $this->hasOne(Patient::class, 'id', 'main_intakeId');
    }

	public function writeDocumentDetails()
    {
        return $this->hasOne(WriteDocument::class, 'document_patient_id', 'id');
    }
	public function reviewDetails(){
        return $this->hasOne(User::class,"id","review_by");
    }

	public function doctors()
    {
        return $this->hasOne(Doctor::class, 'id', 'doctor_id');
    }

	public static function getAllDetails($groupId)
	{
		$query  = DocumentSentReport::where('document_submit_status', '=', 1)->where('groupId', $groupId)->orderBy('id', 'desc')->first();
		return $query;
	}

	public function getDocusignDetail()
    {
        return $this->hasOne(DocusignDetail::class, 'document_report_id', 'id')->where('del_flag', 'N');
    }
}
