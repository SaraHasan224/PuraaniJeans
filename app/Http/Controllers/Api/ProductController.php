<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponseHandler;
use App\Helpers\AppException;
use App\Helpers\Constant;
use App\Http\Controllers\Controller;
use App\Models\CustomerProductRecentlyViewed;
use App\Models\PimProduct;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;

class ProductController extends Controller
{

    /**
     * @OA\Post(
     *
     *     path="/api/product/{productId}",
     *     tags={"Products"},
     *     summary="Product detail",
     *     operationId="getProductDetail",
     *
     *     @OA\Response(response=200,description="Success"),
     *
     *     @OA\Parameter(
     *         name="productId",
     *         in="path",
     *         description="product id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         description="Get product detail",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="referrer_type",
     *                     example="Featured Product",
     *                     description="referrer type",
     *                     type="string"
     *                 )
     *              )
     *         )
     *     ),
     * )
     */

    public function getProductDetail(Request $request, $productHandle)
    {
        try {
            $response = [];
            $requestData = $request->all();

            $products = PimProduct::getByHandle($productHandle);
            if (!empty($products)) {
                $requestData['product_id'] = $products->id;
                if(array_key_exists("customer_id", $requestData) && !empty($requestData['customer_id'])) {
                    CustomerProductRecentlyViewed::viewProduct($requestData);
                }
                $result = PimProduct::getProductDetail($productHandle);

                if (!empty($result)) {
                    $response = $result;
                    return ApiResponseHandler::success($response, __('messages.general.success'));
                } else {
                    return ApiResponseHandler::failure('Product not found', '', ['not_found' => Constant::Yes]);
                }
            } else {
                return ApiResponseHandler::failure('Product not found', '', ['not_found' => Constant::Yes]);
            }
        } catch (\Exception $e) {
            AppException::log($e);
            return ApiResponseHandler::failure(__('messages.general.failed'), $e->getMessage());
        }
    }
}
