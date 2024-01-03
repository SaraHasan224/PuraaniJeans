<?php

namespace Database\Seeders;

use App\Helpers\AppException;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            DB::table('provinces')->truncate();
            $country_id = DB::table('country')->where("is_default",1)->first()->country_id;

            DB::table('provinces')->insert([
                ['id' => 1, 'country_id' => $country_id, 'name' => 'Sindh', 'weight' => 1],
                ['id' => 2, 'country_id' => $country_id, 'name' => 'Punjab', 'weight' => 2],
                ['id' => 3, 'country_id' => $country_id, 'name' => 'Balochistan', 'weight' => 3],
                ['id' => 4, 'country_id' => $country_id, 'name' => 'KPK', 'weight' => 4],
                ['id' => 5, 'country_id' => $country_id, 'name' => '‎Gilgit-Baltistan‎', 'weight' => 5],
                ['id' => 6, 'country_id' => $country_id, 'name' => '‎AJK‎', 'weight' => 6],
            ]);
        } catch (\Exception $e) {
            AppException::log($e);
        }
    }
}
