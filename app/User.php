<?php
namespace App;

use App\Model\LoginLog;
use Illuminate\Notifications\Notifiable;

use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Support\Facades\DB;

use Spatie\Permission\Traits\HasRoles;
use App\Model\UserWiseAgency;
use App\Model\NurseLanguage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Role;
class User extends Authenticatable
{

    use Notifiable, HasRoles,SoftDeletes;


    /**

     * The attributes that are mass assignable.

     *

     * @var array

     */

    protected $fillable = [

        'first_name','last_name','user_type_fk','login_type_fk', 'email', 'phone','type','password','remember_token','created_by','created_at','updated_by','updated_at','deleted_by','deleted_at','agency_fk','referenceId','referenceBy','last_login_ip','last_login_at','limit_access','hospital_flag','exmedc_flag','send_invitation','ext','login_attemps','record_access','department','role_access', 'show_in_directory','is_nurse','is_mdo','is_telehealth','show_hub','two_factor_auth','creator_email_noti_toggle','old_agency_id','send_email_btn_show',

    ];



    /**

     * The attributes that should be hidden for arrays.

     *

     * @var array

     */

    protected $hidden = [

        'password', 'remember_token',

    ];

    protected $appends = ['full_name'];


    public function getFullNameAttribute()
    {
        return ucwords("{$this->first_name} {$this->last_name}");
    }


	public static function getData($first_name,$last_name,$email,$login_type,$user_type,$agency,$recordAccess,$roles){

        $temp = 'users.delete_flag = "N"';
        if($first_name!=''){
            $temp.= ' and users.first_name  LIKE "%'.$first_name.'%"' ;
        } if($last_name!=''){
                $temp.= ' and users.last_name  LIKE "%'.$last_name.'%"' ;
        }if($email!=''){
                $temp.= ' and users.email  LIKE "%'.$email.'%"' ;
        }if($login_type!=''){
                $temp.= ' and users.login_type_fk  ="'.$login_type.'"' ;
        }if($user_type!=''){
                $temp.= ' and users.user_type_fk  = "'.$user_type.'"' ;
        }if($agency!=''){
                $temp.= ' and users.agency_fk  = "'.$agency.'"' ;
        }
        if($recordAccess !=""){
            if($recordAccess !="All"){
                $temp.= ' and users.record_access  = "'.$recordAccess.'"' ;
            }
            
        }


       
            $query = User::selectRaw('users.*,users.login_type_fk as login_type_fkm,users.user_type_fk as user_type_fkm,mt.name as login_type_fk,mtu.name as user_type_fk,age.agency_name')

            ->leftjoin('master_table as mt', function($join) {

                    $join->on('mt.id', '=', 'users.login_type_fk');

                })
            ->leftjoin('master_table as mtu', function($join) {

                    $join->on('mtu.id', '=', 'users.user_type_fk');

                })
            ->leftjoin('agency as age', function($join) {

                    $join->on('age.id', '=', 'users.agency_fk');

                })

            ->whereRaw($temp);
            if ($roles) {
                $query->role($roles); // Spatie helper
            }
            $query = $query->where('users.user_type_fk',184)->where('login_type_fk',183)->orderBy('users.id','desc')->paginate(50);

		return $query;

    }
    public static function getDataByAgency($agency_fk,$first_name,$last_name,$email){

         $temp = 'users.delete_flag = "N" and users.agency_fk = "'.$agency_fk.'" and (users.user_type_fk=5 or  users.user_type_fk=6)' ;
        if($first_name!=''){
            $temp.= ' and users.first_name  LIKE "%'.$first_name.'%"' ;
        } if($last_name!=''){
                $temp.= ' and users.last_name  LIKE "%'.$last_name.'%"' ;
        }if($email!=''){
                $temp.= ' and users.email  LIKE "%'.$email.'%"' ;
        }
         $query = User::selectRaw("users.*,users.login_type_fk as login_type_fkm,users.user_type_fk as user_type_fkm,mt.name as login_type_fk,mtu.name as user_type_fk,age.agency_name")
                  ->leftjoin('master_table as mt', function($join) {

                    $join->on('mt.id', '=', 'users.login_type_fk');

                })
            ->leftjoin('master_table as mtu', function($join) {

                    $join->on('mtu.id', '=', 'users.user_type_fk');

                })
            ->leftjoin('agency as age', function($join) {

                    $join->on('age.id', '=', 'users.agency_fk');

                })
                ->whereRaw($temp)
                ->where('users.user_type_fk',184)->where('login_type_fk',183)
                ->orderBy('users.id','desc')
               ->paginate(50);
        return $query;

    }
    public static function getDataById($id){

        $query= User::with('nurseLanguages.languages:id,name')->SelectRaw("users.*,mt.name as login_type_fk,mtu.name as user_type_fk,age.agency_name,lan.name")
                ->leftjoin("master_table as mt","mt.id", "=", "users.login_type_fk")
                ->leftjoin("master_table as mtu","mtu.id", "=", "users.user_type_fk")
                ->leftjoin("agency as age","age.id", "=", "users.agency_fk")
                ->leftjoin("nurse_language as nlang","nlang.nurse_id", "=", "users.id")
                ->leftjoin("language as lan","lan.id", "=", "nlang.language_id")
                ->where("users.delete_flag","N")
                ->where("users.id",$id)
                ->orderBy("users.id","desc")
                ->first();



        return $query;

    }
    public static function getEMCUsers(){

		$query = User::select('id','name')

        ->where('delete_flag','N')
        ->whereIn('type',array('0','1'))

		->orderBy('name','desc')

		->get();

		return $query;
    }

