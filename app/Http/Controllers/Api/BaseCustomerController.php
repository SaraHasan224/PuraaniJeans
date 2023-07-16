<?php
/**
 * Created by PhpStorm.
 * User: Sara Hasan
 * Date: 2/3/2022
 * Time: 10:15 AM
 */

namespace App\Http\Controllers\Api;

use App\Helpers\Constant;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\CustomerAppSession;
use App\Models\Otp;

class BaseCustomerController extends Controller
{
    public function getCustomerAppSessionConfig( $sessionId )
    {
        $session = CustomerAppSession::findLatestBySessionId( $sessionId );
        $sessionOtp = Otp::getCustomerOtpByRequestId( $sessionId );

        $phoneNumber  = "";
        if(!empty($sessionOtp) && (!empty($sessionOtp->country_code) || !empty($sessionOtp->phone_number))){
            $phoneNumber = Helper::structurePhoneFormat( $sessionOtp->country_code,$sessionOtp->phone_number );

        }
        return [
            'masked_phone_number' => $phoneNumber,
            'phone_number' => !empty($sessionOtp) ? $sessionOtp->country_code.$sessionOtp->phone_number : "",
            'otp_sent' => !empty($session) ? $session->otp_verified : Constant::No,
            'otp_verified' => !empty($sessionOtp) ? $sessionOtp->is_verified : Constant::No ?? 0,
            'app_journey' => optional($session)->app_journey ?? Constant::APP_JOURNEY['GUEST_USER'],
            'onboarding_completed' => optional(optional(optional($session)->customer)->addresses)->count() > 0 && !empty(optional(optional($session)->customer)->default_payment_method_id) ? Constant::Yes : Constant::No,
            'user_journey' => optional(optional($session)->customer)->orderCount() == 0 ? Constant::USER_JOURNEY['NEW_USER'] : Constant::USER_JOURNEY['RETURNING_USER'],
        ];
    }

    public function getCustomerAppConfig()
    {
        return [
            'default_lang' => "en",
            'currency_code' => "PKR",
        ];
    }

    public function __getCustomerInfo($customerRecord)
    {
        return [
            'name' => $customerRecord->name,
            'email' => $customerRecord->email,
            'phone_number' => $customerRecord->hasDummyPhone() ? "" : $customerRecord->getPhoneNumber(),
            'address_count' => $customerRecord->addresses->count(),
            'default_payment_method_id' => $customerRecord->default_payment_method_id,
        ];
    }

}
