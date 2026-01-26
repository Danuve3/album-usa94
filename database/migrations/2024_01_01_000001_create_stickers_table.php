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
        Schema::create('stickers', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('number')->unique();
            $table->string('name');
            $table->unsignedSmallInteger('page_number');
            $table->unsignedSmallInteger('position_x');
            $table->unsignedSmallInteger('position_y');
            $table->unsignedSmallInteger('width');
            $table->unsignedSmallInteger('height');
            $table->boolean('is_horizontal')->default(false);
            $table->enum('rarity', ['common', 'shiny'])->default('common');
            $table->string('image_path')->nullable();
            $table->timestamps();

            $table->index('number');
            $table->index('page_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stickers');
    }
};
