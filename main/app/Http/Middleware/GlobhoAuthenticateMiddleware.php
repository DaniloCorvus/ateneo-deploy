<?php

namespace App\Http\Middleware;

use Closure;

class GlobhoAuthenticateMiddleware
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
        if ($request->get('Username') !== 'GlobhoClient' || $request->get('Password') !== 'p9HNGsqMr8wAEEU7Qrf') {
            return response(['error' => true, 'respuesta' => 'No autorizado'], 403);
        }

        return $next($request);
    }
}
