<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use App\Helpers\AppException;
use App\Helpers\Constant;
use App\Helpers\Helper;
use App\Helpers\ApiResponseHandler;

use App\Models\Customer;
use App\Models\CustomerAppSession;
use App\Models\CustomerProductRecentlyViewed;
use App\Models\Otp;


class OtpController extends BaseCustomerController
{
    /**
     * @OA\Post(
     *     path="/api/verify-phone",
     *     tags={"Auth Verification"},
     *     summary="Send Otp",
     *     operationId="sendOtp",
     *
     *     @OA\Response(response=200,description="Success"),
     *
     *     @OA\RequestBody(
     *         description="Send OTP for provided phone number",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="country_code",
     *                     example=92,
     *                     description="Country code ie. 92 for Pakistan",
     *                     type="number",
     *                 ),@OA\Property(
     *                     property="phone_number",
     *                     description="Phone Number",
     *                     type="string",
     *                     example=3002927320,
     *                 )
     *              )
     *         )
     *     ),
     *     security={
     *          {"user_access_token": {}}
     *     }
     * )
     */

    public function sendOtp(Request $request)
    {
        try {
            $response = [];
            $requestData = $request->all();
            $error_body = [];
            $validator = Validator::make($requestData, Otp::$validationRules['send']);

            if ($validator->fails()) {
                return ApiResponseHandler::validationError($validator->errors());
            }
            $country_code = $requestData['country_code'];
            $phone_number = $requestData['phone_number'];

            $customer = Customer::findByPhoneNumber($country_code, $phone_number);
            if (!empty($customer) && $customer->status == Constant::CUSTOMER_STATUS['Blocked']) {
                if (Auth::user()) {
                    //  Auth::user()->killSession($request->session_id);
                }
                return ApiResponseHandler::failure(__('messages.customer.otp.customer_is_blocked'), '', $error_body);
            }

            DB::beginTransaction();

            $otpData = [
                'session_id' => $customer->identifier,
                'action' => Constant::OTP_EVENTS['send'],
                'customer_id' => $customer->id,
                'phone_number' => $phone_number,
                'country_code' => $country_code,
            ];

            Otp::revokeOldOtpForCustomer($request->customer_id, Constant::OTP_MODULES['customers'], $request->session_id);
            Otp::createOtp($otpData, $request);

            $timerVal = env('OTP_EXPIRE_TIME');
            $sessionId = $requestData['session_id'];

            CustomerAppSession::updateJourney($sessionId, Constant::APP_JOURNEY['ONBOARDING']);

            DB::commit();

            $response['otp_timer'] = Helper::convertSecondsIntoMilliseconds($timerVal);
            return ApiResponseHandler::success($response, __('messages.general.success'));
        } catch (\Exception $e) {
            DB::rollBack();
            AppException::log($e);
            return ApiResponseHandler::failure(__('messages.general.failure'), $e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/v1/portal/otp/verify",
     *     tags={"Verification"},
     *     summary="Verify Otp",
     *     operationId="verifyOtp",
     *
     *     @OA\Response(response=200,description="Success"),
     *
     *     @OA\RequestBody(
     *         description="Verify OTP",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="otp",
     *                     example="000000",
     *                     description="6 Ditig OTP Sent to your mobile number",
     *                     type="string"
     *                 )
     *              )
     *         )
     *     ),
     *     security={
     *          {"user_access_token": {}}
     *     }
     * )
     */
    public function verifyOtp(Request $request)
    {
        try {
            $response = [];
            $requestData = $request->all();

            $validator = Validator::make($requestData, Otp::$validationRules['verify']);

            if ($validator->fails()) {
                return ApiResponseHandler::validationError($validator->errors());
            }

            DB::beginTransaction();

            $verifiedOtp = Otp::verifyCustomerPortalOtp($request->session_id, $requestData['otp']);

            if ($verifiedOtp) {
                $existingCustomer = Customer::findNonAnonymousCustomerByOtp($verifiedOtp);
                $askReferralCode = Constant::No;
                
                if ($existingCustomer) {
                    $existingCustomer->updateAnonymousOrNonVerifiedCustomer($verifiedOtp);
                    $verifiedOtp->updateCustomer($existingCustomer);
                    
                    $customer = $existingCustomer;

                    if(!empty($customer) && $customer->status == Constant::CUSTOMER_STATUS['Blocked']){
                        $session = CustomerAppSession::findLatestBySessionId($request->session_id);
                        $reason = "Customer with phone# {$customer->country_code}{$customer->phone_number} is blocked thus the session ip has been blocked on verifyOtp Call";
                        return ApiResponseHandler::userBlockedException( $session, $reason, $request->session_id, $customer );
                    }
                    
                    CustomerProductRecentlyViewed::updateRecentlyViewedCustomerId($request->customer_id, $customer->id);
                    Customer::removeCustomer($request->customer_id);

                } //elseif( Auth::user()->is_anonymous == Constant::Yes || Auth::user()->is_verified == Constant::No )
                else {
                    Auth::user()->updateAnonymousOrNonVerifiedCustomer($verifiedOtp);
                    
                    $customer = Auth::user();
                    $askReferralCode = Constant::Yes;
                }

                $session = $customer->createAccessToken($request, true);
                $sessionToken = $session['token'];
                $sessionId = $session['id'];
                $response['session_token'] = $sessionToken;
                $response['session_id'] = $sessionId;

                CustomerAppSession::updateOtpVerified($sessionId);

                $response['user_id'] = $customer->token;
                $response['address_count'] = $customer->addresses->count();
                $response['default_payment_method_id'] = $customer->default_payment_method_id;
                $response['ask_referral'] = $askReferralCode;
                $response['session_config'] = $this->getCustomerAppSessionConfig($sessionId);
                $response['customer'] = $this->__getCustomerInfo($customer);

                DB::commit();
                return ApiResponseHandler::success($response, __('messages.customer.otp.success'));
            } else {
                DB::commit();
                return ApiResponseHandler::failure(__('messages.customer.otp.failure'));
            }
        } catch (\Exception $e) {
            DB::rollBack();
            AppException::log($e);
            return ApiResponseHandler::failure(__('messages.general.failure'), $e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/v1/portal/otp/resend",
     *     tags={"Verification"},
     *     summary="Resend Otp",
     *     operationId="resendOtp",
     *
     *     @OA\Response(response=200,description="Success"),
     *
     *     @OA\RequestBody(
     *         description="Resend OTP",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="network_id",
     *                     description="Network Id of customer's phone number",
     *                     type="number",
     *                     example=Null
     *                 )
     *              )
     *         )
     *     ),
     *     security={
     *          {"user_access_token": {}}
     *     }
     * )
     */
    public function resendOtp(Request $request)
    {
        try {
            $requestData = $request->all();
            $response = [];

            $validator = Validator::make($requestData, Otp::$validationRules['resend']);

            if ($validator->fails()) {
                return ApiResponseHandler::validationError($validator->errors());
            }

            $customer = Auth::user();

//            $allow_otp_resend = Otp::allowOtpReSend($customer->id, $request->session_id, 'customer-portal');

            DB::beginTransaction();
            $allowOtpSend = Otp::allowOtpSendOnApp($request->customer_id);
            if ($allowOtpSend) {
                Otp::revokeOldOtpForCustomer($customer->id, Constant::OTP_MODULES['customers'], $request->session_id);

                $lastOtpSent = Otp::getLastOtpSentToCustomer($customer->id, $request->session_id, 'customer-portal');

                if($lastOtpSent->country_code == 92) {
                    $otpData = [
                        'session_id' => $request->session_id,
                        'action' => Constant::OTP_EVENTS['resend'],
                        'customer_id' => $request->customer_id,
                        'network_id' => 0,
                        'phone_number' => $lastOtpSent->phone_number,
                        'country_code' => $lastOtpSent->country_code,
                        'phone_otp' => $lastOtpSent->phone_otp,
                    ];

                    Otp::createOtp($otpData, $request, "customer-portal");
                }
                $timerVal = env('OTP_EXPIRE_TIME');
                $response['otp_timer'] = Helper::convertSecondsIntoMilliseconds($timerVal);
                DB::commit();

                return ApiResponseHandler::success([], __('messages.customer.otp.resend.success'));
            } else {
                $error_body = [];
                #TODO: Delete in next sprint start
                Auth::user()->killSession($request->session_id);
                #TODO: Delete in next sprint start
                return ApiResponseHandler::failure(__('messages.customer.otp.customer_is_blocked'), '', $error_body);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            AppException::log($e);
            return ApiResponseHandler::failure(__('messages.general.failure'), $e->getMessage());
        }
    }

}
