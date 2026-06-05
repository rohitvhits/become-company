<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class DocumentMaster extends Model
{
    use Notifiable;

    public $timestamps = false;
    protected $table = 'document_master';
    protected $fillable = ['id', 'file_name', 'image', 'created_date', 'del_flag','created_by','updated_date','updated_by','deleled_date','deleted_by'];
   
	public static function getDocumentList(){
		$query = DocumentMaster::select('document_master.*','users.FIRSTNAME','users.LASTNAME')
								->leftjoin('users',function($join){
									$join->on('users.USERID','=','document_master.created_by');
								})
								->where('document_master.del_flag','N')->orderBy('id','desc')->simplePaginate(50);
		return $query;
	}
}
