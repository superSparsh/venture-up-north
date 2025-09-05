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
        Schema::create('venture_days', function (Blueprint $t) {
            $t->id();
            $t->foreignId('venture_id')->constrained()->cascadeOnDelete();
            $t->string('title');
            $t->unsignedSmallInteger('day_index');
            $t->timestamps();
            $t->softDeletes();
            $t->unique(['venture_id', 'day_index']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venture_days');
    }
};
