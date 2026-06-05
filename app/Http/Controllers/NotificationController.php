<?php
namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as FacadeRequest;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\User;
use Illuminate\Support\Facades\DB;
use URL;
use Illuminate\Notifications\Notification;
use App\Notifications\MyFirstNotification;
class NotificationController extends BaseController
{
	 public function __construct()
    {
        
    }
	
	public function NotifMarkAsRead(Request $request){
		$notificationid = $request->input('notif_id');
		
		$notification = auth()->user()->notifications()->find($notificationid)->markAsRead();
		if($notification) {
			// $notification->markAsRead();
			echo "success";
		}else{
			echo "fail";
		}
		
	}
	
	function unreadNotificationByUser(){
//		return 1;
		$auth =auth()->user();
		
		$totalcount = $auth->unreadNotifications->count();
		echo $totalcount;
	}


}