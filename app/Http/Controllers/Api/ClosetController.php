<?php
namespace App\Http\Controllers\Api;


use App\Helpers\ApiResponseHandler;
use App\Helpers\AppException;
use App\Helpers\Constant;
use App\Http\Controllers\Controller;
use App\Models\MerchantStore;
use App\Models\PimCategory;
use App\Models\PimProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class ClosetController extends Controller
{
    /**
     * @OA\Get(
     *
     *     path="/api/closets/get-all/{type}",
     *     tags={"Closet"},
     *     summary="Manage Store",
     *     operationId="getAllClosets",
     *
     *     @OA\Response(response=200,description="Success"),
     *
     *     @OA\Parameter(
     *         name="type",
     *         in="path",
     *         description="type",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *
     *     security={
     *          {"user_access_token": {}, "locale": {}}
     *     }
     * )
     */
    public function getAllClosets(Request $request, $type)
    {
        try
        {
            $response = [];
            $storeTypes = [ Constant::CUSTOMER_APP_STORE_LISTING_TYPE['Featured'], Constant::CUSTOMER_APP_STORE_LISTING_TYPE['All'] ];
            if (!in_array($type, $storeTypes))
            {
                $errorResponse = 'Invalid store type requested';
                return ApiResponseHandler::failure( $errorResponse );
            }

            $store = self::getCachedFeaturedStores($request, $type);
            if(!empty($store)){
                $response['stores'] = $store;
                return ApiResponseHandler::success($response, __('messages.general.success'));
            } else {
                return ApiResponseHandler::failure( 'Store not found', '', ['not_found' => Constant::Yes] );
            }
        }
        catch (\Exception $e)
        {
            AppException::log($e);
            return ApiResponseHandler::failure(__('messages.general.failure'), $e->getMessage());
        }
    }

    public function getCachedFeaturedStores($request, $type){
        $page = $request->input('page') ?? 1;
        $perPage = 20;
        if($type == Constant::CUSTOMER_APP_STORE_LISTING_TYPE['Featured']){
            $cacheType = "featured_stores";
        }else {
            $cacheType = "is_bshop_enabled_stores".'_'.$page.'_'.$perPage;
        }
        $cacheKey = 'get_app_'.$cacheType;
        return Cache::remember($cacheKey, env('CACHE_REMEMBER_SECONDS'), function () use($type, $perPage) {
            return MerchantStore::getStoresListing($type, $perPage);
        });
    }

    /**
     * @OA\Get(
     *
     *     path="/api/closet/{slug}",
     *     tags={"Closet"},
     *     summary="Manage Store",
     *     operationId="getStore",
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
    public function getCloset(Request $request, $storeSlug)
    {
        try
        {
            $response = [
                'products' => [],
                'metadata' => [],
                'catalog_settings' => [],
                'categories' => [],
            ];
            $requestData['store_slug'] = $storeSlug;
            $validator = Validator::make($requestData, MerchantStore::$validationRules['store']);

            if ($validator->fails())
            {
                return ApiResponseHandler::validationError($validator->errors());
            }
            $store = self::getCachedStore($storeSlug);
            if($store) {
                if(!($store->merchant->canPlaceOrder( env('UNIVERSAL_CHECKOUT_INTEGRATION_TYPE')))){
                    return ApiResponseHandler::failure(__('messages.general.merchant_not_found'), "", ['not_found' => Constant::Yes]);
                }
                $response = self::getCachedStoreConfig($store);
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
    public function getFilteredClosetProducts(Request $request, $storeSlug)
    {
        try
        {
            $requestData = $request->all();
            $response = [];
            $requestData['store_slug'] = $storeSlug;
            $validator = Validator::make($requestData, MerchantStore::$validationRules['store']);

            if ($validator->fails())
            {
                return ApiResponseHandler::validationError($validator->errors());
            }
            $store = self::getCachedStore($storeSlug);
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
                $listType = Constant::CUSTOMER_APP_PRODUCT_LISTING['STORES_PRODUCTS'];
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
    public function getClosetProducts(Request $request, $storeSlug)
    {
        try
        {
            $response = [];
            $requestData['store_slug'] = $storeSlug;
            $validator = Validator::make($requestData, MerchantStore::$validationRules['store']);

            if ($validator->fails())
            {
                return ApiResponseHandler::validationError($validator->errors());
            }

            $store = self::getCachedStore($storeSlug);
            if($store) {
                if(!($store->merchant->canPlaceOrder( env('UNIVERSAL_CHECKOUT_INTEGRATION_TYPE')))){
                    return ApiResponseHandler::failure( __('messages.general.merchant_not_found'), '', ['not_found' => Constant::Yes] );
                }

                $shipment = $store->merchant->getDefaultShipmentMethodsExceptBykea();
                $response = self::getCachedStoreProducts($request, $store, $shipment);
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

    public function getCachedStore($storeSlug){
        $cacheKey = 'get_store_'.$storeSlug;
        return Cache::remember($cacheKey, env('CACHE_REMEMBER_SECONDS'), function () use ($storeSlug) {
            return MerchantStore::getByStoreSlug($storeSlug);
        });
    }

    public function getCachedStoreConfig($store){
        $cacheKey = 'get_store_configuration_'.$store->store_slug;
        return Cache::remember($cacheKey, env('CACHE_REMEMBER_SECONDS'), function () use ($store) {
            return [
                'metadata'                  => MerchantStore::__getMetaData($store),
                'catalog_settings'          => [],
                'categories'                => [],
//            'categories'                => self::getMerchantPimAvailableCategories($store->merchant_id, $store),
            ];
        });
    }

    public function getCachedStoreProducts($request, $store, $shipment){
        $page = $request->input('page') ?? 1;
        $perPage = 12;
        $cacheKey = 'get_store_'.$store->store_slug.'_all_products_'.$page.'_'.$perPage;
        return Cache::remember($cacheKey, env('CACHE_REMEMBER_SECONDS'), function () use ($perPage, $store, $shipment) {
            $listType = Constant::CUSTOMER_APP_PRODUCT_LISTING['STORES_PRODUCTS'];
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
     *     path="/api/closet/{slug}/category/{catSlug}",
     *     tags={"Closet"},
     *     summary="Manage Store Category Products",
     *     operationId="getStoreCategories",
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
    public function getClosetCategory(Request $request, $storeSlug, $catSlug)
    {
        try
        {
            $response = [
                'products' => [],
                'category' => []
            ];
            $requestData['store_slug'] = $storeSlug;
            $requestData['category_slug'] = $catSlug;
            $validator = Validator::make($requestData, MerchantStore::$validationRules['storeCategories']);

            if ($validator->fails())
            {
                return ApiResponseHandler::validationError($validator->errors());
            }
            $store = self::getCachedStore($storeSlug);
            if($store) {
                if(!($store->merchant->canPlaceOrder( env('UNIVERSAL_CHECKOUT_INTEGRATION_TYPE')))){
                    return ApiResponseHandler::failure( __('messages.general.merchant_not_found'), '', ['not_found' => Constant::Yes] );
                }
                $category = self::getCachedCategory($catSlug,$store->id);
                if(empty($category)){
                    return ApiResponseHandler::failure( __('messages.app.stores.products.category.failure'), '', ['not_found' => Constant::Yes] );
                }
                $response = self::getCachedStoreConfig($store);
                $response['products'] = self::getCachedStoreCategoryProducts($request, $store, $category);
                $response['category'] = [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->handle,
                    'category_banner' => $category->image,
                    'description' => $category->description,
                ];
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


    /**
     * @OA\Get(
     *
     *     path="/api/closet/{slug}/category/{catSlug}/product",
     *     tags={"Closet"},
     *     summary="Manage Store Category Products",
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
    public function getClosetCategoryProducts(Request $request, $storeSlug, $catSlug)
    {
        try
        {
            $response = [];
            $requestData = [];
            $requestData['store_slug'] = $storeSlug;
            $requestData['category_slug'] = $catSlug;
            $validator = Validator::make($requestData, MerchantStore::$validationRules['storeCategories']);

            if ($validator->fails())
            {
                return ApiResponseHandler::validationError($validator->errors());
            }
            $store = self::getCachedStore($storeSlug);
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
            return PimCategory::getStoreCategoryBySlug($catSlug, $storeId);
        });
    }

    public function getCachedStoreCategoryProducts($request, $store, $category){
        $page = $request->input('page') ?? 1;
        $perPage = 12;
        $cacheKey = 'get_store_category_products_'.$store->store_slug.'_'.$page.'_'.$perPage;
        return Cache::remember($cacheKey, env('CACHE_REMEMBER_SECONDS'), function () use ($perPage, $store, $category) {
            $listType = Constant::CUSTOMER_APP_PRODUCT_LISTING['STORES_PRODUCTS'];
            $listOptions = [
                'store' => $store,
                'categoryId' => $category->id,
            ];
            return PimProduct::getProductsForApp($listType, $perPage, $listOptions);
        });
    }


}