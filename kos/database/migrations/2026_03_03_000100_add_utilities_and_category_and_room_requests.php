<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('payments', 'category')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->enum('category', ['rent', 'electricity', 'water'])->default('rent')->after('amount');
            });
        }

        if (!Schema::hasColumn('rooms', 'electricity_status')) {
            Schema::table('rooms', function (Blueprint $table) {
                $table->enum('electricity_status', ['on', 'off'])->default('on')->after('status');
                $table->enum('water_status', ['on', 'off'])->default('on')->after('electricity_status');
            });
        }

        if (!Schema::hasTable('room_requests')) {
            Schema::create('room_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('room_requests')) {
            Schema::dropIfExists('room_requests');
        }
        if (Schema::hasColumn('rooms', 'water_status')) {
            Schema::table('rooms', function (Blueprint $table) {
                $table->dropColumn('water_status');
            });
        }
        if (Schema::hasColumn('rooms', 'electricity_status')) {
            Schema::table('rooms', function (Blueprint $table) {
                $table->dropColumn('electricity_status');
            });
        }
        if (Schema::hasColumn('payments', 'category')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropColumn('category');
            });
        }
    }
};

