<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/**
 * Від партнерів (Добробут, Мегамаркет, Glovo, Puma) відмовилися (власник, 2026-11-12).
 * Прибираємо функцію: прапорець «партнер», логотип партнера, новину про Добробут,
 * яку вставила міграція 2026_10_07_010000. Інші новини лишаються звичайними новинами.
 */
return new class extends Migration
{
    private const DOBROBUT_SLUG = 'strategic-partnership-kan-dobrobut';

    public function up(): void
    {
        $disk = Storage::disk('public');

        if (Schema::hasColumn('news', 'partner_logo')) {
            DB::table('news')->whereNotNull('partner_logo')->pluck('partner_logo')
                ->each(fn (string $path) => $disk->delete($path));
        }

        $dobrobut = DB::table('news')->where('slug', self::DOBROBUT_SLUG)->first();

        if ($dobrobut) {
            if ($dobrobut->picture) {
                $disk->delete($dobrobut->picture);
            }

            DB::table('news')->where('id', $dobrobut->id)->delete(); // теги — cascade
        }

        $columns = array_values(array_filter(
            ['is_partner', 'partner_logo', 'partner_logo_alt'],
            fn (string $column) => Schema::hasColumn('news', $column),
        ));

        if ($columns) {
            Schema::table('news', fn (Blueprint $table) => $table->dropColumn($columns));
        }
    }

    /** Повертає лише колонки (порожні); дані й файли не відновлюються. */
    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->boolean('is_partner')->default(false)->after('big_picture');
            $table->string('partner_logo')->nullable()->after('picture');
            $table->json('partner_logo_alt')->nullable()->after('partner_logo');
        });
    }
};
