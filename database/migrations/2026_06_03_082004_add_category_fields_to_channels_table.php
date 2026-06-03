<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('channels', function (Blueprint $table) {
            $table->string('category_name')->nullable()->after('type');
            $table->string('subcategory_name')->nullable()->after('category_name');
            $table->integer('channel_number')->nullable()->after('subcategory_name');
        });
    }

    public function down(): void
    {
        Schema::table('channels', function (Blueprint $table) {
            $table->dropColumn([
                'category_name',
                'subcategory_name',
                'channel_number',
            ]);
        });
    }
};