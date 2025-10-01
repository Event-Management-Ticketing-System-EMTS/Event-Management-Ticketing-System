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
        Schema::table('events', function (Blueprint $table) {
            // Add simple approval status - pending, approved, rejected
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending')->after('status');

            // Add admin comments for approval/rejection reason
            $table->text('admin_comments')->nullable()->after('approval_status');

            // Track which admin approved/rejected
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->after('admin_comments');

            // When was it reviewed
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by']);
            $table->dropColumn(['approval_status', 'admin_comments', 'reviewed_by', 'reviewed_at']);
        });
    }
};
