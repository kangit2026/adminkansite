<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Redirect extends Model
{
    protected $fillable = ['from_path', 'to_path', 'locale', 'status_code'];

    /**
     * Записує редирект зі старого шляху на новий. Якщо колись вже існував
     * редирект НА старий шлях — переліновує його одразу на новий (щоб не
     * утворювався ланцюжок A->B->C, а завжди був один стрибок A->C).
     */
    public static function point(string $fromPath, string $toPath, ?string $locale = null): void
    {
        if ($fromPath === $toPath) {
            return;
        }

        static::where('to_path', $fromPath)->update(['to_path' => $toPath]);

        static::updateOrCreate(
            ['from_path' => $fromPath],
            ['to_path' => $toPath, 'locale' => $locale]
        );

        // Прибираємо петлю, якщо раптом from == to після переліновки вище.
        static::where('from_path', $toPath)->delete();
    }
}
