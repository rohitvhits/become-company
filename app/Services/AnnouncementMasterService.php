<?php

namespace App\Services;

use App\Model\AnnouncementMaster;
use App\Model\AnnouncementMedia;
use App\Model\AnnouncementUserStatus;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnnouncementMasterService
{
    /**
     * Get paginated data for admin list
     */
    public function getData()
    {
        $query = AnnouncementMaster::with('media')
            ->where('del_flag', 'N')
            ->orderBy('id', 'desc')
            ->paginate(50);
        return $query;
    }

    /**
     * Save new announcement
     */
    public function save($data)
    {
        $data['created_date'] = date('Y-m-d H:i:s');
        $data['created_by'] = auth()->user()->id;
        $insert = new AnnouncementMaster($data);
        $insert->save();
        return $insert;
    }

    /**
     * Get detail by ID
     */
    public function getDetailById($id)
    {
        $query = AnnouncementMaster::with('media')
            ->where('del_flag', 'N')
            ->where('id', $id)
            ->first();
        return $query;
    }

    /**
     * Update announcement
     */
    public function update($data, $where)
    {
        $data['updated_date'] = date('Y-m-d H:i:s');
        $data['updated_by'] = auth()->user()->id;
        $update = AnnouncementMaster::where($where)->update($data);
        return $update;
    }

    /**
     * Soft delete
     */
    public function delete($id)
    {
        $data = [
            'deleted_date' => date('Y-m-d H:i:s'),
            'deleted_by' => Auth::id(),
            'del_flag' => 'Y',
        ];
        $response = AnnouncementMaster::where('id', $id)->update($data);
        return $response;
    }

    /**
     * Publish announcement - creates user status records for all active users
     * Optimized for large user bases (1M+ users) using chunked processing and batch upserts
     */
    public function publish($announcementId)
    {
        try {
            // Update announcement status first
            $this->update(['is_published' => '1'], ['id' => $announcementId]);

            $createdDate = date('Y-m-d H:i:s');
            $chunkSize = 1000;

            // Process users in chunks to avoid memory exhaustion
            User::select('id')
                ->whereNull('agency_fk')
                ->where('delete_flag', 'N')
                ->chunkById($chunkSize, function ($users) use ($announcementId, $createdDate) {
                    $records = [];

                    foreach ($users as $user) {
                        $records[] = [
                            'announcement_id' => $announcementId,
                            'user_id' => $user->id,
                            'is_read' => '0',
                            'is_shown' => '0',
                            'del_flag' => 'N',
                            'created_date' => $createdDate
                        ];
                    }

                    // Batch upsert - much faster than individual updateOrCreate calls
                    AnnouncementUserStatus::upsert(
                        $records,
                        ['announcement_id', 'user_id'], // Unique keys for matching
                        ['is_read', 'del_flag', 'created_date'] // Columns to update if exists
                    );
                });

            return true;
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Announcement publish failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get unread announcements for current user (for popup)
     */
    public function getUnreadAnnouncementsForUser($page = null)
    {
        $userId = auth()->id();

        $query = AnnouncementMaster::with('media', 'creator')
            ->join('announcement_user_status', 'announcements_master.id', '=', 'announcement_user_status.announcement_id')
            ->where('announcements_master.del_flag', 'N')
            ->where('announcements_master.is_published', '1')
            ->where('announcement_user_status.user_id', $userId)
            ->where('announcement_user_status.is_read', '0')
            ->where('announcement_user_status.del_flag', 'N')
            ->select('announcements_master.*')
            ->orderBy('announcements_master.id', 'desc');

        if ($page !== null) {
            return $query->paginate(10, ['*'], 'page', $page);
        }

        return $query->get();
    }

    /**
     * Get unread count for badge
     */
    public function getUnreadCount()
    {
        $userId = auth()->id();

        return AnnouncementUserStatus::select('id', 'is_read', 'del_flag', 'user_id')->where('user_id', $userId)
            ->where('is_read', '0')
            ->where('del_flag', 'N')
            ->limit(1)
            ->count();
    }

    /**
     * Get all announcements for user (read and unread)
     */
    public function getAllAnnouncementsForUser()
    {
        $userId = auth()->id();

        return AnnouncementMaster::with('media', 'userStatus')
            ->join('announcement_user_status', 'announcements_master.id', '=', 'announcement_user_status.announcement_id')
            ->where('announcements_master.del_flag', 'N')
            ->where('announcements_master.is_published', '1')
            ->where('announcement_user_status.user_id', $userId)
            ->where('announcement_user_status.del_flag', 'N')
            ->select('announcements_master.*', 'announcement_user_status.is_read', 'announcement_user_status.read_at')
            ->orderBy('announcement_user_status.is_read', 'asc')
            ->orderBy('announcements_master.id', 'desc')
            ->paginate(20);
    }

    /**
     * Mark announcement as read
     */
    public function markAsRead($announcementId, $userId = null)
    {
        $userId = $userId ?? auth()->id();

        $data = [
            'is_read' => '1',
            'read_at' => date('Y-m-d H:i:s'),
            'updated_date' => date('Y-m-d H:i:s')
        ];

        return AnnouncementUserStatus::where('announcement_id', $announcementId)
            ->where('user_id', $userId)
            ->update($data);
    }

    /**
     * Mark announcement as shown (modal displayed to user)
     */
    public function markAsShown($announcementId, $userId = null)
    {
        $userId = $userId ?? auth()->id();

        $data = [
            'is_shown' => '1',
            'shown_at' => date('Y-m-d H:i:s'),
            'updated_date' => date('Y-m-d H:i:s')
        ];

        return AnnouncementUserStatus::where('announcement_id', $announcementId)
            ->where('user_id', $userId)
            ->update($data);
    }

    /**
     * Get unshown announcements for current user (for popup - shows only once)
     */
    public function getUnshownAnnouncementsForUser()
    {
        $userId = auth()->id();

        return AnnouncementMaster::with('media', 'creator')
            ->join('announcement_user_status', 'announcements_master.id', '=', 'announcement_user_status.announcement_id')
            ->where('announcements_master.del_flag', 'N')
            ->where('announcements_master.is_published', '1')
            ->where('announcement_user_status.user_id', $userId)
            ->where('announcement_user_status.is_shown', '0')
            ->where('announcement_user_status.del_flag', 'N')
            ->select('announcements_master.*')
            ->orderBy('announcements_master.id', 'desc')
            ->get();
    }
}
