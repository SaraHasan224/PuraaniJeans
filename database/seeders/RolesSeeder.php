<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;
use App\Helpers\Constant;
use Illuminate\Support\Facades\Schema;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // ... Some Truncate Query
        Schema::disableForeignKeyConstraints();
        DB::table('roles')->truncate();
        DB::table('model_has_roles')->truncate();
        Schema::enableForeignKeyConstraints();


        $user_types = array_flip(Constant::USER_TYPES);
        $roles = [];
        foreach ($user_types as $key => $role) {
            $new_role = [
                'type_id'       => $key,
                'name'          => $role,
                'guard_name'    => 'web',
                'is_default'    => 1,
                'created_at' => date("Y-m-d h:i:s"),
                'updated_at' => date("Y-m-d h:i:s")
            ];
            array_push($roles, $new_role);
        }
        DB::table('roles')->insert($roles);
    }
}
