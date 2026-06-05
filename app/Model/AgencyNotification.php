<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\User;

class AgencyNotification extends Model
{
    use SoftDeletes;
    protected $table = 'agency_notification';
    protected $guarded = ['id'];

    public function users()
	{
		return $this->belongsTo(User::class,'created_by','id');
	}
}