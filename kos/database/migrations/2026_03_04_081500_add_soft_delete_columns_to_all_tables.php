<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'users','properties','rooms','tenants','payments','issue_reports',
            'room_inspections','room_requests','messages','operations',
            'role_permissions','settings','exit_requests'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    if (!Schema::hasColumn($table->getTable(), 'deleted')) {
                        $table->unsignedTinyInteger('deleted')->default(0)->after('updated_at');
                    }
                    if (!Schema::hasColumn($table->getTable(), 'deleted_at')) {
                        $table->timestamp('deleted_at')->nullable()->after('deleted');
                    }
                    if (!Schema::hasColumn($table->getTable(), 'deleted_by')) {
                        $table->foreignId('deleted_by')->nullable()->after('deleted_at')->constrained('users')->nullOnDelete();
                    }
                    if (!Schema::hasColumn($table->getTable(), 'deleted_ip')) {
                        $table->string('deleted_ip', 45)->nullable()->after('deleted_by');
                    }
                });
            }
        }
    }

    public function down(): void
    {
        $tables = [
            'users','properties','rooms','tenants','payments','issue_reports',
            'room_inspections','room_requests','messages','operations',
            'role_permissions','settings','exit_requests'
        ];
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    if (Schema::hasColumn($table->getTable(), 'deleted_ip')) {
                        $table->dropColumn('deleted_ip');
                    }
                    if (Schema::hasColumn($table->getTable(), 'deleted_by')) {
                        $table->dropConstrainedForeignId('deleted_by');
                    }
                    if (Schema::hasColumn($table->getTable(), 'deleted_at')) {
                        $table->dropColumn('deleted_at');
                    }
                    if (Schema::hasColumn($table->getTable(), 'deleted')) {
                        $table->dropColumn('deleted');
                    }
                });
            }
        }
    }
};
