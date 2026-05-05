<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            DesignationSeeder::class,
            RoleSeeder::class,

            UserSeeder::class,

            PermissionSeeder::class,

            RoleUserSeeder::class,
            PermissionUserSeeder::class,

            SourceSeeder::class,
            LeadStatusSeeder::class,
            InteractionTypeSeeder::class,
            TaskStatusSeeder::class,

            // Dummy data for API testing
            LeadSeeder::class,
            InteractionSeeder::class,
            TaskSeeder::class,
        ]);
    }
}