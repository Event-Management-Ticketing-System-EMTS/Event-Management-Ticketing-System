<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            
            // Who gets the notification
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Short title and detailed message
            $table->string('title');              // e.g. "Booking Confirmed"
            $table->text('message')->nullable();  // can be empty for very short notifications

            // Type of notification (general, booking, cancellation, system, etc.)
            $table->string('type')->default('general');

            // Link to related resource (like /tickets/1 or /events/5)
            $table->string('link')->nullable();

            // Read/unread
            $table->boolean('is_read')->default(false);

            // Extra payload (like {"ticket_id": 1, "event_id": 5})
            $table->json('data')->nullable();

            $table->timestamps();

            // Indexes for quick queries
            $table->index(['user_id', 'is_read']);
            $table->index(['type']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
