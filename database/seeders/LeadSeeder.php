<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeadSeeder extends Seeder
{
    public function run(): void
    {
        // sources:      1=website, 2=facebook, 3=instagram, 4=referral, 5=ads, 6=other[cite: 7]
        // lead_statuses: 1=new, 2=contacted, 3=qualified, 4=converted, 5=lost[cite: 7]
        // assigned_to:   1=Admin, 2=Rahul, 3=Priya[cite: 7]

        DB::table('leads')->insert([
            [
                'name'        => 'Amit Verma',
                'email'       => 'amit.verma@example.com',
                'phone'       => '9876543210',
                'company'     => 'Verma Enterprises',
                'source_id'   => 1,
                'status_id'   => 1,
                'assigned_to' => 2,
                'score'       => 60,
                'ai_analysis' => 'Lead showing moderate interest from website inquiry.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'Sneha Kapoor',
                'email'       => 'sneha.kapoor@example.com',
                'phone'       => '9123456780',
                'company'     => 'Kapoor Solutions',
                'source_id'   => 2,
                'status_id'   => 2,
                'assigned_to' => 2,
                'score'       => 75,
                'ai_analysis' => 'Strong social media engagement; ready for follow-up.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'Ravi Mehta',
                'email'       => 'ravi.mehta@example.com',
                'phone'       => '9988776655',
                'company'     => 'Mehta Corp',
                'source_id'   => 4,
                'status_id'   => 3,
                'assigned_to' => 3,
                'score'       => 85,
                'ai_analysis' => 'High-value referral; qualified for enterprise plan.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'Pooja Singh',
                'email'       => 'pooja.singh@example.com',
                'phone'       => '9871234560',
                'company'     => 'Singh & Co',
                'source_id'   => 3,
                'status_id'   => 4,
                'assigned_to' => 3,
                'score'       => 95,
                'ai_analysis' => 'Converted lead with high lifetime value potential.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'Deepak Joshi',
                'email'       => 'deepak.joshi@example.com',
                'phone'       => '9765432100',
                'company'     => 'Joshi Industries',
                'source_id'   => 5,
                'status_id'   => 5,
                'assigned_to' => 2,
                'score'       => 30,
                'ai_analysis' => 'Lead lost due to budget constraints.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'Anita Rao',
                'email'       => 'anita.rao@example.com',
                'phone'       => '9654321098',
                'company'     => 'Rao Technologies',
                'source_id'   => 1,
                'status_id'   => 2,
                'assigned_to' => 3,
                'score'       => 70,
                'ai_analysis' => 'Consistent website visitor; contact initiated.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'Suresh Nair',
                'email'       => 'suresh.nair@example.com',
                'phone'       => '9543210987',
                'company'     => 'Nair Logistics',
                'source_id'   => 6,
                'status_id'   => 1,
                'assigned_to' => 2,
                'score'       => 50,
                'ai_analysis' => 'New lead from external offline source.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'Kavita Sharma',
                'email'       => 'kavita.sharma@example.com',
                'phone'       => '9432109876',
                'company'     => 'Sharma Retail',
                'source_id'   => 2,
                'status_id'   => 3,
                'assigned_to' => 3,
                'score'       => 80,
                'ai_analysis' => 'Retail sector lead; highly qualified.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }
}