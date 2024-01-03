<?php

namespace Database\Seeders;

use App\Helpers\CloudinaryUpload;
use App\Helpers\Constant;
use App\Helpers\ImageUpload;
use App\Models\PimProductImage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class PimProductDefaultImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultImage = public_path("assets/products/images.jpg");

        $path = "images/products/default";
        $result = ImageUpload::downloadExternalFile($path, $defaultImage);

//        $result = (array)CloudinaryUpload::uploadFile($defaultImage, "assets/closets/0/products/default_placeholder");
//        if (!array_key_exists('public_id', $result)) {
//            throw new \Exception('Error in upload assets to cloudinary');
//        } else {
//            $urlPublicPath = $result['public_id'];
//            $url = $result['url'];
//            $url = $result;

            PimProductImage::create([
                'product_id' => Constant::No,
                'url' => $result,
                'position' => 1,
                'is_default' => Constant::Yes,
                'status' => Constant::Yes,
            ]);
//        }
    }
}
