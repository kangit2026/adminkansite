<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->boolean('is_partner')->default(false)->after('big_picture');
        });

        DB::table('news')->whereIn('slug', [
            'partnerstvo-z-merezeiu-supermarketiv-megamarket-ta-kinoteatriv-batterfliai',
            'glovo-veze-vse-shhe-svidse-dlia-meskanciv-kan',
            'puma-ta-kan-objednalis-v-neimovirnii-kolaboraciyi',
        ])->update(['is_partner' => true]);

        if (DB::table('news')->where('slug', 'strategic-partnership-kan-dobrobut')->exists()) {
            return;
        }

        $filename = '70c6fc2b-5caf-456e-b3cc-43d14a802dc8.png';
        $source = '/var/lib/docker/volumes/kan-files-data/_data/file/'.$filename;
        $destDir = storage_path('app/public/news');
        $picture = null;

        if (is_file($source)) {
            if (! is_dir($destDir)) {
                mkdir($destDir, 0775, true);
            }
            copy($source, $destDir.'/'.$filename);
            $picture = 'news/'.$filename;
        }

        $title = 'Стратегічне партнерство KAN та медичної мережі “ДОБРОБУТ”';

        DB::table('news')->insert([
            'slug' => 'strategic-partnership-kan-dobrobut',
            'title' => json_encode(['uk' => $title, 'en' => $title], JSON_UNESCAPED_UNICODE),
            'text' => json_encode(['uk' => '', 'en' => ''], JSON_UNESCAPED_UNICODE),
            'picture' => $picture,
            'external_link' => 'https://kyiv.tsn.ua/partnerstvo-kan-dobrobut-z-turbotoyu-pro-zdorov-ya-meshkanciv-1927051.html',
            'big_picture' => false,
            'is_partner' => true,
            'status' => 'published',
            'published_at' => '2024-01-15 00:00:00',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('news')->where('slug', 'strategic-partnership-kan-dobrobut')->delete();

        Schema::table('news', function (Blueprint $table) {
            $table->dropColumn('is_partner');
        });
    }
};
