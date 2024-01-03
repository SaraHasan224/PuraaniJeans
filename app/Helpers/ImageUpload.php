<?php
/**
 * Created by PhpStorm.
 * User: Sara
 * Date: 6/24/2023
 * Time: 4:28 PM
 */

namespace App\Helpers;


use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageUpload
{
    public static function file_get_contents_curl( $url ) {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }

    public static function  downloadFile( $folderPath, $imageContent, $imgName = null )
    {
        if($imgName == null) {
            $imgName = strtotime("now");
        }

        $extension = pathinfo($imageContent, PATHINFO_EXTENSION);

        $contents = file_get_contents($imageContent);
        $fileName = $folderPath."/".$imgName.".".$extension;

        Storage::put($fileName, $contents);
        return $fileName;
    }

    public static function downloadExternalFile( $path, $url, $imgName = null )
    {
        if($imgName == null) {
            $imgName = strtotime("now");
        }

        $parsedURL = parse_url($url);
        $pathinfo = pathinfo($parsedURL['path']);
        $extension = $pathinfo['extension'];
        $contents = file_get_contents($url);
        $fileName = $path."/".$imgName.".".$extension;
        Storage::put( "public/".$fileName, $contents);

        return $fileName;
    }

    public static function saveDataImage($file, $fileName, $folderPath) {
        $base64Image = explode(";base64,", $file);

        $explodeImage = explode("image/", $base64Image[0]);
        $image_base64 = base64_decode($base64Image[1]);

        $imageType = $explodeImage[1];
        $file = public_path($folderPath . $fileName . '. '.$imageType);

        file_put_contents($file, $image_base64);
        return $file;
    }
}