<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('playlists', function (Blueprint $table) {
            $table->string('import_status')->default('pending')->after('is_active');
            $table->text('import_message')->nullable()->after('import_status');
            $table->unsignedInteger('imported_channels_count')->default(0)->after('import_message');
            $table->timestamp('import_started_at')->nullable()->after('imported_channels_count');
            $table->timestamp('import_finished_at')->nullable()->after('import_started_at');
        });
    }

    public function down(): void
    {
        Schema::table('playlists', function (Blueprint $table) {
            $table->dropColumn([
                'import_status',
                'import_message',
                'imported_channels_count',
                'import_started_at',
                'import_finished_at',
            ]);
        });
    }
};
