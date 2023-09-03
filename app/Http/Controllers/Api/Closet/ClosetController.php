<?php
/**
 * Created by PhpStorm.
 * User: Sara
 * Date: 6/28/2023
 * Time: 12:05 AM
 */

namespace App\Http\Controllers\Api\Closet;


use App\Helpers\ApiResponseHandler;
use App\Helpers\AppException;
use App\Helpers\Constant;
use App\Models\Closet;
use App\Models\Country;
use App\Models\Customer;
use App\Models\PimCategory;
use App\Models\PimProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use function Ramsey\Uuid\v4;

class ClosetController
{
    /**
     * @OA\Post(
     *
     *     path="/api/closets",
     *     tags={"Closet"},
     *     summary="Manage Closet",
     *     operationId="getAllClosets",
     *
     *     @OA\Response(response=200,description="Success"),
     *
     * )
     */
    public function getAllClosets(Request $request)
    {
        try
        {
            $response = [];
            $response['closets'] = self::getCachedClosetsList($request);
            return ApiResponseHandler::success($response, __('messages.general.success'));
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
     *     path="/api/closets/trending",
     *     tags={"Closet"},
     *     summary="Manage Closet",
     *     operationId="getAllTrendingClosets",
     *
     *     @OA\Response(response=200,description="Success"),
     * )
     */
    public function getAllTrendingClosets(Request $request)
    {
        try
        {
            $response = [];
            $response['closets'] = self::getCachedClosetsList($request, Constant::PJ_CLOSETS_LIST_TYPES['Trending']);
            return ApiResponseHandler::success($response, __('messages.general.success'));
        }
        catch (\Exception $e)
        {
            AppException::log($e);
            return ApiResponseHandler::failure(__('messages.general.failure'), $e->getMessage());
        }
    }

    public function getCachedClosetsList($request, $type = Constant::PJ_CLOSETS_LIST_TYPES['All']){
        $page = $request->input('page') ?? 1;
        $perPage = 20;
        $cacheType = "all_closets";
//        $cacheKey = 'get_app_'.$cacheType;
//        return Cache::remember($cacheKey, env('CACHE_REMEMBER_SECONDS'), function () use($type, $perPage) {
        return Closet::getClosetListing($perPage, $type);
//        });
    }

    /**
     * @OA\Post(
     *     path="/api/closet/{reference}",
     *     tags={"Closet"},
     *     summary="get closet details",
     *     operationId="getClosetDetails",
     *
     *     @OA\Response(response=200,description="Success"),
     * )
     */
    public function getClosetDetails(Request $request, $reference)
    {
        $requestData = $request->all();
        $result = [];
        $closet = Closet::findByReference($reference);

        if(!$closet) {
            return ApiResponseHandler::failure(__('Closet not found'));
        }else {
            $result['trending_products'] = self::getCachedTrendingProducts($closet);
            $result['recent_orders'] = self::getCachedRecentClosetOrders($closet);
            $result['all_products'] = self::getCachedAllClosetProducts($request, $closet);
            $result['all_orders'] = self::getCachedAllClosetOrders($closet);
            $result['categories'] = PimCategory::getClosetCategory($closet->id);

            $response = self::getCachedClosetConfig($closet, $result);
            return ApiResponseHandler::success($response);
        }
    }

    private function getCachedClosetConfig($closet, $response) {
        $response['closet_ref'] = $closet->closet_reference;
        $response['closet'] = [
            'name' => $closet->closet_name,
            'logo' => $closet->logo,
            'banner' => $closet->banner,
            'description' => $closet->about_closet,
            'closet_ref' => $closet->closet_reference,
            'email' => $closet->customer->email,
        ];
        return $response;
    }

    private function getCachedTrendingProducts($closet){
        $listType = Constant::PJ_PRODUCT_LIST['CLOSET_TRENDING_PRODUCTS'];
        $listOptions = [
            'closet' => $closet,
            'limit_record' => 10
        ];
        $perPage = 10;
        return PimProduct::getProductsForApp($listType, $perPage, $listOptions, true);
    }

    private function getCachedAllClosetProducts($request, $closet){
        $page = $request->input('page') ?? 1;
        $perPage = 10;
        $listType = Constant::PJ_PRODUCT_LIST['CLOSET_PRODUCTS'];
        $listOptions = [
            'closet' => $closet,
            'page' => $page,
        ];
        return PimProduct::getProductsForApp($listType, $perPage, $listOptions);
    }

    private function getCachedAllClosetOrders($closet){
        $listType = Constant::PJ_ORDER_LIST['CLOSET_ORDERS'];
        $listOptions = [
            'closet' => $closet,
            'limit_record' => 10
        ];
        $perPage = 10;
        return []; //Closet::getProductsForApp($listType, $perPage, $listOptions, true);
    }

    private function getCachedRecentClosetOrders($closet){
        $listType = Constant::PJ_ORDER_LIST['CLOSET_ORDERS'];
        $listOptions = [
            'closet' => $closet,
            'limit_record' => 10
        ];
        $perPage = 10;
        return []; //Closet::getProductsForApp($listType, $perPage, $listOptions, true);
    }

    /**
     * @OA\Post(
     *
     *     path="/api/closet/image-upload",
     *     tags={"Closet"},
     *     summary="Closet Icon and Banner Avatar",
     *     operationId="imageUpload",
     *
     *     @OA\Response(response=200,description="Success"),
     *
     *     @OA\RequestBody(
     *         description="Icon and Banner Upload for a Closet",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="banner",
     *                     example="base64:",
     *                     description="base64 ",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="icon",
     *                     example="base64:",
     *                     description="base64 ",
     *                     type="string"
     *                 )
     *              )
     *         )
     *     )
     * )
     */

    public function imageUpload(Request $request)
    {
        try {

            // Check if the incoming request is valid...
            (object)$requestData = $request->all();
            // Check if the incoming request is valid...
            $validator = Validator::make($requestData, Closet::$validationRules['image-upload']);

            if ($validator->fails())
            {
                return ApiResponseHandler::validationError($validator->errors());
            }
            $customer = Customer::findById($requestData['customer_id']);

            $filename = trim($customer->request_id) .".png";
            $imagePath = 'storage/images/closets/' .$customer->closet->id. '/'.$filename;

            $img = Image::make($requestData['image'])->resize(350,350);
            $img->save($imagePath);


            $customer->image = $filename;
            $customer->save();

            DB::commit();
            return ApiResponseHandler::success($customer, __('messages.general.success'));
        } catch (\Exception $e) {
            AppException::log($e);
            DB::rollBack();
            return ApiResponseHandler::failure(__('messages.general.failure'), $e->getMessage());
        }
    }


    /**
     * @OA\Get(
     *
     *     path="/api/closet/{slug}/category/{catSlug}",
     *     tags={"Closet"},
     *     summary="Manage Closet Category Products",
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
     * )
     */
    public function getClosetCategory(Request $request, $closetRef, $catSlug)
    {
        try
        {
            $result = [
                'products' => [],
                'category' => []
            ];
            $requestData['category_slug'] = $catSlug;
            $requestData['closet_ref'] = $closetRef;
            $validator = Validator::make($requestData, Closet::$validationRules['closet_categories']);

            if ($validator->fails())
            {
                return ApiResponseHandler::validationError($validator->errors());
            }
            $closet = Closet::findByReference($closetRef);
            if($closet) {
                $category = PimCategory::getClosetCategoryByCategoryRef($catSlug,$closet->id);
                if(empty($category)){
                    return ApiResponseHandler::failure( __('messages.app.stores.products.category.failure'), '', ['not_found' => Constant::Yes] );
                }
                $result['products'] = self::getCachedCategoryClosetProducts($request, $closet, $category);
                $result['category'] = [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->pim_cat_reference,
                    'category_banner' => $category->image,
                    'description' => $category->description,
                ];

                $response = self::getCachedClosetConfig($closet, $result);
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

    private function getCachedCategoryClosetProducts($request, $closet, $category){
        $page = $request->input('page') ?? 1;
        $perPage = 10;
        $listType = Constant::PJ_PRODUCT_LIST['CLOSET_CATEGORY_PRODUCTS'];
        $listOptions = [
            'closet' => $closet,
            'category' => $category,
            'page' => $page,
        ];
        return PimProduct::getProductsForApp($listType, $perPage, $listOptions);
    }

}