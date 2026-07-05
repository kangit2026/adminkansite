<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Один slug на новину (замість окремого на кожну локаль) — простіше,
     * і URL новини не "стрибає" в залежності від мови.
     */
    public function up(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->string('slug_flat')->nullable()->after('slug');
        });

        foreach (DB::table('news')->select('id', 'slug')->get() as $row) {
            $decoded = json_decode((string) $row->slug, true) ?: [];
            $value = $decoded['en'] ?? $decoded['uk'] ?? null;

            DB::table('news')->where('id', $row->id)->update(['slug_flat' => $value]);
        }

        Schema::table('news', function (Blueprint $table) {
            $table->dropColumn('slug');
        });

        DB::statement('ALTER TABLE news CHANGE slug_flat slug VARCHAR(255) NULL');

        Schema::table('news', function (Blueprint $table) {
            $table->unique('slug');
        });
    }

    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->json('slug_json')->nullable()->after('slug');
        });

        foreach (DB::table('news')->select('id', 'slug')->get() as $row) {
            DB::table('news')->where('id', $row->id)->update([
                'slug_json' => json_encode(['uk' => $row->slug, 'en' => $row->slug]),
            ]);
        }

        Schema::table('news', function (Blueprint $table) {
            $table->dropColumn('slug');
        });

        DB::statement('ALTER TABLE news CHANGE slug_json slug JSON NULL');
    }
};
