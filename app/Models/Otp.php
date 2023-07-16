<?php

namespace App\Models;


use App\Helpers\Helper;
use Carbon\Carbon;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;

use App\Helpers\ApiResponseHandler;
use App\Helpers\AppException;
use App\Helpers\Constant;
use App\Helpers\Http;

use Jenssegers\Agent\Agent;

class Otp extends Model
{
    protected $fillable = [
        'model', 'model_id', 'email_otp', 'phone_otp', 'action',
    ];

    protected $guarded = [];

    public static $validationRules = [
        'send' => [
            'country_code' => 'required|string',
            'phone_number' => 'required|string',
            'network_id' => 'nullable|exists:mobile_telecom_networks,id'
        ],
        'verify' => [
            'otp' => 'required|min:6|max:6'
        ],
        'resend' => [
            'network_id' => 'nullable|exists:mobile_telecom_networks,id'
        ],
    ];

    public static function getOtpByUserId($action, $userId)
    {
        $expireTime = Carbon::now()->addMinute(Constant::OTP_EXPIRE_TIME);
        return self::where('model_id', $userId)
            ->where('action', $action)
            ->where('is_verified', Constant::No)
            ->where('is_used', Constant::No)
            ->where('created_at', '<=', $expireTime)
            ->orderBy('created_at', 'DESC')
            ->first();
    }

    public static function getOtpByRequestId( $request_id )
    {
        return self::where('model', Constant::OTP_MODULES['customers'])
            ->where('request_id', $request_id)
            ->where('is_verified', Constant::No)
            ->where('is_used', Constant::No)
            //->where('expire_at', '>', NOW())
            ->first();
    }

    public static function storeOtpInSession($emailOtp, $phoneOtp)
    {
        Session::put('email_otp', $emailOtp);
        Session::put('phone_otp', $phoneOtp);
        Session::put('opt_created_at', Carbon::now());
    }

    public static function doesVerificationRequired( $user, $request )
    {
        $otpVerifyGapInSeconds = env('OTP_VERIFY_GAP');
        $time = date("Y-m-d H:i:s", time() - $otpVerifyGapInSeconds);
        $iPDetails = Http::getIpDetails($request);
        $agent = new Agent();
        $verificationCountQuery = self::where('model_id', $user->id)
            ->where('model', Constant::OTP_MODULES['users'])
            ->where('is_verified', Constant::Yes)
            ->where('country', $iPDetails['country_name'] )
            ->where('user_agent', $agent->getUserAgent())
            ->where('verified_at', '>', $time);
        $verificationCount = $verificationCountQuery->count();
        return ! $verificationCount > 0;
    }

    public static function getLastOtpSentToCustomer( $customer_id, $identifier, $callFrom )
    {
        if( $callFrom == "checkout" )
        {
            return self::where('model', Constant::OTP_MODULES['customers'])
                ->where('order_ref', $identifier)
                ->where('model_id', $customer_id)->latest('created_at')->first();
        }
        elseif( $callFrom == "sso" || $callFrom == "identity-verification")
        {
            return self::where('model', Constant::OTP_MODULES['customers'])
                ->where('request_id', $identifier)
                ->where('model_id', $customer_id)->latest('created_at')->first();
        }
        elseif( $callFrom == "customer-portal")
        {
            return self::where('model', Constant::OTP_MODULES['customers'])
                ->where('request_id', $identifier)
                ->where('model_id', $customer_id)->latest('created_at')->first();
        }
    }

    public static function createOtp($action = 'register', $user, $resend = false, $phoneNumber = false, $sendSms = false)
    {
        try{
            $otp = new self;

            Otp::where('model_id', $user->id)->where('action',$action)->update(['is_used' => 1]);
            $optCreatedAt = session('opt_created_at');
            $diffInMinutes = Carbon::now()->diffInMinutes($optCreatedAt);

            if ($resend && $diffInMinutes <= config("app.OTP_resend_time"))
            {
                $emailOtp = session('email_otp');
                $phoneOtp = session('phone_otp');
            }
            else
            {
                $randomNumber = Helper::randomDigits();
                $emailOtp = $randomNumber;
                $phoneOtp = $randomNumber;
                self::storeOtpInSession($emailOtp, $phoneOtp);
            }

            if(!$phoneNumber){
                $phoneNumber = $user->country_code . $user->phone;
            }

            $otp->fill([
                'model'     => Constant::OTP_MODULES['users'],
                'model_id'  => $user->id,
                'action'    => $action,
                'email_otp' => Hash::make($emailOtp),
                'phone_otp' => Hash::make($phoneOtp),
            ])->id;

            $otp->save();
            $otp['non_hashed_email_otp'] = $emailOtp;
            return $otp;
        }catch (\Exception $e){
            AppException::log($e);
            return ApiResponseHandler::failure(__('messages.general.failed'), $e->getMessage());
        }
    }

