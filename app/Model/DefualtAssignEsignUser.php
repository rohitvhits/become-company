<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class DefualtAssignEsignUser extends Model
{
	use SoftDeletes;
	protected $table = 'defualt_assign_esign_user';
	protected $guarded = ['id'];

}
