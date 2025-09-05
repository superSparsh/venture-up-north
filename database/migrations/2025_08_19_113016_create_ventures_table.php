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
        Schema::create('ventures', function (Blueprint $t) {
            $t->id();
            $t->string('slug')->unique();
            $t->string('title');
            $t->string('cover_image_url')->nullable();
            $t->text('summary')->nullable();
            $t->enum('visibility', ['public', 'unlisted', 'private'])->default('unlisted');
            $t->enum('status', ['draft', 'submitted', 'approved', 'published', 'archived'])->default('draft');
            $t->foreignId('owner_user_id')->nullable()->constrained('users')->nullOnDelete();
            $t->string('owner_guest_name')->nullable();
            $t->string('owner_guest_email')->nullable();
            $t->foreignId('created_by_admin_id')->nullable()->constrained('users')->nullOnDelete();
            $t->boolean('is_featured')->default(false);
            $t->unsignedInteger('items_count')->default(0);
            $t->unsignedInteger('days_count')->default(0);
            $t->json('data_snapshot')->nullable();
            // SEO
            $t->string('seo_title')->nullable();
            $t->text('seo_description')->nullable();
            $t->string('og_image_url')->nullable();
            $t->string('canonical_url')->nullable();

            $t->timestamp('published_at')->nullable();
            $t->timestamps();
            $t->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventures');
    }
};
