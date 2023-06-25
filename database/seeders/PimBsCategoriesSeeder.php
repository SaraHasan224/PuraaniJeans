<?php

namespace Database\Seeders;

use App\Helpers\AppException;
use App\Helpers\Constant;
use App\Helpers\Helper;
use App\Models\PimBsCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PimBsCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            DB::table('pim_bs_categories')->truncate();
            $data = [
                [
                    'name' => "Men",
                    'options' => [
                        "Shirts",
                        "T-Shirt",
                        "Polos",
                        "Active Wear",
                        "Trousers",
                        "Shorts",
                        "Jeans",
                        "Shoes",
                        "Coats | Blazers",
                        "Sweaters | Cardigans",
                        "SweatShirts | Cardigans",
                        "SweatShirts | Hoodies",
                        "Jackets | Over Shirts",
                        "Accessories",
                    ]
                ],
                [
                    'name' => "Women",
                    'options' => [
                        "Fabric",
                        "Ready to wear",
                        "Pret",
                        "Shirts | Blouses",
                        "T-Shirts",
                        "Activewear",
                        "Skirts",
                        "Trousers",
                        "Jeans",
                        "Dresses | Jumpsuits",
                        "SweatShirts | Hoodies",
                        "SweatShirts | Cardigans",
                        "Jackets",
                        "Coats | Blazers",
                        "Dupatta",
                        "Shawl",
                        "Sleepwear",
                        "Shoes",
                        "Accessories",
                    ]
                ],
                [
                    'name' => "Jewelery",
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
                    'name' => "Brands",
                ],
                [
                    'name' => "More",
                ],
            ];

            foreach ($data as $key => $d) {
                $attr = PimBsCategory::create([
                    'parent_id' => Constant::No,
                    'name' => $d['name'],
                    'slug' => Helper::generateSlugReference($d['name']),
                    'icon' => '',
                    'image' => '',
                    'position' => $key+1,
                    'status' => Constant::Yes,
                ]);
                if(array_key_exists('options',$d)) {
                    foreach ($d['options'] as $key2 => $sd) {
                        PimBsCategory::create([
                            'parent_id' => $attr->id,
                            'name' => $sd,
                            'slug' => Helper::generateSlugReference($sd),
                            'icon' => '',
                            'image' => '',
                            'position' => $key2+1+$key,
                            'status' => Constant::Yes,
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            AppException::log($e);
        }
    }
}