    public static function getDataExport($first_name,$last_name,$email,$record_access,$role_name){

        $temp = 'users.delete_flag = "N"';
        if($first_name!=''){
            $temp.= ' and users.first_name  LIKE "%'.$first_name.'%"' ;
        } if($last_name!=''){
                $temp.= ' and users.last_name  LIKE "%'.$last_name.'%"' ;
        }if($email!=''){
                $temp.= ' and users.email  LIKE "%'.$email.'%"' ;
        }
        if($record_access !=""){
            $temp.= ' and users.record_access  LIKE "%'.$record_access.'%"' ;
        }

            $query = User::selectRaw('users.*,mt.name as login_type_fk,mtu.name as user_type_fk,age.agency_name')

            ->leftjoin('master_table as mt', function($join) {

                    $join->on('mt.id', '=', 'users.login_type_fk');

                })
            ->leftjoin('master_table as mtu', function($join) {

                    $join->on('mtu.id', '=', 'users.user_type_fk');

                })
            ->leftjoin('agency as age', function($join) {

                    $join->on('age.id', '=', 'users.agency_fk');

                })

            ->whereRaw($temp);
            if ($role_name) {
                $query->role($role_name); // Spatie helper
            }
            $query= $query->where('users.user_type_fk',184)->where('login_type_fk',183)->orderBy('users.id','desc')->get();

        return $query;

    }
    public static function getDataByAgencyExport($agency_fk,$first_name,$last_name,$email){

         $temp = 'users.delete_flag = "N" and users.agency_fk = "'.$agency_fk.'" and (users.user_type_fk=5 or  users.user_type_fk=6)' ;
        if($first_name!=''){
            $temp.= ' and users.first_name  LIKE "%'.$first_name.'%"' ;
        } if($last_name!=''){
                $temp.= ' and users.last_name  LIKE "%'.$last_name.'%"' ;
        }if($email!=''){
                $temp.= ' and users.email  LIKE "%'.$email.'%"' ;
        }
         $query = User::selectRaw("users.*,mt.name as login_type_fk,mtu.name as user_type_fk,age.agency_name")
                  ->leftjoin('master_table as mt', function($join) {

                    $join->on('mt.id', '=', 'users.login_type_fk');

                })
            ->leftjoin('master_table as mtu', function($join) {

                    $join->on('mtu.id', '=', 'users.user_type_fk');

                })
            ->leftjoin('agency as age', function($join) {

                    $join->on('age.id', '=', 'users.agency_fk');

                })
                ->whereRaw($temp)
                ->orderBy('users.id','desc')
               ->get();
        return $query;

    }
	public static function checkReferenceID($id){
		$query =  User::where('referenceId',$id)->where('delete_flag','N')->first();
		return $query;
	
	}
	
