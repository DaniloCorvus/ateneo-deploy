<?php

namespace App\Http\Middleware;

use Closure;

class EnsureTokenIsValid
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
        
        if ($request->get('Username') !== 'DyalogoConsulta' || $request->get('Password') !== 'p9HNGsqMr8wAEEU7Qrf') {
            return response(['error'=>true,'respuesta'=>$request],403); 
        } 

        return $next($request);
    }
}
