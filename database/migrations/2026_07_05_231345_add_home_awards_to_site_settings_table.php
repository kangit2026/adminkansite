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
            $table->json('motto')->nullable();
            $table->json('awards_title')->nullable();

            $table->unsignedInteger('award_first_value')->nullable();
            $table->json('award_first_description')->nullable();

            $table->unsignedInteger('award_second_value')->nullable();
            $table->json('award_second_description')->nullable();

            $table->unsignedInteger('award_third_value')->nullable();
            $table->json('award_third_description')->nullable();

            $table->unsignedInteger('award_fourth_value')->nullable();
            $table->json('award_fourth_description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'motto',
                'awards_title',
                'award_first_value',
                'award_first_description',
                'award_second_value',
                'award_second_description',
                'award_third_value',
                'award_third_description',
                'award_fourth_value',
                'award_fourth_description',
            ]);
        });
    }
};
