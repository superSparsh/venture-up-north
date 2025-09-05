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
            $table->text('note')->nullable()->after('pending_payload');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->text('note')->nullable()->after('pending_payload');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('venture_magazines', function (Blueprint $table) {
            $table->dropColumn('note');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('note');
        });
    }
};
