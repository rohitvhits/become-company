<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;

class AnnouncementMaster extends Model
{
    protected $table = 'announcements_master';
    protected $guarded = ['id'];
    public $timestamps = false;

    /**
     * Get all media for this announcement
     */
    public function media()
    {
        return $this->hasMany(AnnouncementMedia::class, 'announcement_id', 'id')
                    ->where('del_flag', 'N')
                    ->orderBy('sort_order', 'asc');
    }

    /**
     * Get all user statuses for this announcement
     */
    public function userStatuses()
    {
        return $this->hasMany(AnnouncementUserStatus::class, 'announcement_id', 'id')
                    ->where('del_flag', 'N');
    }

    /**
     * Get user status for current authenticated user
     */
    public function userStatus()
    {
        return $this->hasOne(AnnouncementUserStatus::class, 'announcement_id', 'id')
                    ->where('user_id', auth()->id())
                    ->where('del_flag', 'N');
    }

    /**
     * Get the user who created this announcement
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
