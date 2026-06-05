<?php
namespace App\Http\Controllers;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\User;
use Auth;
use App\Helpers\AwsHelper;
use App\Helpers\Utility;
use App\Services\LogsService;
class MyProfileController extends BaseController
{
    
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(Request $request)
    {
        $data['menu'] = "My Profile";
        $data['title'] = "My Profile";
        $data['user'] = auth()->user();
        if(auth()->user()->agency_fk !=""){
			//abort(404);
		}
       
        return view("myProfile.profile", $data);
    }
    
    public function profileUpdate(Request $request){
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'profile_img' => 'image|mimes:jpg,jpeg,png|max:2048'
        ]);

        if ($validator->fails()) {
	
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
		} else {
            
            $name="";
            if ($request->file('profile_img') != '') {
                $stampImage = $request->file('profile_img');
                $name = uniqid() . time() . '.' . $stampImage->getClientOriginalExtension();
                if (env('FILE_UPLOAD_PERMISSION')  == 'development') {
                    $destination = public_path('user-profile');
                    $stampImage->move($destination, $name);
                    
                } else {
                    Storage::disk('s3')->putFileAs('user-profile', $stampImage, $name);
                }
    
            }
            
            // Allowlist: only these fields can be updated via profile
            $updateArray = $request->only(['first_name', 'last_name', 'phone', 'ext']);

            if($name !=""){
                $updateArray['profile_img'] = $name;
            }

            $userId = auth()->user()->id;
            $old_response = User::find($userId);
            $update = User::where('id', $userId)->update($updateArray);

            $ipaddress = Utility::getIP();
            $new_response = User::find($userId);
            $insertLog = [
                'type' => 'Profile Update',
                'link' => url('update-my-profile'),
                'module' => 'User',
                'object_id' => $userId,
                'message' => auth()->user()->first_name . ' ' . auth()->user()->last_name . ' has updated their profile',
                'ip' => $ipaddress,
                'old_response' => serialize($old_response),
                'new_response' => serialize($new_response),
            ];
            LogsService::save($insertLog);

            return response()->json(['status' => true, 'error_msg' => 'Profile successfully updated'], 200);
        }

    }

    public function getUserProfileImage(){
        $getUserImage = User::where('id',auth()->user()->id)->first();
        if( $getUserImage->profile_img !=""){
            return AwsHelper::getImagesFromAWS('user-profile','user-profile',$getUserImage->profile_img);
        }
        
    }
}
