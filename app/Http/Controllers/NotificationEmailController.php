<?php

namespace App\Http\Controllers;

use App\Model\NotificationEmail;
use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationEmailController extends Controller
{
    
    public function index()
    {
        $data['menu'] = "user";

		$data['user'] = $user = auth()->user();
		$data['query'] = NotificationEmail::NotificationList();

        return view('notification_email.index',$data);
    }

    
    public function create()
    {
        $data['menu'] = "user";

		$data['user'] = $user = auth()->user();
        return view('notification_email.create',$data);
    }

    
    public function store(Request $request)
    {
        
        $user = Auth::user();
        $this->validate($request, [
            'title' => 'required',
            'message' => 'required',
        ]);
        
        $insertNotificationEmail = [
            'title' => $request->title,
            'message' => $request->message,
            'created_date' => date('Y-m-d H:i:s'),
            'created_by' => $user->id,
            
        ];
        NotificationEmail::create($insertNotificationEmail);
        
        
        if ($insertNotificationEmail) {
            Session::flash('success', 'Notification Email successfully inserted.');
            return redirect()->route('notification-email.index');
        } else {
            Session::flash('error', 'Sorry, something went wrong. Please try again.');
            return redirect()->route('notification-email.index');
        }
        
        
    }

    public function show($id)
    {
        //
    }

   
    public function edit($id)
    {
        $data['menu'] = "user";

		$data['user'] = $user = auth()->user();
        
        $data['notificationEmailData'] = NotificationEmail::NotificationDataById($id);
        
        return view('notification_email.edit',$data);
    }

    
    public function update(Request $request ,$id)
    {
        
        $user = Auth::user();
        $this->validate($request, [
            'title' => 'required',
            'message' => 'required',
        ]);

        $updateNotificationEmail = NotificationEmail::NotificationDataById($id);
        
        $updateNotificationEmailData = [
            'title' => $request->title,
            'message' => $request->message,
            'updated_date' => date('Y-m-d H:i:s'),
            'updated_by' => $user->id,
            
        ];
        $updateNotificationEmail->update($updateNotificationEmailData);
        return redirect()->route('notification-email.index')->with('success', 'Notification Email successfully updated ');;
    }

    
    public function destroy($id)
    {
        $user = Auth::user();
        $role = NotificationEmail::where('id', $id)->update(['del_flag' => 'Y','deleted_by' => $user->id]);
        $role = NotificationEmail::where('id', $id)->delete();
        
        return response()->json(['status' => true, 'msg' => 'Notification Email successfully deleted ']);
    }
}
