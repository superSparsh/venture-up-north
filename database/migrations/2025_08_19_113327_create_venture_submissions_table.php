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
        Schema::create('venture_submissions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('venture_id')->constrained()->cascadeOnDelete();
            $t->string('submitted_by_name');
            $t->string('submitted_by_email')->nullable();
            $t->text('message')->nullable();
            $t->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $t->foreignId('reviewed_by_admin_id')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamp('reviewed_at')->nullable();
            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venture_submissions');
    }
};
