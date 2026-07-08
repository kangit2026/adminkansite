<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * achievements_text — цитата "Головні досягнення...", помилково додана
     * в загальні site_settings; насправді керується окремо, разом з рештою
     * контенту /about.
     */
    public function up(): void
    {
        Schema::table('about_page', function (Blueprint $table) {
            $table->json('achievements_text')->nullable();
        });

        $value = DB::table('site_settings')->where('id', 1)->value('achievements_text');

        if ($value) {
            DB::table('about_page')->where('id', 1)->update(['achievements_text' => $value]);
        }

        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn('achievements_text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->json('achievements_text')->nullable();
        });

        $value = DB::table('about_page')->where('id', 1)->value('achievements_text');

        if ($value) {
            DB::table('site_settings')->where('id', 1)->update(['achievements_text' => $value]);
        }

        Schema::table('about_page', function (Blueprint $table) {
            $table->dropColumn('achievements_text');
        });
    }
};
