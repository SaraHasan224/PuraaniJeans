<?php

namespace Database\Seeders;

use App\Helpers\AppException;
use App\Helpers\Constant;
use App\Models\PimAttribute;
use App\Models\PimAttributeOption;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PimAttributeAndAttributeOptionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            DB::table('pim_attributes')->truncate();
            DB::table('pim_attribute_options')->truncate();

            $data = [
                [
                    'name' => "Size",
                    'options' => Constant::SIZE_BY_FILTERS
                ],
                [
                    'name' => "Condition",
                    'options' => Constant::CONDITION_BY_FILTERS
                ],
                [
                    'name' => "Standard",
                    'options' => Constant::STANDARD_BY_FILTERS
                ],
                [
                    'name' => "Color",
                    'options' => array_flip(Constant::COLORS_BY_FILTERS)
                ],
            ];

            foreach ($data as $d) {
                $attr = PimAttribute::create([
                    'name' => $d['name'],
                    'status' => Constant::Yes
                ]);
                foreach ($d['options'] as $key => $opt) {
                    PimAttributeOption::create([
                        'attribute_id' => $attr->id,
                        'option_label' => $opt,
                        'option_value' => $key,
                        'status' => Constant::Yes
                    ]);
                }
            }
        } catch (\Exception $e) {
            AppException::log($e);
        }
    }
}
