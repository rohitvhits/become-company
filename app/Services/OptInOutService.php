<?php
namespace App\Services;
use App\Model\OptInOut;

class OptInOutService{

	public  function save($data){
		$data['created_date'] = date('Y-m-d H:i:s');
		$data['del_flag'] = "N";
		
		$insert = new OptInOut($data);
		$insert_id = $insert->save();
		return $insert_id;
		
	}

}