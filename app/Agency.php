<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use App\Model\AgencySkill;
use App\Helpers\Utility;
use App\Model\Patient;
use App\Model\PatientServiceRequest;

class Agency extends Model
{
	use Notifiable;

	protected $table = 'agency';
	protected $fillable = ['id','county','other_email', 'agency_name', 'address1', 'address2','email', 'phone', 'state','city','zip_code','billing_email','bill_date','monthly_bill','active', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'deleted_by', 'delete_flag','paid_amount','due_amount','invoice_ninja_id','service_expert_medicaid','service_md_appointment','app_name','app_key','app_token','notification_email','nybest_email_notification','notes_email_notification','is_sms','robort_status','robort_user_name','robort_user_password','robort_grant_type','alayacare_url','agency_id','client_name','show_hub','view_payment_report','enable_file_manager','show_reporting_tool','ai_call_logs_enabled','enable_review'];

	protected const START_TIME =' 00:00:00';
	protected const END_TIME =' 23:59:59';
    public function agencySkillDetails()
    {
        return $this->hasMany(AgencySkill::class, 'agency_id', 'id')->where('del_flag', 'N');
    }

    public static function getData($agency_name, $email, $phone, $city, $isSMS)
    {
        $temp = 'delete_flag = "N" ';
        if ($agency_name != '') {
            $temp .= ' and agency_name LIKE "%' . $agency_name . '%"';
        }
        if ($email != '') {
            $temp .= ' and email  LIKE "%' . $email . '%"';
        }
        if ($phone != '') {
            $temp .= ' and phone  LIKE "%' . $phone . '%"';
        }
        if ($city != '') {
            $temp .= ' and city  LIKE "%' . $city . '%"';
        }
        if ($isSMS != '') {
            $temp .= ' and is_sms ="' . $isSMS . '"';
        }

        return Agency::whereRaw($temp)->orderBy('id', 'DESC')->paginate(10);
    }

    public static function nyBestAgencyList($agency_name, $email, $phone, $city)
    {
        $temp = 'delete_flag = "N" and service_md_appointment="1" ';
        if ($agency_name != '') {
            $temp .= ' and agency_name LIKE "%' . $agency_name . '%"';
        }
        if ($email != '') {
            $temp .= ' and email  LIKE "%' . $email . '%"';
        }
        if ($phone != '') {
            $temp .= ' and phone  LIKE "%' . $phone . '%"';
        }
        if ($city != '') {
            $temp .= ' and city  LIKE "%' . $city . '%"';
        }

        return Agency::whereRaw($temp)->orderBy('agency_name', 'asc')->paginate(50);
    }

    public static function getAllAgencies()
    {
        return Agency::where('delete_flag', 'N')->orderBy('agency_name', 'asc')->get(['id', 'agency_name']);
    }

    public static function getMyAgency()
    {
        $currentUser = auth()->user();
        $query = Agency::where('delete_flag', 'N');

        if (!in_array($currentUser->user_type_fk, array("184",'4'))) {
          $agencyids = Utility::getUserWiseAgency();

        if(auth()->user()->agency_fk !=""){
            $agencyids[] = auth()->user()->agency_fk;
        } 
        if(!empty($agencyids)){
            $query->whereIn('id',$agencyids);
        }   
        //$query = $query->where("id", $currentUser->agency_fk);
        }
        $query = $query->orderBy('agency_name', 'asc')->get();
        return $query;
    }

    public static function getDataExport($agency_name, $email, $phone, $city, $isSMS)
    {
        $temp = 'delete_flag = "N" ';
        if ($agency_name != '') {
            $temp .= ' and agency_name LIKE "%' . $agency_name . '%"';
        }
        if ($email != '') {
            $temp .= ' and email  LIKE "%' . $email . '%"';
        }
        if ($phone != '') {
            $temp .= ' and phone  LIKE "%' . $phone . '%"';
        }
        if ($city != '') {
            $temp .= ' and city  LIKE "%' . $city . '%"';
        }
        if ($isSMS != '') {
            $temp .= ' and is_sms ="' . $isSMS . '"';
        }

        return Agency::whereRaw($temp)->orderBy('agency_name', 'asc')->get();
    }

    public static function getAllAgencyList()
    {
        return Agency::where('delete_flag','N')->orderBy('agency_name','asc')->get();
    }

