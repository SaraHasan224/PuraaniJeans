<?php

namespace App\Http\Controllers\Api\Products;

use App\Helpers\ApiResponseHandler;
use App\Helpers\AppException;
use App\Helpers\Constant;
use App\Http\Controllers\Controller;
use App\Models\CustomerProductRecentlyViewed;
use App\Models\PimProduct;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;

class FeaturedProductController extends Controller
{
    /**
     * @OA\Get(
     *
     *     path="/api/featured-products",
     *     tags={"Products"},
     *     summary="Get Homepage featured products",
     *     operationId="getFeaturedProducts",
     *
     *     @OA\Response(response=200,description="Success"),
     *
     *     security={
     *          {"user_access_token": {}, "locale": {}}
     *     }
     * )
     */

    public function getFeaturedProducts(Request $request)
    {
        try {
            $listType = Constant::PJ_PRODUCT_LIST['FEATURED_PRODUCTS'];
            $listOptions = [];
            $response = PimProduct::getProductsForApp($listType, 10, $listOptions);

            return ApiResponseHandler::success($response, __('messages.general.success'));
        } catch (\Exception $e) {
            AppException::log($e);
            return ApiResponseHandler::failure(__('messages.general.failed'), $e->getMessage());
        }
    }


    /**
     * @OA\Post(
     *
     *     path="/api/filter/featured-products",
     *     tags={"Products"},
     *     summary="Get Homepage featured products",
     *     operationId="getFilteredFeaturedProducts",
     *
     *     @OA\Response(response=200,description="Success"),
     *
     *     @OA\RequestBody(
     *         description="Set Order Shipment Details",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                  @OA\Property(
     *                     property="filters",
     *                     description="filters",
     *                     type="object",
     *                      @OA\Property(
     *                         property="price_range",
     *                         description="Price Range",
     *                         type="object",
     *                         @OA\Property(
     *                              property="min",
     *                              description="1",
     *                              type="string",
     *                          ),
     *                         @OA\Property(
     *                              property="max",
     *                              description="1",
     *                              type="string",
     *                          )
     *                      ),
     *                      @OA\Property(
     *                         property="store_slug",
     *                         description="Filter by store slug",
     *                         type="string"
     *                      ),
     *                      @OA\Property(
     *                         property="sort_by",
     *                         description="Sort by",
     *                         type="object",
     *                         @OA\Property(
     *                              property="featured",
     *                              description="1",
     *                              type="integer",
     *                          ),
     *                         @OA\Property(
     *                              property="newest_arrival",
     *                              description="1",
     *                              type="integer",
     *                          ),
     *                         @OA\Property(
     *                              property="price_high_to_low",
     *                              description="1",
     *                              type="integer",
     *                          ),
     *                         @OA\Property(
     *                              property="price_low_to_high",
     *                              description="0",
     *                              type="integer",
     *                        )
     *                      ),
     *                  ),
     *              )
     *         )
     *     ),
     *
     *     security={
     *          {"user_access_token": {}, "locale": {}}
     *     }
     * )
     */

    public function getFilteredFeaturedProducts(Request $request)
    {
        try {
            $requestData = $request->all();
            $validator = Validator::make($requestData, PimProduct::getValidationRules('filters',$requestData));

            if( $validator->fails() )
            {
                return ApiResponseHandler::validationError($validator->errors());
            }

            $filterData = $requestData['filters'];
            $listOptions = [
                'filters' => $filterData
            ];

            $listType = Constant::PJ_PRODUCT_LIST['FEATURED_PRODUCTS'];

            $response = PimProduct::getProductsForApp($listType, 50, $listOptions);

            return ApiResponseHandler::success($response, __('messages.general.success'));
        } catch (\Exception $e) {
            AppException::log($e);
            return ApiResponseHandler::failure(__('messages.general.failed'), $e->getMessage());
        }
    }
}
