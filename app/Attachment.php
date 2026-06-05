<?php



namespace App;
use Illuminate\Notifications\Notifiable;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;
class Attachment extends Model

{
    use Notifiable;

  protected $table = 'attachment';
  protected $fillable = ['id','document_type', 'file_name','file_path','del_flag','del_flag','created_at','created_by','updated_at','updated_by','deleted_by','deleted_at','record_id','is_share','required_status'];


  public static function getDataAttachment(){

		 $query = DB::table("attachment")
		            ->selectRaw("attachment.*,master_table.name as document_type")
		            ->leftjoin("master_table","master_table.id", "=", "attachment.document_type")
		           ->where('attachment.del_flag','=','N')
		           ->paginate(5);
				return $query;
	}
	public static function getDataAttachmentIDS($id){
		$currentUser = auth()->user();
		$temp = '';
		if( in_array($currentUser['user_type_fk'], array(5,6)) ){
			$agencyArray = array(0,$currentUser['agency_fk']);
			$agencyArray = implode("," ,$agencyArray);
			$temp =' ((attachment.is_share =1 OR master_table.user_id IN("'.$agencyArray.'")))';
			
		}
		
		$query = Attachment::selectRaw("attachment.*,master_table.name as document_type,users.first_name,users.last_name")
		            ->leftjoin("master_table","master_table.id", "=", "attachment.document_type")
					->leftjoin("users","users.id", "=", "attachment.created_by")
		           ->where('attachment.del_flag','=','N');
					if( $temp  !=''){
						$query->whereRaw($temp);
					}
				   $query->where('attachment.record_id',$id);
				  
				  $mysql = $query->get();

				return $mysql;
	}
	public static function getDataAttachmentById($id){
		 $query=Attachment::where('del_flag','=','N')
		           ->where('id','=',$id)
		           ->first();
				return $query;
	}
	public static function getDatabyRecordId($id,$agency_fk){				
		$query = Attachment::selectRaw("attachment.*,master_table.name as document_type,users.first_name,users.last_name")		            				
			->leftjoin("master_table","master_table.id", "=", "attachment.document_type")									
			->leftjoin("users","users.id", "=", "attachment.created_by")		           				
			->where('attachment.del_flag','=','N')				
			->whereRaw('(attachment.is_share =1 OR master_table.user_id ="'.$agency_fk.'" )')				   				
			->where('attachment.record_id',$id);				  				
			$mysql = $query->paginate(5);								
			return $mysql;					
	}


	public static function getMissingUploadDocument($id){
		$query = Attachment::where('record_id',$id)->where('del_flag','N')->get();
		return $query;
		
	}
}

