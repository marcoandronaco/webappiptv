<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE playlists MODIFY import_message LONGTEXT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE playlists MODIFY import_message VARCHAR(255) NULL');
    }
};
