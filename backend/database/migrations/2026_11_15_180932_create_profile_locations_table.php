<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ERD profile_locations: the place of work of a profile (at most ONE per profile).
        Schema::create('profile_locations', function (Blueprint $table) {
            $table->id();
            // unique(): one location per profile. Deleting a profile deletes its location too.
            $table->foreignId('profile_id')->unique()->constrained('profiles')->cascadeOnDelete();
            $table->decimal('latitude', 10, 7);  // e.g. 33.5138000 (north / south)
            $table->decimal('longitude', 10, 7); // e.g. 36.2765000 (east / west)
            $table->string('place_image')->nullable(); // e.g. profiles/7/location/abc.jpg
            $table->text('directions')->nullable();    // "Second floor, next to the bakery"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profile_locations');
    }
};
