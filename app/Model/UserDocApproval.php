<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;

class UserDocApproval extends Model
{
	public $timestamps = false;
	protected $table = 'user_doc_approval';
	protected $fillable = ['id','user_id','type','del_flag','created_date','created_by','updated_date','updated_by','deleted_date','deleted_by','key','name'];

	public function createdBy()
	{
		return $this->belongsTo(User::class, 'created_by', 'id');
	}
}
 