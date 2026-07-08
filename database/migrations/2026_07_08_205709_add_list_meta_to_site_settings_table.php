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
            $table->json('news_meta_title')->nullable();
            $table->json('news_meta_description')->nullable();
            $table->json('projects_meta_title')->nullable();
            $table->json('projects_meta_description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'news_meta_title',
                'news_meta_description',
                'projects_meta_title',
                'projects_meta_description',
            ]);
        });
    }
};
