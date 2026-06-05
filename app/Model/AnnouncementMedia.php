<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AnnouncementMedia extends Model
{
    protected $table = 'announcement_media';
    protected $guarded = ['id'];
    public $timestamps = false;

    /**
     * Get the announcement that owns this media
     */
    public function announcement()
    {
        return $this->belongsTo(AnnouncementMaster::class, 'announcement_id', 'id');
    }
}
