<?php

namespace Database\Seeders;

use App\Helpers\AppException;
use App\Helpers\CloudinaryUpload;
use App\Helpers\Constant;
use App\Helpers\Helper;
use App\Helpers\ImageUpload;
use App\Models\Closet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use function Ramsey\Uuid\v4;

class ClosetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        try {
            DB::table('closets')->truncate();
            $data = (object) [
                [
                    'closet_name' => "SH Bridals",
                    'closet_reference' => "SH-".v4(),
                    'logo' => "assets/closet/SH-Closet/logo-white.png",
                    'banner' => "assets/closet/SH-Closet/banner.jpg",
                    'about_closet' =>"SH Bridals debuted earlier as Silk by Fawad Khan in the year 2012. The brand started out with an exclusive focus on silk designs in chic and classic silhouettes. The brand later evolved as a complete design house offering bridal and formal couture for both men and women under the label -SH Bridals in 2016.",
                    'status' => Constant::Yes
                ],
                [
                    'closet_name' => "Faiza Saqlain",
                    'closet_reference' => "FS-".v4(),
                    'logo' => "assets/closet/Faiza-Saqlain/logo.png",
                    'banner' => "assets/closet/Faiza-Saqlain/banner.png",
                    'about_closet' => "The house of Faiza Saqlain is synonymous with sophistication and elegance. Imbued with a time-honoured aesthetic and inimitable artistry the brand stands as an icon of our gloriously regal heritage reimagined through a kaleidoscope of colour, cut and craft.<br/>Built over a decade of brilliance, the fashion powerhouse has carved a niche for itself by constantly reinvigorating the elite craft of both bridal and haute couture.",
                    'status' => Constant::Yes
                ],
                [
                    'closet_name' => "Maria B",
                    'closet_reference' => "MB-".v4(),
                    'logo' => "assets/closet/Maria-B/logo.png",
                    'banner' => "assets/closet/Maria-B/banner.png",
                    "about_closet" => "A fashion magazine labelled Maria.B as the ‘Coco Chanel’ of Pakistan - indeed the innovation and transformation triggered by MARIA.B’s entry into the fashion industry justifies the comparison. With a design philosophy rooted in constant change, improvement and originality, the designer has already become a force to be reckoned with. One of the brightest fashion stars of the industry, she remains committed to bringing the very best to her customers without fail. She says, “I have been given tremendous love by everybody but my vision goes far beyond that. I regard myself as an entrepreneur as someone who’s artistic ability can be portrayed in everything that I do. Fashion is a medium that can speak volumes, bridge the gap between cultures, allows human expression like nothing else can. I am lucky enough to be in love with my work, and I intend to use that privilege to the fullest”. The designer admits that amongst all of her muses, her family remains the most important.",
                    'status' => Constant::Yes
                ],
            ];

            foreach ($data as $d) {
                $d = (object) $d;
                $customer = DB::table('customers')->where('email', "sarahasan224@gmail.com")->first();
                $closet = Closet::create([
                    'customer_id' => $customer->id,
                    'closet_name' => $d->closet_name,
                    'closet_reference' => $d->closet_reference,
                    "about_closet" => $d->about_closet,
                    'status' => $d->status
                ]);

                $fileName = Helper::clean(trim(strtolower($d->closet_name)));
//                $logo = (array) CloudinaryUpload::uploadFile($d->logo, "assets/closets/".$closet->id."/".$logoFileName);
                $logo = ImageUpload::downloadFile("images/closets/" . $closet->id . "/logo", asset($d->logo), "logo-".$fileName);

//                $banner = (array) CloudinaryUpload::uploadFile($d->banner, "assets/closets/".$closet->id."/".$bannerFileName);
                $banner = ImageUpload::downloadFile("images/closets/" . $closet->id . "/banner", asset($d->banner), "banner-".$fileName);

                    Closet::where('id', $closet->id)->update([
                        'logo' => $logo,
                        'banner' => $banner,
                    ]);
            }
        } catch (\Exception $e) {
            AppException::log($e);
        }
    }
}
