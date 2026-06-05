<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class Announcement extends Model
{
	use Notifiable; 
    public $timestamps =false;
	protected $table = 'announcement';
	protected $fillable = ['id','title','description', 'del_flag', 'created_date', 'created_by','updated_date', 'updated_by', 'deleted_date', 'deleted_by'];

}
