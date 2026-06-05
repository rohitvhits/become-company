<?php
namespace App\Helpers;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Model\HubGenerateAgencyToken;

class HubGenerateAgencyTokenHelper
{
    public function __construct()
	{}
	
	 
	
    public static  function insert($data)
    {
		$insert_data = $data; 
		$inser_id = new HubGenerateAgencyToken($insert_data);
		$inser_id->save();
		$Insert = $inser_id->id; 

		return $Insert;
	
		
    }
	 public static  function update($data,$where)
    {	
		$insert = HubGenerateAgencyToken::where($where)->update($data);
		return $insert;
	
		
    }
	
	public static function getData($id){
		$temp = ' hub_agency_token.delete_flag="N"';
		if($id !=''){
			$temp .=' and hub_agency_token.agency_id ="'.$id.'"';
		}
		$query = HubGenerateAgencyToken::select('hub_agency_token.*','hub_company.agency_name')
				->leftjoin('agency',function($join){
					$join->on('hub_company.id','=','hub_agency_token.agency_id');
					$join->where('hub_agency_token.delete_flag','N');
				})->whereRaw($temp)->orderBy('hub_agency_token.id','desc')->paginate(50);
		return $query;
	
	}
	public static function getDataExport($id){		
			$temp = ' hub_agency_token.delete_flag="N"';		
				if($id !=''){			
				$temp .=' and hub_agency_token.agency_id ="'.$id.'"';		
			}				
			$query = HubGenerateAgencyToken::select('hub_agency_token.*','hub_company.agency_name')				
			->leftjoin('hub_company',function($join){					
				$join->on('hub_company.id','=','hub_agency_token.agency_id');					
				$join->where('hub_agency_token.delete_flag','N');				
			})->whereRaw($temp)->orderBy('hub_agency_token.id','desc')->get();					
			return $query;	
	}		
			
	public static function checkToken($token){				
			$query = HubGenerateAgencyToken::where('token',$token)->where('delete_flag','N')->first();				
			return $query;			
	}

	public static function getDetailsById($id){				
		$query = HubGenerateAgencyToken::where('id',$id)->where('delete_flag','N')->first();				
		return $query;			
	}
	
	public static function checkTokenAccess($token){
		$query = HubGenerateAgencyToken::leftjoin('hub_company', function($join){
			$join->on('hub_company.id','=','hub_agency_token.agency_id');
			$join->where('show_hub','=',1);
		})->where('token',$token)->where('hub_agency_token.delete_flag','N')->first();
		return $query;
	}
}