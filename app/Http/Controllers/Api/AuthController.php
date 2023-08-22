<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponseHandler;
use App\Helpers\Constant;
use App\Http\Controllers\Controller;
use App\Models\AccessToken;
use App\Models\Closet;
use App\Models\Country;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use function Ramsey\Uuid\v4;

class AuthController extends Controller
{

    /**
     * @OA\Post(
     * path="/api/register",
     * operationId="Register",
     * tags={"Register"},
     * summary="User Register",
     * description="User Register here",
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"country","email_address","first_name","last_name", "password", "password_confirmation"},
     *               @OA\Property(property="country", type="numeric"),
     *               @OA\Property(property="email_address", type="email"),
     *               @OA\Property(property="first_name", type="text"),
     *               @OA\Property(property="last_name", type="text"),
     *               @OA\Property(property="password", type="password"),
     *               @OA\Property(property="password_confirmation", type="password"),
     *               @OA\Property(property="subscription", type="boolean"),
     *            ),
     *        ),
     *    ),
     *      @OA\Response(
     *          response=201,
     *          description="Register Successfully",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="Register Successfully",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function register(Request $request)
    {
        $requestData = $request->all();
        $response = [];
        $validator = Validator::make($requestData, Customer::$validationRules['register']);

        if ($validator->fails()) {
            return ApiResponseHandler::validationError($validator->errors());
        }

        $requestData['password'] = Hash::make($requestData['password']);
        $requestData['country_id'] = Country::getCountryByCountryCode($requestData['country'], true)->id;
        $requestData['remember_token'] = Str::random(10);
        $identifier = v4();
        $customer = Customer::createCustomer($requestData, $identifier);


        $response['token'] =  $customer->createToken($identifier, ['customer'])->accessToken;
        $response['customer'] = [
            'first_name' => $customer->first_name,
            'last_name' => $customer->last_name,
            'email' => $customer->email,
            'country_code' => $customer->country_code,
            'phone_number' => $customer->phone_number,
            'country_id' => $customer->country_id,
            'identifier' => $customer->identifier,
        ];
        return ApiResponseHandler::success($response,"You have successfully registered to ".env('APP_NAME').".");
    }
    /**
     * @OA\Post(
     * path="/api/login",
     * operationId="authLogin",
     * tags={"Login"},
     * summary="User Login",
     * description="Login User Here",
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"email", "password"},
     *               @OA\Property(property="email", type="email"),
     *               @OA\Property(property="password", type="password")
     *            ),
     *        ),
     *    ),
     *      @OA\Response(
     *          response=201,
     *          description="Register Successfully",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="Register Successfully",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function login(Request $request)
    {
        $requestData = $request->all();
        $response = [];
        $error_body = [];

        $validator = Validator::make($requestData, Customer::$validationRules['login']);

        if ($validator->fails()) {
            return ApiResponseHandler::validationError($validator->errors());
        }
        $customer = Customer::findByEmail($requestData['email_address']);
        if (empty($customer)) {
            return ApiResponseHandler::failure(__('Customer not found'));
        }
        if (!empty($customer) && $customer->status == Constant::CUSTOMER_STATUS['Blocked']) {
            return ApiResponseHandler::failure(__('Customer blocked'));
        }else {
            if (Hash::check($requestData['password'], $customer->password)) {
                $closet = Closet::findByCustomerId($customer->id);
                AccessToken::revokeOldTokensByName($customer->identifier);
                $response['token'] = $customer->createToken($customer->identifier, ['customer'])->accessToken;
                $response['screen'] = $this->appScreen($customer);
                $response['customer'] = [
                    'first_name' => $customer->first_name,
                    'last_name' => $customer->last_name,
                    'email' => $customer->email,
                    'country_code' => $customer->country_code,
                    'phone_number' => $customer->phone_number,
                    'country_id' => $customer->country_id,
                    'identifier' => $customer->identifier,
                    'closet_ref' => optional($closet)->closet_reference,
                    'closet' => $closet,
                ];
                return ApiResponseHandler::success($response, "You have successfully registered to " . env('APP_NAME') . ".");
            }else {
                return ApiResponseHandler::failure("Incorrect password");
            }
        }

    }

    private function appScreen($customer) {
        if(empty($customer->country_code) && empty($customer->country_code)) {
            return "phone";
        }else if(empty($customer->phone_verified_at)) {
            return "otp";
        }
        return "login";
    }
}
