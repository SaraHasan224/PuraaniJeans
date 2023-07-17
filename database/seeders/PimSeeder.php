<?php

namespace Database\Seeders;

use App\Helpers\AppException;
use App\Helpers\CloudinaryUpload;
use App\Helpers\Constant;
use App\Helpers\Helper;
use App\Helpers\ImageUpload;
use App\Models\PimAttribute;
use App\Models\PimAttributeOption;
use App\Models\PimBrand;
use App\Models\PimCategory;
use App\Models\PimProduct;
use App\Models\PimProductAttribute;
use App\Models\PimProductAttributeOption;
use App\Models\PimProductCategory;
use App\Models\PimProductImage;
use App\Models\PimProductVariant;
use App\Models\PimProductVariantOption;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use function Ramsey\Uuid\v4;

class PimSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        try {
            DB::table('pim_products')->truncate();
            DB::table('pim_product_attributes')->truncate();
            DB::table('pim_product_attribute_options')->truncate();
            DB::table('pim_product_categories')->truncate();
            DB::table('pim_product_images')->truncate();
            DB::table('pim_product_variants')->truncate();
            DB::table('pim_product_variant_options')->truncate();



            $closet = DB::table('closets')->where('closet_name', "SH Bridals")->first();
            $brands = PimBrand::updateOrCreate([
                'closet_id' => $closet->id,
                'name' => "Unstitched",
            ]);
            $products = (object)[
                [
                    'closet_id' => $closet->id,
                    'brand_id' => $brands->id,
                    'name' => 'EL-23-05-Sea Green',
                    'sku' => 'EL-23-05',
                    'handle' => Helper::generateSlugReference("EL-23-05"),
                    'short_description' => 'ONLY AVAILABLE FOR PRE-ORDER AT THE MOMENT, WILL BE SHIPPED POST 30TH MAY.',
                    'pim_product_reference' => v4(),
                    'price' => '15450',
                    'max_quantity' => '10',
                    'is_featured' => Constant::Yes,
                    'featured_position' => 1,
                    'featured_at' => Carbon::now(),
                    'featured_by' => 0,
                    'is_recommended' => Constant::Yes,
                    'recommended_position' => '1',
                    'recommended_at' => Carbon::now(),
                    'recommended_by' => 0,
                    'rank' => '',
                    'variants' => [
                        [
                            'quantity' => 100,
                            'discount' => 500,
                            'discount_type' => '1',
                            'price' => 15450,
                            'image_id' => '',
                            'short_description' => '',
                            'status' => Constant::Yes,
                            'attributes' => [
                                [
                                    [
                                        "attribute" => "Color",
                                        "options" => "Blue"
                                    ],
                                    [
                                        "attribute" => "Condition",
                                        "options" => "New"
                                    ],
                                    [
                                        "attribute" => "Style",
                                        "options" => "Festive"
                                    ]
                                ],
                                [
                                    [
                                        "attribute" => "Color",
                                        "options" => "Blue"
                                    ],
                                    [
                                        "attribute" => "Condition",
                                        "options" => "Old"
                                    ],
                                    [
                                        "attribute" => "Style",
                                        "options" => "Festive"
                                    ]
                                ]
                            ],
                        ]
                    ],
                    "category" => [
                        "name" => "Women",
                        "options" => "Fabric"
                    ],
                    "images" => [
                        "https://cdn.shopify.com/s/files/1/0620/8788/9062/files/D-5Front_44b0f85b-f54c-410b-a45d-d0d08ba433b6_360x.jpg?v=1685099766",
                        "https://cdn.shopify.com/s/files/1/0620/8788/9062/files/D-5Closeup-2_611c24d6-8d14-4a17-a22e-8b128aee635a_360x.jpg?v=1685099766000",
                        "https://cdn.shopify.com/s/files/1/0620/8788/9062/files/D-5Closeup_b1172bc9-b578-42d0-ab37-171be304e6cd_120x.jpg?v=1685099766",
                        "https://cdn.shopify.com/s/files/1/0620/8788/9062/files/D-5Front-2_120x.jpg?v=1685099756"
                    ]
                ],
                [
                    'closet_id' => $closet->id,
                    'brand_id' => $brands->id,
                    'name' => 'SF-02 MANHA',
                    'sku' => 'SF-02',
                    'handle' => Helper::generateSlugReference("SF-02"),
                    'short_description' => '<b>Shirt</b><br/>
                            Gotta Embroidered Neckline<br/>
                            Gotta Embroidered Lace (Front)<br/>
                            Gotta Embroidered Border (Front -Hand Made)<br/>
                            Embroidered Lace (Front)<br/>
                            Gotta Embroidered (Front Patch)<br/>
                            Gotta Embroidered Border (Front & Back)<br/>
                            Gotta Embroidered Patch on Raw Silk (Front-Hand Made )<br/>
                            Gotta Embroidered Patch on Raw Silk (Back)<br/>
                            Gotta Embroidered Back Neckline<br/>
                            Gotta Embroidered Patch (Sleeves) on Raw Silk<br/>
                            Gotta Embroidered Patch (Sleeves )on Organza<br/>
                            Gotta Embroidered Border on Oragnza (Sleeves-Hand Made)<br/>
                            Gotta Embroidered Border Sleeves on Oragnza<br/>
                            Gotta Embroidered Sleeves Border<br/>
                            Plain Slip Fabric<br/>
                            Jacquard Fabric (Shirt)<br/>
                            <b>Dupatta</b><br/>
                            Jacquard Fabric Dupatta<br/>
                            Gotta Embroidered Four Side Border<br/>
                            <b>Trouser</b><br/>
                            Plain Raw silk<br/><br/>
                            <b>Fabric Details</b><br/>
                            Shirt: Jacquard Weaved <br/>
                            Trouser: Raw Silk<br/>
                            Dupatta: Jacquard Weaved<br/><br/>
                            <i>Disclaimer: Product color may slightly vary due to photographic lighting sources or your monitor settings.</i>',
                    'pim_product_reference' => v4(),
                    'price' => '24390',
                    'max_quantity' => '50',
                    'is_featured' => Constant::Yes,
                    'featured_position' => 1,
                    'featured_at' => Carbon::now(),
                    'featured_by' => 0,
                    'is_recommended' => Constant::Yes,
                    'recommended_position' => '1',
                    'recommended_at' => Carbon::now(),
                    'recommended_by' => 0,
                    'rank' => '',
                    'variants' => [
                        [
                            'quantity' => 100,
                            'discount' => 500,
                            'discount_type' => '1',
                            'price' => 15450,
                            'image_id' => '',
                            'short_description' => '
                                    Pakistan delivery time period               5-7 Days            for unstitched
                                    Pakistan delivery time period               15-20 Days        for stitched
                                    International Delivery time period        7-10 Days            for unstitched
                                    International Delivery time period        20-25 Days        for stitched
                                 ',
                            'status' => Constant::Yes,
                            'attributes' => [
                                [
                                    [
                                        "attribute" => "Color",
                                        "options" => ["Yellow"]
                                    ],
                                    [
                                        "attribute" => "Condition",
                                        "options" => ["New"]
                                    ],
                                    [
                                        "attribute" => "Style",
                                        "options" => ["Jacquard Weaved"]
                                    ]
                                ]
                            ],
                        ]
                    ],
                    "category" => [
                        "name" => "Women",
                        "options" => "Festive Collection"
                    ],
                    "images" => [
                        "https://cdn.shopify.com/s/files/1/2044/1461/files/7_a9e12dd9-d60f-441a-ae09-a868200a34df_1800x1800.jpg?v=1685092186",
                        "https://cdn.shopify.com/s/files/1/2044/1461/files/10_f0183439-60f6-4d57-a218-92098de371d8_1800x1800.jpg?v=1685092204",
                        "https://cdn.shopify.com/s/files/1/2044/1461/files/9_634fa23d-65d6-4a1a-8169-bdc2e6a99d7e_1800x1800.jpg?v=1685092204",
                        "https://cdn.shopify.com/s/files/1/2044/1461/files/12_b5113da5-d60e-496a-9f1a-61fc6d73dfef_1800x1800.jpg?v=1685092204",
                        "https://cdn.shopify.com/s/files/1/2044/1461/files/11_4c7ceab1-96bd-403d-aab2-53fd76406b7f_1800x1800.jpg?v=1685092203"
                    ]
                ],
                [
                    'closet_id' => $closet->id,
                    'brand_id' => $brands->id,
                    'name' => 'SF-07 MAHRA',
                    'sku' => 'SF-07',
                    'handle' => Helper::generateSlugReference("SF-07"),
                    'short_description' => '<b>Shirt</b><br/>
                                Gotta Embroidered Neckline<br/>
                                Gotta Embroidered Lace (Front)<br/>
                                Gotta Embroidered Border (Front -Hand Made)<br/>
                                Embroidered Lace (Front)<br/>
                                Gotta Embroidered (Front Patch)<br/>
                                Gotta Embroidered Border (Front & Back)<br/>
                                Gotta Embroidered Patch on Raw Silk (Front-Hand Made )<br/>
                                Gotta Embroidered Patch on Raw Silk (Back)<br/>
                                Gotta Embroidered Back Neckline<br/>
                                Gotta Embroidered Patch (Sleeves) on Raw Silk<br/>
                                Gotta Embroidered Patch (Sleeves )on Organza<br/>
                                Gotta Embroidered Border on Oragnza (Sleeves-Hand Made)<br/>
                                Gotta Embroidered Border Sleeves on Oragnza<br/>
                                Gotta Embroidered Sleeves Border<br/>
                                Plain Slip Fabric<br/>
                                Jacquard Fabric (Shirt)<br/>
                                <b>Dupatta</b><br/>
                                Jacquard Fabric Dupatta<br/>
                                Gotta Embroidered Four Side Border<br/>
                                <b>Trouser</b><br/>
                                Plain Raw silk<br/><br/>
                                <b>Fabric Details</b><br/>
                                Shirt: Jacquard Weaved <br/>
                                Trouser: Raw Silk<br/>
                                Dupatta: Jacquard Weaved<br/><br/>
                                <i>Disclaimer: Product color may slightly vary due to photographic lighting sources or your monitor settings.</i>',
                    'pim_product_reference' => v4(),
                    'price' => '24390',
                    'max_quantity' => '50',
                    'is_featured' => Constant::Yes,
                    'featured_position' => 1,
                    'featured_at' => Carbon::now(),
                    'featured_by' => 0,
                    'is_recommended' => Constant::Yes,
                    'recommended_position' => '1',
                    'recommended_at' => Carbon::now(),
                    'recommended_by' => 0,
                    'rank' => '',
                    'variants' => [
                        [
                            'quantity' => 100,
                            'discount' => 500,
                            'discount_type' => '1',
                            'price' => 15450,
                            'image_id' => '',
                            'short_description' => '
                                        Pakistan delivery time period               5-7 Days            for unstitched
                                        Pakistan delivery time period               15-20 Days        for stitched
                                        International Delivery time period        7-10 Days            for unstitched
                                        International Delivery time period        20-25 Days        for stitched
                                     ',
                            'status' => Constant::Yes,
                            'attributes' => [
                                [
                                    [
                                        "attribute" => "Color",
                                        "options" => ["Yellow"]
                                    ],
                                    [
                                        "attribute" => "Condition",
                                        "options" => ["New"]
                                    ],
                                    [
                                        "attribute" => "Style",
                                        "options" => ["Jacquard Weaved"]
                                    ],
                                    [
                                        "attribute" => "Size",
                                        "options" => ["Small"]
                                    ]
                                ],
                                [
                                    [
                                        "attribute" => "Color",
                                        "options" => ["Yellow"]
                                    ],
                                    [
                                        "attribute" => "Condition",
                                        "options" => ["New"]
                                    ],
                                    [
                                        "attribute" => "Style",
                                        "options" => ["Jacquard Weaved"]
                                    ],
                                    [
                                        "attribute" => "Size",
                                        "options" => ["Medium"]
                                    ]
                                ],
                                [
                                    [
                                        "attribute" => "Color",
                                        "options" => ["Yellow"]
                                    ],
                                    [
                                        "attribute" => "Condition",
                                        "options" => ["New"]
                                    ],
                                    [
                                        "attribute" => "Style",
                                        "options" => ["Jacquard Weaved"]
                                    ],
                                    [
                                        "attribute" => "Size",
                                        "options" => ["Large"]
                                    ]
                                ],
                                [
                                    [
                                        "attribute" => "Color",
                                        "options" => ["Yellow"]
                                    ],
                                    [
                                        "attribute" => "Condition",
                                        "options" => ["New"]
                                    ],
                                    [
                                        "attribute" => "Style",
                                        "options" => ["Jacquard Weaved"]
                                    ],
                                    [
                                        "attribute" => "Size",
                                        "options" => ["Extra Large"]
                                    ]
                                ],
                            ],
                        ],
                    ],
                    "category" => [
                        "name" => "Women",
                        "options" => "Festive Collection"
                    ],
                    "images" => [
                        "https://cdn.shopify.com/s/files/1/2044/1461/files/33_6d8a4a7e-ce54-4e51-b4d2-74b422c72d83_1800x1800.jpg?v=1685093024",
                        "https://cdn.shopify.com/s/files/1/2044/1461/files/32_92025441-3e7f-44d8-972e-4169e6dff5fa_1800x1800.jpg?v=1685093024",
                        "https://cdn.shopify.com/s/files/1/2044/1461/files/31_c7d22ce6-1cd2-44f4-bfd6-7cbe6b93eacf_1800x1800.jpg?v=1685093024",
                        "https://cdn.shopify.com/s/files/1/2044/1461/files/34_3ff8d986-4e08-49ba-a7aa-2c48cee3349f_1800x1800.jpg?v=1685093024",
                        "https://cdn.shopify.com/s/files/1/2044/1461/files/36_a588c0e0-f429-45bd-937a-b683eeaa77a6_1800x1800.jpg?v=1685093024",
                        "https://cdn.shopify.com/s/files/1/2044/1461/files/35_6db7f8a5-df82-4451-a20f-8f7647c008f1_1800x1800.jpg?v=1685093024"
                    ]
                ],
                [
                    'closet_id' => $closet->id,
                    'brand_id' => $brands->id,
                    'name' => 'SF-05 MAHENOOR',
                    'sku' => 'SF-05',
                    'handle' => Helper::generateSlugReference("SF-05"),
                    'short_description' => '<b>Shirt</b><br/>
                            Gotta Embroidered Neckline<br/>
                            Gotta Embroidered Lace (Front)<br/>
                            Gotta Embroidered Border (Front -Hand Made)<br/>
                            Embroidered Lace (Front)<br/>
                            Gotta Embroidered (Front Patch)<br/>
                            Gotta Embroidered Border (Front & Back)<br/>
                            Gotta Embroidered Patch on Raw Silk (Front-Hand Made )<br/>
                            Gotta Embroidered Patch on Raw Silk (Back)<br/>
                            Gotta Embroidered Back Neckline<br/>
                            Gotta Embroidered Patch (Sleeves) on Raw Silk<br/>
                            Gotta Embroidered Patch (Sleeves )on Organza<br/>
                            Gotta Embroidered Border on Oragnza (Sleeves-Hand Made)<br/>
                            Gotta Embroidered Border Sleeves on Oragnza<br/>
                            Gotta Embroidered Sleeves Border<br/>
                            Plain Slip Fabric<br/>
                            Jacquard Fabric (Shirt)<br/>
                            <b>Dupatta</b><br/>
                            Jacquard Fabric Dupatta<br/>
                            Gotta Embroidered Four Side Border<br/>
                            <b>Trouser</b><br/>
                            Plain Raw silk<br/><br/>
                            <b>Fabric Details</b><br/>
                            Shirt: Jacquard Weaved <br/>
                            Trouser: Raw Silk<br/>
                            Dupatta: Jacquard Weaved<br/><br/>
                            <i>Disclaimer: Product color may slightly vary due to photographic lighting sources or your monitor settings.</i>',
                    'pim_product_reference' => v4(),
                    'price' => '24390',
                    'max_quantity' => '50',
                    'is_featured' => Constant::Yes,
                    'featured_position' => 1,
                    'featured_at' => Carbon::now(),
                    'featured_by' => 0,
                    'is_recommended' => Constant::Yes,
                    'recommended_position' => '1',
                    'recommended_at' => Carbon::now(),
                    'recommended_by' => 0,
                    'rank' => '',
                    'variants' => [
                        [
                            'quantity' => 100,
                            'discount' => 500,
                            'discount_type' => '1',
                            'price' => 15450,
                            'image_id' => '',
                            'short_description' => '
                                    Pakistan delivery time period               5-7 Days            for unstitched
                                    Pakistan delivery time period               15-20 Days        for stitched
                                    International Delivery time period        7-10 Days            for unstitched
                                    International Delivery time period        20-25 Days        for stitched
                                 ',
                            'status' => Constant::Yes,
                            'attributes' => [
                                [
                                    [
                                        "attribute" => "Color",
                                        "options" => "Yellow"
                                    ],
                                    [
                                        "attribute" => "Condition",
                                        "options" => "New"
                                    ],
                                    [
                                        "attribute" => "Style",
                                        "options" => "Jacquard Weaved"
                                    ]
                                ]
                            ],
                        ]
                    ],
                    "category" => [
                        "name" => "Women",
                        "options" => "Festive Collection"
                    ],
                    "images" => [
                        "https://cdn.shopify.com/s/files/1/2044/1461/files/13_fe811da2-6761-4ab2-bd94-70e89903846b_1800x1800.jpg?v=1685092721",
                        "https://cdn.shopify.com/s/files/1/2044/1461/files/14_c0080b6c-959e-401e-adc5-e8ec7620de94_1800x1800.jpg?v=1685092721",
                        "https://cdn.shopify.com/s/files/1/2044/1461/files/16_8e20bce8-4515-4ca8-b54d-0e9bb796c2f9_1800x1800.jpg?v=1685092731",
                        "https://cdn.shopify.com/s/files/1/2044/1461/files/15_cb0952db-c0a4-4ba7-b370-6dfb875ad68d_1800x1800.jpg?v=1685092731",
                        "https://cdn.shopify.com/s/files/1/2044/1461/files/18_476bbdf8-8e07-47e3-a953-689c3473e7ad_1800x1800.jpg?v=1685092731",
                        "https://cdn.shopify.com/s/files/1/2044/1461/files/17_725b81d0-2820-46e1-806b-a75ef0a3b07c_1800x1800.jpg?v=1685092731"
                    ]
                ],
                [
                    'closet_id' => $closet->id,
                    'brand_id' => $brands->id,
                    'name' => 'SF-03 AYLA',
                    'sku' => 'SF-03',
                    'handle' => Helper::generateSlugReference("SF-03"),
                    'short_description' => '<b>Shirt</b><br/>
                            Gotta Embroidered Neckline<br/>
                            Gotta Embroidered Lace (Front)<br/>
                            Gotta Embroidered Border (Front -Hand Made)<br/>
                            Embroidered Lace (Front)<br/>
                            Gotta Embroidered (Front Patch)<br/>
                            Gotta Embroidered Border (Front & Back)<br/>
                            Gotta Embroidered Patch on Raw Silk (Front-Hand Made )<br/>
                            Gotta Embroidered Patch on Raw Silk (Back)<br/>
                            Gotta Embroidered Back Neckline<br/>
                            Gotta Embroidered Patch (Sleeves) on Raw Silk<br/>
                            Gotta Embroidered Patch (Sleeves )on Organza<br/>
                            Gotta Embroidered Border on Oragnza (Sleeves-Hand Made)<br/>
                            Gotta Embroidered Border Sleeves on Oragnza<br/>
                            Gotta Embroidered Sleeves Border<br/>
                            Plain Slip Fabric<br/>
                            Jacquard Fabric (Shirt)<br/>
                            <b>Dupatta</b><br/>
                            Jacquard Fabric Dupatta<br/>
                            Gotta Embroidered Four Side Border<br/>
                            <b>Trouser</b><br/>
                            Plain Raw silk<br/><br/>
                            <b>Fabric Details</b><br/>
                            Shirt: Jacquard Weaved <br/>
                            Trouser: Raw Silk<br/>
                            Dupatta: Jacquard Weaved<br/><br/>
                            <i>Disclaimer: Product color may slightly vary due to photographic lighting sources or your monitor settings.</i>',
                    'pim_product_reference' => v4(),
                    'price' => '24390',
                    'max_quantity' => '50',
                    'is_featured' => Constant::Yes,
                    'featured_position' => 1,
                    'featured_at' => Carbon::now(),
                    'featured_by' => 0,
                    'is_recommended' => Constant::Yes,
                    'recommended_position' => '1',
                    'recommended_at' => Carbon::now(),
                    'recommended_by' => 0,
                    'rank' => '',
                    'variants' => [
                        [
                            'quantity' => 100,
                            'discount' => 500,
                            'discount_type' => '1',
                            'price' => 15450,
                            'image_id' => '',
                            'short_description' => '
                                    Pakistan delivery time period               5-7 Days            for unstitched
                                    Pakistan delivery time period               15-20 Days        for stitched
                                    International Delivery time period        7-10 Days            for unstitched
                                    International Delivery time period        20-25 Days        for stitched
                                 ',
                            'status' => Constant::Yes,
                            'attributes' => [
                                [
                                    [
                                        "attribute" => "Condition",
                                        "options" => ["New"]
                                    ],
                                    [
                                        "attribute" => "Size",
                                        "options" => ["Small"]
                                    ]
                                ],
                                [
                                    [
                                        "attribute" => "Condition",
                                        "options" => ["New"]
                                    ],
                                    [
                                        "attribute" => "Size",
                                        "options" => ["Medium"]
                                    ]
                                ],
                                [
                                    [
                                        "attribute" => "Condition",
                                        "options" => ["New"]
                                    ],
                                    [
                                        "attribute" => "Size",
                                        "options" => ["Large"]
                                    ]
                                ],
                                [
                                    [
                                        "attribute" => "Condition",
                                        "options" => ["New"]
                                    ],
                                    [
                                        "attribute" => "Size",
                                        "options" => ["Extra Large"]
                                    ]
                                ],
                            ],
                        ]
                    ],
                    "category" => [
                        "name" => "Women",
                        "options" => "Festive Collection"
                    ],
                    "images" => [
                        "https://cdn.shopify.com/s/files/1/2044/1461/files/37_e23a8a2c-56bc-472f-a361-26c782fa83de_1800x1800.jpg?v=1685092327",
                        "https://cdn.shopify.com/s/files/1/2044/1461/files/38_1bf2b8f4-1ebd-45a1-85fc-53789bf14fcc_1800x1800.jpg?v=1685092327",
                        "https://cdn.shopify.com/s/files/1/2044/1461/files/42_953cfd64-a317-4ac4-9551-376f222293bd_1800x1800.jpg?v=1685092337",
                        "https://cdn.shopify.com/s/files/1/2044/1461/files/40_662f8fc6-077a-4f83-8df8-0553928b9b67_1800x1800.jpg?v=1685092337",
                        "https://cdn.shopify.com/s/files/1/2044/1461/files/39_3c3a1fd3-1361-41de-bb7b-c5162a88182a_1800x1800.jpg?v=1685092337",
                        "https://cdn.shopify.com/s/files/1/2044/1461/files/41_72496720-675d-4070-aa84-8936e45a93b0_1800x1800.jpg?v=1685092334"
                    ]
                ],
                [
                    'closet_id' => $closet->id,
                    'brand_id' => $brands->id,
                    'name' => 'SF-01 RIHA',
                    'sku' => 'SF-01',
                    'handle' => Helper::generateSlugReference("SF-01"),
                    'short_description' => '<b>Shirt</b><br/>
                            Gotta Embroidered Neckline<br/>
                            Gotta Embroidered Lace (Front)<br/>
                            Gotta Embroidered Border (Front -Hand Made)<br/>
                            Embroidered Lace (Front)<br/>
                            Gotta Embroidered (Front Patch)<br/>
                            Gotta Embroidered Border (Front & Back)<br/>
                            Gotta Embroidered Patch on Raw Silk (Front-Hand Made )<br/>
                            Gotta Embroidered Patch on Raw Silk (Back)<br/>
                            Gotta Embroidered Back Neckline<br/>
                            Gotta Embroidered Patch (Sleeves) on Raw Silk<br/>
                            Gotta Embroidered Patch (Sleeves )on Organza<br/>
                            Gotta Embroidered Border on Oragnza (Sleeves-Hand Made)<br/>
                            Gotta Embroidered Border Sleeves on Oragnza<br/>
                            Gotta Embroidered Sleeves Border<br/>
                            Plain Slip Fabric<br/>
                            Jacquard Fabric (Shirt)<br/>
                            <b>Dupatta</b><br/>
                            Jacquard Fabric Dupatta<br/>
                            Gotta Embroidered Four Side Border<br/>
                            <b>Trouser</b><br/>
                            Plain Raw silk<br/><br/>
                            <b>Fabric Details</b><br/>
                            Shirt: Jacquard Weaved <br/>
                            Trouser: Raw Silk<br/>
                            Dupatta: Jacquard Weaved<br/><br/>
                            <i>Disclaimer: Product color may slightly vary due to photographic lighting sources or your monitor settings.</i>',
                    'pim_product_reference' => v4(),
                    'price' => '24390',
                    'max_quantity' => '50',
                    'is_featured' => Constant::Yes,
                    'featured_position' => 1,
                    'featured_at' => Carbon::now(),
                    'featured_by' => 0,
                    'is_recommended' => Constant::Yes,
                    'recommended_position' => '1',
                    'recommended_at' => Carbon::now(),
                    'recommended_by' => 0,
                    'rank' => '',
                    'variants' => [
                        [
                            'quantity' => 100,
                            'discount' => 500,
                            'discount_type' => '1',
                            'price' => 15450,
                            'image_id' => '',
                            'short_description' => '
                                    Pakistan delivery time period               5-7 Days            for unstitched
                                    Pakistan delivery time period               15-20 Days        for stitched
                                    International Delivery time period        7-10 Days            for unstitched
                                    International Delivery time period        20-25 Days        for stitched
                                 ',
                            'status' => Constant::Yes,
                            'attributes' => [
                                [
                                    [
                                        "attribute" => "Condition",
                                        "options" => ["New"]
                                    ],
                                    [
                                        "attribute" => "Size",
                                        "options" => ["Small"]
                                    ]
                                ],
                                [
                                    [
                                        "attribute" => "Condition",
                                        "options" => ["New"]
                                    ],
                                    [
                                        "attribute" => "Size",
                                        "options" => ["Medium"]
                                    ]
                                ],
                                [
                                    [
                                        "attribute" => "Condition",
                                        "options" => ["New"]
                                    ],
                                    [
                                        "attribute" => "Size",
                                        "options" => ["Large"]
                                    ]
                                ],
                                [
                                    [
                                        "attribute" => "Condition",
                                        "options" => ["New"]
                                    ],
                                    [
                                        "attribute" => "Size",
                                        "options" => ["Extra Large"]
                                    ]
                                ],
                            ],
                        ]
                    ],
                    "category" => [
                        "name" => "Women",
                        "options" => "Festive Collection"
                    ],
                    "images" => [
                        "https://cdn.shopify.com/s/files/1/2044/1461/files/25_378a4814-40f6-4240-80ec-ea13048526f3_1800x1800.jpg?v=1685086591",
                        "https://cdn.shopify.com/s/files/1/2044/1461/files/28_220bef8a-e15f-4a0c-845e-adb60d9107e6_1800x1800.jpg?v=1685086591",
                        "https://cdn.shopify.com/s/files/1/2044/1461/files/26_27e414d4-6a40-4540-91e7-957083dfe989_1800x1800.jpg?v=1685086591",
                        "https://cdn.shopify.com/s/files/1/2044/1461/files/27_8ea8ca48-be0b-431f-b5c9-81b1ab0254b3_1800x1800.jpg?v=1685086591",
                        "https://cdn.shopify.com/s/files/1/2044/1461/files/30_02f4b89c-d27a-4971-be04-aa2feefe24ad_1800x1800.jpg?v=1685086591",
                        "https://cdn.shopify.com/s/files/1/2044/1461/files/29_7548451d-f7f0-44f1-a5ea-3eb864ef3540_1800x1800.jpg?v=1685086591"
                    ]
                ],
                [
                    'closet_id' => $closet->id,
                    'brand_id' => $brands->id,
                    'name' => 'SF-04 INSHA',
                    'sku' => 'SF-04',
                    'handle' => Helper::generateSlugReference("SF-04"),
                    'short_description' => '<b>Shirt</b><br/>
                            Gotta Embroidered Neckline<br/>
                            Gotta Embroidered Lace (Front)<br/>
                            Gotta Embroidered Border (Front -Hand Made)<br/>
                            Embroidered Lace (Front)<br/>
                            Gotta Embroidered (Front Patch)<br/>
                            Gotta Embroidered Border (Front & Back)<br/>
                            Gotta Embroidered Patch on Raw Silk (Front-Hand Made )<br/>
                            Gotta Embroidered Patch on Raw Silk (Back)<br/>
                            Gotta Embroidered Back Neckline<br/>
                            Gotta Embroidered Patch (Sleeves) on Raw Silk<br/>
                            Gotta Embroidered Patch (Sleeves )on Organza<br/>
                            Gotta Embroidered Border on Oragnza (Sleeves-Hand Made)<br/>
                            Gotta Embroidered Border Sleeves on Oragnza<br/>
                            Gotta Embroidered Sleeves Border<br/>
                            Plain Slip Fabric<br/>
                            Jacquard Fabric (Shirt)<br/>
                            <b>Dupatta</b><br/>
                            Jacquard Fabric Dupatta<br/>
                            Gotta Embroidered Four Side Border<br/>
                            <b>Trouser</b><br/>
                            Plain Raw silk<br/><br/>
                            <b>Fabric Details</b><br/>
                            Shirt: Jacquard Weaved <br/>
                            Trouser: Raw Silk<br/>
                            Dupatta: Jacquard Weaved<br/><br/>
                            <i>Disclaimer: Product color may slightly vary due to photographic lighting sources or your monitor settings.</i>',
                    'pim_product_reference' => v4(),
                    'price' => '24390',
                    'max_quantity' => '50',
                    'is_featured' => Constant::Yes,
                    'featured_position' => 1,
                    'featured_at' => Carbon::now(),
                    'featured_by' => 0,
                    'is_recommended' => Constant::Yes,
                    'recommended_position' => '1',
                    'recommended_at' => Carbon::now(),
                    'recommended_by' => 0,
                    'rank' => '',
                    'variants' => [
                        [
                            'quantity' => 100,
                            'discount' => 500,
                            'discount_type' => '1',
                            'price' => 15450,
                            'image_id' => '',
                            'short_description' => '
                                    Pakistan delivery time period               5-7 Days            for unstitched
                                    Pakistan delivery time period               15-20 Days        for stitched
                                    International Delivery time period        7-10 Days            for unstitched
                                    International Delivery time period        20-25 Days        for stitched
                                 ',
                            'status' => Constant::Yes,
                            'attributes' => [
                                [
                                    [
                                        "attribute" => "Condition",
                                        "options" => ["New"]
                                    ],
                                    [
                                        "attribute" => "Size",
                                        "options" => ["Small"]
                                    ]
                                ],
                                [
                                    [
                                        "attribute" => "Condition",
                                        "options" => ["New"]
                                    ],
                                    [
                                        "attribute" => "Size",
                                        "options" => ["Medium"]
                                    ]
                                ],
                                [
                                    [
                                        "attribute" => "Condition",
                                        "options" => ["New"]
                                    ],
                                    [
                                        "attribute" => "Size",
                                        "options" => ["Large"]
                                    ]
                                ],
                                [
                                    [
                                        "attribute" => "Condition",
                                        "options" => ["New"]
                                    ],
                                    [
                                        "attribute" => "Size",
                                        "options" => ["Extra Large"]
                                    ]
                                ],
                            ],
                        ]
                    ],
                    "category" => [
                        "name" => "Women",
                        "options" => "Festive Collection"
                    ],
                    "images" => [
                        "https://cdn.shopify.com/s/files/1/2044/1461/files/19_57889bdc-ee10-4e9f-aa19-ef8ad696fc2e_1800x1800.jpg?v=1685092559",
                        "https://cdn.shopify.com/s/files/1/2044/1461/files/20_25e9dca4-4093-4cfe-9e58-953af036e7be_1800x1800.jpg?v=1685092559",
                        "https://cdn.shopify.com/s/files/1/2044/1461/files/24_648a8746-084a-44b7-bd9b-10e2f78749ee_1800x1800.jpg?v=1685092586",
                        "https://cdn.shopify.com/s/files/1/2044/1461/files/21_c0803610-830f-4b1e-a847-530cb732b67d_1800x1800.jpg?v=1685092586",
                        "https://cdn.shopify.com/s/files/1/2044/1461/files/22_f91be665-1649-4ffc-a1c6-c2f8e34e2f33_1800x1800.jpg?v=1685092586",
                        "https://cdn.shopify.com/s/files/1/2044/1461/files/23_51da5d86-4ab6-4278-88b4-e662b8788c56_1800x1800.jpg?v=1685092569"
                    ]
                ],
                [
                    'closet_id' => $closet->id,
                    'brand_id' => $brands->id,
                    'name' => 'G-04',
                    'sku' => 'G-04',
                    'handle' => Helper::generateSlugReference("G-04"),
                    'short_description' => 'Constructed from the finest fabric yam dyed cotton this kurta trouser comes in a “Oxford Blue” shade that complete this  eye-catching serene with perfection.<br/>
                            <i>Disclaimer: Product color may slightly vary due to photographic lighting sources or your monitor settings.</i>',
                    'pim_product_reference' => v4(),
                    'price' => '24390',
                    'max_quantity' => '50',
                    'is_featured' => Constant::No,
                    'featured_position' => 1,
                    'featured_at' => Carbon::now(),
                    'featured_by' => 0,
                    'is_recommended' => Constant::No,
                    'recommended_position' => '1',
                    'recommended_at' => Carbon::now(),
                    'recommended_by' => 0,
                    'rank' => '',
                    'variants' => [
                        [
                            'quantity' => 100,
                            'discount' => 500,
                            'discount_type' => '1',
                            'price' => 15450,
                            'image_id' => '',
                            'short_description' => '
                                    Pakistan delivery time period               5-7 Days            for unstitched
                                    Pakistan delivery time period               15-20 Days        for stitched
                                    International Delivery time period        7-10 Days            for unstitched
                                    International Delivery time period        20-25 Days        for stitched
                                 ',
                            'status' => Constant::Yes,
                            'attributes' => [
                                [
                                    [
                                        "attribute" => "Condition",
                                        "options" => ["New"]
                                    ],
                                    [
                                        "attribute" => "Size",
                                        "options" => ["Small"]
                                    ]
                                ],
                                [
                                    [
                                        "attribute" => "Condition",
                                        "options" => ["New"]
                                    ],
                                    [
                                        "attribute" => "Size",
                                        "options" => ["Medium"]
                                    ]
                                ],
                                [
                                    [
                                        "attribute" => "Condition",
                                        "options" => ["New"]
                                    ],
                                    [
                                        "attribute" => "Size",
                                        "options" => ["Large"]
                                    ]
                                ],
                                [
                                    [
                                        "attribute" => "Condition",
                                        "options" => ["New"]
                                    ],
                                    [
                                        "attribute" => "Size",
                                        "options" => ["Extra Large"]
                                    ]
                                ],
                            ],
                        ]
                    ],
                    "category" => [
                        "name" => "Men",
                        "options" => "Stitched Collection"
                    ],
                    "images" => [
                        "https://cdn.shopify.com/s/files/1/2044/1461/products/M1_1800x1800.jpg?v=1667556215",
                        "https://cdn.shopify.com/s/files/1/2044/1461/products/M5_1800x1800.jpg?v=1667556215",
                        "https://cdn.shopify.com/s/files/1/2044/1461/products/M2_1800x1800.jpg?v=1667556215",
                        "https://cdn.shopify.com/s/files/1/2044/1461/products/M4_1800x1800.jpg?v=1667556215",
                        "https://cdn.shopify.com/s/files/1/2044/1461/products/M3_1800x1800.jpg?v=1667556215"
                    ]
                ],
                [
                    'closet_id' => $closet->id,
                    'brand_id' => $brands->id,
                    'name' => 'G-05',
                    'sku' => 'G-05',
                    'handle' => Helper::generateSlugReference("G-05"),
                    'short_description' => 'Constructed from the finest fabric yam dyed cotton this kurta trouser comes in a “Oxford Blue” shade that complete this  eye-catching serene with perfection.<br/>
                            <i>Disclaimer: Product color may slightly vary due to photographic lighting sources or your monitor settings.</i>',
                    'pim_product_reference' => v4(),
                    'price' => '24390',
                    'max_quantity' => '50',
                    'is_featured' => Constant::No,
                    'featured_position' => 1,
                    'featured_at' => Carbon::now(),
                    'featured_by' => 0,
                    'is_recommended' => Constant::No,
                    'recommended_position' => '1',
                    'recommended_at' => Carbon::now(),
                    'recommended_by' => 0,
                    'rank' => '',
                    'variants' => [
                        [
                            'quantity' => 100,
                            'discount' => 500,
                            'discount_type' => '1',
                            'price' => 15450,
                            'image_id' => '',
                            'short_description' => '
                                    Pakistan delivery time period               5-7 Days            for unstitched
                                    Pakistan delivery time period               15-20 Days        for stitched
                                    International Delivery time period        7-10 Days            for unstitched
                                    International Delivery time period        20-25 Days        for stitched
                                 ',
                            'status' => Constant::Yes,
                            'attributes' => [
                                [
                                    [
                                        "attribute" => "Condition",
                                        "options" => ["New"]
                                    ],
                                    [
                                        "attribute" => "Size",
                                        "options" => ["Small"]
                                    ]
                                ],
                                [
                                    [
                                        "attribute" => "Condition",
                                        "options" => ["New"]
                                    ],
                                    [
                                        "attribute" => "Size",
                                        "options" => ["Medium"]
                                    ]
                                ],
                                [
                                    [
                                        "attribute" => "Condition",
                                        "options" => ["New"]
                                    ],
                                    [
                                        "attribute" => "Size",
                                        "options" => ["Large"]
                                    ]
                                ],
                                [
                                    [
                                        "attribute" => "Condition",
                                        "options" => ["New"]
                                    ],
                                    [
                                        "attribute" => "Size",
                                        "options" => ["Extra Large"]
                                    ]
                                ],
                            ],
                        ]
                    ],
                    "category" => [
                        "name" => "Men",
                        "options" => "Stitched Collection"
                    ],
                    "images" => [
                        "https://cdn.shopify.com/s/files/1/2044/1461/products/M21_1800x1800.jpg?v=1667556334",
                        "https://cdn.shopify.com/s/files/1/2044/1461/products/M21_1800x1800.jpg?v=1667556334",
                        "https://cdn.shopify.com/s/files/1/2044/1461/products/M25_1800x1800.jpg?v=1667556334",
                        "https://cdn.shopify.com/s/files/1/2044/1461/products/M22_1800x1800.jpg?v=1667556334",
                        "https://cdn.shopify.com/s/files/1/2044/1461/products/M23_1800x1800.jpg?v=1667556334",
                        "https://cdn.shopify.com/s/files/1/2044/1461/products/M24_1800x1800.jpg?v=1667556334"
                    ]
                ],
                [
                    'closet_id' => $closet->id,
                    'brand_id' => $brands->id,
                    'name' => 'GW-07 WAISTCOAT',
                    'sku' => 'GW-07',
                    'handle' => Helper::generateSlugReference("GW-07"),
                    'short_description' => 'Constructed from the finest fabric yam dyed cotton this kurta trouser comes in a “Oxford Blue” shade that complete this  eye-catching serene with perfection.<br/>
                            <i>Disclaimer: Product color may slightly vary due to photographic lighting sources or your monitor settings.</i>',
                    'pim_product_reference' => v4(),
                    'price' => '24390',
                    'max_quantity' => '50',
                    'is_featured' => Constant::No,
                    'featured_position' => 1,
                    'featured_at' => Carbon::now(),
                    'featured_by' => 0,
                    'is_recommended' => Constant::No,
                    'recommended_position' => '1',
                    'recommended_at' => Carbon::now(),
                    'recommended_by' => 0,
                    'rank' => '',
                    'variants' => [
                        [
                            'quantity' => 100,
                            'discount' => 500,
                            'discount_type' => '1',
                            'price' => 15450,
                            'image_id' => '',
                            'short_description' => '
                                    Pakistan delivery time period               5-7 Days            for unstitched
                                    Pakistan delivery time period               15-20 Days        for stitched
                                    International Delivery time period        7-10 Days            for unstitched
                                    International Delivery time period        20-25 Days        for stitched
                                 ',
                            'status' => Constant::Yes,
                            'attributes' => [
                                [
                                    [
                                        "attribute" => "Condition",
                                        "options" => ["New"]
                                    ],
                                    [
                                        "attribute" => "Size",
                                        "options" => ["Small"]
                                    ]
                                ],
                                [
                                    [
                                        "attribute" => "Condition",
                                        "options" => ["New"]
                                    ],
                                    [
                                        "attribute" => "Size",
                                        "options" => ["Medium"]
                                    ]
                                ],
                                [
                                    [
                                        "attribute" => "Condition",
                                        "options" => ["New"]
                                    ],
                                    [
                                        "attribute" => "Size",
                                        "options" => ["Large"]
                                    ]
                                ],
                                [
                                    [
                                        "attribute" => "Condition",
                                        "options" => ["New"]
                                    ],
                                    [
                                        "attribute" => "Size",
                                        "options" => ["Extra Large"]
                                    ]
                                ],
                            ],
                        ]
                    ],
                    "category" => [
                        "name" => "Men",
                        "options" => "WAISTCOAT"
                    ],
                    "images" => [
                        "https://cdn.shopify.com/s/files/1/2044/1461/products/M9_1800x1800.jpg?v=1667556879",
                        "https://cdn.shopify.com/s/files/1/2044/1461/products/M8_1800x1800.jpg?v=1667556879",
                        "https://cdn.shopify.com/s/files/1/2044/1461/products/M7_1800x1800.jpg?v=1667556879",
                        "https://cdn.shopify.com/s/files/1/2044/1461/products/M6_1800x1800.jpg?v=1667556879",
                    ]
                ],
                [
                    'closet_id' => $closet->id,
                    'brand_id' => $brands->id,
                    'name' => 'G-06',
                    'sku' => 'G-06',
                    'handle' => Helper::generateSlugReference("G-06"),
                    'short_description' => 'Constructed from the finest fabric yam dyed cotton this kurta trouser comes in a “Oxford Blue” shade that complete this  eye-catching serene with perfection.<br/>
                            <i>Disclaimer: Product color may slightly vary due to photographic lighting sources or your monitor settings.</i>',
                    'pim_product_reference' => v4(),
                    'price' => '24390',
                    'max_quantity' => '50',
                    'is_featured' => Constant::No,
                    'featured_position' => 1,
                    'featured_at' => Carbon::now(),
                    'featured_by' => 0,
                    'is_recommended' => Constant::No,
                    'recommended_position' => '1',
                    'recommended_at' => Carbon::now(),
                    'recommended_by' => 0,
                    'rank' => '',
                    'variants' => [
                        [
                            'quantity' => 100,
                            'discount' => 500,
                            'discount_type' => '1',
                            'price' => 15450,
                            'image_id' => '',
                            'short_description' => '
                                    Pakistan delivery time period               5-7 Days            for unstitched
                                    Pakistan delivery time period               15-20 Days        for stitched
                                    International Delivery time period        7-10 Days            for unstitched
                                    International Delivery time period        20-25 Days        for stitched
                                 ',
                            'status' => Constant::Yes,
                            'attributes' => [
                                [
                                    [
                                        "attribute" => "Condition",
                                        "options" => ["New"]
                                    ],
                                    [
                                        "attribute" => "Size",
                                        "options" => ["Small"]
                                    ]
                                ],
                                [
                                    [
                                        "attribute" => "Condition",
                                        "options" => ["New"]
                                    ],
                                    [
                                        "attribute" => "Size",
                                        "options" => ["Medium"]
                                    ]
                                ],
                                [
                                    [
                                        "attribute" => "Condition",
                                        "options" => ["New"]
                                    ],
                                    [
                                        "attribute" => "Size",
                                        "options" => ["Large"]
                                    ]
                                ],
                                [
                                    [
                                        "attribute" => "Condition",
                                        "options" => ["New"]
                                    ],
                                    [
                                        "attribute" => "Size",
                                        "options" => ["Extra Large"]
                                    ]
                                ],
                            ],
                        ]
                    ],
                    "category" => [
                        "name" => "Men",
                        "options" => "Stitched Collection"
                    ],
                    "images" => [
                        "https://cdn.shopify.com/s/files/1/2044/1461/products/M33._1800x1800.jpg?v=1667556422",
                        "https://cdn.shopify.com/s/files/1/2044/1461/products/M34._1800x1800.jpg?v=1667556434",
                        "https://cdn.shopify.com/s/files/1/2044/1461/products/M35_1800x1800.jpg?v=1667556434",
                        "https://cdn.shopify.com/s/files/1/2044/1461/products/M33_1800x1800.jpg?v=1667556434",
                    ]
                ],
                [
                    'closet_id' => $closet->id,
                    'brand_id' => $brands->id,
                    'name' => 'GW-08  WAISTCOAT',
                    'sku' => 'GW-08 ',
                    'handle' => Helper::generateSlugReference("GW-08"),
                    'short_description' => 'Constructed from the finest fabric yam dyed cotton this kurta trouser comes in a “Oxford Blue” shade that complete this  eye-catching serene with perfection.<br/>
                            <i>Disclaimer: Product color may slightly vary due to photographic lighting sources or your monitor settings.</i>',
                    'pim_product_reference' => v4(),
                    'price' => '24390',
                    'max_quantity' => '50',
                    'is_featured' => Constant::No,
                    'featured_position' => 1,
                    'featured_at' => Carbon::now(),
                    'featured_by' => 0,
                    'is_recommended' => Constant::No,
                    'recommended_position' => '1',
                    'recommended_at' => Carbon::now(),
                    'recommended_by' => 0,
                    'rank' => '',
                    'variants' => [
                        [
                            'quantity' => 100,
                            'discount' => 500,
                            'discount_type' => '1',
                            'price' => 15450,
                            'image_id' => '',
                            'short_description' => '
                                    Pakistan delivery time period               5-7 Days            for unstitched
                                    Pakistan delivery time period               15-20 Days        for stitched
                                    International Delivery time period        7-10 Days            for unstitched
                                    International Delivery time period        20-25 Days        for stitched
                                 ',
                            'status' => Constant::Yes,
                            'attributes' => [
                                [
                                    [
                                        "attribute" => "Condition",
                                        "options" => ["New"]
                                    ],
                                    [
                                        "attribute" => "Size",
                                        "options" => ["Small"]
                                    ]
                                ],
                                [
                                    [
                                        "attribute" => "Condition",
                                        "options" => ["New"]
                                    ],
                                    [
                                        "attribute" => "Size",
                                        "options" => ["Medium"]
                                    ]
                                ],
                                [
                                    [
                                        "attribute" => "Condition",
                                        "options" => ["New"]
                                    ],
                                    [
                                        "attribute" => "Size",
                                        "options" => ["Large"]
                                    ]
                                ],
                                [
                                    [
                                        "attribute" => "Condition",
                                        "options" => ["New"]
                                    ],
                                    [
                                        "attribute" => "Size",
                                        "options" => ["Extra Large"]
                                    ]
                                ],
                            ],
                        ]
                    ],
                    "category" => [
                        "name" => "Men",
                        "options" => "WAISTCOAT"
                    ],
                    "images" => [
                        "https://cdn.shopify.com/s/files/1/2044/1461/products/M29_1800x1800.jpg?v=1667557033",
                        "https://cdn.shopify.com/s/files/1/2044/1461/products/M31_1800x1800.jpg?v=1667557033",
                        "https://cdn.shopify.com/s/files/1/2044/1461/products/M27_1800x1800.jpg?v=1667557033",
                        "https://cdn.shopify.com/s/files/1/2044/1461/products/M30_1800x1800.jpg?v=1667557033",
                        "https://cdn.shopify.com/s/files/1/2044/1461/products/M28_1800x1800.jpg?v=1667557033"
                    ]
                ],
                [
                    'closet_id' => $closet->id,
                    'brand_id' => $brands->id,
                    'name' => 'ME-07',
                    'sku' => 'ME-07 ',
                    'handle' => Helper::generateSlugReference("ME-07"),
                    'short_description' => 'Constructed from the finest fabric yam dyed cotton this kurta trouser comes in a “Oxford Blue” shade that complete this  eye-catching serene with perfection.<br/>
                            <i>Disclaimer: Product color may slightly vary due to photographic lighting sources or your monitor settings.</i>',
                    'pim_product_reference' => v4(),
                    'price' => '24390',
                    'max_quantity' => '50',
                    'is_featured' => Constant::No,
                    'featured_position' => 1,
                    'featured_at' => Carbon::now(),
                    'featured_by' => 0,
                    'is_recommended' => Constant::No,
                    'recommended_position' => '1',
                    'recommended_at' => Carbon::now(),
                    'recommended_by' => 0,
                    'rank' => '',
                    'variants' => [
                        [
                            'quantity' => 100,
                            'discount' => 500,
                            'discount_type' => '1',
                            'price' => 15450,
                            'image_id' => '',
                            'short_description' => '
                                    Pakistan delivery time period               5-7 Days            for unstitched
                                    Pakistan delivery time period               15-20 Days        for stitched
                                    International Delivery time period        7-10 Days            for unstitched
                                    International Delivery time period        20-25 Days        for stitched
                                 ',
                            'status' => Constant::Yes,
                            'attributes' => [
                                [
                                    [
                                        "attribute" => "Condition",
                                        "options" => ["New"]
                                    ],
                                    [
                                        "attribute" => "Size",
                                        "options" => ["Small"]
                                    ]
                                ],
                                [
                                    [
                                        "attribute" => "Condition",
                                        "options" => ["New"]
                                    ],
                                    [
                                        "attribute" => "Size",
                                        "options" => ["Medium"]
                                    ]
                                ],
                                [
                                    [
                                        "attribute" => "Condition",
                                        "options" => ["New"]
                                    ],
                                    [
                                        "attribute" => "Size",
                                        "options" => ["Large"]
                                    ]
                                ],
                                [
                                    [
                                        "attribute" => "Condition",
                                        "options" => ["New"]
                                    ],
                                    [
                                        "attribute" => "Size",
                                        "options" => ["Extra Large"]
                                    ]
                                ],
                            ],
                        ]
                    ],
                    "category" => [
                        "name" => "Men",
                        "options" => "Stitched Collection"
                    ],
                    "images" => [
                        "https://cdn.shopify.com/s/files/1/2044/1461/products/DSCF8727_1800x1800.jpg?v=1656066061",
                        "https://cdn.shopify.com/s/files/1/2044/1461/products/DSCF8735_1800x1800.jpg?v=1656066061",
                        "https://cdn.shopify.com/s/files/1/2044/1461/products/DSCF8740_1800x1800.jpg?v=1656066061",
                        "https://cdn.shopify.com/s/files/1/2044/1461/products/DSCF8760coverthephoto_1800x1800.jpg?v=1656066061",
                    ]
                ],
            ];


