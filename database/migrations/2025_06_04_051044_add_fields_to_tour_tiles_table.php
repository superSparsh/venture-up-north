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
        Schema::table('tour_tiles', function (Blueprint $table) {
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
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tour_tiles', function (Blueprint $table) {
            //
        });
    }
};
