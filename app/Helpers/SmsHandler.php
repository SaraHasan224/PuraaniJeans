<?php
/**
 * Created by PhpStorm.
 * User: Sara Hasan
 * Date: 8/25/2020
 * Time: 10:07 AM.
 */

namespace App\Helpers;

use App\Helpers\Order\BaseOrderHelper;
use App\Http\Traits\LoggingTrait;
use App\Models\OrderMisc;

class SmsHandler
{
    public static function otpSms($otp, $otpData, $otp_encrypt, $appType)
    {
        try {
            $phoneNumber = $otpData['phone_number'];
            $countryCode = $otpData['country_code'];

            $phoneNumber = "{$countryCode}{$phoneNumber}";
            $message = "{$otp_encrypt} is your verification OTP for {$appType}";

            $requestData = [
                'phone_number' => $phoneNumber,
                'country_code' => $otpData['country_code'],
                'identifier' => $otp->id,
                'message' => $message,
                'order' => array_key_exists('order', $otpData) ? $otpData['order'] : null,
                'title' => 'otp',
                'action' => $otpData['action'],
            ];

            M3Sms::sendSms($requestData);

            return true;
        } catch (\Exception $e) {
            AppException::log($e);

            return ApiResponseHandler::failure(__(Constant::GeneralError), $e->getMessage());
        }
    }

}
