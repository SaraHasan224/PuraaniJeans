<?php

namespace App\Http\Controllers\Api\Products;

use App\Helpers\ApiResponseHandler;
use App\Helpers\AppException;
use App\Helpers\Constant;
use App\Http\Controllers\Controller;
use App\Models\CustomerProductRecentlyViewed;
use App\Models\PimBsCategory;
use App\Models\PimBsCategoryMapping;
use App\Models\PimProduct;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;

class CategoryProductController extends Controller
{


    /**
     * @OA\Get(
     *
     *     path="/api/categories/{slug}/products",
     *     tags={"Categories"},
     *     summary="Get Category Products",
     *     operationId="getCategoryProducts",
     *
     *     @OA\Response(response=200,description="Success"),
     *
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         description="category slug",
     *         required=true,
     *         @OA\Schema(type="string")
     *     )
     * )
     */

    public function getProducts(Request $request, $categorySlug)
    {
        try
        {
            $bSecureCategoryIds = $this->getCachedSubCategoriesIds( $categorySlug );
            if( $bSecureCategoryIds )
            {
                $merchantCategoryIds = $this->getCachedMerchantCategories( $categorySlug, $bSecureCategoryIds );
                $productIds = $this->getCachedPimCategoryProductIds( $categorySlug, $merchantCategoryIds );
                $response = $this->getCachedProducts( $request, $categorySlug, $merchantCategoryIds, $productIds );

                return ApiResponseHandler::success( $response, __('messages.general.success') );
            }

            return ApiResponseHandler::failure( 'Category not found', '', ['not_found' => Constant::Yes] );
        }
        catch( \Exception $e )
        {
            AppException::log($e);
            return ApiResponseHandler::failure( __('messages.general.failed'), $e->getMessage() );
        }
    }

    public function getCachedMerchantCategories( $categorySlug, $bSecureCategoryIds )
    {
//        $cacheKey = 'bsecure_categories_mapped_'.$categorySlug;
//        return Cache::remember($cacheKey, env('CACHE_REMEMBER_SECONDS'), function () use ($bSecureCategoryIds) {
        return PimBsCategoryMapping::getAllMerchantCategoryIds( $bSecureCategoryIds );
//        });
    }

    public function getCachedSubCategoriesIds( $categorySlug )
    {
//        $cacheKey = 'get_all_subcategory_ids_'.$categorySlug;
//        return Cache::remember($cacheKey, env('CACHE_REMEMBER_SECONDS'), function () use ($categorySlug) {
        return PimBsCategory    ::getAllSubCategoryIds( $categorySlug );
//        });
    }

    public function getCachedPimCategoryProductIds( $categorySlug, $merchantCategoryIds )
    {
//        $cacheKey = 'get_products_'.$categorySlug;
//        return Cache::remember($cacheKey, env('CACHE_REMEMBER_SECONDS'), function () use ($merchantCategoryIds) {
        return PimProduct::getPimCategoryProductIds($merchantCategoryIds);
//        });
    }

    public function getCachedProducts( $request, $categorySlug, $merchantCategoryIds, $productIds )
    {
        $page = $request->input('page') ?? 1;
        $perPage = 10;
        $cacheKey = 'get_products_'.$categorySlug.'_'.$page.'_'.$perPage;
//        return Cache::remember($cacheKey, env('CACHE_REMEMBER_SECONDS'), function () use ($merchantCategoryIds, $perPage, $categorySlug, $productIds) {
        $listType = Constant::PJ_PRODUCT_LIST['CATEGORY_PRODUCTS'];
        $listOptions = [
            "categoryIds" => $merchantCategoryIds,
            "bsCategorySlug" => $categorySlug,
            'filter_by_product_ids' => $productIds,
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
        return PimProduct::getProductsForApp($listType, $perPage, $listOptions);
//        });
    }


    /**
     * @OA\Post(
     *
     *     path="/api/filter/categories/{slug}/products",
     *     tags={"Categories"},
     *     summary="Get Filtered Category Products",
     *     operationId="getFilteredCategoryProducts",
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
     *     *
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
     *                         property="records_range",
     *                         description="Record Range",
     *                         type="object",
     *                         @OA\Property(
     *                              property="show_count",
     *                              description="24",
     *                              type="string",
     *                          )
     *                      ),
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
     *                         property="sort_by",
     *                         description="Sort by",
     *                         type="object",
     *                         @OA\Property(
     *                              property="newest_arrival",
     *                              description="1",
     *                              type="integer",
     *                          ),
     *                         @OA\Property(
     *                              property="featured",
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

    public function getFilteredCategoryProducts(Request $request, $categorySlug)
    {
        try
        {
            $requestData = $request->all();
            $validator = Validator::make($requestData, PimProduct::getValidationRules('filters',$requestData));

            if( $validator->fails() )
            {
                return ApiResponseHandler::validationError($validator->errors());
            }

            $filterData = $requestData['filters'];
            $showCount = array_key_exists("filters", $requestData) && !empty($requestData['filters'])&& !empty($requestData['filters']['show_count']) ? $requestData['filters']['show_count'] : 15;
            $bSecureCategoryIds = $this->getCachedSubCategoriesIds( $categorySlug );

            if( $bSecureCategoryIds )
            {
                $merchantCategoryIds = $this->getCachedMerchantCategories( $categorySlug, $bSecureCategoryIds );
                $validator = Validator::make($requestData, PimProduct::getValidationRules('filters',$requestData));

                if( $validator->fails() )
                {
                    return ApiResponseHandler::validationError($validator->errors());
                }
                $listType = Constant::PJ_PRODUCT_LIST['CATEGORY_PRODUCTS'];
                $listOptions = [
                    "categoryIds" => $merchantCategoryIds,
                    "bsCategorySlug" => $categorySlug,
                    'filters' => $filterData,
                    'filter_by_product_ids' => $this->getCachedPimCategoryProductIds( $categorySlug, $merchantCategoryIds )
                ];
                $response = PimProduct::getProductsForApp($listType, $showCount, $listOptions);

                return ApiResponseHandler::success( $response, __('messages.general.success') );
            }

            return ApiResponseHandler::failure( 'Category not found', '', ['not_found' => Constant::Yes] );
        }
        catch( \Exception $e )
        {
            AppException::log($e);
            return ApiResponseHandler::failure( __('messages.general.failed'), $e->getMessage() );
        }
    }
}
