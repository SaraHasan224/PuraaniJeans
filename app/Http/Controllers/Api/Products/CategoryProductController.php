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
            $requestData = $request->all();
            if($categorySlug == "all") {
                $result = self::getCachedAllCategoriesProducts( $request, $requestData );
                return ApiResponseHandler::success( $result, __('messages.general.success') );
            }else {
                $bSecureCategoryIds = PimBsCategory::getAllSubCategoryIds( $categorySlug );
//                return $bSecureCategoryIds; //25:62556_silk
                if( $bSecureCategoryIds )
                {
                    $merchantCategoryIds = PimBsCategoryMapping::getAllMappedCategoryIds( $bSecureCategoryIds );
                    $productIds = $this->getCachedPimCategoryProductIds( $categorySlug, $merchantCategoryIds ); //8,9,10,11
//                return $productIds;

                    $response = $this->getCachedProducts( $request, $categorySlug, $merchantCategoryIds, $productIds, $requestData );

                    return ApiResponseHandler::success( $response, __('messages.general.success') );
                }

            }
            return ApiResponseHandler::failure( 'Category not found', '', ['not_found' => Constant::Yes] );
        }
        catch( \Exception $e )
        {
            AppException::log($e);
            return ApiResponseHandler::failure( __('messages.general.failed'), $e->getMessage() );
        }
    }


    public function getCachedPimCategoryProductIds( $categorySlug, $merchantCategoryIds )
    {
//        $cacheKey = 'get_products_'.$categorySlug;
//        return Cache::remember($cacheKey, env('CACHE_REMEMBER_SECONDS'), function () use ($merchantCategoryIds) {
        return PimProduct::getPimCategoryProductIds($merchantCategoryIds);
//        });
    }

    public function getCachedAllCategoriesProducts( $request, $requestData )
    {
        $page = $request->input('page') ?? 1;
        $perPage = 10;
        $listType = Constant::PJ_PRODUCT_LIST['ALL_PRODUCTS'];
        $listOptions = [
            'filters' => [
                'records_range' => [
                    "show_count" =>  !empty($filterData) && array_key_exists("records_range", $filterData) && !empty($filterData['records_range'])? $filterData['records_range']['show_count'] : 24
                ],
                'sort_by' =>  [
                    'newest_arrival' => !empty($filterData) ? $filterData['sort_by']['newest_arrival'] : 1,
                    'featured' => !empty($filterData) ? $filterData['sort_by']['featured'] : 0,
                    'price_high_to_low' => !empty($filterData) ? $filterData['sort_by']['price_high_to_low'] : 0,
                    'price_low_to_high' => !empty($filterData) ? $filterData['sort_by']['price_low_to_high'] : 0,
                ],
                'price_range' => [
                    "max" => !empty($filterData) ? $filterData['price_range']['max'] : -1,
                    "min" => !empty($filterData) ? $filterData['price_range']['min'] : 0
                ],
                "categories"=> !empty($filterData) ? $filterData['categories'] : "",
                "brands" => !empty($filterData) ? $filterData['brands'] : "",
                "condition" => !empty($filterData) ? $filterData['condition'] : "",
                "size" => !empty($filterData) ? $filterData['size'] : "",
                "standard" => !empty($filterData) ? $filterData['standard'] : "",
                "color" => !empty($filterData) ? $filterData['color'] : ""
            ]
        ];
        return PimProduct::getProductsForApp($listType, $perPage, $listOptions);
    }
    public function getCachedProducts( $request, $categorySlug, $merchantCategoryIds, $productIds, $filterData )
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
                'records_range' => [
                    "show_count" =>  !empty($filterData) && array_key_exists("records_range", $filterData) && !empty($filterData['records_range']) ? $filterData['records_range']['show_count'] : 24
                ],
                'sort_by' =>  [
                    'newest_arrival' => !empty($filterData) && array_key_exists("sort_by", $filterData) && !empty($filterData['sort_by'])  ? $filterData['sort_by']['newest_arrival'] : 1,
                    'featured' => !empty($filterData) && array_key_exists("sort_by", $filterData) && !empty($filterData['sort_by'])  ? $filterData['sort_by']['featured'] : 0,
                    'price_high_to_low' => !empty($filterData) && array_key_exists("sort_by", $filterData) && !empty($filterData['sort_by'])  ? $filterData['sort_by']['price_high_to_low'] : 0,
                    'price_low_to_high' => !empty($filterData) && array_key_exists("sort_by", $filterData) && !empty($filterData['sort_by'])  ? $filterData['sort_by']['price_low_to_high'] : 0,
                ],
                'price_range' => [
                    "max" => !empty($filterData) && array_key_exists("price_range", $filterData) && !empty($filterData['price_range'])  ? $filterData['price_range']['max'] : -1,
                    "min" => !empty($filterData) && array_key_exists("price_range", $filterData) && !empty($filterData['price_range'])  ? $filterData['price_range']['min'] : 0
                ],
                "categories"=> !empty($filterData) && array_key_exists("categories", $filterData) && !empty($filterData['categories'])   ? $filterData['categories'] : "",
                "brands" => !empty($filterData) && array_key_exists("brands", $filterData) && !empty($filterData['brands'])   ? $filterData['brands'] : "",
                "condition" => !empty($filterData) && array_key_exists("condition", $filterData) && !empty($filterData['condition'])   ? $filterData['condition'] : "",
                "size" => !empty($filterData) && array_key_exists("size", $filterData) && !empty($filterData['size'])   ? $filterData['size'] : "",
                "standard" => !empty($filterData) && array_key_exists("standard", $filterData) && !empty($filterData['standard'])   ? $filterData['standard'] : "",
                "color" => !empty($filterData) && array_key_exists("color", $filterData) && !empty($filterData['color'])   ? $filterData['color'] : ""
            ]
        ];
        return PimProduct::getProductsForApp($listType, $perPage, $listOptions);
//        });
    }
}
