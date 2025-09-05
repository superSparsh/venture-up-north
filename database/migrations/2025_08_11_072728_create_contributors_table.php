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
        Schema::create('contributors', function (Blueprint $table) {
            $table->id();
            // The underlying application user
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete(); // if user is deleted, remove contributor

            // Linked author (must exist)
            $table->foreignId('author_id')
                ->constrained('authors')
                ->restrictOnDelete(); // prevent deleting author while contributor exists

            // Admin control
            $table->enum('status', ['active', 'blocked'])
                ->default('active')
                ->index();
            $table->text('blocked_reason')->nullable();
            $table->timestamp('blocked_at')->nullable();

            // (Optional but handy for dashboards; keep if you want)
            $table->unsignedInteger('submissions_count')->default(0);
            $table->unsignedInteger('approved_count')->default(0);
            $table->unsignedInteger('rejected_count')->default(0);
            $table->timestamp('last_submission_at')->nullable();
            // One contributor per user
            $table->unique('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contributors');
    }
};
