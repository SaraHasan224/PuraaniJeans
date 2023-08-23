<?php
/**
 * Created by PhpStorm.
 * User: Sara
 * Date: 6/28/2023
 * Time: 12:05 AM
 */

namespace App\Http\Controllers\Api;


use App\Helpers\ApiResponseHandler;
use App\Helpers\AppException;
use App\Helpers\Helper;
use App\Models\Closet;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController
{
    /**
     * @OA\Get(
     *
     *     path="/api/customer",
     *     tags={"Customer"},
     *     summary="Get meta data content",
     *     operationId="getCustomerMetaContent",
     *
     *     @OA\Response(response=200,description="Success"),
     *
     *     security={
     *          {"user_access_token": {}}
     *     }
     * )
     */

    public function getCustomerMetaContent(Request $request)
    {
        try {
            $requestData = $request->all();
            $customerId = $requestData['customer_id'];
            $customer = Customer::findById($customerId);
            $closet = Closet::findByCustomerId($customerId);

            $response = [
                'customer' => [
                    'first_name' => optional($customer)->first_name,
                    'last_name' => optional($customer)->last_name,
                    'email' => optional($customer)->email,
                    'country_code' => optional($customer)->country_code,
                    'phone_number' => optional($customer)->phone_number,
                    'identifier' => optional($customer)->identifier,
                    'closet' => [
                        'closet_ref' => optional($closet)->closet_reference,
                    ],
                ],
            ];
            return ApiResponseHandler::success($response, __('messages.general.success'));
        } catch (\Exception $e) {
            AppException::log($e);
            return ApiResponseHandler::failure(__('messages.general.failed'), $e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/closet/create",
     *     tags={"Closet"},
     *     summary="create closet",
     *     operationId="createCloset",
     *
     *     @OA\Response(response=200,description="Success"),
     *
     *     @OA\RequestBody(
     *         description="create closet",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="name",
     *                     example="",
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
    public function createCloset(Request $request)
    {
        $requestData = $request->all();
        $response = [];
        $validator = Validator::make($requestData, Customer::$validationRules['create-closet']);

        if ($validator->fails()) {
            return ApiResponseHandler::validationError($validator->errors());
        }
        $customerId = $requestData['customer_id'];
        $customer = Customer::findById($customerId);
        if(!empty($customer->closet)) {
            return ApiResponseHandler::failure("You have already created a closet against your email address.");
        }

        $closet = Closet::createCloset($requestData);
        $response['closet_ref'] = $closet->closet_reference;
        $response['closet'] = [
            'name' => $closet->name,
            'closet_ref' => $closet->closet_reference,
            'email' => $closet->customer->email,
            'logo' => $closet->logo,
            'banner' => $closet->banner,
            'description' => $closet->about_closet,
        ];
        return ApiResponseHandler::success($response, "You have successfully registered to " . env('APP_NAME') . ".");
    }

    /**
     * @OA\Post(
     *     path="/api/closet/{reference}/edit",
     *     tags={"Closet"},
     *     summary="update closet settings",
     *     operationId="updateCloset",
     *
     *     @OA\Response(response=200,description="Success"),
     *
     *     @OA\RequestBody(
     *         description="update closet settings",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="name",
     *                     example="",
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
    public function updateCloset(Request $request, $reference)
    {
        $requestData = $request->all();
        $response = [];
        $validator = Validator::make($requestData, Customer::getValidationRules('update-closet',$requestData));

        if( $validator->fails() )
        {
            return ApiResponseHandler::validationError($validator->errors());
        }

        $customerId = $requestData['customer_id'];
        $customer = Customer::findById($customerId);

        $customerCloset = $customer->closet;

        if(empty($customerCloset)) {
            return ApiResponseHandler::failure("Unable to find closet.");
        }
        if(!empty($customerCloset->closet_reference) && $customerCloset->closet_reference != $reference) {
            return ApiResponseHandler::failure("Unauthorized closet update action triggered.");
        }

        if(empty($customerCloset->logo)) {
            $requestData['logo'] = $customerCloset->logo;
        }
        if(empty($customerCloset->banner)) {
            $requestData['banner'] = $customerCloset->banner;
        }

        $closet = Closet::updateCloset($reference, $requestData);

        $response['closet_ref'] = $closet->closet_reference;
        $response['closet'] = [
            'name' => $closet->closet_name,
            'closet_ref' => $closet->closet_reference,
            'email' => $closet->customer->email,
            'logo' => $closet->logo,
            'banner' => $closet->banner,
            'description' => $closet->about_closet,
        ];
        return ApiResponseHandler::success($response, "Your settings have been updated as per your preferences.");
    }

}