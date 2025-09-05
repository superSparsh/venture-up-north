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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('hero_image')->nullable();
            $table->string('big_hero_image')->nullable();
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
            $table->string('event_date_label')->nullable();
            $table->date('start_date')->nullable(); 
            $table->date('end_date')->nullable();     
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
        Schema::dropIfExists('events');
    }
};
