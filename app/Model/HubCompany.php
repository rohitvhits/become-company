<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use	App\Model\AgencySkill;
use App\Helpers\Utility;
use App\Model\Patient;

class HubCompany extends Model
{
	use Notifiable; 

	protected $table = 'hub_company';
	protected $fillable = ['id','county','other_email', 'agency_name', 'address1', 'address2','email', 'phone', 'state','city','zip_code','billing_email','bill_date','monthly_bill','active', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'deleted_by', 'delete_flag','paid_amount','due_amount','invoice_ninja_id','service_expert_medicaid','service_md_appointment','app_name','app_key','app_token','notification_email','nybest_email_notification','notes_email_notification','is_sms','robort_status','robort_user_name','robort_user_password','robort_grant_type','alayacare_url','agency_id','client_name','show_hub'];
	
	public static function getAgencyListHub(){
		// $agencyids = Utility::getUserWiseAgency();

		$query = HubCompany::where('delete_flag', 'N');
		$query = $query->where('show_hub',1)->orderBy('agency_name', 'asc')->get();
		return $query;
		// if(auth()->user()->agency_fk !=""){
		// 	$agencyids[] = auth()->user()->agency_fk;
		// }
		// 
		// if(!empty($agencyids)){
		// 	$query->whereIn('id',$agencyids);
		// }
	}

	public static function getDetailsByAgencyId($id){
		$query = HubCompany::where('delete_flag','N')->where('id',$id)->first();
		return $query;
	}

	// Relationship with hub records through hub_record_agency
	public function hubRecords()
	{
		return $this->hasManyThrough(
			HubRecord::class,
			HubRecordAgency::class,
			'agency_id', // Foreign key on hub_record_agency table
			'id', // Foreign key on hub_record table
			'id', // Local key on hub_company table
			'hub_record_id' // Local key on hub_record_agency table
		)->where('hub_record.deleted_flag', 'N')
		 ->where('hub_record_agency.del_flag', 'N');
	}

	// Direct relationship with hub_record_agency
	public function hubRecordAgencies()
	{
		return $this->hasMany(HubRecordAgency::class, 'agency_id', 'id')
					->where('del_flag', 'N');
	}
}