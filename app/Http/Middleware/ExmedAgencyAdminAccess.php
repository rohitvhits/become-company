<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\Auth;
class ExmedAgencyAdminAccess
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


            if(Auth::user()->user_type_fk!=5){
                return  abort(404);

            }
            return $next($request);


    }


}


