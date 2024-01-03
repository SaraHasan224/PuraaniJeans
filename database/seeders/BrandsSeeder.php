<?php

namespace Database\Seeders;

use App\Helpers\AppException;
use App\Helpers\CloudinaryUpload;
use App\Helpers\Constant;
use App\Helpers\Helper;
use App\Helpers\ImageUpload;
use App\Models\PimBrand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrandsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            DB::table('pim_brands')->truncate();
            $closet = DB::table('closets')->where('closet_name', "SH Bridals")->first();
            $data = [
                [
                    'name' => "Samsung",
                    'closet_id' => Constant::No,
                    'icon' => "assets/brands/1.png",
                    'status' => Constant::Yes
                ],
                [
                    'name' => "Asus",
                    'closet_id' => Constant::No,
                    'icon' => "assets/brands/2.png",
                    'status' => Constant::Yes
                ],
                [
                    'name' => "B&o",
                    'closet_id' => Constant::No,
                    'icon' => "assets/brands/3.png",
                    'status' => Constant::Yes
                ],
                [
                    'name' => "decakila",
                    'closet_id' => Constant::No,
                    'icon' => "assets/brands/4.png",
                    'status' => Constant::Yes
                ],
                [
                    'name' => "tpLink",
                    'closet_id' => Constant::No,
                    'icon' => "assets/brands/5.png",
                    'status' => Constant::Yes
                ],
                [
                    'name' => "H&M",
                    'closet_id' => Constant::No,
                    'icon' => "assets/brands/6.png",
                    'status' => Constant::Yes
                ],
                [
                    'name' => "Sony",
                    'closet_id' => Constant::No,
                    'icon' => "assets/brands/7.png",
                    'status' => Constant::Yes
                ],
                [
                    'name' => "Apparel",
                    'closet_id' => $closet->id,
                    'icon' => "assets/brands/7.png",
                    'status' => Constant::Yes
                ],
            ];

            foreach ($data as $d) {
                $icon = $d['icon'];
                $fileName = Helper::clean(trim(strtolower($d['name'])));
//                $result = (array) CloudinaryUpload::uploadFile($icon, "assets/closets/".$closet->id."/brands/".$fileName);
                $d['icon'] = ImageUpload::downloadFile("images/closets/".$closet->id."/brands", public_path($icon) , $fileName );
                PimBrand::create($d);
            }
        } catch (\Exception $e) {
            AppException::log($e);
        }
    }
}
