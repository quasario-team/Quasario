<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('display_name', 100);
            $table->foreignId('job_title_id')->constrained('job_titles')->restrictOnDelete();
            $table->foreignId('city_id')->constrained('cities')->restrictOnDelete();
            $table->string('profile_image')->nullable();
            $table->string('cover_image')->nullable();
            $table->text('bio')->nullable();
            $table->text('study')->nullable();
            $table->text('experience')->nullable();
            $table->boolean('is_active')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            // ERD: one active profile per account, enforced by the database.
            // VIRTUAL generated column: user_id when active, NULL otherwise.
            // UNIQUE ignores NULLs, so only one row per account can be active.
            $table->unsignedBigInteger('active_user_id')
                ->nullable()
                ->virtualAs('CASE WHEN is_active = 1 THEN user_id END');
            $table->unique('active_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
