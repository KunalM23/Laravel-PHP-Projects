<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskStatusSeeder extends Seeder
{
    public function run()
    {
        DB::table('task_statuses')->insert([
            ['name' => 'pending'],
            ['name' => 'in_progress'],
            ['name' => 'completed'],
        ]);
    }
}