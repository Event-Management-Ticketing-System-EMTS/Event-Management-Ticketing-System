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
        Schema::table('tickets', function (Blueprint $table) {
            // Simple payment status - pending, paid, failed, refunded
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending')->after('status');

            // Payment amount
            $table->decimal('payment_amount', 10, 2)->default(0)->after('payment_status');

            // When payment was completed
            $table->timestamp('paid_at')->nullable()->after('payment_amount');

            // Simple payment reference/transaction ID
            $table->string('payment_reference')->nullable()->after('paid_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn(['payment_status', 'payment_amount', 'paid_at', 'payment_reference']);
        });
    }
};
