<?php
namespace App\Helpers;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\SiteSetting;
class SiteSettingHelper
{
    public function __construct()
	{}
	
	 
	public static  function insert($data)
    {
		$insert_data = $data; 
		$inser_id = new SiteSetting($insert_data);
		$inser_id->save();
		$Insert = $inser_id->id; 

		return $Insert;
	
		
    }
	 public static  function update($data,$where)
    {	
		$insert = SiteSetting::where($where)->update($data);
		return $insert;
	
		
    }
	
}