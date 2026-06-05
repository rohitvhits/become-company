<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\User;

class AnnouncementUserStatus extends Model
{
    protected $table = 'announcement_user_status';
    protected $guarded = ['id'];
    public $timestamps = false;

    /**
     * Get the announcement that owns this status
     */
    public function announcement()
    {
        return $this->belongsTo(AnnouncementMaster::class, 'announcement_id', 'id');
    }

    /**
     * Get the user that owns this status
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
