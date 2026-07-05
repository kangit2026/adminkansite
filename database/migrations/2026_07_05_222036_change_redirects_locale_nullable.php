<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * URL тепер один на всі мови (єдиний slug), тож локаль для редиректу
     * більше не потрібна — робимо колонку необов'язковою (null = діє для
     * будь-якої локалі) і унікальність лишаємо тільки по from_path.
     */
    public function up(): void
    {
        Schema::table('redirects', function (Blueprint $table) {
            $table->dropUnique(['from_path', 'locale']);
        });

        DB::statement('ALTER TABLE redirects MODIFY locale VARCHAR(5) NULL');

        Schema::table('redirects', function (Blueprint $table) {
            $table->unique('from_path');
        });
    }

    public function down(): void
    {
        Schema::table('redirects', function (Blueprint $table) {
            $table->dropUnique(['from_path']);
        });

        DB::statement("ALTER TABLE redirects MODIFY locale VARCHAR(5) NOT NULL");

        Schema::table('redirects', function (Blueprint $table) {
            $table->unique(['from_path', 'locale']);
        });
    }
};
