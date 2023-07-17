<?php
/**
 * Created by PhpStorm.
 * User: Sara
 * Date: 5/24/2023
 * Time: 9:13 PM
 */

namespace App\Http\Controllers\Api;


use App\Helpers\ApiResponseHandler;
use App\Helpers\AppException;
use App\Helpers\Constant;
use App\Helpers\Helper;
use App\Helpers\ImageUpload;
use App\Models\PimBrand;
use App\Models\PimBsCategory;
use App\Models\PimCategory;
use App\Models\PimProduct;
use App\Models\PimProductCategory;
use App\Models\PimProductImage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use function Ramsey\Uuid\v4;

class HomeController
{

    /**
     * @OA\Get(
     *
     *     path="/api/meta-data",
     *     tags={"HomePage"},
     *     summary="Get meta data content",
     *     operationId="getMetaContent",
     *
     *     @OA\Response(response=200,description="Success"),
     *
     * )
     */

    public function getMetaContent()
    {
        try {
            $response = [
                'metadata' => [
                    'app_name' => 'PuraniJeans',
                    'app_title' => 'PuraniJeans - Your shopping partner',
                    'favicon' => URL::asset('assets/logo/favicon.png'),
                    'logo' => URL::asset('assets/logo/logo.png'),
                    'logo_white' => URL::asset('assets/logo/logo-bg-white.png'),
                    'banner_background' => URL::asset('assets/banners/backgrounds/home-bg-1.png')
                ],
                'banners' => $this->getCachedBanner(),
                'auth_banners' => $this->getCachedBanner("auth"),
            ];
            return ApiResponseHandler::success($response, __('messages.general.success'));
        } catch (\Exception $e) {
            AppException::log($e);
            return ApiResponseHandler::failure(__('messages.general.failed'), $e->getMessage());
        }
    }
    /**
     * @OA\Get(
     *
     *     path="/api/homepage",
     *     tags={"HomePage"},
     *     summary="Get Homepage content",
     *     operationId="getHomePageContent",
     *
     *     @OA\Response(response=200,description="Success"),
     *
     * )
     */

    public function getHomePageContent()
    {
        try {
            $response = [
                'recommended' => $this->getCachedRecommendedProducts(),
                'brands' => $this->getCachedBrands(),
            ];
            return ApiResponseHandler::success($response, __('messages.general.success'));
        } catch (\Exception $e) {
            AppException::log($e);
            return ApiResponseHandler::failure(__('messages.general.failed'), $e->getTraceAsString());
        }
    }

    /**
     * @OA\Get(
     *
     *     path="/api/homepage/featured-section",
     *     tags={"HomePage"},
     *     summary="Get Homepage featured content",
     *     operationId="getHomePageFeaturedContent",
     *
     *     @OA\Response(response=200,description="Success"),
     *
     * )
     */

    public function getHomePageFeaturedContent()
    {
        try {
            $response = [
                'featured_by' => [
                    "sections" => [
                        [
                            "title" => 'popular',
                            "data" => $this->getCachedFeaturedCategoryProducts("popular")
                        ],
                        [
                            "title" => 'top searches',
                            "data" => $this->getCachedFeaturedCategoryProducts("top_searches")
                        ],
                        [
                            "title" => 'recommended',
                            "data" => $this->getCachedFeaturedCategoryProducts("recommended")
                        ],
                        [
                            "title" => 'best sellers',
                            "data" => $this->getCachedFeaturedCategoryProducts("best_sellers")
                        ],
                        [
                            "title" => 'by brands',
                            "data" => $this->getCachedFeaturedCategoryProducts("by_brands")
                        ],
                    ],

                ],
            ];
            return ApiResponseHandler::success($response, __('messages.general.success'));
        } catch (\Exception $e) {
            AppException::log($e);
            return ApiResponseHandler::failure(__('messages.general.failed'), $e->getMessage());
        }
    }

    public function getCachedBanner($type = "general")
    {
        $cacheKey = 'get_app_banners';
//        return Cache::remember($cacheKey, env('CACHE_REMEMBER_SECONDS'), function () {
            if($type == "auth")
                return self::getAuthBanners();
            else
                return self::getBanners();
//        });
    }

    private static function getBanners()
    {
        return [
            [
                'index' => 1,
                'image' => URL::asset('assets/banners/primary_banners/1.png'),
                'text' => 'Shoes',
                'product_count' => 15,
                'is_centered' => Constant::No,
            ],
            [
                'index' => 2,
                'image' => URL::asset('assets/banners/primary_banners/2.png'),
                'text' => 'Watches',
                'product_count' => 20,
                'is_centered' => Constant::No,
            ],
            [
                'index' => 3,
                'image' => URL::asset('assets/banners/primary_banners/3.png'),
                'text' => 'Mens Wear',
                'product_count' => 70,
                'is_centered' => Constant::Yes,
            ],
            [
                'index' => 4,
                'image' => URL::asset('assets/banners/primary_banners/4.png'),
                'is_centered' => Constant::No,
                'text' => 'Beauty',
                'product_count' => 15,
            ],
            [
                'index' => 5,
                'image' => URL::asset('assets/banners/primary_banners/5.png'),
                'is_centered' => Constant::No,
                'text' => 'Hand Bags',
                'product_count' => 15,
            ],
        ];
    }

    private static function getAuthBanners()
    {
        return [
            [
                'index' => 1,
                'image' => URL::asset('assets/banners/auth_banners/1.png'),
            ],
            [
                'index' => 2,
                'image' => URL::asset('assets/banners/auth_banners/2.png'),
            ],
            [
                'index' => 3,
                'image' => URL::asset('assets/banners/auth_banners/3.png'),
            ]
        ];
    }

    public function getCachedRecommendedProducts()
    {
        $cacheKey = 'get_app_recommended_products';
//        return Cache::remember($cacheKey, env('CACHE_REMEMBER_SECONDS'), function () {
        return self::getRecommendedItems();
//        });
    }

    private static function getRecommendedItems()
    {
        return PimProduct::where('is_recommended', Constant::Yes)
            ->select('id', 'name', 'handle', 'short_description')
            ->with(['defaultImage:id,product_id,url,position'])
            ->where('status', Constant::Yes)
            ->orderBy('recommended_position', 'ASC')
            ->take(3)
            ->get()
            ->map(function ($item) {
                $item['image'] = $item->defaultImage->url;
                unset($item->id);
                unset($item->defaultImage);
                return $item;
            });
    }

    public function getCachedBrands()
    {
        $cacheKey = 'get_app_brands';
//        return Cache::remember($cacheKey, env('CACHE_REMEMBER_SECONDS'), function () {
        return self::getRecommendedBrands();
//        });
    }

    private static function getRecommendedBrands()
    {
        return PimBrand::where('closet_id', Constant::No)
            ->select('name', 'icon')
            ->where('status', Constant::Yes)
            ->take(8)
            ->get();
    }

    public function getCachedFeaturedCategoryProducts($type)
    {
        $listType = Constant::CUSTOMER_APP_PRODUCT_LISTING['FEATURED_PRODUCTS'];
        $listOptionsSectionA = [
            'limit_record' => 8
        ];
//        $cacheKey = 'get_featured_categories';
//        return Cache::remember($cacheKey, env('CACHE_REMEMBER_SECONDS'), function () {
            return  PimProduct::getProductsForApp($listType, 8, $listOptionsSectionA, true);
//        });
    }
}