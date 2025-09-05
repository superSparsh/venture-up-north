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
        Schema::create('tour_tile_town', function (Blueprint $table) {
            $table->unsignedBigInteger('tour_tile_id');
            $table->unsignedBigInteger('town_id');

            $table->primary(['tour_tile_id', 'town_id']);

            $table->foreign('tour_tile_id')
                ->references('id')
                ->on('tour_tiles')
                ->onDelete('cascade');

            $table->foreign('town_id')
                ->references('id')
                ->on('towns')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_tile_town');
    }
};
