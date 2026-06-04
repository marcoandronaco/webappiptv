<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('playlists')) {
            Schema::table('playlists', function (Blueprint $table) {
                if (!Schema::hasColumn('playlists', 'epg_url')) {
                    $table->text('epg_url')->nullable();
                }

                if (!Schema::hasColumn('playlists', 'last_epg_import_at')) {
                    $table->timestamp('last_epg_import_at')->nullable();
                }
            });
        }

        if (Schema::hasTable('channels')) {
            Schema::table('channels', function (Blueprint $table) {
                if (!Schema::hasColumn('channels', 'tvg_id')) {
                    $table->string('tvg_id')->nullable()->index();
                }

                if (!Schema::hasColumn('channels', 'epg_channel_id')) {
                    $table->string('epg_channel_id')->nullable()->index();
                }

                if (!Schema::hasColumn('channels', 'stream_id')) {
                    $table->string('stream_id')->nullable()->index();
                }

                if (!Schema::hasColumn('channels', 'channel_number')) {
                    $table->unsignedInteger('channel_number')->nullable()->index();
                }
            });
        }

        if (!Schema::hasTable('epg_programmes')) {
            Schema::create('epg_programmes', function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger('playlist_id')->nullable()->index();
                $table->unsignedBigInteger('channel_id')->index();

                $table->string('epg_channel_id')->nullable()->index();
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('category')->nullable();

                $table->dateTime('start_at')->index();
                $table->dateTime('end_at')->index();

                $table->timestamps();

                $table->unique(['channel_id', 'start_at', 'end_at'], 'epg_channel_time_unique');
                $table->index(['channel_id', 'start_at', 'end_at'], 'epg_channel_time_index');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('epg_programmes');

        if (Schema::hasTable('playlists')) {
            Schema::table('playlists', function (Blueprint $table) {
                if (Schema::hasColumn('playlists', 'epg_url')) {
                    $table->dropColumn('epg_url');
                }

                if (Schema::hasColumn('playlists', 'last_epg_import_at')) {
                    $table->dropColumn('last_epg_import_at');
                }
            });
        }

        if (Schema::hasTable('channels')) {
            Schema::table('channels', function (Blueprint $table) {
                foreach (['tvg_id', 'epg_channel_id', 'stream_id', 'channel_number'] as $column) {
                    if (Schema::hasColumn('channels', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
