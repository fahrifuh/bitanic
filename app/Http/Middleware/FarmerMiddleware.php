<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class FarmerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->role === 'farmer') {
            return $next($request);
        }

        return abort(403, 'You does not have access rights to the content;');
    }
}
