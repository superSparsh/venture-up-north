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
        Schema::create('venture_magazine_tour_tiles', function (Blueprint $table) {
            $table->foreignId('venture_magazine_id')->constrained()->onDelete('cascade');
            $table->foreignId('tour_tile_id')->constrained()->onDelete('cascade');
            $table->primary(['venture_magazine_id', 'tour_tile_id']);
        });

        Schema::create('venture_magazine_experiences', function (Blueprint $table) {
            $table->foreignId('venture_magazine_id')->constrained()->onDelete('cascade');
            $table->foreignId('experience_id')->constrained()->onDelete('cascade');
            $table->primary(['venture_magazine_id', 'experience_id']);
        });

        Schema::create('venture_magazine_towns', function (Blueprint $table) {
            $table->foreignId('venture_magazine_id')->constrained()->onDelete('cascade');
            $table->foreignId('town_id')->constrained()->onDelete('cascade');
            $table->primary(['venture_magazine_id', 'town_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venture_magazine_tour_tiles');
        Schema::dropIfExists('venture_magazine_experiences');
        Schema::dropIfExists('venture_magazine_towns');
    }
};
