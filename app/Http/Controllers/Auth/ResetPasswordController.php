<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\User;
use App\Helpers\Utility;
use App\UserIpAddress;
use App\Model\AgencyWiseIpBlocker;
use Auth;
class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */
    use ResetsPasswords;
    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */    protected $redirectTo = '/home';
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    
    public function reset(Request $request)
    {
  
        $request->validate($this->rules(), $this->validationErrorMessages());
        $user = User::where('email', $request->email)->where('delete_flag','N')->first();
      
        if ($user && ($user->active == 'block' || $user->active == 'inactive')) {
            if($user->active == 'block'){
                return back()->withErrors(['email' => 'This account is blocked, password reset is not allowed.','status' =>'1']);
            }else if($user->active == 'inactive'){
                return back()->withErrors(['email' => 'This account is inactive, password reset is not allowed.','status' =>'1']);
            }
        }

        if(isset($user->id)){

            $ipaddress = Utility::getIp();
            if($user->agency_fk != ""){
                $agencyIps = AgencyWiseIpBlocker::where('del_flag','N')->where('agency_id', $user->agency_fk)->get();
                $ipArr = array();
                if(isset($agencyIps) && !empty($agencyIps) && count($agencyIps) > 0){
                    foreach ($agencyIps as $ips) {
                        $ipsList = explode(',', $ips->ip);
                        foreach ($ipsList as $ip) {
                            $ipArr[] = trim($ip);
                        }
                    }
                    
                    if(!in_array($ipaddress,$ipArr)){
                       $message_ip = "Access denied: Your IP address ($ipaddress) is not allowed.";
                       return back()->withErrors(['email' => $message_ip,'status' =>'1']);
                    }
                }
            }

            $blockIpData = UserIpAddress::blockUserIpData($user->id);
            if(isset($blockIpData) && !empty($blockIpData) && in_array($ipaddress,$blockIpData)){
                $message_ip = "Access denied: Your IP address ($ipaddress) is not allowed.";
                return back()->withErrors(['email' => $message_ip,'status' =>'1']);
            }
        }
        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        if(isset($user->id) && $user->id){
            $response = $this->broker()->reset(
                $this->credentials($request), function ($user, $password) {
                    $this->resetPassword($user, $password);
                }
            );

            if($user->two_fact_auth =='Y'){
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect('/login');
            }
            // If the password was successfully reset, we will redirect the user back to
            // the application's home authenticated view. If there is an error we can
            // redirect them back to where they came from with their error message.
            return $response == Password::PASSWORD_RESET
                        ? $this->sendResetResponse($request, $response)
                        : $this->sendResetFailedResponse($request, $response);
        }else{
            return back()->withErrors(['email' => 'This account is not exist.','status' =>'1']);
        }
    }
}