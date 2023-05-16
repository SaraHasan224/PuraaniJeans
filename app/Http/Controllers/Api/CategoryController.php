<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponseHandler;
use App\Helpers\AppException;
use App\Helpers\Constant;
use App\Models\PimBsCategory;
use App\Models\PimBsCategoryMapping;
use App\Models\PimProduct;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Validator;

class CategoryController
{
    /**
     * @OA\Get(
     *
     *     path="/v1/portal/categories",
     *     tags={"Customer Portal"},
     *     summary="Get Categories List",
     *     operationId="getCategoriesList",
     *
     *     @OA\Response(response=200,description="Success"),
     *
     *     security={
     *          {"user_access_token": {}, "locale": {}}
     *     }
     * )
     */

    public function getCategories()
    {
        try
        {
            $cacheKey = 'get_all_categories';
            $categories = Cache::remember($cacheKey, env('CACHE_REMEMBER_SECONDS'), function () {
                return PimBsCategory::getCategories();
            });

            if($categories){
                $response = [
                    'categories' => $categories
                ];
                return ApiResponseHandler::success( $response, __('messages.general.success'));
            } else {
                return ApiResponseHandler::failure( 'Category not found', '', ['not_found' => Constant::Yes] );
            }
        }
        catch( \Exception $e )
        {
            AppException::log($e);
            return ApiResponseHandler::failure( __('messages.general.failed'), $e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *
     *     path="/v1/portal/categories/{slug}",
     *     tags={"Customer Portal"},
     *     summary="Get Sub Categories List",
     *     operationId="getSubCategoriesList",
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
     *
     *     security={
     *          {"user_access_token": {}, "locale": {}}
     *     }
     * )
     */

    public function getSubCategories($parentSlug)
    {
        try
        {
            $category = $this->getCachedCategory( $parentSlug );
            $parentCategory = [
              'id'      => null,
              'name'    => null,
              'slug'    => null,
            ];

            if($category)
            {
                $categoryDetail = [
                  'id'      => $category->id,
                  'name'    => $category->name,
                  'slug'    => $category->slug,
                ];

                if (!empty($category->parent)){
                    $parentCategory['id']= $category->parent->id;
                    $parentCategory['name']= $category->parent->name;
                    $parentCategory['slug']= $category->parent->slug;
                }

                $response = [
                    'sub_categories' => $this->getCachedSubCategories( $category ),
                    'category'       => $categoryDetail,
                    'parent_category'=> $parentCategory,
                ];

                return ApiResponseHandler::success( $response, __('messages.general.success') );
            }
            else
            {
                return ApiResponseHandler::failure(__('messages.portal.category.not_found'));
            }
        }
        catch( \Exception $e )
        {
            AppException::log($e);
            return ApiResponseHandler::failure( __('messages.general.failed'), $e->getMessage() );
        }
    }

    public function getCachedCategory( $categorySlug )
    {
        $cacheKey = 'get_category_'.$categorySlug;
        return Cache::remember($cacheKey, env('CACHE_REMEMBER_SECONDS'), function () use ($categorySlug) {
            return PimBsCategory::getCategoryBySlug( $categorySlug );
        });
    }

    public function getCachedSubCategories( $category )
    {
        $cacheKey = 'get_subcategories_'.$category->slug;
        return Cache::remember($cacheKey, env('CACHE_REMEMBER_SECONDS'), function () use ($category) {
            return PimBsCategory::getCategories( $category->id );
        });
    }


    /**
     * @OA\Get(
     *
     *     path="/v1/portal/categories/{slug}/products",
     *     tags={"Customer Portal"},
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
     *     ),
     *
     *     security={
     *          {"user_access_token": {}, "locale": {}}
     *     }
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

    public function getCachedSubCategoriesIds( $categorySlug )
    {
        $cacheKey = 'get_all_subcategory_ids_'.$categorySlug;
        return Cache::remember($cacheKey, env('CACHE_REMEMBER_SECONDS'), function () use ($categorySlug) {
            return PimBsCategory::getAllSubCategoryIds( $categorySlug );
        });
    }

    public function getCachedPimCategoryProductIds( $categorySlug, $merchantCategoryIds )
    {
        $cacheKey = 'get_products_'.$categorySlug;
        return Cache::remember($cacheKey, env('CACHE_REMEMBER_SECONDS'), function () use ($merchantCategoryIds) {
            return PimProduct::getPimCategoryProductIds($merchantCategoryIds);
        });
    }

    public function getCachedProducts( $request, $categorySlug, $merchantCategoryIds, $productIds )
    {
        $page = $request->input('page') ?? 1;
        $perPage = 10;
        $cacheKey = 'get_products_'.$categorySlug.'_'.$page.'_'.$perPage;
        return Cache::remember($cacheKey, env('CACHE_REMEMBER_SECONDS'), function () use ($merchantCategoryIds, $perPage, $categorySlug, $productIds) {
            $listType = Constant::CUSTOMER_APP_PRODUCT_LISTING['CATEGORY_PRODUCTS'];
            $listOptions = [
                "categoryIds" => $merchantCategoryIds,
                "bsCategorySlug" => $categorySlug,
                'filter_by_product_ids' => $productIds
            ];
            return PimProduct::getProductsForCustomerPortal($listType, $perPage, $listOptions);
        });
    }

    public function getCachedMerchantCategories( $categorySlug, $bSecureCategoryIds )
    {
        $cacheKey = 'bsecure_categories_mapped_'.$categorySlug;
        return Cache::remember($cacheKey, env('CACHE_REMEMBER_SECONDS'), function () use ($bSecureCategoryIds) {
            return PimBsCategoryMapping::getAllMerchantCategoryIds( $bSecureCategoryIds );
        });
    }




    /**
     * @OA\Post(
     *
     *     path="/v1/portal/filter/categories/{slug}/products",
     *     tags={"Customer Portal"},
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
            $bSecureCategoryIds = $this->getCachedSubCategoriesIds( $categorySlug );

            if( $bSecureCategoryIds )
            {
                $merchantCategoryIds = $this->getCachedMerchantCategories( $categorySlug, $bSecureCategoryIds );
                $validator = Validator::make($requestData, PimProduct::getValidationRules('filters',$requestData));

                if( $validator->fails() )
                {
                    return ApiResponseHandler::validationError($validator->errors());
                }

                $listType = Constant::CUSTOMER_APP_PRODUCT_LISTING['CATEGORY_PRODUCTS'];
                $listOptions = [
                    "categoryIds" => $merchantCategoryIds,
                    "bsCategorySlug" => $categorySlug,
                    'filters' => $filterData,
                    'filter_by_product_ids' => $this->getCachedPimCategoryProductIds( $categorySlug, $merchantCategoryIds )
                ];
                $response = PimProduct::getProductsForCustomerPortal($listType, 15, $listOptions);

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
