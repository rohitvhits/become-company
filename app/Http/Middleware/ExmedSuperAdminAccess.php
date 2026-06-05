<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\Auth;
class ExmedSuperAdminAccess
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
        if(Auth::user()->user_type_fk!=184){
            if(Auth::user()->role_access != 1){
                return  abort(500);
            }
           
        }
        if(Auth::user()->delete_flag == 'Y'){
            return  abort(404);

        }
        return $next($request);


    }


}


