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
        Schema::create('collection_towns', function (Blueprint $table) {
            $table->unsignedBigInteger('town_id');
            $table->unsignedBigInteger('collection_id');

            $table->primary(['town_id', 'collection_id']);

            $table->foreign('town_id')
                ->references('id')
                ->on('towns')
                ->onDelete('cascade');

            $table->foreign('collection_id')
                ->references('id')
                ->on('collections')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collection_towns');
    }
};
