<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Event;
use App\Models\Ticket;
use Carbon\Carbon;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find or create the test user
        $user = User::firstOrCreate(
            ['email' => 'abcd@gmail.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
                'role' => 'user'
            ]
        );

        // Create an admin user for organizing events
        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'role' => 'admin'
            ]
        );

        // Create approved events
        $events = [
            [
                'title' => 'Summer Music Festival 2025',
                'description' => 'A fantastic outdoor music festival featuring top artists from around the world.',
                'event_date' => Carbon::now()->addDays(15),
                'start_time' => Carbon::now()->addDays(15)->setTime(18, 0),
                'end_time' => Carbon::now()->addDays(15)->setTime(23, 0),
                'venue' => 'Central Park Amphitheater',
                'address' => '123 Park Avenue',
                'city' => 'New York',
                'total_tickets' => 1000,
                'tickets_sold' => 250,
                'price' => 75.00,
                'status' => 'published',
                'approval_status' => 'approved',
                'organizer_id' => $admin->id,
            ],
            [
                'title' => 'Tech Innovation Conference',
                'description' => 'Join industry leaders to discuss the latest in technology and innovation.',
                'event_date' => Carbon::now()->addDays(30),
                'start_time' => Carbon::now()->addDays(30)->setTime(9, 0),
                'end_time' => Carbon::now()->addDays(30)->setTime(17, 0),
                'venue' => 'Convention Center',
                'address' => '456 Tech Street',
                'city' => 'San Francisco',
                'total_tickets' => 500,
                'tickets_sold' => 150,
                'price' => 120.00,
                'status' => 'published',
                'approval_status' => 'approved',
                'organizer_id' => $admin->id,
            ],
            [
                'title' => 'Food & Wine Tasting Event',
                'description' => 'Experience exquisite cuisines and fine wines from local restaurants.',
                'event_date' => Carbon::now()->addDays(10),
                'start_time' => Carbon::now()->addDays(10)->setTime(19, 0),
                'end_time' => Carbon::now()->addDays(10)->setTime(22, 0),
                'venue' => 'Downtown Hotel Ballroom',
                'address' => '789 Main Street',
                'city' => 'Chicago',
                'total_tickets' => 200,
                'tickets_sold' => 80,
                'price' => 65.00,
                'status' => 'published',
                'approval_status' => 'approved',
                'organizer_id' => $admin->id,
            ],
            [
                'title' => 'Marathon Championship 2025',
                'description' => 'Annual city marathon with participants from around the globe.',
                'event_date' => Carbon::now()->addDays(45),
                'start_time' => Carbon::now()->addDays(45)->setTime(7, 0),
                'end_time' => Carbon::now()->addDays(45)->setTime(15, 0),
                'venue' => 'City Sports Complex',
                'address' => '321 Sports Avenue',
                'city' => 'Boston',
                'total_tickets' => 2000,
                'tickets_sold' => 800,
                'price' => 25.00,
                'status' => 'published',
                'approval_status' => 'approved',
                'organizer_id' => $admin->id,
            ],
            [
                'title' => 'Art Gallery Opening',
                'description' => 'Exclusive opening of contemporary art exhibition featuring emerging artists.',
                'event_date' => Carbon::now()->addDays(7),
                'start_time' => Carbon::now()->addDays(7)->setTime(18, 30),
                'end_time' => Carbon::now()->addDays(7)->setTime(21, 0),
                'venue' => 'Modern Art Gallery',
                'address' => '555 Art District',
                'city' => 'Los Angeles',
                'total_tickets' => 150,
                'tickets_sold' => 45,
                'price' => 40.00,
                'status' => 'published',
                'approval_status' => 'approved',
                'organizer_id' => $admin->id,
            ],
            [
                'title' => 'Comedy Night Special',
                'description' => 'Laugh out loud with top comedians in an intimate venue setting.',
                'event_date' => Carbon::now()->addDays(20),
                'start_time' => Carbon::now()->addDays(20)->setTime(20, 0),
                'end_time' => Carbon::now()->addDays(20)->setTime(22, 30),
                'venue' => 'Comedy Club Downtown',
                'address' => '888 Laugh Lane',
                'city' => 'Austin',
                'total_tickets' => 300,
                'tickets_sold' => 120,
                'price' => 35.00,
                'status' => 'published',
                'approval_status' => 'approved',
                'organizer_id' => $admin->id,
            ]
        ];

        // Create the events
        $createdEvents = [];
        foreach ($events as $eventData) {
            $event = Event::create($eventData);
            $createdEvents[] = $event;
            echo "Created event: {$event->title}\n";
        }

        // Create tickets for the user for some of these events
        $ticketData = [
            [
                'event_id' => $createdEvents[0]->id, // Summer Music Festival
                'quantity' => 2,
                'total_price' => 150.00, // 2 × $75
                'purchase_date' => Carbon::now()->subDays(5),
                'status' => 'confirmed',
                'payment_status' => 'paid',
            ],
            [
                'event_id' => $createdEvents[1]->id, // Tech Conference
                'quantity' => 1,
                'total_price' => 120.00,
                'purchase_date' => Carbon::now()->subDays(3),
                'status' => 'confirmed',
                'payment_status' => 'paid',
            ],
            [
                'event_id' => $createdEvents[2]->id, // Food & Wine
                'quantity' => 2,
                'total_price' => 130.00, // 2 × $65
                'purchase_date' => Carbon::now()->subDays(1),
                'status' => 'pending',
                'payment_status' => 'pending',
            ],
            [
                'event_id' => $createdEvents[4]->id, // Art Gallery
                'quantity' => 1,
                'total_price' => 40.00,
                'purchase_date' => Carbon::now()->subDays(2),
                'status' => 'confirmed',
                'payment_status' => 'paid',
            ],
            [
                'event_id' => $createdEvents[5]->id, // Comedy Night
                'quantity' => 3,
                'total_price' => 105.00, // 3 × $35
                'purchase_date' => Carbon::now()->subDays(4),
                'status' => 'cancelled',
                'payment_status' => 'refunded',
            ]
        ];

        // Create tickets for the test user
        foreach ($ticketData as $index => $ticket) {
            $ticket['user_id'] = $user->id;

            Ticket::create($ticket);

            // Find the event for this ticket
            $eventIndex = array_search($ticket['event_id'], array_column($createdEvents, 'id'));
            $eventTitle = $eventIndex !== false ? $createdEvents[$eventIndex]->title : 'Unknown Event';

            echo "Created ticket for {$user->name} for event: {$eventTitle}\n";
        }
        echo "\nTest data seeded successfully!\n";
        echo "User: {$user->email} (password: 'password')\n";
        echo "Admin: {$admin->email} (password: 'password')\n";
        echo "Created " . count($createdEvents) . " events and " . count($ticketData) . " tickets\n";
    }
}
