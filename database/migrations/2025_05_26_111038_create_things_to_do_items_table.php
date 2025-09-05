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
        Schema::create('things_to_do_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('things_to_do_categories')->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('image')->nullable();
            $table->string('big_image')->nullable();
            $table->text('summary')->nullable();
            $table->json('content')->nullable();
            $table->longText('address')->nullable();
            $table->longText('opening_times')->nullable();
            $table->string('email')->nullable();
            $table->string('phone_number')->nullable();
            $table->longText('location')->nullable();
            $table->longText('video')->nullable();
            $table->json('custom_fields')->nullable();
            $table->json('custom_buttons')->nullable();
            $table->json('social_links')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('seo_image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('things_to_do_items');
    }
};
