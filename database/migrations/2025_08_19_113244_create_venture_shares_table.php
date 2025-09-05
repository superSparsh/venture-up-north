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
        Schema::create('venture_shares', function (Blueprint $t) {
            $t->id();
            $t->foreignId('venture_id')->constrained()->cascadeOnDelete();
            $t->string('code')->unique();
            $t->unsignedInteger('clicks')->default(0);
            $t->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venture_shares');
    }
};
