<?php

namespace App\Http\Middleware;

use App\Helpers\ApiResponseHandler;
use App\Helpers\Constant;
use Closure;

class ThirdPartyApi
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
       $token = isset( Constant::ThirdPartyRoutesToken[ $request->path() ] ) ?
           Constant::ThirdPartyRoutesToken[ $request->path() ] : false;

       if( $token && $request->has('api_key') )
       {
           if( $request->get('api_key') == $token )
           {
               return $next($request);
           }
       }

        return ApiResponseHandler::authenticationError();
    }
}
