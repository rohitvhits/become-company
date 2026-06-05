<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class DocumentSendSmsLog extends Model
{

    use Notifiable;

    public $timestamps = false;
    protected $table = 'document_send_sms_log';
    protected $fillable = ['id', 'document_id', 'caregiver_id','email', 'created_date', 'created_by', 'updated_date', 'updated_by', 'deleted_date','deleted_by','del_flag','mobile','message'];

    public function userDetail(){
        return $this->hasOne(User::class,"id","created_by");
    }
}
