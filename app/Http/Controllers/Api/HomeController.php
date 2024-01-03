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
use App\Models\Closet;
use App\Models\Customer;
use App\Models\PimBrand;
use App\Models\PimBsCategory;
use App\Models\PimProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

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
                'home' => [
                    'title' => "BUY. SELL.DO IT ALL OVER.",
                    'sub_title' => "Welcome to the community-powered circular fashion marketplace.",
                ],
                'brands' => [
                    'title' => "RELAX & GET THE PRODUCT BY TOP BRANDS",
                    'sub_title' => "In today's modern world, Household Shopping can be an extreme sport when you are making a list to grab things from physical stores therefore.",
                ],
                'subscription' => [
                    'title' => "SUBSCRIBE US",
                    'sub_title' => "We will not share your email with anyone else.",
                ],
                'sellers_watch' => [
                    'list' => $this->getRecommendedSellerClosets(),
                ],
                'banners' => $this->getCachedBanner(),
                'cities' => $this->getCachedCities(),
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
                'brands' => $this->getRecommendedStoreBrands(),
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


    /**
     * @OA\Get(
     *
     *     path="/api/mega-menu",
     *     tags={"HomePage"},
     *     summary="Get mega menu content",
     *     operationId="getMegaMenu",
     *
     *     @OA\Response(response=200,description="Success"),
     *
     * )
     */

    public function getMegaMenu()
    {
        try {
            $response = [
                'menu' => PimBsCategory::getAllMenuItems(),
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
                return PimBsCategory::getAllFeaturedCategories();
//        });
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
        return [
            [
                'index' => 1,
                'image' => URL::asset('assets/brands/1.png'),
            ],
            [
                'index' => 2,
                'image' => URL::asset('assets/brands/2.png'),
            ],
            [
                'index' => 3,
                'image' => URL::asset('assets/brands/3.png'),
            ],
            [
                'index' => 4,
                'image' => URL::asset('assets/brands/4.png'),
            ],
            [
                'index' => 5,
                'image' => URL::asset('assets/brands/5.png'),
            ],
            [
                'index' => 6,
                'image' => URL::asset('assets/brands/6.png'),
            ],
            [
                'index' => 7,
                'image' => URL::asset('assets/brands/7.png'),
            ]
        ];
//        });
    }

    private static function getRecommendedStoreBrands()
    {
        return PimBrand::select('id', 'name', 'icon')
//            ->where('closet_id', Constant::No)
            ->where('status', Constant::Yes)
            ->take(8)
            ->get();
    }

    private static function getRecommendedSellerClosets()
    {
        return Closet::getClosetListing(7, Constant::PJ_CLOSETS_LIST_TYPES['Trending'], true);
    }

    public function getCachedCities()
    {
        return [
            [
                "Lahore",
            ],
            [
                "Islamabad"
            ],
            [
                'Karachi',
            ]
        ];
    }

    public function getCachedFeaturedCategoryProducts($type)
    {
        $listType = Constant::PJ_PRODUCT_LIST['FEATURED_PRODUCTS'];
        $listOptionsSectionA = [
            'limit_record' => 8
        ];
//        $cacheKey = 'get_featured_categories';
//        return Cache::remember($cacheKey, env('CACHE_REMEMBER_SECONDS'), function () {
            return  PimProduct::getProductsForApp($listType, 8, $listOptionsSectionA, true);
//        });
    }
}