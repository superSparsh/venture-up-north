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
            // Add a new column for linking directly to contributors table
            if (!Schema::hasColumn('venture_magazines', 'real_contributor_id')) {
                $table->unsignedBigInteger('real_contributor_id')->nullable()->after('contributor_id');

                $table->foreign('real_contributor_id', 'vm_real_contributor_fk')
                    ->references('id')->on('contributors')
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('venture_magazines', function (Blueprint $table) {
            $table->dropForeign('vm_real_contributor_fk');
            $table->dropColumn('real_contributor_id');
        });
    }
};
