<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->json('main_picture_alt')->nullable()->after('main_picture');
            $table->json('logo_alt')->nullable()->after('logo');
        });

        Schema::table('project_pictures', function (Blueprint $table) {
            $table->json('alt')->nullable()->after('path');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['main_picture_alt', 'logo_alt']);
        });

        Schema::table('project_pictures', function (Blueprint $table) {
            $table->dropColumn('alt');
        });
    }
};
