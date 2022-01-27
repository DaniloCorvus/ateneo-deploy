<?php

namespace App\Http\Middleware;

use Closure;
use Exception;

class VerifyUserAuthenticateMiddlelware
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

        if (auth()->user()) {
            return $next($request);
        }
        return response(['error' => true, 'respuesta' => 'no autenticado'], 200);
    }
}
