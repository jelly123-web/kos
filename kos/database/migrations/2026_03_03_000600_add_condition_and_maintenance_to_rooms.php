<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add 'condition' column if not exists
        if (!Schema::hasColumn('rooms', 'condition')) {
            Schema::table('rooms', function (Blueprint $table) {
                $table->enum('condition', ['good', 'damaged', 'needs_repair'])->default('good')->after('status');
            });
        }

        // Expand enum of status to include 'maintenance' (MySQL)
        try {
            DB::statement("ALTER TABLE rooms MODIFY status ENUM('empty','occupied','maintenance') DEFAULT 'empty'");
        } catch (\Throwable $e) {
            // Ignore if not MySQL or already altered
        }
    }

    public function down(): void
    {
        // Revert status enum back to 'empty'/'occupied' only
        try {
            DB::statement("ALTER TABLE rooms MODIFY status ENUM('empty','occupied') DEFAULT 'empty'");
        } catch (\Throwable $e) {
            // ignore
        }

        if (Schema::hasColumn('rooms', 'condition')) {
            Schema::table('rooms', function (Blueprint $table) {
                $table->dropColumn('condition');
            });
        }
    }
};
