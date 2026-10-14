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
            $table->string('partner_logo')->nullable()->after('picture');
        });

        $logos = [
            'puma-ta-kan-objednalis-v-neimovirnii-kolaboraciyi' => 'ee29f4e2-b597-4a5e-8263-3b7869ca2300.png',
            'strategic-partnership-kan-dobrobut' => '70c6fc2b-5caf-456e-b3cc-43d14a802dc8.png',
            'partnerstvo-z-merezeiu-supermarketiv-megamarket-ta-kinoteatriv-batterfliai' => '59f0af96-486c-443a-9804-d52bcb515ddc.png',
            'glovo-veze-vse-shhe-svidse-dlia-meskanciv-kan' => 'c0bc5f69-47cc-4d3a-acac-b51c7a511b85.png',
        ];

        $sourceDir = '/var/lib/docker/volumes/kan-files-data/_data/file';
        $destDir = storage_path('app/public/partners');

        if (! is_dir($destDir)) {
            mkdir($destDir, 0775, true);
        }

        @chown($destDir, 'www-data');
        @chgrp($destDir, 'www-data');

        foreach ($logos as $slug => $filename) {
            $source = $sourceDir.'/'.$filename;

            if (! is_file($source)) {
                continue;
            }

            $dest = $destDir.'/'.$filename;
            copy($source, $dest);
            chmod($dest, 0644);
            @chown($dest, 'www-data');
            @chgrp($dest, 'www-data');

            DB::table('news')->where('slug', $slug)->update([
                'partner_logo' => 'partners/'.$filename,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->dropColumn('partner_logo');
        });
    }
};
