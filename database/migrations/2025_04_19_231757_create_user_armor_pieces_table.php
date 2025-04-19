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
        Schema::create('user_armor_pieces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('armor_piece_id')->constrained()->onDelete('cascade');
            $table->boolean('obtained')->default(false);
            $table->timestamp('obtained_at')->nullable();
            $table->timestamps();
        
            $table->unique(['user_id', 'armor_piece_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_armor_pieces');
    }
};
