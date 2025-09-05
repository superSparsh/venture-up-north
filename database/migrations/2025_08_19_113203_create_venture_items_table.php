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
        Schema::create('venture_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('venture_id')->constrained()->cascadeOnDelete();
            $t->foreignId('venture_day_id')->nullable()->constrained('venture_days')->nullOnDelete();
            $t->unsignedInteger('position')->default(1);
            $t->enum('item_type', ['event', 'experience', 'tour', 'listing', 'town']);
            $t->unsignedBigInteger('item_id');
            $t->string('source_url');
            $t->string('title');
            $t->string('image_url')->nullable();
            $t->json('tags')->nullable();
            $t->decimal('lat', 10, 7)->nullable();
            $t->decimal('lng', 10, 7)->nullable();
            $t->json('payload')->nullable();
            $t->timestamps();
            $t->softDeletes();

            $t->index(['venture_id', 'venture_day_id', 'position']);
            $t->index(['item_type', 'item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venture_items');
    }
};
