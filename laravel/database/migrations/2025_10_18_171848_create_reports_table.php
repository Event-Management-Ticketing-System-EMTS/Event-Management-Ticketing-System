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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id');      // Admin who generated the report
            $table->string('report_type');                // e.g. 'event', 'ticket', 'user'
            $table->text('report_summary')->nullable();   // Short summary
            $table->longText('report_data')->nullable();  // JSON or detailed info
            $table->timestamp('generated_at')->useCurrent();
            $table->timestamps();

            // foreign key (optional)
            $table->foreign('admin_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
