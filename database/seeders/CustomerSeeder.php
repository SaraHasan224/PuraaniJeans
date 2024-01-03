<?php

namespace Database\Seeders;

use App\Helpers\Constant;
use App\Models\Country;
use App\Models\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use function Ramsey\Uuid\v4;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('customers')->truncate();

        $country = Country::getCountryByCountryCode("PK");

        Customer::create([
            'first_name' => 'Sara',
            'last_name' => 'Hasan',
            'username' => 'Sara.hasan',
            'email' => 'sarahasan224@gmail.com',
            'country_code' => "92",
            'phone_number' => "3452099689",
            'country_id' => $country->id,
            'status' => Constant::Yes,
            'subscription_status' => Constant::Yes,
            'identifier' => v4(),
            'login_attempts' => Constant::No,
        ]);
    }
}
