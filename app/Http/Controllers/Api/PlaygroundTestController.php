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
use App\Helpers\CloudinaryUpload;
use App\Helpers\Constant;
use App\Helpers\Helper;
use App\Helpers\ImageUpload;
use App\Models\Closet;
use App\Models\PimAttribute;
use App\Models\PimAttributeOption;
use App\Models\PimBrand;
use App\Models\PimBsCategory;
use App\Models\PimCategory;
use App\Models\PimProduct;
use App\Models\PimProductAttribute;
use App\Models\PimProductAttributeOption;
use App\Models\PimProductCategory;
use App\Models\PimProductImage;
use App\Models\PimProductVariant;
use App\Models\PimProductVariantOption;
use Carbon\Carbon;
use Cloudinary\Cloudinary;
use Cloudinary\Transformation\Resize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use function Ramsey\Uuid\v4;

class PlaygroundTestController
{

    /**
     * @OA\Post(
     *
     *     path="/api/cloudinary/image-upload-test",
     *     tags={"Playground"},
     *     summary="Get Homepage content",
     *     operationId="uploadImageToCloudinary",
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
     *                     property="image",
     *                     description="filters",
     *                     type="string"
     *                  ),
     *              )
     *         )
     *     ),
     * )
     */

    public function uploadImageToCloudinary(Request $request)
    {
//        $result = CloudinaryUpload::uploadFile("assets/brands/3.png", "assets/brands/brand4");
//        if(empty($result)) {
//            return "Error in upload assets to cloudinary";
//        }
//        return optional($result)->url;
//    }
            try {
                DB::table('pim_bs_categories')->truncate();
                $data = [
                    [
                        'name' => "Mens Wear",
                        'is_featured' => Constant::Yes,
                        'is_featured_weight' => 100,
                        'bs_image' => URL::asset('assets/banners/primary_banners/3.png'),
                        'options' => [
                            "Shirts" => [
                                "Shirts",
                                "T-Shirt",
                                "Polos",
                            ],
                            "Active Wear" => [
                                "Active Wear",
                                "Trousers",
                                "Shorts",
                                "Jeans",
                            ],
                            "Winter" => [
                                "Coats | Blazers",
                                "Sweaters | Cardigans",
                                "SweatShirts | Cardigans",
                                "SweatShirts | Hoodies",
                                "Jackets | Over Shirts",
                            ],
                            "Accessories" => [
                                "Shoes"
                            ],
                        ]
                    ],
                    [
                        'name' => "Women",
                        'is_featured' => Constant::Yes,
                        'is_featured_weight' => 96,
                        'bs_image' => URL::asset('assets/banners/primary_banners/5.png'),
                        'options' => [
                            "Fabric" => [
                                "Summer 23",
                                "Formal",
                                "Lawn",
                                "Cambric",
                                "Silk",
                            ],
                            "Pret" => [
                                "Casual",
                                "Semi Formal",
                                "Formal",
                                "Daily Wear",
                                "Co-ords",
                                "Summer",
                                "Winters",
                            ],
                            "Western" => [
                                "Shirts | Blouses",
                                "T-Shirts",
                                "Activewear",
                                "Skirts",
                                "Trousers",
                                "Jeans",
                                "Dresses | Jumpsuits",
                            ],
                            "Winter" => [
                                "SweatShirts | Hoodies",
                                "SweatShirts | Cardigans",
                                "Jackets",
                                "Coats | Blazers",
                            ],
                            "Accessories" => [
                                "Shoes",
                                "Dupatta",
                                "Shawl",
                                "Sleepwear",
                            ],
                        ],
                    ],
                    [
                        'name' => "Jewelery",
                        'bs_image' => URL::asset('assets/banners/primary_banners/1.png'),
                        'is_featured' => Constant::Yes,
                        'is_featured_weight' => 99,
                        'options' => [
                            "Earrings",
                            "Bracelets",
                            "Sets",
                            "Rings",
                            "Pendants",
                            "Anklets",
                            "Jhoomar",
                            "Necklace",
                            "Bracelets",
                            "Nose ring",
                            "Jhumka",
                            "Men",
                        ]
                    ],
                    [
                        'name' => "Beauty",
                        'bs_image' => URL::asset('assets/banners/primary_banners/4.png'),
                        'is_featured' => Constant::Yes,
                        'is_featured_weight' => 97,
                        'options' => [
                            "The Ordinary",
                            "Tiam",
                            "Yuja Niacin",
                            "Garnier",
                            "Bioderma",
                            "Dr Rashel",
                            "Hira Ali Beauty",
                            "Loreal Professional",
                        ]
                    ],
                    [
                        'name' => "Watches",
                        'bs_image' => URL::asset('assets/banners/primary_banners/2.png'),
                        'is_featured' => Constant::Yes,
                        'is_featured_weight' => 98,
                    ],
                    [
                        'name' => "More",
                        'is_featured' => Constant::No,
                        'is_featured_weight' => 0,
                    ],
                ];

                foreach ($data as $key => $d) {
                    $position = $key+1;
                    $path = "images/categories";
                    $image = array_key_exists("bs_image", $d) ? ImageUpload::downloadExternalFile($path, $d['bs_image'], $d['name'] . "-" . strtotime("now")) : "";
                    $isFeatured = array_key_exists("is_featured", $d) ? $d['is_featured'] : "";
                    $isFeaturedWeight = array_key_exists("is_featured_weight", $d) ? $d['is_featured_weight'] : "";
                    $attr = self::createCategory($d['name'], Constant::No, $image, $position, $isFeatured, $isFeaturedWeight);
                    if(array_key_exists('options',$d)) {
                        foreach ($d['options'] as $key2 => $sd) {
                            if(is_array($sd)) {
                                $name = $key2;
                                $position = 1+$key;
                                $subAttr = self::createCategory($name, $attr->id, '', $position);
                                foreach ($sd as $key3 => $s) {
                                    $name = $s;
                                    $position = 1+$key+$key3;
                                    self::createCategory($name, $subAttr->id, '', $position);
                                }
                            }else {
                                $name = $sd;
                                $position = $key2+1+$key;
                                self::createCategory($name, $attr->id, '', $position);
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                AppException::log($e);
            }
        }

        private static function createCategory($name, $parentId, $image, $position, $isFeatured = null, $isFeaturedWeight = null) {
        try {
            return PimBsCategory::create([
                'parent_id' => $parentId,
                'name' => $name,
                'slug' => Helper::generateSlugReference($name),
                'icon' => '',
                'image' => $image,
                'is_featured' => $isFeatured,
                'is_featured_weight' => $isFeaturedWeight,
                'position' => $position,
                'status' => Constant::Yes,

            ]);
        }catch( \Exception $e ) {
            AppException::log($e);

        }
    }

    public function uploadImage(Request $request)
    {
        $url = "https://logos-download.com/wp-content/uploads/2016/09/Laravel_logo.png";
        $path = "images/uploads";
        return ImageUpload::downloadFile( $path, $url );
    }
}