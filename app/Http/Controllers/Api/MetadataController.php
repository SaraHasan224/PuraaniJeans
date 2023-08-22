<?php
/**
 * Created by PhpStorm.
 * User: Sara
 * Date: 5/24/2023
 * Time: 9:13 PM
 */

namespace App\Http\Controllers\Api;


use App\Helpers\ApiResponseHandler;
use App\Helpers\Constant;
use App\Models\Country;
use Cloudinary\Cloudinary;
use Cloudinary\Transformation\Resize;

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
        $responseData = (array) self::getAllCountriesList();
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
            ->get()
            ->toArray();

        return $metadata;
    }

}