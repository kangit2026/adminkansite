<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class CareerPage extends Model
{
    use HasTranslations;

    protected $table = 'career_page';

    public array $translatable = [
        'heading',
        'city_label',
        'all_categories_label',
        'intro_text',
        'meta_title',
        'meta_description',
    ];

    protected $fillable = [
        'heading',
        'city_label',
        'all_categories_label',
        'intro_text',
        'meta_title',
        'meta_description',
        'og_image',
        'notify_emails',
        'referral_url',
    ];

    public static function current(): self
    {
        return static::query()->firstOrCreate(['id' => 1]);
    }

    /** Адреси для листа про новий відгук (у налаштуваннях — через кому). */
    public function notifyEmailList(): array
    {
        return array_values(array_filter(
            array_map('trim', explode(',', (string) $this->notify_emails)),
            fn (string $email) => filter_var($email, FILTER_VALIDATE_EMAIL) !== false,
        ));
    }
}
