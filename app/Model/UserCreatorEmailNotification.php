<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use App\User;
class UserCreatorEmailNotification extends Model
{
    public $timestamps = false;
    protected $table = 'user_creator_email_notification';
    protected $fillable = ['id','agency_id','data','del_flag','created_date','created_by','updated_date','updated_by','deleted_date','deleted_by'];
	
	public function createdUserDetails()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function updatedUserDetails()
    {
        return $this->hasOne(User::class, 'id', 'updated_by');
    }
}
