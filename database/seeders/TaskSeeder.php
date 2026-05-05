<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        // task_statuses: 1=pending, 2=in_progress, 3=completed
        // priority: low, medium, high
        // leads: 1–8, users: 1=Admin, 2=Rahul, 3=Priya

        DB::table('tasks')->insert([
            [
                'title'       => 'Send demo to Amit Verma',
                'lead_id'     => 1,
                'user_id'     => 2,
                'status_id'   => 1,
                'priority'    => 'high',
                'due_date'    => '2026-04-22',
                'description' => 'Prepare and send product demo link to Amit.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'title'       => 'Follow up with Sneha Kapoor',
                'lead_id'     => 2,
                'user_id'     => 2,
                'status_id'   => 2,
                'priority'    => 'medium',
                'due_date'    => '2026-04-20',
                'description' => 'Call Sneha to discuss pricing concerns.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'title'       => 'Prepare contract for Ravi Mehta',
                'lead_id'     => 3,
                'user_id'     => 3,
                'status_id'   => 2,
                'priority'    => 'high',
                'due_date'    => '2026-04-21',
                'description' => 'Draft and send contract to Ravi for review.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'title'       => 'Close deal with Pooja Singh',
                'lead_id'     => 4,
                'user_id'     => 3,
                'status_id'   => 3,
                'priority'    => 'high',
                'due_date'    => '2026-04-10',
                'description' => 'Finalize and sign the agreement.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'title'       => 'Re-engage Deepak Joshi',
                'lead_id'     => 5,
                'user_id'     => 2,
                'status_id'   => 1,
                'priority'    => 'low',
                'due_date'    => '2026-04-30',
                'description' => 'Try to re-engage Deepak with a new offer.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'title'       => 'Schedule demo for Anita Rao',
                'lead_id'     => 6,
                'user_id'     => 3,
                'status_id'   => 1,
                'priority'    => 'medium',
                'due_date'    => '2026-04-23',
                'description' => 'Book a product demo session with Anita.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'title'       => 'Send proposal to Suresh Nair',
                'lead_id'     => 7,
                'user_id'     => 2,
                'status_id'   => 2,
                'priority'    => 'medium',
                'due_date'    => '2026-04-24',
                'description' => 'Prepare and send detailed proposal.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'title'       => 'Negotiate pricing with Kavita Sharma',
                'lead_id'     => 8,
                'user_id'     => 3,
                'status_id'   => 1,
                'priority'    => 'high',
                'due_date'    => '2026-04-25',
                'description' => 'Discuss revised pricing with Kavita.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'title'       => 'Update CRM notes for Amit',
                'lead_id'     => 1,
                'user_id'     => 1,
                'status_id'   => 3,
                'priority'    => 'low',
                'due_date'    => '2026-04-15',
                'description' => 'Update all interaction notes in CRM.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'title'       => 'Weekly report for all leads',
                'lead_id'     => 2,
                'user_id'     => 1,
                'status_id'   => 3,
                'priority'    => 'medium',
                'due_date'    => '2026-04-18',
                'description' => 'Compile weekly lead status report.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }
}