    public static function getHHAAgencyList(){
        $agencyids = Utility::getUserWiseAgency();
        $query = Agency::where('delete_flag','N')->where('enable_hha','1');
        if(!empty($agencyids)){
            $query->whereIn('id',$agencyids);
        }
        $query = $query->orderBy('agency_name', 'asc')->get();
        return $query;
    }

    public static function getDetailsByAgencyId($id){
        return Agency::where('delete_flag','N')->where('id',$id)->first();
    }
    
    public static function getAllDetailsbyAgencyId($id){
        return self::getDetailsByAgencyId($id);
    }

    public static function getIdById($id)
    {
        return Agency::whereRaw('id = "' . $id . '"')->first();
    }
    
	public static function getPatientServiceCount($locationId,$agencyId,$typeId,$from_date="",$to_date=""){
		$query = Agency::withCount(['patientCaregiver'=>function($q) use($locationId,$typeId,$from_date,$to_date){
			if($locationId != ''){
				$q->where('location_id','=',$locationId);
			}
			if($typeId != ''){
				$q->where('type',$typeId);
			}
			if(!empty($from_date) && !empty($to_date)){
				$q->whereBetween('created_date', [$from_date.' '.self::START_TIME, $to_date.' '.self::END_TIME]);
			}
		}])->withCount(['patientTotalPatient'=>function($q) use($locationId,$typeId,$from_date,$to_date){
			if($locationId != ''){
				$q->where('location_id','=',$locationId);
			}
			if($typeId != ''){
				$q->where('type',$typeId);
			}
			if(!empty($from_date) && !empty($to_date)){
				$q->whereBetween('created_date', [$from_date.' '.self::START_TIME, $to_date.' '.self::END_TIME]);
			}
		}]);
		if($agencyId !=""){
			$explode = explode(',',$agencyId);
			$query->whereIn('id',$explode);
		}
		$query = $query->get();
		return $query;
	}

    public static function getAgencyData()
    {
        return Agency::where('alaycare_status','1')->get();
    }

    public static function getAgencyList(){
        $agencyids = Utility::getUserWiseAgency();

        if(auth()->user()->agency_fk !=""){
            $agencyids[] = auth()->user()->agency_fk;
        }
        $query = Agency::where('delete_flag', 'N');
        if(!empty($agencyids)){
            $query->whereIn('id',$agencyids);
        }
        $query = $query->orderBy('agency_name', 'asc')->get();
        return $query;
    }

    public static function getAgencyList2(){
        $agencyids = Utility::getUserWiseAgency();

        $query = Agency::where('delete_flag', 'N');
        if(!empty($agencyids)){
            $query->whereIn('id',$agencyids);
        }
        $query = $query->orderBy('agency_name', 'asc')->get();
        return $query;
    }

    public static function getAgencyListAlayaCare(){
        $agencyids = Utility::getUserWiseAgency();

        $query = Agency::where('delete_flag', 'N')->where('alaycare_status', 1);
        if(!empty($agencyids)){
            $query->whereIn('id',$agencyids);
        }
        $query = $query->orderBy('agency_name', 'asc')->get();
        return $query;
    }

    public static function getAllAgencyListWithoutAnyCondition(){
        return Agency::select('id','agency_name')->orderBy('agency_name','asc')->get();
    }

    public static function totalCountForAgencies(){
        return Agency::where('delete_flag', 'N')->count();
    }

    public function patientCaregiver(){
        return $this->hasMany(Patient::class,'agency_id','id')->where('type','Caregiver')->where('deleted_flag','N');
    }

    public function patientTotalPatient(){
        return $this->hasMany(Patient::class,'agency_id','id')->where('type','Patient')->where('deleted_flag','N');
    }

    public static function getAllAgencyIds($agencyId){
        $query = Agency::where('delete_flag', 'N');
        if($agencyId !=""){
            $explode = explode(',',$agencyId);
            $query->whereIn('id',$explode);
        }
        return $query = $query->pluck('id');
    }

    public static function totalCountForAgenciesDateWise($from_date,$to_date){
        $query = Agency::where('delete_flag', 'N');
        if(!empty($from_date) && !empty($to_date)){
            $query->whereBetween('created_at', [$from_date.' 00:00:00', $to_date.' 23:59:59']);
        }
        return $query->count();
    }
    
