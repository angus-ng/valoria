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
        Schema::create('armor_pieces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('armor_set_id')->constrained()->onDelete('cascade');
            $table->enum('slot', ['Head', 'Chest', 'Arms', 'Waist', 'Legs']);
            $table->timestamps();
        
            $table->unique(['armor_set_id', 'slot']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('armor_pieces');
    }
};
