<?php

namespace Database\Seeders;

use App\Helpers\AppException;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            DB::table('cities')->truncate();
            DB::table('cities')->insert([
                ['id' => 1, 'province_id' => 1, 'name' => 'Karachi', 'weight' => 1],
                ['id' => 2, 'province_id' => 1, 'name' => 'Hyderabad', 'weight' => 2],
                ['id' => 3, 'province_id' => 1, 'name' => 'Sukkur', 'weight' => 3],
                ['id' => 4, 'province_id' => 1, 'name' => 'Nawabshah', 'weight' => 4],
                ['id' => 5, 'province_id' => 1, 'name' => '‎Larkana‎', 'weight' => 5],
                ['id' => 6, 'province_id' => 1, 'name' => 'Mirpur Khas', 'weight' => 6],

                ['id' => 7, 'province_id' => 2, 'name' => 'Lahore', 'weight' => 1],
                ['id' => 8, 'province_id' => 2, 'name' => 'Faisalabad', 'weight' => 2],
                ['id' => 9, 'province_id' => 2, 'name' => 'Rawalpindi', 'weight' => 3],
                ['id' => 10, 'province_id' => 2, 'name' => 'Gujranwala', 'weight' => 4],
                ['id' => 11, 'province_id' => 2, 'name' => 'Multan', 'weight' => 5],
                ['id' => 12, 'province_id' => 2, 'name' => 'Sargodha', 'weight' => 6],
                ['id' => 13, 'province_id' => 2, 'name' => 'Bahawalpur', 'weight' => 7],
                ['id' => 14, 'province_id' => 2, 'name' => 'Sialkot', 'weight' => 8],
                ['id' => 15, 'province_id' => 2, 'name' => 'Sheikhupura', 'weight' => 9],
                ['id' => 16, 'province_id' => 2, 'name' => 'Rahim Yar Khan', 'weight' => 10],
                ['id' => 17, 'province_id' => 2, 'name' => 'Jhang', 'weight' => 11],
                ['id' => 18, 'province_id' => 2, 'name' => 'Dera Ghazi Khan', 'weight' =>12],
                ['id' => 19, 'province_id' => 2, 'name' => 'Gujrat', 'weight' => 13],
                ['id' => 20, 'province_id' => 2, 'name' => 'Sahiwal', 'weight' => 14],
                ['id' => 21, 'province_id' => 2, 'name' => 'Wah Cantonment', 'weight' => 15],
                ['id' => 22, 'province_id' => 2, 'name' => 'Kasur', 'weight' => 16],
                ['id' => 23, 'province_id' => 2, 'name' => 'Okara', 'weight' => 17],
                ['id' => 24, 'province_id' => 2, 'name' => 'Chiniot', 'weight' => 18],
                ['id' => 25, 'province_id' => 2, 'name' => 'Kamoke', 'weight' => 6],
                ['id' => 26, 'province_id' => 2, 'name' => 'Hafizabad', 'weight' => 6],
                ['id' => 27, 'province_id' => 2, 'name' => 'Sadiqabad', 'weight' => 6],
                ['id' => 28, 'province_id' => 2, 'name' => 'Burewala', 'weight' => 6],
                ['id' => 29, 'province_id' => 2, 'name' => 'Khanewal', 'weight' => 6],
                ['id' => 30, 'province_id' => 2, 'name' => 'Muzaffargarh', 'weight' => 6],
                ['id' => 31, 'province_id' => 2, 'name' => 'Mandi Bahauddin', 'weight' => 6],
                ['id' => 32, 'province_id' => 2, 'name' => 'Jhelum', 'weight' => 6],
                ['id' => 33, 'province_id' => 2, 'name' => 'Khanpur', 'weight' => 6],
                ['id' => 34, 'province_id' => 2, 'name' => 'Pakpattan', 'weight' => 6],
                ['id' => 35, 'province_id' => 2, 'name' => 'Daska', 'weight' => 6],
                ['id' => 36, 'province_id' => 2, 'name' => 'Gojra', 'weight' => 6],
                ['id' => 37, 'province_id' => 2, 'name' => 'Muridke', 'weight' => 6],
                ['id' => 38, 'province_id' => 2, 'name' => 'Bahawalnagar', 'weight' => 6],
                ['id' => 39, 'province_id' => 2, 'name' => 'Samundri', 'weight' => 6],
                ['id' => 40, 'province_id' => 2, 'name' => 'Jaranwala', 'weight' => 6],
                ['id' => 41, 'province_id' => 2, 'name' => 'Chishtian', 'weight' => 6],
                ['id' => 42, 'province_id' => 2, 'name' => 'Attock', 'weight' => 6],
                ['id' => 43, 'province_id' => 2, 'name' => 'Vehari', 'weight' => 6],
                ['id' => 44, 'province_id' => 2, 'name' => 'Kot Abdul Malik', 'weight' => 6],
                ['id' => 45, 'province_id' => 2, 'name' => 'Ferozewala', 'weight' => 6],
                ['id' => 46, 'province_id' => 2, 'name' => 'Chakwal', 'weight' => 6],
                ['id' => 47, 'province_id' => 2, 'name' => 'Gujranwala Cantonment', 'weight' => 6],
                ['id' => 48, 'province_id' => 2, 'name' => 'Kamalia', 'weight' => 6],
                ['id' => 49, 'province_id' => 2, 'name' => 'Ahmedpur East', 'weight' => 6],
                ['id' => 50, 'province_id' => 2, 'name' => 'Kot Addu', 'weight' => 6],
                ['id' => 51, 'province_id' => 2, 'name' => 'Wazirabad', 'weight' => 6],
                ['id' => 52, 'province_id' => 2, 'name' => 'Layyah', 'weight' => 6],
                ['id' => 53, 'province_id' => 2, 'name' => 'Taxila', 'weight' => 6],
                ['id' => 54, 'province_id' => 2, 'name' => 'Khushab', 'weight' => 6],
                ['id' => 55, 'province_id' => 2, 'name' => 'Mianwali', 'weight' => 6],
                ['id' => 56, 'province_id' => 2, 'name' => 'Lodhran', 'weight' => 6],
                ['id' => 57, 'province_id' => 2, 'name' => 'Hasilpur', 'weight' => 6],
                ['id' => 58, 'province_id' => 2, 'name' => 'Bhakkar', 'weight' => 6],
                ['id' => 59, 'province_id' => 2, 'name' => 'Arif Wala', 'weight' => 6],
                ['id' => 60, 'province_id' => 2, 'name' => 'Sambrial', 'weight' => 6],
                ['id' => 61, 'province_id' => 2, 'name' => 'Jatoi', 'weight' => 6],
                ['id' => 62, 'province_id' => 2, 'name' => 'Haroonabad', 'weight' => 6],
                ['id' => 63, 'province_id' => 2, 'name' => 'Narowal', 'weight' => 6],
                ['id' => 64, 'province_id' => 2, 'name' => 'Bhalwal', 'weight' => 6],

                ['id' => 65, 'province_id' => 3, 'name' => 'Quetta', 'weight' => 1],
                ['id' => 66, 'province_id' => 3, 'name' => 'Khuzdar', 'weight' => 2],
                ['id' => 67, 'province_id' => 3, 'name' => 'Chaman', 'weight' => 3],
                ['id' => 68, 'province_id' => 3, 'name' => 'Gwadar', 'weight' => 4],
                ['id' => 69, 'province_id' => 3, 'name' => 'Loralai', 'weight' => 5],
                ['id' => 70, 'province_id' => 3, 'name' => 'Hub', 'weight' => 6],


                ['id' => 71, 'province_id' => 4, 'name' => 'Peshawar', 'weight' => 1],
                ['id' => 72, 'province_id' => 4, 'name' => 'Mardan', 'weight' => 2],
                ['id' => 73, 'province_id' => 4, 'name' => 'Kohat', 'weight' => 3],
                ['id' => 74, 'province_id' => 4, 'name' => 'Abboutabad', 'weight' => 4],
                ['id' => 75, 'province_id' => 4, 'name' => 'Bannu', 'weight' => 5],
                ['id' => 76, 'province_id' => 4, 'name' => 'Mansehra', 'weight' => 6],
                ['id' => 77, 'province_id' => 4, 'name' => 'Nowshera', 'weight' => 6],
                ['id' => 78, 'province_id' => 4, 'name' => 'Swat', 'weight' => 7],

                ['id' => 79, 'province_id' => 5, 'name' => 'Gilgit', 'weight' => 1],
                ['id' => 80, 'province_id' => 5, 'name' => 'Danyor', 'weight' => 2],
                ['id' => 81, 'province_id' => 5, 'name' => 'Astore', 'weight' => 3],
                ['id' => 82, 'province_id' => 5, 'name' => 'Skardu', 'weight' => 4],

                ['id' => 83, 'province_id' => 6, 'name' => 'Muzaffarabad', 'weight' => 1],
                ['id' => 84, 'province_id' => 6, 'name' => 'Mirpur', 'weight' => 2],
                ['id' => 85, 'province_id' => 6, 'name' => 'Poonch', 'weight' => 3],
            ]);
        } catch (\Exception $e) {
            AppException::log($e);
        }
    }
}
