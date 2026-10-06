<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('career_page', function (Blueprint $table) {
            $table->id();

            $table->json('heading')->nullable();
            $table->json('city_label')->nullable();
            $table->json('all_categories_label')->nullable();
            $table->json('intro_text')->nullable();

            $table->json('meta_title')->nullable();
            $table->json('meta_description')->nullable();
            $table->string('og_image')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('career_page');
    }
};
