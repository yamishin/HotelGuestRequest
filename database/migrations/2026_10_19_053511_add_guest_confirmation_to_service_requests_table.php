<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->timestamp('guest_confirmed_at')->nullable()->after('completed_at');
            $table->text('staff_notes')->nullable()->after('guest_confirmed_at');
            $table->boolean('returned_by_guest')->default(false)->after('staff_notes');
        });
    }

    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropColumn(['guest_confirmed_at', 'staff_notes', 'returned_by_guest']);
        });
    }
};