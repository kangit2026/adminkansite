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
        Schema::table('site_settings', function (Blueprint $table) {
            // Головна — відео-маніфест у хіро-блоці (зараз захардкоджений
            // файл public/home/manifest.mp4 на фронті). Якщо порожньо —
            // фронт лишає дефолтний файл.
            $table->string('home_video')->nullable();

            // Банер "Продаж комерційних приміщень" (гол. сторінка + футер
            // на /areas) і банер "Шукаємо ділянки для нових проектів"
            // (футер на решті сторінок) — зараз захардкоджені в i18n.
            $table->json('commercial_ad_title')->nullable();
            $table->string('commercial_ad_link')->nullable();
            $table->json('project_ad_title')->nullable();
            $table->string('project_ad_link')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'home_video',
                'commercial_ad_title',
                'commercial_ad_link',
                'project_ad_title',
                'project_ad_link',
            ]);
        });
    }
};
