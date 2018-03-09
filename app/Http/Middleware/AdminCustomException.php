<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

class AdminCustomException
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

        $response = $next($request);
        $exception = $response->exception;

        if(!is_null($exception) && !config('app.debug')){
            return new Response(view('admin.errors.500'));
        }
        
        return $response;

    }
}
