<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Autenticador
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
        if (!Auth::check()) {
            throw new AuthenticationException();
        }

        /*
        if (!session()->has('usuario')) {

            session()->put('usuario', 'calopsita');         // dd($request->session()->get('usuario'));
        }       
        */
        
        /*
        $request->merge(["usuario" => "calopsita"]);            // dd($request->usuario);
        */
        
        return $next($request);
    }
}