    public static function getAgencyListWithIds($ids){
        $query = Agency::where('delete_flag', 'N');
        if(!empty($ids)){
            $query->whereIn('id',$ids);
        }
        $query = $query->orderBy('agency_name', 'asc')->get();
        return $query;
    }

    public static function getHHAAgencyListById($ids){
        $agencyids = Utility::getUserWiseAgency();
        $agencyids[] = $ids;
    
        $query = Agency::where('delete_flag','N')->where('enable_hha','1');
        if(!empty($agencyids)){
            $query->whereIn('id',$agencyids);
        }
        
        $query = $query->orderBy('agency_name', 'asc')->get();
        return $query;
    }

	public static function getAgencyListWise(){
		$data['user'] = $user = auth()->user();
		$permissions = [];
		foreach ($user->roles as $role) {
			$permissions[] = $role->name;
		}
		if(!in_array('Super Admin',$permissions)){
			$agencyids = Utility::getUserWiseAgency();
			if(auth()->user()->agency_fk !=""){
				$agencyids[] = auth()->user()->agency_fk;
			}
		}
		$query = Agency::where('delete_flag', 'N');
		if(!empty($agencyids)){
			$query->whereIn('id',$agencyids);
		}
		$query = $query->orderBy('agency_name', 'asc')->get();
		return $query;
	}

    /*******************Third party related */
    public static function getAgencyListByAgencyToken(){
        return Agency::select('agency.id','agency.agency_name')
        ->join('agency_token',function($join){
            $join->on('agency_token.agency_id','=','agency.id');
        })->where('agency.delete_flag', 'N')->where('agency_token.delete_flag', 'N')->groupBy('agency.id')->orderBy('agency.agency_name', 'asc')->get();
    }

    public static function getAgencyListWithUserAgency(){
        $user = auth()->user();
    
        $agencyids = Utility::getUserWiseAgency();
        if($user->agency_fk !=""){
            $agencyids[] = $user->agency_fk;
        }

        $query = Agency::where('delete_flag', 'N');
        if(!empty($agencyids)){
            $query->whereIn('id',$agencyids);
        }
        $query = $query->orderBy('agency_name', 'asc')->get();
        return $query;
    }

    public static function getAgencyListHub(){
        $agencyids = Utility::getUserWiseAgency();

        if(auth()->user()->agency_fk !=""){
            $agencyids[] = auth()->user()->agency_fk;
        }
        $query = Agency::where('delete_flag', 'N');
        if(!empty($agencyids)){
            $query->whereIn('id',$agencyids);
        }
        $query = $query->where('show_hub',1)->orderBy('agency_name', 'asc')->get();
        return $query;
    }

	public static function agencyList($ids=""){
		$query = Agency::where('delete_flag','N');
		if(!empty($ids)){
			$query->whereIn('id',$ids);
		}
		$query =  $query->orderBy('agency_name','asc')->get();
		return $query;
	}

	public static function getRemoteFocusAgencies(){
		return Agency::where('delete_flag', 'N')->where('robort_status',1)->orderBy('agency_name', 'asc')->get();
	}

	public static function getHHAMDOAgencyList(){
		$agencyids = Utility::getUserWiseAgency();
		$query = Agency::select('agency.id','agency.agency_name')
		->join('hha_mdo_client',function($join){
			$join->on('hha_mdo_client.agency_id','=','agency.id');
		})
		->where('agency.delete_flag','N')->where('hha_mdo_client.is_status',1);
		if(!empty($agencyids)){
			$query->whereIn('agency.id',$agencyids);
		}
		$query = $query->orderBy('agency.agency_name', 'asc')->get();
		return $query;
	}

    public static function getStatusOfFileManager($id){
        $agency = Agency::where('id', $id)->where('delete_flag', 'N')->first();
        if(isset($agency) && !empty($agency->id) && $agency->enable_file_manager == 1){
            return '1';
        }
        return $agency->enable_file_manager;
    }

    public static function getStatusOfTaskHealth($id){
        $status = '0';
        $getAgencyExist = Agency::leftjoin('agency_task_health',function($join){
				$join->on('agency.id','=','agency_task_health.agency_id');
		})->select('agency.id','agency_task_health.status')->where('agency.id',$id)->where('agency.delete_flag', 'N')->first();
        if(isset($getAgencyExist) && !empty($getAgencyExist->id) && $getAgencyExist->status == 1){
            $status = '1';
        }
        return $status;
    }
}
