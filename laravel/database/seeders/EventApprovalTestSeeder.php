<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\User;

class EventApprovalTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get an organizer user
        $organizer = User::where('role', 'organizer')->first();
        
        if (!$organizer) {
            // Create an organizer if none exists
            $organizer = User::create([
                'name' => 'Test Organizer',
                'email' => 'organizer@test.com',
                'password' => bcrypt('password'),
                'role' => 'organizer',
                'email_verified_at' => now(),
            ]);
        }

        // Create events with different approval statuses
        $events = [
            [
                'title' => 'Summer Music Festival 2025',
                'description' => 'Join us for the biggest music festival of the summer! Featuring top artists from around the world.',
                'event_date' => now()->addMonths(2),
                'start_time' => now()->addMonths(2)->setTime(18, 0),
                'end_time' => now()->addMonths(2)->setTime(23, 0),
                'venue' => 'Central Park Amphitheater',
                'address' => '123 Park Avenue',
                'city' => 'New York',
                'total_tickets' => 5000,
                'price' => 75.00,
                'approval_status' => 'pending',
                'status' => 'draft',
            ],
            [
                'title' => 'Tech Conference 2025',
                'description' => 'Annual technology conference featuring keynotes from industry leaders and innovative tech demos.',
                'event_date' => now()->addMonth(),
                'start_time' => now()->addMonth()->setTime(9, 0),
                'end_time' => now()->addMonth()->setTime(17, 0),
                'venue' => 'Convention Center',
                'address' => '456 Tech Boulevard',
                'city' => 'San Francisco',
                'total_tickets' => 1000,
                'price' => 150.00,
                'approval_status' => 'pending',
                'status' => 'draft',
            ],
            [
                'title' => 'Food & Wine Expo',
                'description' => 'Taste the finest cuisines and wines from local and international vendors.',
                'event_date' => now()->addWeeks(3),
                'start_time' => now()->addWeeks(3)->setTime(12, 0),
                'end_time' => now()->addWeeks(3)->setTime(20, 0),
                'venue' => 'Grand Ballroom Hotel',
                'address' => '789 Culinary Street',
                'city' => 'Chicago',
                'total_tickets' => 500,
                'price' => 45.00,
                'approval_status' => 'pending',
                'status' => 'draft',
            ],
            [
                'title' => 'Art Gallery Opening',
                'description' => 'Exclusive preview of contemporary art from emerging artists.',
                'event_date' => now()->addDays(10),
                'start_time' => now()->addDays(10)->setTime(19, 0),
                'end_time' => now()->addDays(10)->setTime(22, 0),
                'venue' => 'Metropolitan Art Gallery',
                'address' => '321 Art District',
                'city' => 'Los Angeles',
                'total_tickets' => 200,
                'price' => 25.00,
                'approval_status' => 'approved',
                'status' => 'published',
                'reviewed_by' => 1, // Assuming admin user ID 1
                'reviewed_at' => now()->subDays(1),
                'admin_comments' => 'Great event concept, approved for publication.',
            ],
        ];

        foreach ($events as $eventData) {
            Event::create(array_merge($eventData, [
                'organizer_id' => $organizer->id,
            ]));
        }

        $this->command->info('Event approval test data created successfully!');
        $this->command->info('- 3 events pending approval');
        $this->command->info('- 1 event already approved');
    }
}