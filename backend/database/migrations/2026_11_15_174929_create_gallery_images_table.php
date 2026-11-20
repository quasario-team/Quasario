<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ERD gallery_images: the work gallery of a profile.
        Schema::create('gallery_images', function (Blueprint $table) {
            $table->id();
            // Deleting a profile deletes its gallery rows too.
            $table->foreignId('profile_id')->constrained('profiles')->cascadeOnDelete();
            $table->string('image_path'); // e.g. profiles/7/gallery/abc.jpg
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_images');
    }
};
