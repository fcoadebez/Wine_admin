<?php

namespace App\Http\Middleware;

use App\Http\APIResponse;
use Closure;
use App\Model\Devis;

class APIToken
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
        $GLOBALS["quote"] = Devis::where("link", "=", $request->route('link'))->first();
        if(is_null($GLOBALS["quote"])){
            $response = new APIResponse($request);
            $response->setStatus(404);
            $response->setBody("Not found");
            return $response->json();
        }
        return $next($request);
    }
}
