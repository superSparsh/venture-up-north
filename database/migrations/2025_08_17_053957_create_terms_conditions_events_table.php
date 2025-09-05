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
        Schema::create('terms_conditions_events', function (Blueprint $table) {
            $table->id();
            // scope allows different placements (keep string for flexibility)
            $table->string('context')->default('contributor_submission')->index();
            $table->longText('body')->nullable(); // markdown/html content
            // boxes: [{label:string, enabled:bool, required:bool}]
            $table->json('boxes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['context']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('terms_conditions_events');
    }
};
