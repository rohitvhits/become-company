<?php

namespace App\Http\Controllers;

use App\Services\AnnouncementMasterService;
use Illuminate\Http\Request;

class AnnouncementUserController extends Controller
{
    protected $announcementMasterService;

    public function __construct(AnnouncementMasterService $announcementMasterService)
    {
        $this->announcementMasterService = $announcementMasterService;
        $this->middleware('auth');
    }

    /**
     * Get unread announcements for popup
     */
    public function getUnreadAnnouncements()
    {
        $announcements = $this->announcementMasterService->getUnreadAnnouncementsForUser();
        $count = $this->announcementMasterService->getUnreadCount();

        return response()->json([
            'status' => true,
            'data' => $announcements,
            'count' => $count
        ]);
    }

    /**
     * Get unread announcements for header dropdown (returns HTML view like notifications)
     */
    public function getUnreadAnnouncementsDropdown(Request $request)
    {
        $data['data'] = $this->announcementMasterService->getUnreadAnnouncementsForUser($request->page ?? '');
        return view('announcement_user.unread_announcement_dropdown', $data);
    }

    /**
     * Get unread count for badge
     */
    public function getUnreadCount()
    {
        $count = $this->announcementMasterService->getUnreadCount();

        return response()->json([
            'status' => true,
            'count' => $count
        ]);
    }

    /**
     * Mark as read (single or multiple)
     */
    public function markAsRead(Request $request)
    {
        // Handle multiple IDs
        if ($request->has('announcement_ids') && is_array($request->announcement_ids)) {
            $result = true;
            foreach ($request->announcement_ids as $id) {
                if (!$this->announcementMasterService->markAsRead($id)) {
                    $result = false;
                }
            }
        } else {
            // Handle single ID
            $announcementId = $request->announcement_id;
            $result = $this->announcementMasterService->markAsRead($announcementId);
        }

        if ($result) {
            return response()->json(['status' => true, 'msg' => 'Marked as read']);
        } else {
            return response()->json(['status' => false, 'msg' => 'Failed to mark as read']);
        }
    }

    /**
     * Announcement list page
     */
    public function announcementList()
    {
        $data['menu'] = "Announcement List";
        $data['user'] = auth()->user();
        return view("announcement_user/announcement_list", $data);
    }

    /**
     * AJAX list for announcement list page
     */
    public function ajaxAnnouncementList()
    {
        $data['query'] = $this->announcementMasterService->getAllAnnouncementsForUser();
        return view("announcement_user/announcement_ajax_list", $data);
    }

    /**
     * Get unshown announcements for popup (shows modal only once)
     */
    public function getUnshownAnnouncements()
    {
        $announcements = $this->announcementMasterService->getUnshownAnnouncementsForUser();

        return response()->json([
            'status' => true,
            'data' => $announcements
        ]);
    }

    /**
     * Mark announcement as shown (modal was displayed to user)
     * Supports both single ID and multiple IDs
     */
    public function markAsShown(Request $request)
    {
        // Handle multiple IDs (mark all as shown at once)
        if ($request->has('announcement_ids') && is_array($request->announcement_ids)) {
            $result = true;
            foreach ($request->announcement_ids as $id) {
                if (!$this->announcementMasterService->markAsShown($id)) {
                    $result = false;
                }
            }
        } else {
            // Handle single ID
            $announcementId = $request->announcement_id;
            $result = $this->announcementMasterService->markAsShown($announcementId);
        }

        if ($result) {
            return response()->json(['status' => true, 'msg' => 'Marked as shown']);
        } else {
            return response()->json(['status' => false, 'msg' => 'Failed to mark as shown']);
        }
    }
}
