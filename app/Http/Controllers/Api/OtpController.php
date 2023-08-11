<?php

namespace App\Http\Controllers\Api;

use App\Models\AccessToken;
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
     *     path="/api/send/otp",
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
            $response = [
                'phone' => ""
            ];
            $requestData = $request->all();
            $error_body = [];
            $validator = Validator::make($requestData, Otp::$validationRules['send']);

            if ($validator->fails()) {
                return ApiResponseHandler::validationError($validator->errors());
            }

            $customerId = $requestData['customer_id'];
            $customerRef = $requestData['customer_ref'];
            $customer = Customer::findById($customerId);
            if (empty($customer)) {
                return ApiResponseHandler::failure(__('Customer not found'));
            }
            if (!empty($customer) && $customer->status == Constant::CUSTOMER_STATUS['Blocked']) {
                if (Auth::user()) {
                      Auth::user()->killSession($request->customer_ref);
                }
                return ApiResponseHandler::failure(__('Customer blocked'));
            }
            $allowOtpSend = Otp::allowOtpReSend($customerId, $customerRef);
            if($allowOtpSend) {
                DB::beginTransaction();
                $country_code = $requestData['country_code'];
                $phone_number = $requestData['phone_number'];

                $otpData = [
                    'identifier' => $customer->identifier,
                    'action' => Constant::OTP_EVENTS['send'],
                    'customer_id' => $customer->id,
                    'phone_number' => $phone_number,
                    'country_code' => $country_code,
                ];
                Otp::revokeOldOtpForCustomer($customerId, Constant::OTP_MODULES['customers'], $customer->identifier);
                Otp::createOtp( $otpData, $request);
                DB::commit();

                $response['phone'] = "+({$country_code}){$phone_number}";
            }

            return ApiResponseHandler::success($response, __('messages.general.success'));
        } catch (\Exception $e) {
            DB::rollBack();
            AppException::log($e);
            return ApiResponseHandler::failure(__('messages.general.failure'), $e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/verify/otp",
     *     tags={"Auth Verification"},
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

            $customerRef = $requestData['customer_ref'];
            $verifiedOtp = Otp::verifyCustomerOtp($customerRef, $requestData['otp']);
            if ($verifiedOtp) {
                $customer = Customer::findByRef($customerRef);
                $customer->updateNonVerifiedCustomer($verifiedOtp);
                AccessToken::revokeOldTokensByName($customer->identifier);
                $response['token'] =  $customer->createToken($customer->identifier, ['customer'])->accessToken;
                $response['customer'] = [
                    'first_name' => $customer->first_name,
                    'last_name' => $customer->last_name,
                    'email' => $customer->email,
                    'country_code' => $customer->country_code,
                    'phone_number' => $customer->phone_number,
                    'identifier' => $customer->identifier,
                    'closet_ref' => optional(optional($customer)->closet)->closet_reference,
                ];
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
     *     path="/api/resend/otp",
     *     tags={"Auth Verification"},
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

            $customerRef = $requestData['customer_ref'];
            $customerId = $requestData['customer_id'];
            DB::beginTransaction();
            $allowOtpSend = Otp::allowOtpReSend($customerId, $customerRef);
//            $allowOtpSend = Otp::allowOtpSendOnApp($request->customer_id);
            if ($allowOtpSend) {
                Otp::revokeOldOtpForCustomer($customerId, Constant::OTP_MODULES['customers'], $request->session_id);

                $lastOtpSent = Otp::getLastOtpSentToCustomer($customerId, $customerRef);

                if($lastOtpSent->country_code == 92) {
                    $otpData = [
                        'session_id' => $customerRef,
                        'action' => Constant::OTP_EVENTS['resend'],
                        'customer_id' => $customerId,
                        'network_id' => 0,
                        'phone_number' => $lastOtpSent->phone_number,
                        'country_code' => $lastOtpSent->country_code,
                        'phone_otp' => $lastOtpSent->phone_otp,
                    ];

                    Otp::createOtp($otpData, $request);
                }
                DB::commit();

                return ApiResponseHandler::success([], __('messages.customer.otp.resend.success'));
            } else {
                $error_body = [];
                #TODO: Delete in next sprint start
                Auth::user()->killSession($request->customer_ref);
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
