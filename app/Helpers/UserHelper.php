<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\User;
use Illuminate\Support\Facades\Hash;
class UserHelper
{
	public function __construct()
	{
	}
	
	public static  function insert($data)
	{
		$insert_data = $data;
		$auth = auth()->user();
		$insert_data['created_at'] = date('Y-m-d H:i:s');
		$insert_data['created_by'] = $auth['id'];
		$inser_id = new User($insert_data);
		$inser_id->save();
		$Insert = $inser_id->id;

		return $Insert;
	}
	public static  function update($data, $where)
	{
		$insert = User::where($where)->update($data);
		return $insert;
	}

	public static function getAgencyWiseUserList($agency_id)
	{


		$temp = 'users.delete_flag = "N"';

		$query = User::selectRaw('users.*,mt.name as login_type_fk,mtu.name as user_type_fk,age.agency_name')

			->leftjoin('master_table as mt', function ($join) {

				$join->on('mt.id', '=', 'users.login_type_fk');
			})
			->leftjoin('master_table as mtu', function ($join) {

				$join->on('mtu.id', '=', 'users.user_type_fk');
			})
			->leftjoin('agency as age', function ($join) {

				$join->on('age.id', '=', 'users.agency_fk');
			})

			->whereRaw('users.delete_flag="N" and users.agency_fk="' . $agency_id . '"')->orderBy('users.id', 'desc')->paginate(1000);

		return $query;
	}

	public static function getAgencyDetails($id)
	{

		$query = User::select('users.id', 'users.first_name', 'users.last_name')
			->leftjoin('record', function ($join) {
				$join->on('users.id', '=', 'record.agency_rep');
				$join->where('record.delete_flag', 'N');
			})
			->where('record.id', $id)->where('users.delete_flag', 'N')->first();
		return $query;
	}
	public static function getCommonLastLoginDate($date)
	{
		$query = User::where('delete_flag', 'N')->whereDate('last_login_at', '<=', $date)->get();
		return $query;
	}
	public static function getCommonLastLoginDateActive($date)
	{
		$query = User::where('delete_flag', 'N')->whereDate('last_login_at', '<=', $date)->where('active', 'active')->get();
		return $query;
	}
	public static function checkUserBlockOrNotByEmail($email)
	{
		$query = User::where('delete_flag', 'N')->where('email', $email)->first();
		return $query;
	}

	public static function getAgencyWiseUserExport($agency_id)
	{


		$temp = 'users.delete_flag = "N"';

		$query = User::selectRaw('users.*,mt.name as login_type_fk,mtu.name as user_type_fk,age.agency_name')

			->leftjoin('master_table as mt', function ($join) {

				$join->on('mt.id', '=', 'users.login_type_fk');
			})
			->leftjoin('master_table as mtu', function ($join) {

				$join->on('mtu.id', '=', 'users.user_type_fk');
			})
			->leftjoin('agency as age', function ($join) {

				$join->on('age.id', '=', 'users.agency_fk');
			})

			->whereRaw('users.delete_flag="N" and users.agency_fk="' . $agency_id . '"')->orderBy('users.id', 'desc')->get();

		return $query;
	}

	public static function searchNybestUser($search)
	{
		return User::selectRaw('id,first_name,last_name')->where('delete_flag','N')->whereNull('agency_fk')->whereRaw('CONCAT(first_name," ",last_name) LIKE "%'.$search.'%"')->get();
	}

	public static function getUserDetails($id){
		return User::where('id',$id)->first();
	}

	public static function searchAllUsers($search)
	{
		return User::selectRaw('id,first_name,last_name,agency_fk')->where('delete_flag','N')->whereIn('login_type_fk',[183])->where('id','!=',Auth()->user()->id)->whereRaw('LCASE(CONCAT(first_name,last_name)) LIKE "%'.str_replace(' ','',strtolower($search)).'%"')->whereNull('agency_fk')->get();
	}

	public static function getDetailsByUserids($ids)
	{
		return User::selectRaw('id,first_name,last_name,agency_fk,email')->where('delete_flag','N')->whereIn('id',$ids)->get();
	}

	public static function searchUsers($search)
	{
		return User::selectRaw('id,first_name,last_name,agency_fk')->where('delete_flag','N')->whereRaw('LCASE(CONCAT(first_name,last_name)) LIKE "%'.str_replace(' ','',strtolower($search)).'%"')->get();
	}
}
