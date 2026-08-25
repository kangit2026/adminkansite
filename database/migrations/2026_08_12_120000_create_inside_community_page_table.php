<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inside_community_page', function (Blueprint $table) {
            $table->id();

            $table->string('hero_video')->nullable();
            $table->string('hero_image_uk')->nullable();
            $table->string('hero_image_en')->nullable();
            $table->string('complex_logo_1')->nullable();
            $table->string('complex_logo_2')->nullable();

            $table->json('hero_title')->nullable();
            $table->json('hero_subtitle')->nullable();
            $table->json('hero_paragraph_1')->nullable();
            $table->json('hero_paragraph_2')->nullable();
            $table->json('hero_paragraph_3')->nullable();

            $table->json('philosophy_title')->nullable();
            $table->json('philosophy_subtitle')->nullable();

            $table->json('meta_title')->nullable();
            $table->json('meta_description')->nullable();
            $table->string('og_image')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inside_community_page');
    }
};
