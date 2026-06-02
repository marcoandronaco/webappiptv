<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('channels', function (Blueprint $table) {
            $table->id();

            $table->foreignId('playlist_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('name');
            $table->string('type')->default('live');
            // live, film, serie

            $table->string('logo')->nullable();
            $table->string('group_title')->nullable();
            $table->string('tvg_id')->nullable();

            $table->text('stream_url');
            $table->string('stream_id')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('channels');
    }
};
