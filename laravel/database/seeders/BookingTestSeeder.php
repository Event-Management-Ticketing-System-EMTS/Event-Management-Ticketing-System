<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Event;
use App\Models\Ticket;
use Carbon\Carbon;

class BookingTestSeeder extends Seeder
{
    /**
     * Seed sample ticket bookings for testing the booking functionality
     */
    public function run(): void
    {
        // Get some users and events (create if they don't exist)
        $users = User::all();
        $events = Event::all();

        if ($users->isEmpty() || $events->isEmpty()) {
            $this->command->info('⚠️  No users or events found. Please run UserSeeder and EventSeeder first.');
            return;
        }

        $this->command->info('🎫 Creating sample ticket bookings...');

        // Create various ticket bookings with different statuses
        $bookings = [
            // Confirmed bookings
            [
                'user_id' => $users->random()->id,
                'event_id' => $events->random()->id,
                'quantity' => rand(1, 3),
                'status' => 'confirmed',
                'created_at' => Carbon::now()->subDays(rand(1, 30)),
            ],
            [
                'user_id' => $users->random()->id,
                'event_id' => $events->random()->id,
                'quantity' => rand(1, 5),
                'status' => 'confirmed',
                'created_at' => Carbon::now()->subDays(rand(1, 20)),
            ],
            [
                'user_id' => $users->random()->id,
                'event_id' => $events->random()->id,
                'quantity' => rand(2, 4),
                'status' => 'confirmed',
                'created_at' => Carbon::now()->subDays(rand(1, 15)),
            ],

            // Pending bookings
            [
                'user_id' => $users->random()->id,
                'event_id' => $events->random()->id,
                'quantity' => rand(1, 2),
                'status' => 'pending',
                'created_at' => Carbon::now()->subHours(rand(1, 48)),
            ],
            [
                'user_id' => $users->random()->id,
                'event_id' => $events->random()->id,
                'quantity' => 1,
                'status' => 'pending',
                'created_at' => Carbon::now()->subHours(rand(1, 24)),
            ],

            // Cancelled bookings
            [
                'user_id' => $users->random()->id,
                'event_id' => $events->random()->id,
                'quantity' => rand(1, 3),
                'status' => 'cancelled',
                'created_at' => Carbon::now()->subDays(rand(5, 25)),
            ],
            [
                'user_id' => $users->random()->id,
                'event_id' => $events->random()->id,
                'quantity' => 2,
                'status' => 'cancelled',
                'created_at' => Carbon::now()->subDays(rand(2, 10)),
            ],
        ];

        foreach ($bookings as $bookingData) {
            $event = Event::find($bookingData['event_id']);
            $ticketPrice = $event->price ?? 25.00;

            Ticket::create([
                'user_id' => $bookingData['user_id'],
                'event_id' => $bookingData['event_id'],
                'quantity' => $bookingData['quantity'],
                'total_price' => $ticketPrice * $bookingData['quantity'],
                'status' => $bookingData['status'],
                'purchase_date' => $bookingData['created_at'],
                'created_at' => $bookingData['created_at'],
                'updated_at' => $bookingData['created_at'],
            ]);
        }

        $this->command->info('✅ Sample ticket bookings created successfully!');
        $this->command->info('📊 Booking stats:');
        $this->command->info('   - Confirmed: ' . Ticket::where('status', 'confirmed')->count());
        $this->command->info('   - Pending: ' . Ticket::where('status', 'pending')->count());
        $this->command->info('   - Cancelled: ' . Ticket::where('status', 'cancelled')->count());
        $this->command->info('   - Total: ' . Ticket::count());
        $this->command->info('');
        $this->command->info('🚀 Now you can test the booking functionality at: /bookings');
    }
}
