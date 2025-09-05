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
        Schema::create('collection_content_listings', function (Blueprint $table) {
            $table->unsignedBigInteger('things_to_do_item_id');
            $table->unsignedBigInteger('collection_id');

            $table->primary(['things_to_do_item_id', 'collection_id']);

            $table->foreign('things_to_do_item_id')
                ->references('id')
                ->on('things_to_do_items')
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
        Schema::dropIfExists('collection_content_listings');
    }
};
