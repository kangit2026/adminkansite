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
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();

            // Головна сторінка — SEO
            $table->json('home_meta_title')->nullable();
            $table->json('home_meta_description')->nullable();
            $table->string('home_og_image')->nullable();
            $table->boolean('home_robots_index')->default(true);
            $table->boolean('home_robots_follow')->default(true);

            // Контакти (виводяться у футері на кожній сторінці)
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('telegram_chat_link')->nullable();

            // Соцмережі
            $table->string('social_instagram')->nullable();
            $table->string('social_youtube')->nullable();
            $table->string('social_facebook')->nullable();
            $table->string('social_pinterest')->nullable();
            $table->string('social_telegram')->nullable();

            // Футер
            $table->json('copyright_text')->nullable();
            $table->unsignedSmallInteger('copyright_year')->default(date('Y'));

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
