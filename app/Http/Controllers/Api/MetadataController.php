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
use App\Models\Country;
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

class MetadataController
{

    /**
     * @OA\Get(
     *
     *     path="/api/countries-meta-data",
     *     tags={"Metadata"},
     *     summary="Get countries data meta content",
     *     operationId="getMetaData",
     *
     *     @OA\Response(response=200,description="Success"),
     * )
     */

    public function getMetaData()
    {
        $responseData = self::getAllWithRelationalData();
        return ApiResponseHandler::success( $responseData, __('messages.general.success'));
    }


    /**
     * @OA\Get(
     *
     *     path="/api/country-list",
     *     tags={"Metadata"},
     *     summary="Get country list meta content",
     *     operationId="getCountriesList",
     *
     *     @OA\Response(response=200,description="Success"),
     * )
     */

    public function getCountriesList()
    {
        $responseData = self::getAllCountriesList();
        return ApiResponseHandler::success( $responseData, __('messages.general.success'));
    }

    private static function getAllWithRelationalData()
    {
        $fields = [
            'code',
            'country_code',
            'currency_code',
            'id',
            'name',
            'status'
        ];

        $metadata =  Country::select( $fields )
            ->orderBy('name', 'ASC')
            ->where('status', Constant::Yes);

        $metadata = $metadata
            ->with('provinces.cities.areas')
            ->get()
            ->toArray();

        return $metadata;
    }


    private static function getAllCountriesList()
    {
        $fields = [
            'code',
            'country_code',
            'id',
            'name'
        ];

        $metadata =  Country::where('status',Constant::Yes)
            ->select( $fields )
            ->orderBy('name', 'ASC')
            ->get();

        return $metadata;
    }

}