<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\User;
use App\Helpers\Utility;
use App\UserIpAddress;
use App\Model\AgencyWiseIpBlocker;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */
    use SendsPasswordResetEmails;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }


    public function showLinkRequestForm()
    {
        $host = request()->getHost();
        if (in_array($host, ['becomecompany.com', 'becomecompany.test'])) {
            return view('auth.passwords.email_becomecompany');
        }
        return view('auth.passwords.email');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $this->validateEmail($request);

        // Check if the user is blocked
        $user = User::where('email', $request->email)->first();
        if ($user && ($user->active == 'block' || $user->active == 'inactive')) {
            if($user->active == 'block'){
                return back()->withErrors(['email' => 'This account is blocked, password reset is not allowed.','status' =>'1']);
            }else if($user->active == 'inactive'){
                return back()->withErrors(['email' => 'This account is inactive, password reset is not allowed.','status' =>'1']);
            }
        }
        if(isset($user->id) && $user->id){
            $ipaddress = Utility::getIp();
            if($user->agency_fk != ""){
                $agencyIps = AgencyWiseIpBlocker::where('del_flag','N')->where('agency_id', $user->agency_fk)->get();
                $ipArr = array();
                if(isset($agencyIps) && !empty($agencyIps) && count($agencyIps) > 0){
                    foreach ($agencyIps as $ips) {
                        $ipsList = explode(',', $ips->ip);
                        foreach ($ipsList as $ip) {
                            $ipArr[] = trim($ip); // Trim to remove extra spaces
                        }
                    }
                    if(!in_array($ipaddress,$ipArr)){
                       $message_ip = "Access denied: Your IP address ($ipaddress) is not allowed.";
                       return back()->withErrors(['email' => $message_ip,'status' =>'1']);
                    }
                }
            }
            // User block
            $blockIpData = UserIpAddress::blockUserIpData($user->id);
            if(isset($blockIpData) && !empty($blockIpData) && in_array($ipaddress,$blockIpData)){
                $message_ip = "Access denied: Your IP address ($ipaddress) is not allowed.";
                return back()->withErrors(['email' => $message_ip,'status' =>'1']);
            }
            // We will send the password reset link to this user. Once we have attempted
            // to send the link, we will examine the response then see the message we
            // need to show to the user. Finally, we'll send out a proper response.
            $response = $this->broker()->sendResetLink(
                $this->credentials($request)
            );
            return $response == Password::RESET_LINK_SENT
                        ? $this->sendResetLinkResponse($request, $response)
                        : $this->sendResetLinkFailedResponse($request, $response);
        }else{
            return back()->withErrors(['email' => 'This account is not exist.','status' =>'1']);
        }
    }
}