<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;

class EventsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin user
        $admin = User::where('role', 'admin')->first();

        if (!$admin) {
            $this->command->error('Admin user not found. Please run TestUsersSeeder first.');
            return;
        }

        // Create events with varying status and dates
        $events = [
            [
                'title' => 'Tech Conference 2025',
                'description' => 'Join us for the biggest tech conference in the city. Learn from industry experts and network with professionals.',
                'event_date' => Carbon::now()->addDays(30),
                'start_time' => '09:00:00',
                'end_time' => '17:00:00',
                'venue' => 'City Convention Center',
                'address' => '123 Main St',
                'city' => 'Tech City',
                'total_tickets' => 500,
                'tickets_sold' => 350,
                'price' => 99.99,
                'status' => 'published',
            ],
            [
                'title' => 'Music Festival',
                'description' => 'A weekend of amazing music performances from local and international artists.',
                'event_date' => Carbon::now()->addDays(45),
                'start_time' => '14:00:00',
                'end_time' => '23:00:00',
                'venue' => 'City Park',
                'address' => '50 Park Avenue',
                'city' => 'Musicville',
                'total_tickets' => 2000,
                'tickets_sold' => 1500,
                'price' => 75.00,
                'status' => 'published',
            ],
            [
                'title' => 'Startup Pitch Night',
                'description' => 'Watch innovative startups pitch their ideas to investors and win funding.',
                'event_date' => Carbon::now()->addDays(15),
                'start_time' => '18:00:00',
                'end_time' => '21:00:00',
                'venue' => 'Innovation Hub',
                'address' => '42 Entrepreneur Street',
                'city' => 'Startupville',
                'total_tickets' => 200,
                'tickets_sold' => 150,
                'price' => 25.00,
                'status' => 'published',
            ],
            [
                'title' => 'Canceled Workshop',
                'description' => 'This workshop was unfortunately canceled.',
                'event_date' => Carbon::now()->addDays(10),
                'start_time' => '10:00:00',
                'end_time' => '16:00:00',
                'venue' => 'Learning Center',
                'address' => '55 Knowledge Way',
                'city' => 'Learntown',
                'total_tickets' => 100,
                'tickets_sold' => 0,
                'price' => 150.00,
                'status' => 'cancelled',
            ],
            [
                'title' => 'Future Conference Draft',
                'description' => 'This is a draft event that is not yet published.',
                'event_date' => Carbon::now()->addDays(60),
                'start_time' => '09:00:00',
                'end_time' => '17:00:00',
                'venue' => 'Future Venue',
                'address' => '100 Future Street',
                'city' => 'Future City',
                'total_tickets' => 300,
                'tickets_sold' => 0,
                'price' => 50.00,
                'status' => 'draft',
            ],
        ];

        foreach ($events as $eventData) {
            $eventData['organizer_id'] = $admin->id;
            Event::create($eventData);
            $this->command->info("Created event: {$eventData['title']}");
        }

        $this->command->info('Events seeded successfully!');
    }
}
