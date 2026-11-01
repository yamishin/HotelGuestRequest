<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            // Front Desk routing fields
            $table->foreignId('routed_by')->nullable()->after('assigned_to');
            $table->timestamp('routed_at')->nullable()->after('routed_by');

            // Rejection / unavailable fields
            $table->text('rejection_reason')->nullable()->after('routed_at');

            // Supervisor resolution fields
            $table->foreignId('resolved_by')->nullable()->after('rejection_reason');
            $table->timestamp('resolved_at')->nullable()->after('resolved_by');

            // Unresolved tracking (replaces returned_by_guest)
            $table->boolean('marked_unresolved')->default(false)->after('resolved_at');
            $table->text('unresolved_reason')->nullable()->after('marked_unresolved');

            // Reassignment tracking
            $table->integer('reassignment_count')->default(0)->after('unresolved_reason');
        });

        // Drop the old guest-confirmation fields (no longer needed)
        Schema::table('service_requests', function (Blueprint $table) {
            if (Schema::hasColumn('service_requests', 'guest_confirmed_at')) {
                $table->dropColumn('guest_confirmed_at');
            }
            if (Schema::hasColumn('service_requests', 'returned_by_guest')) {
                $table->dropColumn('returned_by_guest');
            }
        });
    }

    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropColumn([
                'routed_by', 'routed_at', 'rejection_reason',
                'resolved_by', 'resolved_at', 'marked_unresolved',
                'unresolved_reason', 'reassignment_count',
            ]);

            $table->timestamp('guest_confirmed_at')->nullable();
            $table->boolean('returned_by_guest')->default(false);
        });
    }
};