            $featuredPosition = 0;
            $recommendedPosition = 0;
            foreach ($products as $key => $product) {
                $product = (object)$product;
                if ($product->is_featured == Constant::Yes) {
                    $featuredPosition += $featuredPosition + 1;
                }
                if ($product->is_recommended == Constant::Yes) {
                    $recommendedPosition += $recommendedPosition + 1;
                }
                #Add PIM Product
                $pimProduct = PimProduct::create([
                    'closet_id' => $closet->id,
                    'brand_id' => $brands->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'handle' => Helper::generateSlugReference($product->sku),
                    'short_description' => $product->short_description,
                    'pim_product_reference' => $product->pim_product_reference,
                    'has_variants' => !empty($product->variants) && count($product->variants) > 1,
                    'price' => $product->price,
                    'max_quantity' => $product->max_quantity,
                    'position' => $key + 1,
                    'status' => Constant::Yes,
                    'is_featured' => $product->is_featured,
                    'featured_position' => $featuredPosition,
                    'featured_at' => $product->is_featured == Constant::Yes ? Carbon::now() : null,
                    'is_recommended' => $product->is_recommended,
                    'recommended_position' => $recommendedPosition,
                    'recommended_at' => $product->is_recommended == Constant::Yes ? Carbon::now() : null,
                ]);
                #Add PIM Product Images
                $pimProductImages = [];

                foreach ($product->images as $key2 => $images) {

                    $fileName = Helper::clean(trim(strtolower($product->name)));

//                    $result = (array)CloudinaryUpload::uploadFile($images, "assets/closets/" . $closet->id . "/products/" . $fileName . "_" . $key2);
                    $result = ImageUpload::downloadExternalFile("images/closets/".$closet->id."/products" , $images, $fileName . "_" . $key2);

                    $image = PimProductImage::create([
                        'product_id' => $pimProduct->id,
                        'url' => $result,
                        'position' => $key2 + 1,
                        'is_default' => $key2 == Constant::No ? Constant::Yes : Constant::No,
                        'status' => Constant::Yes,
                    ]);
                    $pimProductImages[] = $image->id;
                }

                #Add PIM Product Categories
                $category = '';
                $parentCategory = PimCategory::addParentPimCategory($closet, $product->category['name']);
                if (!empty($product->category['options'])) {
                    $category = PimCategory::addChildPimCategory($closet, $parentCategory, (string)$product->category['options']);
                }

                PimProductCategory::addPimCategory($pimProduct, $parentCategory, $category);
                #Add PIM Product Variants
                foreach ($product->variants as $key3 => $variants) {
                    $pimVariants = [];
                    $product = (object)$product;
                    $variants = (object)$variants;
                    foreach ($variants->attributes as $key4 => $attributes) {
                        $variantAttribute = [];
                        $variantTitles = [];
                        $variantTitle = "";
                        $variantSKUs = [];
                        $variantSKU = $product->sku;
                        foreach ($attributes as $key5 => $attr) {
                            $attributes = $attr['attribute'];
                            $options = implode(' ', (array)$attr['options']);

                            $attribute = PimAttribute::saveAttribute($attr['attribute']);
                            $productAttribute = PimProductAttribute::saveAttribute($closet, $pimProduct, $attribute, $attr['attribute']);

                            $attributeOptions = PimAttributeOption::saveOption($attribute, $options);
                            $productAttributeOptions = PimProductAttributeOption::saveProductAttributeOption($pimProduct, $attribute, $productAttribute, $attributeOptions, $options);

                            $variantAttribute[] = [
                                'attrId' => $productAttribute->id,
                                'optionId' => $productAttributeOptions->id,
                            ];
                            $variantTitle = $key5 == 0 ? $options : $variantTitle . " / " . $options;
                            $variantSKU = $variantSKU . "-" . substr($options, 0, 3);

                            $variantTitles[$key4] = (string)$variantTitle;
                            $variantSKUs[$key4] = (string)$variantSKU;
                        }
                        $pimVariants[] = [
                            'attr' => $variantAttribute,
                            'variantTitles' => $variantTitles,
                            'variantSKU' => $variantSKUs,
                        ];
                    }

                    foreach ($pimVariants as $vKey => $variant) {
                        $pimProductVariants = PimProductVariant::create([
                            'product_id' => $pimProduct->id,
                            'product_variant' => implode(' ', $variant['variantTitles']),
                            'sku' => implode(" ", $variant['variantSKU']),
                            'quantity' => $variants->quantity,
                            'price' => $variants->price,
                            'discount' => $variants->discount,
                            'discount_type' => $variants->discount_type,
                            'image_id' => $pimProductImages[$key3 + 1],
                            'short_description' => $variants->short_description,
                            'status' => Constant::Yes,
                        ]);
                        $attr = [];

                        foreach ($variant['attr'] as $key5 => $attributes) {
                            foreach ($attributes as $attribute) {
                                try {
                                    PimProductVariantOption::saveProductAttributeOption([
                                        'product_id' => $pimProduct->id,
                                        'variant_id' => $pimProductVariants->id,
                                        'attribute_id' => $attribute['attrId'],
                                        'option_id' => $attribute['optionId'],
                                    ]);
                                } catch (\Exception $e) {
                                    AppException::log($e);
                                }
                            }
                        }
                    }
                }
            }

        } catch (\Exception $e) {
            AppException::log($e);
        }
    }
}
