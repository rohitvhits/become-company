<?php

namespace App\Model;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoginLog extends Model
{
	use SoftDeletes;
    protected $table = "login_log";
    protected $guarded = ["id"];

}
