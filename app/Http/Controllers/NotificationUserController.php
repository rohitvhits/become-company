<?php
namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Services\NotificationUserService;
use Cookie;
use Illuminate\Support\Facades\Session;

class NotificationUserController extends BaseController
{
    protected $notificationUserService = "";
	public function __construct(NotificationUserService $notificationUserService)
    {
        $this->notificationUserService = $notificationUserService;
    }
	
    public function getAllUserWiseNotification(Request $request){
        return view('notification_user.notification_list');
	}

    public function notificationAjaxList(Request $request){
   
		$notification['query'] = $this->notificationUserService->getUserWiseNotification();
        return view('notification_user.notification_ajax_list', $notification);
	}

    public function getAllUnreadUserWiseNotification(Request $request){
		$notification['data'] = $this->notificationUserService->getUnreadUserWiseNotification($request->page);        
        $user = auth()->user()->id;    
        if (Cookie::has('showNotification'.$user)) {
            // Expire the cookie by setting a negative expiration time
            Cookie::queue(Cookie::forget('showNotification'.$user));
        }    
        return view('notification_user.unread_notification', $notification);
	}

	public function NotificationMarkAsRead(Request $request){
        $data = array(
            'is_read' => 1
        ); 
        $this->notificationUserService->update($data,['id' =>$request->id]);
        return response()->json(['error_msg' => 'Mark as read successfully.', 'status' => 1, 'data' => array()], 200);
	}
	
	public function getAllUserCountWiseNotification(){
		$totalcount = $this->notificationUserService->unreadNotificationsCount();        
        return response()->json(['error_msg' => '', 'status' => 1, 'data' => count($totalcount)], 200);
	}

    public function countNotificationOfUser(){
        $totalcount = $this->notificationUserService->unreadNotificationsUserCount();           
        return response()->json(['error_msg' => '', 'status' => 1, 'data' => count($totalcount)], 200);
    }

    public function markReadAllNotification(Request $request){
        $getUnreadNotification = $this->notificationUserService->unreadNotificationsUserCount();
        if(count($getUnreadNotification) > 0){
            $data = array(
                'is_read' => 1
            );
            $this->notificationUserService->update($data,['user_id' =>auth()->user()->id]);
        }
        return response()->json(['error_msg' => 'Mark as read successfully.', 'status' => 1, 'data' => array()], 200);
    }
}