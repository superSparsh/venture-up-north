<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // core workflow fields
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected'])
                ->default('draft')
                ->index()
                ->after('id');
            $table->json('pending_payload')->nullable()->after('status');
            $table->text('rejection_reason')->nullable()->after('pending_payload');
            $table->timestamp('published_at')->nullable()->after('rejection_reason');

            // audit
            $table->foreignId('submitted_by')->nullable()->after('published_at')
                ->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->after('submitted_by')
                ->constrained('users')->nullOnDelete();
        });

        // Backfill existing rows: mark as approved and set published_at = created_at
        DB::table('events')->update([
            'status'       => 'approved',
            'published_at' => DB::raw('COALESCE(published_at, created_at)'),
        ]);

        // Optional: if you already added events.user_id, copy it into submitted_by for history
        if (Schema::hasColumn('events', 'user_id')) {
            DB::statement('UPDATE events SET submitted_by = user_id WHERE submitted_by IS NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['submitted_by']);
            $table->dropForeign(['updated_by']);

            $table->dropColumn([
                'updated_by',
                'submitted_by',
                'published_at',
                'rejection_reason',
                'pending_payload',
                'status',
            ]);
        });
    }
};
