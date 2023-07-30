<?php
/**
 * Created by PhpStorm.
 * User: Sara
 * Date: 6/28/2023
 * Time: 12:05 AM
 */

namespace App\Http\Controllers\Api;


use App\Helpers\ApiResponseHandler;
use App\Models\Closet;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController
{
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
        $closet = Closet::createCloset($requestData);

        $response['closet_ref'] = $closet->closet_reference;
        $response['closet'] = [
            'name' => $closet->name,
            'closet_ref' => $closet->closet_reference,
            'email' => $closet->customer->email,
        ];
        return ApiResponseHandler::success($response, "You have successfully registered to " . env('APP_NAME') . ".");
    }

}