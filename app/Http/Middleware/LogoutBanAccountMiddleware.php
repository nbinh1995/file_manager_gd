<?php

namespace App\Http\Middleware;

use Closure;

class LogoutBanAccountMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(auth()->check()){
            if(auth()->user()->active){
                return $next($request);
            }else{
                auth()->logout();
                return redirect()->route('login')->withFlashDanger('Account has been ban!');
            }
        }
        return $next($request);
    }
}
