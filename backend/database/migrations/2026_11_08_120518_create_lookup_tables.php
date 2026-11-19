<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ERD: lookup tables have no timestamps.
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
        });

        Schema::create('job_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150)->unique();
        });

        Schema::create('job_titles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_category_id')->constrained('job_categories')->restrictOnDelete();
            $table->string('name', 100);

            // The same title name may exist in two categories, but not twice in one.
            $table->unique(['job_category_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_titles');
        Schema::dropIfExists('job_categories');
        Schema::dropIfExists('cities');
    }
};
