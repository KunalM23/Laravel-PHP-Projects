<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SourceSeeder extends Seeder
{
    public function run()
    {
        DB::table('sources')->insert([
            ['name' => 'website'],
            ['name' => 'facebook'],
            ['name' => 'instagram'],
            ['name' => 'referral'],
            ['name' => 'ads'],
            ['name' => 'other'],
        ]);
    }
}
