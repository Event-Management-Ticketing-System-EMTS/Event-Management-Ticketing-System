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
        Schema::create('login_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete(); // delete logs if user is deleted
            $table->string('ip')->nullable();         // store IP address
            $table->text('user_agent')->nullable();   // browser/device info
            $table->boolean('success')->default(true); // login success/failure
            $table->timestamp('logged_in_at')->useCurrent(); // when login happened
            $table->timestamps(); // created_at / updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_logs');
    }
};
