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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()
                ->constrained('project_categories')->nullOnDelete();
            $table->string('slug')->unique();
            $table->json('name');
            $table->json('text')->nullable();
            $table->json('address')->nullable();
            // {"uk": ["пункт 1", ...], "en": [...]} — без окремого довідника,
            // бо джерело даних не дає надійного зв'язку інфраструктури між
            // мовами (різні id, різний порядок) — див. ImportProjectsExport.
            $table->json('infrastructure_items')->nullable();
            $table->json('meta_title')->nullable();
            $table->json('meta_description')->nullable();
            $table->string('main_picture')->nullable();
            $table->string('logo')->nullable();
            $table->string('video')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->string('full_area')->nullable();
            $table->string('project_office')->nullable();
            $table->string('general_contractor')->nullable();
            $table->string('developer')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->string('status')->default('draft'); // draft|published
            $table->timestamp('published_at')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
