<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeadStatusSeeder extends Seeder
{
    public function run()
    {
        DB::table('lead_statuses')->insert([
            ['name' => 'new'],
            ['name' => 'contacted'],
            ['name' => 'qualified'],
            ['name' => 'converted'],
            ['name' => 'lost'],
        ]);
    }
}