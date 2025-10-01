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
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Who gets the notification
            $table->string('title'); // Short title like "Ticket Cancelled"
            $table->text('message'); // Detailed message
            $table->string('type')->default('general'); // Type of notification
            $table->boolean('is_read')->default(false); // Read/unread status
            $table->json('data')->nullable(); // Extra data (ticket details, etc.)
            $table->timestamps();

            // Add indexes for performance
            $table->index(['user_id', 'is_read']);
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
