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

        }

        private static function createCategory($name, $parentId, $image, $position, $isFeatured = 0, $isFeaturedWeight = 0) {
        try {
            return PimBsCategory::create([
                'parent_id' => $parentId,
                'name' => $name,
                'slug' => Helper::generateSlugReference($name),
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