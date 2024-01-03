<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponseHandler;
use App\Helpers\AppException;
use App\Helpers\Constant;
use App\Http\Controllers\Controller;
use App\Models\PimBsCategory;
use App\Models\PimBsCategoryMapping;
use App\Models\PimProduct;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * @OA\Get(
     *
     *     path="/api/categories",
     *     tags={"Categories"},
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
     *     path="/api/categories/{slug}",
     *     tags={"Categories"},
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
            $category = PimBsCategory::getCategoryBySlug( $parentSlug );
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
                    'sub_categories' => PimBsCategory::getCategories( $category->id ),
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

}
