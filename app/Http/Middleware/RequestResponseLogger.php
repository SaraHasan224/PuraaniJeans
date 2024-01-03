<?php

/*
 * Author: Miesam Jafry
 * Dated: 12-November-2018
 */

namespace App\Http\Middleware;

use App\Helpers\Constant;
use App\Models\RequestResponseLog;
use Closure;

class RequestResponseLogger
{
    public function handle($request, Closure $next)
    {
        $request->request->set('request_id', uniqid());
        if (env('LOG_REQUEST_RESPONSE')) {
            RequestResponseLog::$requestStartTime = microtime(false);
        }
        return $next($request);
    }

    public function terminate($request, $response)
    {
        if (env('LOG_REQUEST_RESPONSE')) {
            RequestResponseLog::logRequest($request);
            RequestResponseLog::logResponse($request, $response);

            if (!empty(RequestResponseLog::$requestUpdateData)) {
                RequestResponseLog::where('request_id', $request->request_id)
                    ->update(RequestResponseLog::$requestUpdateData);
            }
        }
    }
}
