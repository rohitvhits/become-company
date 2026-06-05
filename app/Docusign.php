<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use DB;
class Docusign extends Model
{
    use Notifiable; 

    protected $table = 'docusign_detail';
    public $timestamps = false;
    protected $fillable = ['id','template_id','user_id', 'data', 'del_flag', 'created_date'];
    
    public static function GetListing($id){
        $temp = ' docusign_detail.del_flag="N"';
      
        $query = Docusign::select('docusign_detail.*','template_master.template_name',DB::raw("CONCAT(CaregiverDemographicsArchived.FirstName,CaregiverDemographicsArchived.LastName) as caregiverName"),'document_type_master.name')
                ->leftjoin('template_master',function($join){
                    $join->on('docusign_detail.template_id','=','template_master.id');
					$join->where('template_master.del_flag','N');
                })
				->leftjoin('document_type_master',function($join){
                    $join->on('document_type_master.id','=','template_master.document_type');
                })
				->leftjoin('CaregiverDemographicsArchived',function($join){
                    $join->on('CaregiverDemographicsArchived.CaregiverCode','=','docusign_detail.user_id');
                })
                ->whereRaw($temp)
                ->paginate(10);
                return $query;
    }
    
public static function ViewDocusignGetById($id){
	$query = Docusign::select('docusign_detail.*','template_master.upload_document')->leftjoin('template_master',function($join){
                    $join->on('docusign_detail.template_id','=','template_master.id');
					$join->where('template_master.del_flag','N');
                })->where('docusign_detail.id',$id)->where('docusign_detail.del_flag','N')->first();
	return $query;
}
	
	

}
