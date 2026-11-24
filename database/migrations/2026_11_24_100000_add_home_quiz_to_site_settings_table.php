<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Квіз «Що вас цікавить?» у блоці заявки на головній: питання й варіанти відповідей (JSON). Порожньо — беруться типові з database/data/home-quiz.json. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->json('home_quiz')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn('home_quiz');
        });
    }
};
