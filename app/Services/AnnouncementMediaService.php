<?php

namespace App\Services;

use App\Model\AnnouncementMedia;
use Illuminate\Support\Facades\Auth;

class AnnouncementMediaService
{
    /**
     * Save media file
     */
    public function save($data)
    {
        $data['created_date'] = date('Y-m-d H:i:s');
        $data['created_by'] = auth()->user()->id;
        $insert = new AnnouncementMedia($data);
        $insert->save();
        return $insert;
    }

    /**
     * Get media by announcement ID
     */
    public function getByAnnouncementId($announcementId)
    {
        return AnnouncementMedia::where('announcement_id', $announcementId)
            ->where('del_flag', 'N')
            ->orderBy('sort_order', 'asc')
            ->get();
    }

    /**
     * Get media by ID
     */
    public function getById($id)
    {
        return AnnouncementMedia::where('id', $id)
            ->where('del_flag', 'N')
            ->first();
    }

    /**
     * Delete media
     */
    public function delete($id)
    {
        $data = [
            'deleted_date' => date('Y-m-d H:i:s'),
            'deleted_by' => Auth::id(),
            'del_flag' => 'Y',
        ];
        return AnnouncementMedia::where('id', $id)->update($data);
    }

    /**
     * Delete all media for announcement
     */
    public function deleteByAnnouncementId($announcementId)
    {
        $data = [
            'deleted_date' => date('Y-m-d H:i:s'),
            'deleted_by' => Auth::id(),
            'del_flag' => 'Y',
        ];
        return AnnouncementMedia::where('announcement_id', $announcementId)->update($data);
    }
}
