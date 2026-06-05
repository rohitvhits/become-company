<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use App\Model\Announcement;
use App\User;

class AnnouncementUser extends Model
{
	use Notifiable; 
    public $timestamps =false;
	protected $table = 'announcement_user';
	protected $fillable = ['id','announcement_id','user_id','mark_as_read', 'del_flag', 'created_date', 'created_by','updated_date', 'updated_by', 'deleted_date', 'deleted_by'];


	public function announcementDetail(){
        return $this->hasOne(Announcement::class,"id","announcement_id");
    }

	public function userDetails(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
