<?php
namespace App\Http\Controllers\Auth;
use App\Agency;
use App\Helpers\AttachMailer;
use App\Helpers\UserHelper;
use App\User;
use Carbon\Carbon;
use App\Model\LoginLog;
use App\Services\IpInfoService;
use App\Http\Controllers\Controller;
use App\Services\LoginLogService;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Cookie;
use App\Helpers\Utility;
use App\Model\AgencyWiseIpBlocker;
use App\UserIpAddress;
class LoginController extends Controller
{

    /*

    |--------------------------------------------------------------------------

    | Login Controller

    |--------------------------------------------------------------------------

    |

    | This controller handles authenticating users for the application and

    | redirecting them to your home screen. The controller uses a trait

    | to conveniently provide its functionality to your applications.

    |

    */
    use AuthenticatesUsers;
    /**

     * Where to redirect users after login.

     *

     * @var string

     */

    protected $redirectTo = '/home';



    /**

     * Create a new controller instance.

     *

     * @return void

     */

    public function __construct()

    {

        $this->middleware('guest')->except('logout');
    }


    public function showLoginForm(Request $request)
    {
        $host = $request->getHost();
        if (in_array($host, ['becomecompany.com', 'becomecompany.test'])) {
            return view('auth.login_becomecompany');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
     
        $query = UserHelper::checkUserBlockOrNotByEmail($request->email);
        $final_array = array();
        if (isset($query->id) && $query->id != '') {
            $final_array['status'] = $query->active;
            $final_array['id'] = sha1($query->id);
        }
    
        if (isset($final_array['status'])) {
            if ($final_array['status'] == 'inactive') {
                Session::flash('error', 'Your account is inactive.You need to contact your organization to active.');
                return redirect('/login');
            } elseif ($final_array['status'] == 'block') {
                if($query->login_attemps == 0){
                        $error='Your account has been blocked after multiple consecutive login attempts. You need to contact your organization to unblock.';
                }else{
                    $error='Your account is blocked.You need to contact your organization to unblock.';
                }
                Session::flash('error', $error);
                return redirect('/login');
            }
        }
        
        $user = User::whereRaw('LOWER(email) ="'.strtolower($request->email).'"')->where('delete_flag', 'N')->first();

        $host = $request->getHost();
        if ($user && in_array($host, ['becomecompany.com', 'becomecompany.test'])) {
            if (!in_array($user->user_type_fk, [5, 6])) {
                Session::flash('error', 'Access denied. Only agency users can login here.');
                return redirect('/');
            }
        }

        if ($user) {
            if(empty($request->password)){
                throw ValidationException::withMessages([
                    'password' => ["Please enter Password."],
                ]);
            }else{
                if (Hash::check($request->password, $user->password)) {
                    $ipaddress = $this->getUserIP();
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
                                Session::flash('error', $message_ip);
                                return redirect('/login');
                            }
                        }
                    } 
                    if(isset($user->id)){
                        $blockIpData = UserIpAddress::blockUserIpData($user->id);
                        if(isset($blockIpData) && !empty($blockIpData) && in_array($ipaddress,$blockIpData)){
                            $message_ip = "Access denied: Your IP address ($ipaddress) is not allowed.";
                            Session::flash('error', $message_ip);
                            return redirect('/login');
                        }
                    }

                    Cookie::queue('userLogin'.$user->id, date('Y-m-d H:i:s'), 120);
                    Cookie::queue('userLoginNew'.$user->id, "10", 120);
                    Cookie::queue('showNotification'.$user->id, date('Y-m-d'), 120);
                    $password = $request->password;
                    $agencyTwoFactorFeatureEnabled = false;
    
                    $agency = NULL;
                    if (in_array($user->user_type_fk, array(5, 6))) {
                        $agencyTwoFactorFeatureEnabled = false;
                        $agency = Agency::where('id', $user->agency_fk)->first();
                    }
                  
                    if ($agency) {
                        if($agency->two_factor_auth=="Y"){
                           $agencyTwoFactorFeatureEnabled = true;
                        }
                    }
                    
                    if($user->two_fact_auth == 'Y'){
                        $agencyTwoFactorFeatureEnabled = true;
                    }

                    $pattern = '/^(?=.*[!@#$%^&*-])(?=.*[0-9])(?=.*[A-Z]).{8,20}$/';
                    if($agencyTwoFactorFeatureEnabled)
                    {
                      
                        $value = $request->cookie('rememberdevice');
                        $hasActiveCookies = false;
                        if ($value !== '') {
    
                            $rememberToken= DB::table('remeber_device')->where('token',$value)->where('user_id',$user->id)->where('expiry','>=',date('Y-m-d'))->first();
                            if($rememberToken){
                                $hasActiveCookies=true;
                            }
                        }
    
                        if (  !$hasActiveCookies) {
                          
                            $two_factor_code = rand(pow(10, 4 - 1), pow(10, 4) - 1);
                            $data['rand_no'] =   $two_factor_code;
                            $data['otp_expired_time'] = now()->addMinutes(10);
                            $insert =  User::where('id', $user->id)->update($data);
    
                            $_TO = $user->EMAILADDRESS;
                            $_CC = '';
    
                            $first_name = $user->first_name;
                            $last_name = $user->last_name;
                            $user_id = $user->id;
                            $duration = "10 minutes";
    
                            $_SUBJECT = "Two Factor Verification";
                            $from = 'noreply@nybestmedicals.com';
    
                            $emailData = array(
                                'first_name' => $first_name, 
                                'two_factor_code' => $two_factor_code, 
                                'duration'=>$duration
                            );
                            $_CONTENT = Utility::getHtmlContent('email_template.sign_in_attempt_login_template',$emailData);
                            $insert = AttachMailer::sendEmail($from, $user->email, $_SUBJECT, $_CONTENT);
                            if ($insert) {
                                Session::flash('success', 'Mail has been sent to you with verification code.');
                                return redirect(URL::to('/') . '/auth/verify-otp/' . sha1($user->id));
                            }
                        }
                    }
                } else {
    
                    $loginAttemptts = $user->login_attemps;
                    $loginAttemptts--;
    
                    User::where('id', $user->id)->update(['login_attemps' => $loginAttemptts]);
                    if ($loginAttemptts == 0) {
                        User::where('id', $user->id)->update(['active' => 'block']);
                    } 

                    if($loginAttemptts == 1){
                        throw ValidationException::withMessages([
                            $this->username() => ["Invalid email and/or password. You have only " . $loginAttemptts . " attempts"],
                        ]);
                    }else{
                        throw ValidationException::withMessages([
                            $this->username() => ["Invalid email and/or password."],
                        ]);
                    }
                }
            }
        } else {
            throw ValidationException::withMessages([
                $this->username() => ["Invalid username and/or password"],
            ]);
            return redirect()->back();
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }
    }

    protected function attemptLogin(Request $request)
    {
        return $this->guard()->attempt(
            $this->credentials($request),
            $request->filled('remember')
        );
    }


    private function isStorngPasswordCheck($request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $password = $request->password;
                $pattern = '/^(?=.*[!@#$%^&*-])(?=.*[0-9])(?=.*[A-Z]).{8,20}$/';
                if (!preg_match($pattern, $password)) {
                    echo (URL::to('/') . '/make-secure-password/' . sha1($user->id));
                    die;
                }
            } else {
                throw ValidationException::withMessages([
                    $this->username() => ["Invalid email and/or password4"],
                ]);
            }
        } else {
            throw ValidationException::withMessages([
                $this->username() => ["Invalid username and/or password3"],
            ]);
            return redirect()->back();
        }

        $this->guard()->attempt(
            $this->credentials($request),
            $request->filled('remember')
        );
    }
    public function checkEmailPassword(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $id = $user->id;
                $password = $request->password;
                $pattern = '/^(?=.*[!@#$%^&*-])(?=.*[0-9])(?=.*[A-Z]).{8,20}$/';
                
                if (preg_match($pattern, $password)) {
                    return response()->json(['error_msg' => " ", 'status' => 1, 'data' => array()], 200);
                } else {
                    return response()->json(['error_msg' => " ", 'status' => 3, 'data' => array('id' => sha1($user->id))], 200);
                }
            } else {
                return response()->json(['error_msg' => " ", 'status' => 0, 'data' => array()], 200);
            }
        } else {
            return response()->json(['error_msg' => " ", 'status' => 2, 'data' => array()], 200);
        }
    }


    public function authenticated()
    {

        $auth = auth()->user();
        $ipaddress = $this->getUserIP();
        $browserIP = $this->getUserIP();
       
        $browserDetails = '';
        $ipDetails=$countryCode="";

        $log = array('browser_ip' => $browserIP, 'browser_details' => $browserDetails, "ip_details" => $ipDetails);
        $logData = [
            'user_id'     => $auth->id,
            'logs'     => serialize($log),
            'country' => isset($ipDetails['country']) ? $ipDetails['country'] : '',
            'ipaddress' => $ipaddress,

            'country_code' => $countryCode,
            'login_status' => 'success'
        ];
        LoginLogService::insert($logData);

        $uus = User::where('id', $auth['id'])->update(array( 'login_attemps'=>5, 'last_login_ip' => $ipaddress, 'last_login_at' => date('Y-m-d H:i:s')));
    }
    protected function credentials(\Illuminate\Http\Request $request)
    {
     
        return ['email' => $request->{$this->username()}, 'password' => $request->password, 'delete_flag' => 'N'];
    }
    // Failed Login Response check 
    protected function sendFailedLoginResponse(\Illuminate\Http\Request $request)
    {
        $email = $request->{$this->username()};

        $checkUser =  User::getIDByEmail($email);

        if ($checkUser) {
            $ipaddress = request()->getClientIp();
            $ipaddress = $this->getUserIP();
            $browserIP = $_SERVER['HTTP_USER_AGENT'];
            $browserDetails = "";
            $ipDetails = IpInfoService::ipInfo($ipaddress);
            $country = ($ipDetails['country']) ?? "";
            $countryCode = ($ipDetails['country_code']) ?? "";

            //Store Failed login attempted user & browser login log
            $log = array('browser_ip' => $browserIP, 'browser_details' => $browserDetails, "ip_details" => $ipDetails);
            $logData = [
                'user_id'     => $checkUser->id,
                'logs'     => serialize($log),
                'ipaddress' => $ipaddress,
                'country' => $country,
                'country_code' => $countryCode,
                'login_status' => 'failed'
            ];
            LoginLogService::insert($logData);
        }
        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }
    private function getUserIP()
    {

        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
            $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
            $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        }
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = $_SERVER['REMOTE_ADDR'];

        if (filter_var($client, FILTER_VALIDATE_IP)) {
            $ip = $client;
        } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
            $ip = $forward;
        } else {
            $ip = $remote;
        }

        return $ip;
    }
}