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
            $table->json('picture_alt')->nullable()->after('picture');
            $table->json('partner_logo_alt')->nullable()->after('partner_logo');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->json('image_alt')->nullable()->after('image');
        });

        Schema::table('inside_community_blocks', function (Blueprint $table) {
            $table->json('image_alt')->nullable()->after('image');
        });

        Schema::table('inside_community_page', function (Blueprint $table) {
            $table->json('hero_image_alt')->nullable()->after('hero_image_en');
            $table->json('complex_logo_1_alt')->nullable()->after('complex_logo_1');
            $table->json('complex_logo_2_alt')->nullable()->after('complex_logo_2');
        });

        Schema::table('market_page', function (Blueprint $table) {
            $table->json('hero_image_alt')->nullable()->after('hero_image');
        });

        $this->prefillFromTitles();
    }

    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->dropColumn(['picture_alt', 'partner_logo_alt']);
        });
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('image_alt');
        });
        Schema::table('inside_community_blocks', function (Blueprint $table) {
            $table->dropColumn('image_alt');
        });
        Schema::table('inside_community_page', function (Blueprint $table) {
            $table->dropColumn(['hero_image_alt', 'complex_logo_1_alt', 'complex_logo_2_alt']);
        });
        Schema::table('market_page', function (Blueprint $table) {
            $table->dropColumn('hero_image_alt');
        });
    }

    private function prefillFromTitles(): void
    {
        foreach (DB::table('news')->get() as $row) {
            [$uk, $en] = $this->names($row->title ?? null);
            DB::table('news')->where('id', $row->id)->update([
                'picture_alt' => $this->json($uk, $en),
                'partner_logo_alt' => $row->partner_logo
                    ? $this->json(trim($uk.' логотип'), trim($en.' logo'))
                    : null,
            ]);
        }

        foreach (DB::table('services')->whereNotNull('image')->get() as $row) {
            [$uk, $en] = $this->names($row->title ?? null);
            DB::table('services')->where('id', $row->id)->update([
                'image_alt' => $this->json($uk, $en),
            ]);
        }

        foreach (DB::table('inside_community_blocks')->whereNotNull('image')->get() as $row) {
            [$uk, $en] = $this->names($row->title ?? null);
            DB::table('inside_community_blocks')->where('id', $row->id)->update([
                'image_alt' => $this->json($uk, $en),
            ]);
        }
    }

    private function names(?string $json): array
    {
        $data = json_decode((string) $json, true) ?: [];
        $uk = trim((string) ($data['uk'] ?? ''));
        $en = trim((string) ($data['en'] ?? ''));

        return [$uk, $en !== '' ? $en : $uk];
    }

    private function json(string $uk, string $en): string
    {
        return json_encode(['uk' => $uk, 'en' => $en], JSON_UNESCAPED_UNICODE);
    }
};