    public static function verifyUser($action, $data)
    {
        $userId = $data['user_id'];
        $emailOtp = $data['email_code'];
        $phoneOtp = $data['email_code'];
        // $phoneOtp = $data['phone_code'];

        if (!config("app.OTP_ENABLED"))
        {
            if ($emailOtp == config("app.GeneralOTP") && $phoneOtp == config("app.GeneralOTP"))
            {
                self::markUserAttemptVerified($userId, $action);
                return true;
            }
        }
        else
        {
            $userOtp = Otp::getOtpByUserId($action, $userId);
            if ($userOtp)
            {
                if (Hash::check($emailOtp, $userOtp->email_otp) && Hash::check($phoneOtp, $userOtp->phone_otp))
                {
                    self::markUserAttemptVerified($userId, $action);
                    return true;
                }
            }
        }
        return false;
    }

    public static function markUserAttemptVerified($userId, $action)
    {
        Otp::where('model_id', $userId)->where('action', $action)->update(['is_verified' => 1]);
        User::markUserOtpVerified($userId);
    }

    public static function revokeOldOtpForCustomer( $customer_id, $model, $order_ref)
    {
        self::where('model',$model)
            ->where('model_id', $customer_id)
            ->where('is_verified', Constant::No)
            ->where(function ( $query ) use ($order_ref){
                $query->where('order_ref', $order_ref)
                    ->orWhere('request_id', $order_ref);
            })->update(['is_used' => 1]);
    }

    public static function allowOtpSendOnApp ($request) {
        $ip = Helper::getUserIP($request);
        $date = env('APP_ENV') == "production" ? [now()->subHours(env('MAX_OTP_ATTEMPT_IP_TIMEFRAME')), now()] :
            [now()->subMinutes(env('MAX_OTP_ATTEMPT_IP_TIMEFRAME')), now()];
        $query =  Otp::where('model', Constant::OTP_MODULES['customers'])
            ->where('ip',$ip)
            ->whereBetween('created_at',$date);
        $countMaxOtpTimeAttempts = $query->count();

        if($countMaxOtpTimeAttempts > 0) {
            $lastSentOtp = $query->latest('created_at')->first();
            $blockOtpExpTime =  OtpBlocklist::checkIpExpiryTime($lastSentOtp->ip);
            $maxAllowedOtpCount = env('MAX_OTP_IP_ATTEMPT_COUNT');
            if($countMaxOtpTimeAttempts >= $maxAllowedOtpCount) {
                if(empty($blockOtpExpTime)){
                    $reason = "Customer has been blocked due to suspicious activity. Otp sent count is: {$countMaxOtpTimeAttempts} that is greater than max otp send allowed count w.r.t. ip condition that is. {$maxAllowedOtpCount}";
                    OtpBlocklist::blockIP($lastSentOtp, $reason);
                    return false;
                }
                if(!empty($blockOtpExpTime)) {
                    return false;
                }
            }
        }
        return true;
    }

    public static function verifyCustomerPortalOtp($session_id, $phoneOtp )
    {
        $userOtp = Otp::getOtpByRequestId( $session_id );
        return self::__verify( $userOtp, $phoneOtp, 'vault' );
    }

    private static function __verify( $userOtp, $phoneOtp, $callFrom )
    {
        if( $userOtp )
        {
            $phoneWithCountryCode = $userOtp->country_code.$userOtp->phone_number;
            $playStoreAndAppStoreTestingNumbers = explode( ",", env('APP_PLAY_STORE_NUMBERS'));
            $isPlayStoreOrAppStoreRequest = $playStoreAndAppStoreTestingNumbers &&
                is_array( $playStoreAndAppStoreTestingNumbers ) &&
                in_array( $phoneWithCountryCode, $playStoreAndAppStoreTestingNumbers );

            if( (env('OTP_ENABLED') == 0) || ( $isPlayStoreOrAppStoreRequest && $callFrom == "vault" ) )
            {
                if( $phoneOtp == env('GeneralOTP') )
                {
                    $userOtp->markUserAttemptVerified();
                    return $userOtp;
                }
            }
            else
            {
                if( Helper::matchOtp( $phoneOtp, $userOtp->phone_otp ) )
                {
                    $userOtp->markUserAttemptVerified();
                    return $userOtp;
                }
            }
        }

        return false;
    }

}