	 public function routeNotificationForMail($notification)
    {
        // Return email address only...
		
		
        return $this->email;

        // Return name and email address...
        return [$this->email => $this->first_name];
    }

	public static function getAllUser(){
		$query = User::select('id','first_name','last_name')->where('delete_flag','N')->get();
		return $query;
		
	}
	public static function searchUserTemplateModule($search)
    {
		$query = User::select('id',DB::raw('CONCAT(first_name," ",last_name) as name'))->where('delete_flag','N')->whereNull('agency_fk')->whereIn('user_type_fk', array(184))->whereRaw('CONCAT(first_name,last_name) LIKE "%'.$search.'%"')->get();
		return $query;
	}
	
	public static function searchEMCUserTemplateModule($search){
		
		$query = User::select('id',DB::raw('CONCAT(first_name," ",last_name) as name'),'user_type_fk')->where('delete_flag','N')->whereIn('user_type_fk', array(3,4))->whereRaw('CONCAT(first_name,last_name) LIKE "%'.$search.'%" ')->get();
		echo $query;die();
		return $query;
	}
	
	public static function getDetailsById($id){
		$query = User::withTrashed()->where('id',$id)->first();
		return $query;
	}
	public static function getUserDetailsByEmail($email){
		
		$query = User::where('email',$email)->where('delete_flag','N')->first();
		return $query;
	}
	
	public static function getSearchUsers(){
		$auth  = auth()->user();
		$query = User::select('id',DB::raw('CONCAT_WS("",first_name," ",last_name) as name'),'user_type_fk')->where('delete_flag','N');
		if(in_array($auth->user_type_fk,array(5,6))){
			$query->where('agency_fk', $auth->agency_fk);
		}
		
		$mysql = $query->orderBy('name','asc')->get();
		return $mysql;
				
	}
	
	public static function getSearchUsersNew(){
		
		
		$auth  = auth()->user();
		
		$query = User::select('id',DB::raw('CONCAT_WS("",first_name," ",last_name) as name'),'user_type_fk')->where('delete_flag','N');
		if(in_array($auth->user_type_fk,array(5,6))){
			$query->where('agency_fk', $auth->agency_fk);
		}else if($auth->login_type_fk ==183){
			
			$query->where('user_type_fk', 184);
		}
		
		$mysql = $query->orderBy('name','asc')->get();
		return $mysql;
				
	}
	public static function GetConcateData($employee_id){
	
		$query = User::selectRaw('GROUP_CONCAT(CONCAT(first_name," ",last_name)) as employee_name')->where('delete_flag','N')->whereRAW('id IN("'.$employee_id.'")')->first();
		
		return $query;
	}
	public static function getEmcUserData(){
		
		$query = User::select('id',DB::raw('CONCAT(first_name," ",last_name) as name'))->where('delete_flag','N')->whereIn('user_type_fk', array(184,4))->get();
		
		return $query;
	}
	public static function getNYBestUserData(){
		
		$query = User::select('id',DB::raw('CONCAT(first_name," ",last_name) as name'))->where('delete_flag','N')->whereIn('user_type_fk', array(184))->orderBy('first_name','asc')->get();
		
		return $query;
	}
	public static function getHospitalUser(){
        $query =  User::select('id',DB::raw('CONCAT(first_name," ",last_name) as name'))->where('delete_flag','N')->whereIn('user_type_fk', array(184))->orderBy('name','ASC')->get();
		
		return $query;
    }
    public static function getAllUserList(){
        $query = User::select('id','agency_fk',DB::raw('CONCAT_WS(" ",first_name," ",last_name) as name'))->where('delete_flag','N')->get();
        return $query;
    }
    public static function getIDByEmail($email)
    {
        $query = User::select('id', 'email')->where('email', $email)->where('delete_flag', 'N')->first();
        return $query;
    }

