<?php

namespace App\Http\Middleware;

use Closure;

class SuperAdminMiddleware
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
        if (!(auth()->check() && auth()->id() === 1)) {
            return redirect()->route('home')->withFlashDanger('Permission not granted');
        }
        return $next($request);
    }
}
