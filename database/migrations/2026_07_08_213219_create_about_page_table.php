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
        Schema::create('about_page', function (Blueprint $table) {
            $table->id();

            // Хіро-блок
            $table->json('motto')->nullable();
            $table->json('heading')->nullable();

            // Текст-вступ
            $table->json('history')->nullable();
            $table->json('specialists')->nullable();

            // Блок "Сьогодні"
            $table->json('today_title')->nullable();
            $table->json('today_description')->nullable();

            // Блок "Переваги" (+ фіксований список з 4 пунктів)
            $table->json('merit_title')->nullable();
            $table->json('merit_description')->nullable();
            $table->json('merit_list_1')->nullable();
            $table->json('merit_list_2')->nullable();
            $table->json('merit_list_3')->nullable();
            $table->json('merit_list_4')->nullable();

            // SEO
            $table->json('meta_title')->nullable();
            $table->json('meta_description')->nullable();
            $table->string('og_image')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('about_page');
    }
};
