<?php

namespace App\Model;
use App\User;
use Illuminate\Database\Eloquent\Model;
class HubRecordtextMessage extends Model
{
    public $timestamps = false;
    protected $table = 'hub_text_messages';
    protected $fillable = ['id','hub_record_id','mobile','message_type','message','message_file','del_flag','created_date','created_by','deleted_date','deleted_by','phone','hub_agency_id','hub_record_agency_id'];
	
	
    public function userDetails()
	{
		return $this->hasOne(User::class,'id','created_by');
	}
}
