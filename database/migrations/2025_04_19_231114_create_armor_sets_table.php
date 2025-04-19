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
        Schema::create('armor_sets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('monster_id')->nullable()->constrained()->onDelete('set null');
            $table->string('source_type')->nullable();
            $table->integer('rarity');
            $table->boolean('event_only')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('armor_sets');
    }
};
