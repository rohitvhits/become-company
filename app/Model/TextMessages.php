<?php

namespace App\Model;
use App\User;
use Illuminate\Database\Eloquent\Model;
class TextMessages extends Model
{
    public $timestamps = false;
    protected $table = 'text_messages';
    protected $fillable = ['id','case_id','mobile','message_type','message','message_file','del_flag','created_date','created_by','deleted_date','deleted_by','phone'];
	
	
    public function userDetails()
	{
		return $this->hasOne(User::class,'id','created_by');
	}
}
