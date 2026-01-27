<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->integer('lft')->unsigned()->nullable()->after('image_path');
            $table->integer('rgt')->unsigned()->nullable()->after('lft');
            $table->integer('depth')->unsigned()->nullable()->after('rgt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn(['lft', 'rgt', 'depth']);
        });
    }
};