    public static function getNyBestUsersList(){

		return  User::select('id','first_name','last_name')
        ->where('login_type_fk',183)
        ->where('user_type_fk',184)
        ->where('delete_flag','N')
        // ->where('id','!=',auth()->user()->id)
		->orderBy('first_name','asc')
		->get();
    }

    public function agencyDetails(){
        return $this->belongsTo(Agency::class,'agency_fk','id');
    }

    public function userWiseAgencyDetails(){
        return $this->hasOne(UserWiseAgency::class,'user_id','id');
    }

    public static function onlyNybestUsers(){
        $query = User::select('id','agency_fk',DB::raw('CONCAT_WS(" ",first_name," ",last_name) as name'))->where('delete_flag','N')->get();
        return $query;
    }

    public static function getUserDataAgency($search,$agency_fk){
        $auth = auth()->user();
        $temp = 'users.delete_flag = "N" and (users.user_type_fk=6) and users.id !="'.$auth->id.'"' ;
        
        if($search['phone'] !=''){
                $temp.= ' and users.phone  LIKE "%'.$search['phone'].'%"' ;
        }if($search['email'] != ''){
                $temp.= ' and users.email  LIKE "%'.$search['email'].'%"' ;
        }
        if($search['status'] != ""){
            $temp.= ' and users.active LIKE "%'.$search['status'].'%"' ;
        }
        if($search['created_by'] != ""){
            $temp.= ' and users.created_by = "'.$search['created_by'].'"' ;
        }
        if($search['full_name'] !=''){
            $temp.= ' and CONCAT(users.first_name," ",users.last_name)  LIKE "%'.$search['full_name'].'%" ' ;
        } 
        if(!empty($search['start_date']) && !empty($search['end_date'])){
			$temp .=' and DATE_FORMAT(users.created_at,"%Y-%m-%d") >= "' . date('Y-m-d', strtotime($search['start_date'])) . '" and DATE_FORMAT(users.created_at,"%Y-%m-%d") <= "' . date('Y-m-d', strtotime($search['end_date'])) . '"';
		}
        $query = User::selectRaw("users.*,users.login_type_fk as login_type_fkm,users.user_type_fk as user_type_fkm,mt.name as login_type_fk,mtu.name as user_type_fk,age.agency_name,userinfo.first_name as created_by_fname,userinfo.last_name as created_by_lname")
                 ->leftjoin('master_table as mt', function($join) {

                   $join->on('mt.id', '=', 'users.login_type_fk');

               })
           ->leftjoin('master_table as mtu', function($join) {

                   $join->on('mtu.id', '=', 'users.user_type_fk');

               })
           ->leftjoin('agency as age', function($join) {

                   $join->on('age.id', '=', 'users.agency_fk');

               })
            ->leftjoin('users as userinfo', function($join) {
                $join->on('users.created_by', '=', 'userinfo.id');
            })
               ->whereRaw($temp)
               ->whereIn('users.agency_fk',$agency_fk)
               ->orderBy('users.id','desc');
       return $query->get();
   }

   public static function getNurses(){
        $query = User::with('nurseLanguages.languages:id,name')->select('users.id','users.agency_fk',DB::raw('CONCAT_WS(" ",users.first_name," ",users.last_name) as name'))
            ->where('users.delete_flag','N')
            ->where('users.is_nurse',1)
            ->orderBy('name', 'asc')
            ->get();
        return $query;
    }

    public function nurseLanguages()
    {
        return $this->hasMany(NurseLanguage::class, 'nurse_id', 'id');
    }

    public static function getDetailsWithTrashById($id){
		$query = User::withTrashed()->where('id',$id)->first();
		return $query;
	}

    /******use for API V2 */
    public static function getDetailsByIdV2($id){
		return User::where('id',$id)->first();
	}
}