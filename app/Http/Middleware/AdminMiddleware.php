<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $routeName = ['volumes.destroy','books.destroy'];
        if (!(auth()->check() && auth()->user()->is_admin)) {
            return redirect()->route('home')->withFlashDanger('Permission not granted');
        }
        if(in_array($request->route()->getName(),$routeName)){
            if(!Hash::check($request->password, auth()->user()->password)){
                return redirect()->back()->withFlashDanger('Incorrect password!');
            }
        }

        return $next($request);
    }
}
