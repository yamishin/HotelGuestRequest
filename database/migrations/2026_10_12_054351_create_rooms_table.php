<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('room_number')->unique(); // e.g., "501", "1608"
            $table->integer('floor');                // 5 to 16
            $table->string('type')->default('Standard'); // Standard, Deluxe, Suite
            $table->string('status')->default('available'); // available, occupied, cleaning, maintenance
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};