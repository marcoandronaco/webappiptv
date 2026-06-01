<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('playlists', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('type')->default('m3u'); 
            // m3u oppure xtream

            $table->text('m3u_url')->nullable();

            $table->string('xtream_host')->nullable();
            $table->string('xtream_username')->nullable();
            $table->text('xtream_password')->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamp('last_used_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('playlists');
    }
};
