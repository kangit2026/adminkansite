<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Candidate extends Model
{
    public const STATUSES = [
        'new' => 'Новий',
        'in_progress' => 'У роботі',
        'interview' => 'Співбесіда',
        'reserve' => 'Резерв',
        'rejected' => 'Відмова',
        'hired' => 'Найнято',
    ];

    public const GENERAL_LABEL = 'Загальна база';

    /** Диск резюме: storage/app/private, без публічного URL. */
    public const CV_DISK = 'local';

    protected $fillable = [
        'vacancy_id',
        'vacancy_title',
        'direction',
        'name',
        'phone',
        'email',
        'linkedin',
        'message',
        'cv_path',
        'cv_original_name',
        'cv_size',
        'status',
        'responsible_id',
        'last_activity_at',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'last_activity_at' => 'datetime',
            'cv_size' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        // разом із кандидатом зникає і його резюме
        static::deleting(function (Candidate $candidate) {
            if ($candidate->cv_path) {
                Storage::disk(self::CV_DISK)->delete($candidate->cv_path);
            }
        });
    }

    public function vacancy(): BelongsTo
    {
        return $this->belongsTo(Vacancy::class);
    }

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(CandidateNote::class)->latest('id');
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function vacancyLabel(): string
    {
        return $this->vacancy_title ?: self::GENERAL_LABEL;
    }

    /** Запис у стрічці картки; заодно оновлює «Остання дія». */
    public function addNote(string $text, ?User $user = null, bool $system = false): CandidateNote
    {
        $this->forceFill(['last_activity_at' => now()])->save();

        return $this->notes()->create([
            'user_id' => $user?->id,
            'text' => $text,
            'is_system' => $system,
        ]);
    }

    /** «Прізвище_Ім’я_Назва-вакансії.pdf» — як HR зберігає резюме у себе. */
    public function cvDownloadName(): string
    {
        $clean = fn (string $s) => trim(preg_replace('/[^\p{L}\p{N}’\']+/u', '-', $s), '-');

        $parts = array_values(array_filter(array_map($clean, preg_split('/\s+/u', trim($this->name)))));
        if (count($parts) > 1) {
            // «Ірина Коваленко» → «Коваленко_Ірина»
            array_unshift($parts, array_pop($parts));
        }

        $extension = strtolower(pathinfo($this->cv_original_name ?: $this->cv_path, PATHINFO_EXTENSION));

        return implode('_', [...$parts, $clean($this->vacancyLabel())]).($extension ? '.'.$extension : '');
    }

    public function cvSizeLabel(): string
    {
        $mb = ($this->cv_size ?? 0) / 1024 / 1024;

        return $mb >= 1
            ? number_format($mb, 1, ',', ' ').' МБ'
            : max(1, (int) round(($this->cv_size ?? 0) / 1024)).' КБ';
    }
}
