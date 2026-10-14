<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_code')->unique(); // REQ-0001
            $table->foreignId('stay_id')->constrained()->onDelete('cascade');
            $table->string('title');                   // "Extra towels"
            $table->text('description')->nullable();
            $table->string('department');              // Housekeeping, F&B, Maintenance, Front Desk
            $table->string('status')->default('pending'); // pending, assigned, in_progress, completed, cancelled
            $table->foreignId('assigned_to')->nullable(); // References a user (staff)
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_requests');
    }
};