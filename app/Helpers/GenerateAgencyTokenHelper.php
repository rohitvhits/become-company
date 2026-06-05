<?php
namespace App\Helpers;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\GenerateAgencyToken;


class GenerateAgencyTokenHelper
{
    public function __construct()
	{}
	
	 
	
    public static  function insert($data)
    {
		$insert_data = $data; 
		$inser_id = new GenerateAgencyToken($insert_data);
		$inser_id->save();
		$Insert = $inser_id->id; 

		return $Insert;
	
		
    }
	 public static  function update($data,$where)
    {	
		$insert = GenerateAgencyToken::where($where)->update($data);
		return $insert;
	
		
    }
	
	public static function getData($id){
		$temp = ' agency_token.delete_flag="N"';
		if($id !=''){
			$temp .=' and agency_token.agency_id ="'.$id.'"';
		}
		$query = GenerateAgencyToken::select('agency_token.*','agency.agency_name')
				->leftjoin('agency',function($join){
					$join->on('agency.id','=','agency_token.agency_id');
					$join->where('agency_token.delete_flag','N');
				})->whereRaw($temp)->orderBy('agency_token.id','desc')->paginate(50);
		return $query;
	
	}
	public static function getDataExport($id){		
			$temp = ' agency_token.delete_flag="N"';		
				if($id !=''){			
				$temp .=' and agency_token.agency_id ="'.$id.'"';		
			}				
			$query = GenerateAgencyToken::select('agency_token.*','agency.agency_name')				
			->leftjoin('agency',function($join){					
				$join->on('agency.id','=','agency_token.agency_id');					
				$join->where('agency_token.delete_flag','N');				
			})->whereRaw($temp)->orderBy('agency_token.id','desc')->get();					
			return $query;	
	}		
			
	public static function checkToken($token){				
			$query = GenerateAgencyToken::where('token',$token)->where('delete_flag','N')->first();				
			return $query;			
	}

	public static function getDetailsById($id){				
		$query = GenerateAgencyToken::where('id',$id)->where('delete_flag','N')->first();				
		return $query;			
	}
	
	public static function checkTokenAccess($token){
		$query = GenerateAgencyToken::leftjoin('agency', function($join){
			$join->on('agency.id','=','agency_token.agency_id');
			$join->where('show_hub','=',1);
		})->where('token',$token)->where('agency_token.delete_flag','N')->first();
		return $query;
	}
}