<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionUserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('permission_user')->insert([
            
            // User 1 → full_access
            [
                'user_id' => 1,
                'permission_id' => 1
            ],

            // User 2 → read
            [
                'user_id' => 2,
                'permission_id' => 2
            ],

            // User 3 → write
            [
                'user_id' => 3,
                'permission_id' => 3
            ],
        ]);
    }
}