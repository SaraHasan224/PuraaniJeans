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
}
