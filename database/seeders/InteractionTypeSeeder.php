<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InteractionTypeSeeder extends Seeder
{
    public function run()
    {
        DB::table('interaction_types')->insert([
            ['name' => 'call'],
            ['name' => 'email'],
            ['name' => 'meeting'],
            ['name' => 'visit'],
        ]);
    }
}