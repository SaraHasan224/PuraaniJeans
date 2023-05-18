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
     *                 ),
     *                 @OA\Property(
     *                     property="customer_address_id",
     *                     description="customer address id",
     *                     type="integer"
     *                 ),
     *              )
     *         )
     *     ),
     *
     *     security={
     *          {"user_access_token": {}, "locale": {}}
     *     }
     * )
     */

    public function getProductDetail(Request $request, $productId)
    {
        try {
            $response = [];
            $requestData = $request->all();
            $requestData['product_id'] = (int)$productId;
            $requestData['actions_allowed'] = Constant::CUSTOMER_APP_PRODUCT_LISTING;
            $validator = Validator::make($requestData, PimProduct::getValidationRules('product-detail', $requestData));

            if ($validator->fails()) {
                return ApiResponseHandler::validationError($validator->errors());
            }

            $productId = $requestData['product_id'];
            $productListingType = array_key_exists('listing_type', $requestData) ? $requestData['listing_type'] : "";
            $productStoreSlug = array_key_exists('slug', $requestData) ? $requestData['slug'] : "";

            $products = PimProduct::getById($productId);
            if (!empty($products)) {
//                CustomerProductRecentlyViewed::viewProduct($requestData);
                $result = $this->getCachedProductDetail($productId);
                $productStore = $products->store;
                $productMerchant = $products->merchant;

                if (!empty($result)) {
                    $response = $result['product'];
                    $response['shipment'] = [
                        'name' => '',
                        'charge' => 0,
                    ];
                    $response['shipment_available'] = Constant::No;
                    $response['product_unavailable'] = Constant::No;
                    $response['product_unavailable_msg'] = '';

                    $shipment = [];
                    $allow = Constant::Yes;
                    if(!empty($result['product']) && !empty($shipment)){
                        $response['shipment'] = [
                            'name' => optional($shipment)->name,
                            'charge' => empty($shipment) ? 0 : $shipment->updated_charges,
                        ];
                        $response['shipment_available'] = $allow;
                        if(!$allow){
                            $response['product_unavailable'] = !$allow ? Constant::Yes : Constant::No;
                            $response['product_unavailable_msg'] = 'Product not available on your selected address';
                        }
                    }elseif(empty($shipment)) {
                        $response['product_unavailable'] = Constant::Yes;
                        $response['product_unavailable_msg'] = 'Delivery not available in your region.';
                    }
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

    public function getCachedCampaignProductDetail($productId, $productListingType, $productStoreSlug)
    {
        $cacheKey = 'get_campaign_'.$productStoreSlug.'_product_detail_' . $productId;
//        return Cache::remember($cacheKey, env('CACHE_REMEMBER_SECONDS'), function () use ($productId, $productListingType, $productStoreSlug) {
        return PimProduct::getProductDetailForCustomerPortal($productId, $productListingType, $productStoreSlug);
//        });
    }

    public function getCachedProductDetail($productId)
    {
        $cacheKey = 'get_product_detail_' . $productId;
//        return Cache::remember($cacheKey, env('CACHE_REMEMBER_SECONDS'), function () use ($productId) {
        return PimProduct::getProductDetailForCustomerPortal($productId);
//        });
    }

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
            $listType = Constant::CUSTOMER_APP_PRODUCT_LISTING['FEATURED_PRODUCTS'];
            $listOptions = [];
            $response = PimProduct::getProductsForCustomerPortal($listType, 10, $listOptions);

            return ApiResponseHandler::success($response, __('messages.general.success'));
        } catch (\Exception $e) {
            AppException::log($e);
            return ApiResponseHandler::failure(__('messages.general.failed'), $e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *
     *     path="/api/recently-viewed/products",
     *     tags={"Products"},
     *     summary="Get customer's recently viewed products",
     *     operationId="getRecentlyViewedProducts",
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
     *     security={
     *          {"user_access_token": {}, "locale": {}}
     *     }
     * )
     */

    public function getRecentlyViewedProducts(Request $request)
    {
        try {
            $requestData = $request->all();
            $customerId = $requestData['customer_id'];
            $productId = array_key_exists('product_id', $requestData) ? $requestData['product_id'] : "";
            $response['products'] = $this->getCachedRecentlyViewedProducts($customerId, $productId);

            return ApiResponseHandler::success($response, __('messages.general.success'));
        } catch (\Exception $e) {
            AppException::log($e);
            return ApiResponseHandler::failure(__('messages.general.failed'), $e->getMessage());
        }
    }

    public function getCachedRecentlyViewedProducts($customerId, $productId)
    {
        $listType = Constant::CUSTOMER_APP_PRODUCT_LISTING['RECENTLY_VIEWED_PRODUCTS'];
        $listOptions = [
            'limit_record' => 5,
            'customer_id' => $customerId,
            'exclude_product' => $productId
        ];
        return PimProduct::getProductsForCustomerPortal($listType, 5, $listOptions, true);
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

            $listType = Constant::CUSTOMER_APP_PRODUCT_LISTING['FEATURED_PRODUCTS'];

            $response = PimProduct::getProductsForCustomerPortal($listType, 50, $listOptions);

            return ApiResponseHandler::success($response, __('messages.general.success'));
        } catch (\Exception $e) {
            AppException::log($e);
            return ApiResponseHandler::failure(__('messages.general.failed'), $e->getMessage());
        }
    }
}
