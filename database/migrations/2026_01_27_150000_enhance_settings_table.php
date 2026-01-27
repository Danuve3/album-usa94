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
        Schema::table('settings', function (Blueprint $table) {
            $table->string('name')->nullable()->after('key');
            $table->text('description')->nullable()->after('name');
            $table->string('group')->default('general')->after('description');
        });

        Schema::create('configuration_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('setting_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('key');
            $table->text('old_value')->nullable();
            $table->text('new_value');
            $table->timestamps();

            $table->index(['setting_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuration_logs');

        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['name', 'description', 'group']);
        });
    }
};
