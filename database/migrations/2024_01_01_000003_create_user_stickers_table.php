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
        Schema::create('user_stickers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sticker_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_glued')->default(false);
            $table->timestamp('obtained_at');
            $table->timestamps();

            $table->unique(['user_id', 'sticker_id', 'obtained_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_stickers');
    }
};
