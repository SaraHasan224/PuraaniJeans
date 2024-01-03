<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponseHandler;
use App\Helpers\AppException;
use App\Helpers\Constant;
use App\Http\Controllers\Controller;
use App\Models\CustomerProductRecentlyViewed;
use App\Models\PimBrand;
use App\Models\PimBsCategory;
use App\Models\PimProduct;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Validator;

class ProductController extends Controller
{

    /**
     * @OA\Get(
     *
     *     path="/api/products",
     *     tags={"Products"},
     *     summary="Get All Products",
     *     operationId="getProducts",
     *
     *     @OA\Response(response=200,description="Success"),
     *
     * )
     */

    public function getProducts(Request $request)
    {
        try {
            $page = $request->input('page') ?? 1;
            $perPage = 10;
            $listType = Constant::PJ_PRODUCT_LIST['ALL_PRODUCTS'];
            $listOptions = [
                'filters' => [
                    'sort_by' =>  [
                        'newest_arrival' => 1,
                        'featured' => 0,
                        'price_high_to_low' => 0,
                        'price_low_to_high' => 0,
                    ],
                    'price_range' => [
                        "max" => '',
                        "min" => 0
                    ]
                ]
            ];
            $response = PimProduct::getProductsForApp($listType, $perPage, $listOptions);

            return ApiResponseHandler::success($response, __('messages.general.success'));
        } catch (\Exception $e) {
            AppException::log($e);
            return ApiResponseHandler::failure(__('messages.general.failed'), $e->getMessage());
        }
    }

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
                if (array_key_exists("customer_id", $requestData) && !empty($requestData['customer_id'])) {
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
