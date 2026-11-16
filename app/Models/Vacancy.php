<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Vacancy extends Model
{
    public const STATUSES = [
        'draft' => 'Чернетка',
        'active' => 'Активна',
        'closed' => 'Закрита',
    ];

    public const EMPLOYMENT_TYPES = [
        'full_time' => 'Повна зайнятість',
        'part_time' => 'Часткова зайнятість',
        'internship' => 'Стажування',
    ];

    /** Поля-списки: у формі — textarea, кожен пункт з нового рядка. */
    public const LISTS = ['tasks', 'requirements', 'nice_to_have', 'offer'];

    /** Місце роботи, якщо у вакансії не вказано іншого. */
    public const DEFAULT_PLACE = 'Київ, Печерськ';

    protected $fillable = [
        'title',
        'slug',
        'direction_id',
        'project',
        'employment_type',
        'place',
        'summary',
        'about',
        'tasks',
        'requirements',
        'nice_to_have',
        'offer',
        'status',
        'published_at',
        'closed_at',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'tasks' => 'array',
            'requirements' => 'array',
            'nice_to_have' => 'array',
            'offer' => 'array',
            'published_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function direction(): BelongsTo
    {
        return $this->belongsTo(CareerDirection::class, 'direction_id');
    }

    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function employmentLabel(): string
    {
        return self::EMPLOYMENT_TYPES[$this->employment_type] ?? $this->employment_type;
    }

    /** Адресу сторінки міняти не можна, щойно вакансію хоч раз опублікували. */
    public function slugLocked(): bool
    {
        return $this->published_at !== null;
    }

    /** «Коротко про роль» абзацами: новий абзац — з порожнього рядка. */
    public function aboutParagraphs(): array
    {
        return array_values(array_filter(array_map('trim', preg_split("/\R\s*\R/u", (string) $this->about))));
    }

    public static function uniqueSlug(string $source, ?int $ignoreId = null): string
    {
        $base = Str::slug(self::transliterate($source)) ?: 'vacancy';
        $slug = $base;

        for ($i = 2; static::query()->where('slug', $slug)->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists(); $i++) {
            $slug = $base.'-'.$i;
        }

        return $slug;
    }

    /**
     * Офіційна транслітерація (постанова КМУ № 55, 2010): «Інженер-геодезист» → «inzhener-heodezyst».
     * Str::slug(..., 'uk') дає «inzener» — ж без h.
     */
    private static function transliterate(string $text): string
    {
        $text = mb_strtolower(str_replace(['’', "'", 'ʼ', 'ь'], '', $text));

        // є, ї, й, ю, я на початку слова пишуться інакше, ніж усередині
        $text = preg_replace_callback('/(?<!\p{L})[єїйюя]/u', fn ($m) => ['є' => 'ye', 'ї' => 'yi', 'й' => 'y', 'ю' => 'yu', 'я' => 'ya'][$m[0]], $text);

        return strtr($text, [
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'h', 'ґ' => 'g', 'д' => 'd', 'е' => 'e', 'є' => 'ie',
            'ж' => 'zh', 'з' => 'z', 'и' => 'y', 'і' => 'i', 'ї' => 'i', 'й' => 'i', 'к' => 'k', 'л' => 'l',
            'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u',
            'ф' => 'f', 'х' => 'kh', 'ц' => 'ts', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'shch', 'ю' => 'iu', 'я' => 'ia',
        ]);
    }
}
