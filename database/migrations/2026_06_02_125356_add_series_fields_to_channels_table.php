<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('channels', 'is_series_parent')) {
            Schema::table('channels', function (Blueprint $table) {
                $table->boolean('is_series_parent')->default(false)->after('type');
                $table->string('series_id')->nullable()->after('stream_id');
                $table->unsignedInteger('season_number')->nullable()->after('series_id');
                $table->unsignedInteger('episode_number')->nullable()->after('season_number');
            });
        }
    }

    public function down(): void
    {
        Schema::table('channels', function (Blueprint $table) {
            if (Schema::hasColumn('channels', 'is_series_parent')) {
                $table->dropColumn([
                    'is_series_parent',
                    'series_id',
                    'season_number',
                    'episode_number',
                ]);
            }
        });
    }
};
