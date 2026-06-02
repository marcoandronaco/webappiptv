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
                $table->boolean('is_series_parent')
                    ->default(false)
                    ->after('type');
            });
        }

        if (!Schema::hasColumn('channels', 'series_id')) {
            Schema::table('channels', function (Blueprint $table) {
                $table->string('series_id')
                    ->nullable()
                    ->after('stream_id');
            });
        }

        if (!Schema::hasColumn('channels', 'season_number')) {
            Schema::table('channels', function (Blueprint $table) {
                $table->unsignedInteger('season_number')
                    ->nullable()
                    ->after('series_id');
            });
        }

        if (!Schema::hasColumn('channels', 'episode_number')) {
            Schema::table('channels', function (Blueprint $table) {
                $table->unsignedInteger('episode_number')
                    ->nullable()
                    ->after('season_number');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('channels', 'episode_number')) {
            Schema::table('channels', function (Blueprint $table) {
                $table->dropColumn('episode_number');
            });
        }

        if (Schema::hasColumn('channels', 'season_number')) {
            Schema::table('channels', function (Blueprint $table) {
                $table->dropColumn('season_number');
            });
        }

        if (Schema::hasColumn('channels', 'series_id')) {
            Schema::table('channels', function (Blueprint $table) {
                $table->dropColumn('series_id');
            });
        }

        if (Schema::hasColumn('channels', 'is_series_parent')) {
            Schema::table('channels', function (Blueprint $table) {
                $table->dropColumn('is_series_parent');
            });
        }
    }
};
