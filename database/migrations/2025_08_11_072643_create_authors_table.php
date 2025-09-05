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
        Schema::create('authors', function (Blueprint $table) {
            $table->id();
            // Who this author represents (kept simple for now)
            // 'user' (registered user) or 'team_member' (from your team_members table)
            $table->enum('type', ['user', 'team_member'])->index();

            // ID in the source table (users.id or team_members.id)
            $table->unsignedBigInteger('ref_id')->index();

            // Public-facing fields
            $table->string('display_name');     // default from user name; admin can override
            $table->string('slug')->unique();   // for author pages
            $table->string('avatar_path')->nullable();

            // Ensure one author per (type, ref_id)
            $table->unique(['type', 'ref_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('authors');
    }
};
