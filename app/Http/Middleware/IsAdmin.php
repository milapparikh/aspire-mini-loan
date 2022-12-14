<?php

namespace App\Http\Middleware;
use Closure;

class IsAdmin
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
        if($request->user()->role_type == 'A')//Check admin conditions
        {
            return $next($request);
        }
        else{
            return response('Unauthorized Action', 403);
        }
    }
}