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
        Schema::table('venture_magazines', function (Blueprint $table) {
            $table->boolean('is_featured')->nullable()->default(false)->after('is_published');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('venture_magazines', function (Blueprint $table) {
            $table->dropColumn('is_featured');
        });
    }
};
