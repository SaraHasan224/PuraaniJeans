<?php
namespace App\Http\Controllers\Api\Closet;


use App\Helpers\ApiResponseHandler;
use App\Helpers\AppException;
use App\Helpers\Constant;
use App\Http\Controllers\Controller;
use App\Models\Closet;
use App\Models\MerchantStore;
use App\Models\PimCategory;
use App\Models\PimProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class ClosetProductsController extends Controller
{


    /**
     * @OA\Post(
     *
     *     path="/api/filter/closet/{slug}/product",
     *     tags={"Closet"},
     *     summary="Store Products",
     *     operationId="getFilteredClosetProducts",
     *
     *     @OA\Response(response=200,description="Success"),
     *
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         description="store slug",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\RequestBody(
     *         description="Resend OTP",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="filters",
     *                     description="Network Id of customer's phone number",
     *                     type="object",
     *                     example=Null
     *                 )
     *              )
     *         )
     *     ),
     *     security={
     *          {"user_access_token": {}, "locale": {}}
     *     }
     * )
     */
    public function getFilteredClosetProducts(Request $request, $closetRef)
    {
        try
        {
            $requestData = $request->all();
            $response = [];
            $requestData['store_slug'] = $closetRef;
            $validator = Validator::make($requestData, MerchantStore::$validationRules['store']);

            if ($validator->fails())
            {
                return ApiResponseHandler::validationError($validator->errors());
            }
            $store = self::getCachedStore($closetRef);
            $validator = Validator::make($requestData, PimProduct::getValidationRules('filters',$requestData));

            if( $validator->fails() )
            {
                return ApiResponseHandler::validationError($validator->errors());
            }

            $filterData = $requestData['filters'];

            if($store) {
                if(!($store->merchant->canPlaceOrder( env('UNIVERSAL_CHECKOUT_INTEGRATION_TYPE')))){
                    return ApiResponseHandler::failure( __('messages.general.merchant_not_found'), '', ['not_found' => Constant::Yes] );
                }
                $listType = Constant::PJ_PRODUCT_LIST['CLOSET_PRODUCTS'];
                $shipment = $store->merchant->getDefaultShipmentMethodsExceptBykea();
                $listOptions = [
                    'store' => $store,
                    'filters' => $filterData,
                    'store_disabled' => empty($shipment) ? Constant::Yes : Constant::No,
                    'shipment_error' => empty($shipment) ? 'Delivery not available in your region.' : ''
                ];
                $perPage = 12;
                $response = PimProduct::getProductsForApp($listType, $perPage, $listOptions);
                return ApiResponseHandler::success($response, __('messages.general.success'));
            }
            return ApiResponseHandler::failure( "Store not found", '', ['not_found' => Constant::Yes] );
        }
        catch (\Exception $e)
        {
            AppException::log($e);
            return ApiResponseHandler::failure(__('messages.general.failure'), $e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *
     *     path="/api/closet/{slug}/product",
     *     tags={"Closet"},
     *     summary="Store Products",
     *     operationId="getClosetProducts",
     *
     *     @OA\Response(response=200,description="Success"),
     *
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         description="store slug",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *
     *     security={
     *          {"user_access_token": {}, "locale": {}}
     *     }
     * )
     */
    public function getClosetProducts(Request $request, $closetRef)
    {
        try
        {
            $response = [];
            $requestData['store_slug'] = $closetRef;
            $validator = Validator::make($requestData, MerchantStore::$validationRules['store']);

            if ($validator->fails())
            {
                return ApiResponseHandler::validationError($validator->errors());
            }

            $store = self::getCachedStore($closetRef);
            if($store) {
                if(!($store->merchant->canPlaceOrder( env('UNIVERSAL_CHECKOUT_INTEGRATION_TYPE')))){
                    return ApiResponseHandler::failure( __('messages.general.merchant_not_found'), '', ['not_found' => Constant::Yes] );
                }

                $shipment = $store->merchant->getDefaultShipmentMethodsExceptBykea();
                $response = self::getCachedClosetProducts($request, $store, $shipment);
                return ApiResponseHandler::success($response, __('messages.general.success'));
            }
            return ApiResponseHandler::failure( "Store not found", '', ['not_found' => Constant::Yes] );
        }
        catch (\Exception $e)
        {
            AppException::log($e);
            return ApiResponseHandler::failure(__('messages.general.failure'), $e->getMessage());
        }
    }

    public function getCachedStore($closetRef){
        $cacheKey = 'get_store_'.$closetRef;
        return Cache::remember($cacheKey, env('CACHE_REMEMBER_SECONDS'), function () use ($closetRef) {
            return Closet::getByStoreSlug($closetRef);
        });
    }


    public function getCachedClosetProducts($request, $store, $shipment){
        $page = $request->input('page') ?? 1;
        $perPage = 12;
        $cacheKey = 'get_store_'.$store->store_slug.'_all_products_'.$page.'_'.$perPage;
        return Cache::remember($cacheKey, env('CACHE_REMEMBER_SECONDS'), function () use ($perPage, $store, $shipment) {
            $listType = Constant::PJ_PRODUCT_LIST['CLOSET_PRODUCTS'];
            $listOptions = [
                'store' => $store,
                'store_disabled' => empty($shipment) ? Constant::Yes : Constant::No,
                'shipment_error' => empty($shipment) ? 'Delivery not available in your region.' : ''
            ];

            return PimProduct::getProductsForApp($listType, $perPage, $listOptions);
        });
    }


    /**
     * @OA\Get(
     *
     *     path="/api/closet/{slug}/category/{catSlug}/product",
     *     tags={"Closet"},
     *     summary="Manage Closet Category Products",
     *     operationId="getClosetCategoryProducts",
     *
     *     @OA\Response(response=200,description="Success"),
     *
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         description="category slug",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="catSlug",
     *         in="path",
     *         description="category slug",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *
     *     security={
     *          {"user_access_token": {}, "locale": {}}
     *     }
     * )
     */
    public function getClosetCategoryProducts(Request $request, $closetRef, $catSlug)
    {
        try
        {
            $response = [];
            $requestData = [];
            $requestData['store_slug'] = $closetRef;
            $requestData['category_slug'] = $catSlug;
            $validator = Validator::make($requestData, MerchantStore::$validationRules['storeCategories']);

            if ($validator->fails())
            {
                return ApiResponseHandler::validationError($validator->errors());
            }
            $store = self::getCachedStore($closetRef);
            if($store) {
                if(!($store->merchant->canPlaceOrder( env('UNIVERSAL_CHECKOUT_INTEGRATION_TYPE')))){
                    return ApiResponseHandler::failure( __('messages.general.merchant_not_found'), '', ['not_found' => Constant::Yes] );
                }
                $category = self::getCachedCategory($catSlug,$store->id);
                if(empty($category)){
                    return ApiResponseHandler::failure( __('messages.app.stores.products.category.failure'), '', ['not_found' => Constant::Yes] );
                }
                $response['products'] = self::getCachedStoreCategoryProducts($request, $store, $category);
                return ApiResponseHandler::success($response, __('messages.app.stores.products.category.success'));
            }
            return ApiResponseHandler::failure( "Store not found", '', ['not_found' => Constant::Yes] );
        }
        catch (\Exception $e)
        {
            AppException::log($e);
            return ApiResponseHandler::failure( __('messages.app.stores.products.category.failure'), $e->getMessage(), ['not_found' => Constant::Yes] );

        }
    }


    public function getCachedCategory($catSlug, $storeId){
        $cacheKey = 'get_store_'.$storeId.'_category_'.$catSlug;
        return Cache::remember($cacheKey, env('CACHE_REMEMBER_SECONDS'), function () use ($catSlug, $storeId) {
            return PimCategory::getClosetCategoryByCategoryRef($catSlug, $storeId);
        });
    }

    public function getCachedStoreCategoryProducts($request, $store, $category){
        $page = $request->input('page') ?? 1;
        $perPage = 12;
        $cacheKey = 'get_store_category_products_'.$store->store_slug.'_'.$page.'_'.$perPage;
        return Cache::remember($cacheKey, env('CACHE_REMEMBER_SECONDS'), function () use ($perPage, $store, $category) {
            $listType = Constant::PJ_PRODUCT_LIST['CLOSET_PRODUCTS'];
            $listOptions = [
                'store' => $store,
                'categoryId' => $category->id,
            ];
            return PimProduct::getProductsForApp($listType, $perPage, $listOptions);
        });
    }


}
