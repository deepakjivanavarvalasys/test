<?php

namespace App\Http\Middleware;

use App\Models\Module;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckManager
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if(!Auth::check()) {
            if($request->ajax()) {
                return response('Session Timeout, please login to continue.',401);
            }
            return redirect()->route('login');
        }

        $user = Auth::user();

        if(!isset($user->logged_on) || empty($user->logged_on)) {
            return redirect()->route('lockscreen');
            Auth::logout();
            if($request->ajax()) {
                return response('Session Timeout',302);
            }
            return redirect()->route('login');
        }

        $module = Module::whereRoleId($user->role_id)->first();
        if($module->slug != 'manager') {
            Auth::logout();
            if($request->ajax()) {
                return response('Invalid Session',302);
            }
            return redirect()->route('login');
        }

        $user->logged_on = date('Y-m-d H:i:s');
        $user->save();
        return $next($request);
    }
}
