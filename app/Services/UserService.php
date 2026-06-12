<?php
namespace App\Services;

use App\User;
use App\Helpers\Utility;
use Illuminate\Support\Facades\DB;
class UserService{
    public function getUserList($search=[]){
        $query =  User::select('id','first_name','last_name','email','ext','phone','department')->whereNull('agency_fk')->where('delete_flag','N');
        if(isset($search['full_name']) && $search['full_name'] !=""){
            $search['full_name'] = addcslashes($search['full_name'], '%_\\\'"');
            $query->whereRaw('CONCAT(users.first_name," ",users.last_name)  LIKE "%'.$search['full_name'].'%" ') ;
        }
        if(isset($search['email']) && $search['email'] !=""){
            $query->where('email',$search['email']);
        }
        if(isset($search['phone']) && $search['phone'] !=""){
            $query->where('phone',$search['phone']);
        }
        if(isset($search['department']) && $search['department'] !=""){
            $query->where('department',$search['department']);
        }
        if(isset($search['ext']) && $search['ext'] !=""){
            $query->where('ext',$search['ext']);
        }
        $query->where('show_in_directory',1);
        $query = $query->orderBy('users.first_name','asc')->paginate(50);
        return $query;

    }

    public function getAgencyUserList(){

        $query =  User::whereNotNull('agency_fk')->where('delete_flag','N');

        $query = $query->pluck('id');
        return $query;
    }

    public function getDataAgencyReport($first_name,$last_name,$email,$agency_id,$recordAccess,$paginate=""){
        $agencyids = Utility::getUserWiseAgency();
        $temp = 'users.delete_flag = "N"';
        if(!empty($agencyids)){
			 $temp.= ' and users.agency_fk  In "'.implode(',',$agencyids).'"';
		}
        if($first_name!=''){
            $temp.= ' and users.first_name  LIKE "%'.$first_name.'%"' ;
        } if($last_name!=''){
                $temp.= ' and users.last_name  LIKE "%'.$last_name.'%"' ;
        }if($email!=''){
                $temp.= ' and users.email  LIKE "%'.$email.'%"' ;
        }if($recordAccess !="" && strtolower($recordAccess) != 'all'){
            $temp.= ' and users.record_access  = "'.$recordAccess.'"' ;
        }if($agency_id !=''){
            $temp.= ' and users.agency_fk  = "'.$agency_id.'"' ;
        }

        $query = User::selectRaw('users.*,mt.name as login_type_fk,mtu.name as user_type_fk,age.agency_name')

        ->leftjoin('master_table as mt', function($join) {

                $join->on('mt.id', '=', 'users.login_type_fk');

            })
        ->leftjoin('master_table as mtu', function($join) {

                $join->on('mtu.id', '=', 'users.user_type_fk');

            })
        ->join('agency as age', function($join) {

                $join->on('age.id', '=', 'users.agency_fk');

        })

        ->whereRaw($temp)->where('age.delete_flag','N')->whereNotNull('users.agency_fk')->orderBy('users.id','desc');
        if($paginate !=""){
            $query = $query->get();
        }else{
            $query = $query->paginate(50);
        }
		return $query;

    }

    public function getAllUserList(){
     
       return User::select('id', 'first_name', 'last_name', 'email', 'ext', 'phone')->where('delete_flag','N')->whereNull('agency_fk')->where('active','active')->orderBy('id', 'desc')
                ->get();

    }

    public function getAgencyUserListById($user_id){
        return User::whereNotNull('agency_fk')->where('delete_flag','N')->where('id',$user_id)->pluck('id');
    }

    public function getUserDetails($ids){
		$query = User::select('id','first_name','last_name','email')->where('delete_flag','N')->whereIn('id',$ids);
		return $query->get();
	}

    public function getUsersByIds($ids){
        return User::select('id', 'first_name', 'last_name')->where('delete_flag','N')->where('active','active')->whereIn('id',$ids)->get();

     }

    public function getAllNyUserList(){
       return User::select('id',DB::raw('CONCAT_WS("",first_name," ",last_name) as name'))
            ->where('delete_flag', 'N')
            ->whereNull('agency_fk')
            ->where('active', 'active')
            ->orderBy('id', 'desc')
            ->get();
    }

    public function checkDuplicateEmail($email,$id){
        $query =  User::where('email',$email)->orderBy('id', 'desc');
        if($id !=""){
            $query->where('id','!=',$id);
        }
        $query = $query->get();
        return $query;
     }

     public static function getDetailsById($id){
		return User::withTrashed()->where('id',$id)->first();
	}

    public function fetchAgencyUserListByAgencyId($search){
        $agencyIds = explode(',',$search['agency_id']);
        return User::where('delete_flag', 'N')->whereIn('agency_fk',$agencyIds)->whereRaw('CONCAT(users.first_name," ",users.last_name)  LIKE "%'.$search['q'].'%" ')->get() ;
    }

    public function getAllUserListWithAgency(){
        $query = User::select('id','first_name','last_name')->whereNotNull('agency_fk')->get();
        $final = [];
        if(!empty($query[0])){
            foreach($query as $val){
                
                $final[$val->id] = $val->first_name.' '.$val->last_name;
              
            }
        }

        return $final;
    }

    public function getAllUserUsingPluck(){
       return User::where('delete_flag', 'N')
        ->selectRaw('CONCAT(first_name, " ", last_name) as full_name1, id')
        ->pluck('full_name1', 'id');
    }

    public function getNurseList()
    {
        return User::select('id', 'first_name', 'last_name')
            ->where('is_nurse', 1)
            ->where('delete_flag', 'N')
            ->orderBy('first_name')
            ->get();
    }
    public function update($data,$where){
        return User::where($where)->update($data);
    }

    public function getUserDetailsById($userId){
        return User::where('id',$userId)->where('delete_flag', 'N')->first();
    }
}