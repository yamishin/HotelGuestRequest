<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('employee_code')->nullable()->after('phone');
            $table->date('hire_date')->nullable()->after('employee_code');
            $table->string('shift')->nullable()->after('hire_date'); // morning, evening, night
            $table->string('status')->default('active')->after('shift'); // active, inactive, terminated
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['employee_code', 'hire_date', 'shift', 'status']);
        });
    }
};