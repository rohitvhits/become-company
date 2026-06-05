<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use App\DocumentType;
use App\Agency;
use App\Model\FormSetup;

class Template extends Model
{
	use Notifiable;

	protected $table = 'template_master';
	public $timestamps = false;
	protected $fillable = ['id', 'template_name', 'document_type', 'remark', 'upload_document', 'del_flag', 'created_date', 'created_by', 'updated_date', 'updated_by', 'lookup_fields', 'esign', 'esign_workflow', 'response', 'email_notification', 'resouce_tab','custom_form_id','agency_id','docWidth','active_status','old_response','show_verify_by','custom_template','send_caregiver_email','template_signer_type','resolution_update','template_type'];

	public function documentTypeDetails(){
        return $this->belongsTo(DocumentType::class,'document_type','id')->select('id','name')->orderBy('name');
    }

	public function getFullNameAttribute() //Accessor
	{
		$user = Auth::user();
		return $user->first_name . " " . $user->last_name;
	}

	public static function GetListing($id, $template_name, $lookup_fields, $status,$agency_fk, $created_date = '', $updated_date = '')
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

		$query = Template::select('template_master.*', 'document_type_master.name')
			->leftjoin('document_type_master', function ($join) {
				$join->on('document_type_master.id', '=', 'template_master.document_type');
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

	static function getListingCaregiverDetailsNew($docs_type, $temples_name, $status_id, $dates_id, $type, $caregiver)
	{
		//echo "<pre>docs_type :=";print_r($docs_type.'<br>temples_name:=');print_r($temples_name.'<br>status_id:=');print_r($status_id.'<br>dates_id:=');print_r($dates_id.'<br>type:=');print_r($type);
		//die();
		$temp = 'document_sent_report.del_flag ="N" and document_sent_report.type="Caregiver"';

		if ($docs_type != '') {
			$temp .= ' and doc.id ="' . $docs_type . '"';
		}
		if ($temples_name != '') {
			$temp .= ' and tmp.id ="' . $temples_name . '"';
		}
		if ($status_id != '') {
			$temp .= ' and document_sent_report.status ="' . $status_id . '"';
		}

		if ($caregiver != '') {
			$temp .= ' and document_sent_report.main_intakeId="' . $caregiver . '"';
		}
		if ($dates_id != '') {
			$temp .= ' and DATE_FORMAT(document_sent_report.created_date,"%Y-%m-%d") ="' . date('Y-m-d', strtotime($dates_id)) . '"';
		}
		$query = DocumentSentReport::select('us.FIRSTNAME as ufname', 'us.LASTNAME as ulname', 'document_sent_report.*', 'doc.name', 'tmp.template_name')
			->leftjoin('template_master as tmp', function ($join) {
				$join->on('tmp.id', '=', 'document_sent_report.templete_id');
			})
			->leftjoin('document_type_master as doc', function ($join) {
				$join->on('doc.id', '=', 'tmp.document_type');
			})
			->leftjoin('users as us', function ($join) {
				$join->on('us.USERID', '=', 'document_sent_report.created_by');
			})
			->whereRaw($temp)
			->groupBy('document_sent_report.groupId')
			->orderBy('document_sent_report.id', 'desc')
			->paginate(10);
		/*$query = DocumentSentReport::select('us.FIRSTNAME as ufname','us.LASTNAME as ulname','document_sent_report.id','document_sent_report.groupId','CaregiverDemographicsArchived.CaregiverID','CaregiverDemographicsArchived.FirstName','CaregiverDemographicsArchived.MiddleName','CaregiverDemographicsArchived.LastName','document_sent_report.caregiver_code','document_sent_report.receipt_name','document_sent_report.created_date','document_sent_report.status','document_sent_report.pdf_generate','doc.name','tmp.template_name','document_sent_report.main_intakeId')
				->leftjoin('template_master as tmp',function($join){
					$join->on('tmp.id','=','document_sent_report.templete_id');
					
				})
				->leftjoin('document_type_master as doc',function($join){
					$join->on('doc.id','=','tmp.document_type');
					
				})
				->leftjoin('CaregiverDemographicsArchived',function($join){
					$join->on('CaregiverDemographicsArchived.CaregiverCode','=','document_sent_report.main_intakeId');
					
				})
				->leftjoin('users as us',function($join){
					$join->on('us.USERID','=','document_sent_report.created_by');
					
				})
				->whereRaw($temp)
				->groupBy('document_sent_report.groupId')
				->orderBy('document_sent_report.id','desc')
				->paginate(10);
*/
		return $query;
	}
	public static function getTemplatesList()
	{
		$query = Template::select('id', 'template_name')->where('del_flag', 'N')->where('lookup_fields', 'nybest')->orderBy('template_name', 'asc')->get();
		return $query;
	}

	public static function getDetailsById($tid)
	{
		$query = Template::where('del_flag', 'N')->where('id', $tid)->first();
		return $query;
		die();
	}

	public function getFormName()
    {
        return $this->hasOne(FormSetup::class, 'id', 'custom_form_id');
    }

	public function agencies()
	{
		return $this->hasOne(Agency::class);
	}
}
