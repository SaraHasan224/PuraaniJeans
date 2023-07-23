<?php

namespace App\Http\Middleware;

use App\Helpers\ApiResponseHandler;
use App\Helpers\Constant;
use App\Models\CustomerAppSession;
use App\Models\Otp;
use Closure;
use Illuminate\Support\Facades\Auth;

class OtpVerificationCheck
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
        $customer_id = Auth::user()->id;

        if( $request->has('order_ref') )
        {
            $order_ref = $request->order_ref;
            $inCompleteOtp = Otp::getOtpByUserId( $order_ref, $customer_id);

            if( $inCompleteOtp || Auth::user()->isAnonymous() )
            {
                return ApiResponseHandler::failure('OTP Verification Pending');
            }

            return $next($request);
        }
        elseif( $request->has('sso_request_id') )
        {
            $sso_request = $request->sso_request_id;
            $inCompleteOtp = Otp::getOtpByRequestId( $sso_request );

            if( $inCompleteOtp || Auth::user()->isAnonymous() )
            {
                return ApiResponseHandler::failure('OTP Verification Pending');
            }

            return $next($request);
        }
        elseif( $request->has('session_id') )
        {
            $session_id = $request->session_id;
            $sessionInCompleteOtp = CustomerAppSession::findLatestBySessionId( $session_id );
//            $inCompleteOtp = Otp::getCustomerOtpByRequestId( $session_id );

            if( $sessionInCompleteOtp->otp_verified == Constant::No )
            {
                $response = [
                    'otp_pending' => Constant::Yes,
                ];

                return ApiResponseHandler::failure('OTP Verification Pending', null, $response);
            }

            if( Auth::user()->isAnonymous() )
            {
                $response = [
                    'otp_pending' => Constant::Yes,
                ];

                return ApiResponseHandler::failure('Anonymous customer found. OTP Verification Pending', null, $response);
            }
            return $next($request);
        }
        else
        {
            return $next($request);
        }
    }
}
