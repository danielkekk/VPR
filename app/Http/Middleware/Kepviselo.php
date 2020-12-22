<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use Closure;

class Kepviselo
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
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (!in_array((int)Auth::user()->role,[1,2,3,4])) {
            return redirect()->route('accessdenied');
        }

        /*if (Auth::user()->role == 1) {
            return redirect()->route('admin');
        }

        if (Auth::user()->role == 2) {
            return redirect()->route('polgarmester');
        }

        if (Auth::user()->role == 3) {
            return $next($request);
        }*/

        return $next($request);
    }
}
