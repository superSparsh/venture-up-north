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
        Schema::create('event_page_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->text('summary')->nullable();
            $table->longText('description')->nullable();
            $table->string('hero_image')->nullable();
            $table->string('big_hero_image')->nullable();
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
        Schema::dropIfExists('event_page_settings');
    }
};
