<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ERD social_links: the contact links of a profile (WhatsApp, Facebook ...).
        Schema::create('social_links', function (Blueprint $table) {
            $table->id();
            // Deleting a profile deletes its links too.
            $table->foreignId('profile_id')->constrained('profiles')->cascadeOnDelete();
            // The same 9 names as SocialLink::PLATFORMS (written here too, so this
            // old migration never changes if the list changes one day).
            $table->enum('platform', [
                'whatsapp',
                'facebook',
                'linkedin',
                'youtube',
                'x',
                'instagram',
                'telegram',
                'github',
                'website',
            ]);
            $table->string('url', 2048);
            $table->timestamps();

            // One link per platform in each profile.
            $table->unique(['profile_id', 'platform']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_links');
    }
};
