<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InteractionSeeder extends Seeder
{
    public function run(): void
    {
        // interaction_types: 1=call, 2=email, 3=meeting, 4=visit
        // leads: 1–8 (from LeadSeeder)
        // users: 1=Admin, 2=Rahul, 3=Priya

        DB::table('interactions')->insert([
            [
                'lead_id'             => 1,
                'user_id'             => 2,
                'interaction_type_id' => 1,
                'interaction_date'    => '2026-04-01 10:00:00',
                'notes'               => 'Initial call with Amit. Interested in product demo.',
                'created_at'          => now(),
                'updated_at'          => now(),
            ],
            [
                'lead_id'             => 1,
                'user_id'             => 2,
                'interaction_type_id' => 2,
                'interaction_date'    => '2026-04-03 11:30:00',
                'notes'               => 'Sent product brochure via email.',
                'created_at'          => now(),
                'updated_at'          => now(),
            ],
            [
                'lead_id'             => 2,
                'user_id'             => 2,
                'interaction_type_id' => 3,
                'interaction_date'    => '2026-04-05 14:00:00',
                'notes'               => 'Meeting at Kapoor Solutions office. Discussed pricing.',
                'created_at'          => now(),
                'updated_at'          => now(),
            ],
            [
                'lead_id'             => 3,
                'user_id'             => 3,
                'interaction_type_id' => 1,
                'interaction_date'    => '2026-04-06 09:00:00',
                'notes'               => 'Follow-up call with Ravi. Ready to move forward.',
                'created_at'          => now(),
                'updated_at'          => now(),
            ],
            [
                'lead_id'             => 4,
                'user_id'             => 3,
                'interaction_type_id' => 4,
                'interaction_date'    => '2026-04-08 16:00:00',
                'notes'               => 'Site visit to Singh & Co. Deal confirmed.',
                'created_at'          => now(),
                'updated_at'          => now(),
            ],
            [
                'lead_id'             => 5,
                'user_id'             => 2,
                'interaction_type_id' => 2,
                'interaction_date'    => '2026-04-09 10:00:00',
                'notes'               => 'Sent final proposal. No response yet.',
                'created_at'          => now(),
                'updated_at'          => now(),
            ],
            [
                'lead_id'             => 6,
                'user_id'             => 3,
                'interaction_type_id' => 1,
                'interaction_date'    => '2026-04-10 11:00:00',
                'notes'               => 'Called Anita. Scheduled a demo next week.',
                'created_at'          => now(),
                'updated_at'          => now(),
            ],
            [
                'lead_id'             => 7,
                'user_id'             => 2,
                'interaction_type_id' => 3,
                'interaction_date'    => '2026-04-11 15:00:00',
                'notes'               => 'Online meeting with Suresh. Needs more time.',
                'created_at'          => now(),
                'updated_at'          => now(),
            ],
            [
                'lead_id'             => 8,
                'user_id'             => 3,
                'interaction_type_id' => 2,
                'interaction_date'    => '2026-04-12 09:30:00',
                'notes'               => 'Emailed Kavita with updated pricing sheet.',
                'created_at'          => now(),
                'updated_at'          => now(),
            ],
            [
                'lead_id'             => 3,
                'user_id'             => 3,
                'interaction_type_id' => 3,
                'interaction_date'    => '2026-04-14 13:00:00',
                'notes'               => 'Second meeting with Ravi. Contract under review.',
                'created_at'          => now(),
                'updated_at'          => now(),
            ],
        ]);
    }
}
