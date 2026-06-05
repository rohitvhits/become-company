<?php
namespace App\Helpers;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

use App\Model\MedicarePremium;
class MedicarePremiumHelper
{
    public function __construct()
	{}
	
	 
	
    public static  function insert($data)
    {
		$insert_data = $data; 
		$inser_id = new MedicarePremium($insert_data);
		$inser_id->save();
		$Insert = $inser_id->id; 

		return $Insert;
	
		
    }
	 public static  function update($data,$where)
    {	
      $insert = MedicarePremium::where($where)->update($data);
      return $insert;
	
		
    }
    
    public static function getDetailsByRecordId($id){
        $query = MedicarePremium::where('del_flag','N')->where('record_id',$id)->first();
        return $query;
    }

    public static function getDetailsById($id){
      $query = MedicarePremium::where('del_flag','N')->where('id',$id)->first();
      return $query;
    }
	
}