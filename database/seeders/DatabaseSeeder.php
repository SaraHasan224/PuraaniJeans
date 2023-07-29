<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolesSeeder::class);
        $this->call(AdminSeeder::class);

        $this->call(CountriesSeeder::class);
        $this->call(ProvinceSeeder::class);
        $this->call(CitiesSeeder::class);

        $this->call(CustomerSeeder::class);

        $this->call(PimBsCategoriesSeeder::class);
        $this->call(ClosetSeeder::class);
        $this->call(BrandsSeeder::class);
        $this->call(PimAttributeAndAttributeOptionsSeeder::class);
        $this->call(PimSeeder::class);
        $this->call(PimProductDefaultImageSeeder::class);
        $this->call(PimBsCategoryMappingSeeder::class);



        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
