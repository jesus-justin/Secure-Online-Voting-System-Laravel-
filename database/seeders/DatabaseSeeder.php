<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Election;
use App\Models\Candidate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@securevoting.com',
            'password' => Hash::make('admin123'),
            'voter_id' => 'VID-ADMIN001',
            'is_admin' => true,
            'is_verified' => true,
            'verified_at' => now(),
            'email_verified_at' => now(),
        ]);

        // Create Sample Verified Voters
        $voters = [
            ['name' => 'John Doe', 'email' => 'john@example.com'],
            ['name' => 'Jane Smith', 'email' => 'jane@example.com'],
            ['name' => 'Bob Johnson', 'email' => 'bob@example.com'],
            ['name' => 'Alice Williams', 'email' => 'alice@example.com'],
            ['name' => 'Charlie Brown', 'email' => 'charlie@example.com'],
        ];

        foreach ($voters as $index => $voter) {
            User::create([
                'name' => $voter['name'],
                'email' => $voter['email'],
                'password' => Hash::make('password123'),
                'voter_id' => 'VID-VOTER' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'is_admin' => false,
                'is_verified' => true,
                'verified_at' => now(),
                'email_verified_at' => now(),
            ]);
        }

        // Create Sample Election
        $election = Election::create([
            'title' => 'Student Council President 2026',
            'description' => 'Vote for your next Student Council President. This election will determine who leads our student body for the upcoming academic year.',
            'status' => 'active',
            'start_date' => now()->subDay(),
            'end_date' => now()->addDays(6),
            'created_by' => 1,
        ]);

        // Create Candidates for the Election
        $candidates = [
            [
                'name' => 'Sarah Anderson',
                'description' => 'Economics student with 3 years of student leadership experience',
                'position' => 1,
            ],
            [
                'name' => 'Michael Chen',
                'description' => 'Computer Science major passionate about student tech initiatives',
                'position' => 2,
            ],
            [
                'name' => 'Emma Rodriguez',
                'description' => 'Business student focused on student community engagement',
                'position' => 3,
            ],
            [
                'name' => 'David Thompson',
                'description' => 'Engineering student committed to improving campus facilities',
                'position' => 4,
            ],
        ];

        foreach ($candidates as $candidate) {
            Candidate::create([
                'election_id' => $election->id,
                'name' => $candidate['name'],
                'description' => $candidate['description'],
                'position' => $candidate['position'],
            ]);
        }

        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin: admin@securevoting.com / admin123');
        $this->command->info('Voters: john@example.com, jane@example.com, etc. / password123');
    }
}
