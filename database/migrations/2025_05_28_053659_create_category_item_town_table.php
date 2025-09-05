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
        Schema::create('category_item_town', function (Blueprint $table) {
            $table->unsignedBigInteger('things_to_do_item_id');
            $table->unsignedBigInteger('town_id');

            $table->primary(['things_to_do_item_id', 'town_id']);

            $table->foreign('things_to_do_item_id')
                ->references('id')
                ->on('things_to_do_items')
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
        Schema::dropIfExists('category_item_town');
    }
};
