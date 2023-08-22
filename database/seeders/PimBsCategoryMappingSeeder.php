<?php

namespace Database\Seeders;

use App\Helpers\AppException;
use App\Helpers\Constant;
use App\Helpers\Helper;
use App\Models\PimBsCategory;
use App\Models\PimBsCategoryMapping;
use App\Models\PimCategory;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PimBsCategoryMappingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            DB::table('pim_bs_category_mapping')->truncate();

            $records = [
                [
                    'bs_category_id' => "Mens Wear",
                    "pim_category_id" => [
                        'Festive Collection', 'Men', 'Stitched Collection', 'WAISTCOAT'
                    ]
                ],
                [
                    'bs_category_id' => "Active Wear",
                    "pim_category_id" => [
                        'Festive Men',
                    ]
                ],
                [
                    'bs_category_id' => "Accessories",
                    "pim_category_id" => [
                        'Men', 'WAISTCOAT'
                    ]
                ],
                [
                    'bs_category_id' => "Winter",
                    "pim_category_id" => [
                        'Men', 'Festive Collection', 'Stitched Collection',
                    ]
                ],
                [
                    'bs_category_id' => "Fabric",
                    "pim_category_id" => [
                        'Festive Collection', 'Women', 'Stitched Collection'
                    ]
                ],
                [
                    'bs_category_id' => "Pret",
                    "pim_category_id" => [
                        'Festive Collection', 'Stitched Collection'
                    ]
                ],
                [
                    'bs_category_id' => "Western",
                    "pim_category_id" => [
                        'Women'
                    ]
                ],
                [
                    'bs_category_id' => "Winter",
                    "pim_category_id" => [
                        'Women'
                    ]
                ],
                [
                    'bs_category_id' => "Accessories",
                    "pim_category_id" => [
                        'Women', 'Festive Collection', 'Stitched Collection'
                    ]
                ],
            ];

            foreach ($records as $record) {
               foreach ($record as $key => $rec) {
                   PimBsCategoryMapping::create([
                       'bs_category_id' => PimBsCategory::where('name', $record['bs_category_id'])->first()->id,
                       'pim_category_id' => PimCategory::where('name', $record['pim_category_id'][$key])->first()->id,
                       'mapped_by' => 0,
                       'mapped_at' => Carbon::now(),
                   ]);
               }
            }
        } catch (\Exception $e) {
            AppException::log($e);
        }
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
        } catch (\Exception $e) {
            AppException::log($e);

        }
    }
}
