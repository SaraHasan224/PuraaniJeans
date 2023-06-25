<?php
namespace App\Helpers;

use Cloudinary\Cloudinary;
use Cloudinary\Tag\ImageTag;
use Cloudinary\Transformation\Resize;

class CloudinaryUpload
{
    public static function uploadFile($imagePath, $folderPath)
    {
        $result = "";
        try {
            $cloudinary = new Cloudinary(
                [
                    'cloud' => [
                        'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                        'api_key'    => env('CLOUDINARY_CLOUD_API_KEY'),
                        'api_secret' => env('CLOUDINARY_CLOUD_API_SECRET'),
                    ],
                ]
            );
            $result = $cloudinary->uploadApi()->upload(
                $imagePath,
                ['public_id' => $folderPath]
            );
        }
        catch (\Exception $e){
            AppException::log($e);
        }
        finally {
            return $result;
        }
    }

    public static function retrieveFullUrl($folderPath)
    {
        $cloudinary = new Cloudinary(
            [
                'cloud' => [
                    'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                    'api_key'    => env('CLOUDINARY_CLOUD_API_KEY'),
                    'api_secret' => env('CLOUDINARY_CLOUD_API_SECRET'),
                ],
            ]
        );
        return $cloudinary->image($folderPath)->toUrl();
    }

    public static function resizeImageUrl($folderPath, $w, $h)
    {
        $cloudinary = new Cloudinary(
            [
                'cloud' => [
                    'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                    'api_key'    => env('CLOUDINARY_CLOUD_API_KEY'),
                    'api_secret' => env('CLOUDINARY_CLOUD_API_SECRET'),
                ],
            ]
        );
        return $cloudinary->imageTag('sample')
            ->resize(Resize::crop()->width(300)->height(200))->toUrl();
    }
}
;
