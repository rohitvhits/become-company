<?php



namespace App\Http\Middleware;



use Closure;

use Illuminate\Support\Facades\Auth;

use App\User;

class RedirectIfAuthenticated

{

    /**

     * Handle an incoming request.

     *

     * @param  \Illuminate\Http\Request  $request

     * @param  \Closure  $next

     * @param  string|null  $guard

     * @return mixed

     */ 

    public function handle($request, Closure $next, $guard = null)

    {

        if (Auth::guard($guard)->check()) {
            if(isset(Auth::user()->id)){
                if(Auth::user()->agency_fk !=""){
                    return redirect('/appointment');
                }else{
                    return redirect('/home');
                }
            }
            

        }



        return $next($request);

    }

}

