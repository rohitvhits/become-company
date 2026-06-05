<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class XSS
{
    /**
     * Handle an incoming request. for superadmin access
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */

    public function handle($request, Closure $next, $guard = null)

    {

        // echo Auth::user()['login_type_fk'];
        // if(Auth::user()['login_type_fk'] !=183 ){
        //     //  print_r(Auth::user());
        //      //die(Auth::user()['login_type_fk']);
        //     return  abort(404);  

        // }

        if ($request->is('cms/update') ||  $request->routeIs('announcement-master.*')) {
            return $next($request);
        }
        if (Auth::check()) {
            if ((Auth::user()->delete_flag == 'Y') || (Auth::user()->active == 'inactive' || Auth::user()->active == 'block')) {
                return  abort(404);
            }
        }
        $userInput = $request->all();
        array_walk_recursive($userInput, function (&$userInput) {
            $userInput = strip_tags($userInput);
        });
        $request->merge($userInput);
        return $next($request);
    }
}
