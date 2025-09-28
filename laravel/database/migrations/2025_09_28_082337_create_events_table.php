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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->date('event_date');
            $table->time('start_time');
            $table->time('end_time')->nullable();
            $table->string('venue');
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->integer('total_tickets');
            $table->integer('tickets_sold')->default(0);
            $table->decimal('price', 10, 2)->default(0);
            $table->enum('status', ['draft', 'published', 'cancelled'])->default('draft');
            $table->foreignId('organizer_id')->constrained('users');
            $table->string('image_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
