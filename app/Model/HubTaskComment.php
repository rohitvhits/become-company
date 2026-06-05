<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;

class HubTaskComment extends Model
{
    public $timestamps = false;
    protected $table = 'hub_task_comment';
    protected $guarded = ["id"];

    public function userDetails()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
