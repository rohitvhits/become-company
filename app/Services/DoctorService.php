<?php
namespace App\Services;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Model\Doctor;

class DoctorService{

	public  function save($data){
		$auth = auth()->user();
		$data['created_date'] = date('Y-m-d H:i:s');
		$data['created_by'] = $auth['id'];
		$data['deleted_flag'] = "N";
		
		$insert = new Doctor($data);
		$insert->save();
		
		
		return $insert->id;
		
	}
	public  function update($data,$where){
		$auth = auth()->user();
		$data['updated_at'] = date('Y-m-d H:i:s');
		$data['updated_by'] = $auth['id'];
		
		$update =Doctor::where($where)->update($data); 
		return $update;
	}
	public  function SoftDelete($data,$where){
		$auth = auth()->user();
		$data['deleted_at'] = date('Y-m-d H:i:s');
		$data['deleted_by'] = $auth['id'];
		
		$update =Doctor::where($where)->update($data); 
		return $update;
	}

	public function getData($full_name, $email, $phone, $license, $state, $city, $zipcode, $place_of_examination, $date_of_examination, $is_active = null, $is_signature_stamp_active = null){
		$where = 'deleted_flag ="N"';
		if($full_name !=''){
			$where .=' and full_name LIKE "%'.$full_name.'%"';
		}
		if($email !=''){
			$where.= ' and email  LIKE "%'.$email.'%"' ;
		}
		if($phone !=''){
			$where .=' and phone = "'.$phone.'"';
		}
		if ($license != '') {
			$where .= ' and license = "' . $license . '"';
		}
		if ($state != '') {
			$where .= ' and state = "' . $state . '"';
		}
		if ($city != '') {
			$where .= ' and city = "' . $city . '"';
		}
		if ($zipcode != '') {
			$where .= ' and zipcode = "' . $zipcode . '"';
		}
		if ($place_of_examination != '') {
			$where .= ' and place_of_examination = "' . $place_of_examination . '"';
		}
		if ($date_of_examination != '') {
			$where .= ' and date_of_examination = "' . $date_of_examination . '"';
		}
		if ($is_active !== null && $is_active !== '') {
			$where .= ' and is_active = "' . $is_active . '"';
		}
		if ($is_signature_stamp_active !== null && $is_signature_stamp_active !== '') {
			$where .= ' and is_signature_stamp_active = "' . $is_signature_stamp_active . '"';
		}

		$query = Doctor::whereRaw($where)->orderBy('id','desc')->paginate(10);
		return $query;

	}
	
	public function getDetailById($id){
		$query = Doctor::where('deleted_flag','N')->where('id',$id)->first();
		return $query;
	}
	
	public function getDataExport($full_name, $email, $phone, $license, $address, $state, $city, $zipcode, $place_of_examination, $date_of_examination, $is_active = null, $is_signature_stamp_active = null)
	{
		$where = 'deleted_flag ="N"';
		if ($full_name != '') {
			$where .= ' and full_name LIKE "%' . $full_name . '%"';
		}
		if ($email != '') {
			$where .= ' and email ="' . $email . '"';
		}
		if ($phone != '') {
			$where .= ' and phone = "' . $phone . '"';
		}
		if ($license != '') {
			$where .= ' and license = "' . $license . '"';
		}
		if ($state != '') {
			$where .= ' and state = "' . $state . '"';
		}
		if ($city != '') {
			$where .= ' and city = "' . $city . '"';
		}
		if ($zipcode != '') {
			$where .= ' and zipcode = "' . $zipcode . '"';
		}
		if ($place_of_examination != '') {
			$where .= ' and place_of_examination = "' . $place_of_examination . '"';
		}
		if ($date_of_examination != '') {
			$where .= ' and date_of_examination = "' . $date_of_examination . '"';
		}
		if ($is_active !== null && $is_active !== '') {
			$where .= ' and is_active = "' . $is_active . '"';
		}
		if ($is_signature_stamp_active !== null && $is_signature_stamp_active !== '') {
			$where .= ' and is_signature_stamp_active = "' . $is_signature_stamp_active . '"';
		}

		$query = Doctor::whereRaw($where)->get();
		return $query;
	}
	
	public function getDoctorList(){
		$query = Doctor::where('deleted_flag','N')->where('is_active', 1)->get();
		return $query;

	}
}