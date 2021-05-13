<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class verificarTokenValido
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
        if ($request->input('token') !== 'my-secret-token') {
           
            return redirect('api/test');
        }
        
    
        return $next($request);
        
    }
}
