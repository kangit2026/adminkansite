<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('market_page', function (Blueprint $table) {
            $table->id();

            $table->string('hero_image')->nullable();
            $table->string('phone')->nullable();

            $table->json('badge')->nullable();
            $table->json('title')->nullable();
            $table->json('sell_text')->nullable();
            $table->json('question')->nullable();
            $table->json('answer')->nullable();
            $table->json('contact_label')->nullable();
            $table->json('pros_title')->nullable();
            $table->json('pros_items')->nullable();
            $table->json('trust_text')->nullable();

            $table->json('meta_title')->nullable();
            $table->json('meta_description')->nullable();
            $table->string('og_image')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_page');
    }
};
