<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ContactSubmission extends Model
{
    /** Приватний диск для вкладень (той самий, що й для резюме кандидатів). */
    public const FILES_DISK = 'local';

    public const TYPES = [
        'contact' => 'Зворотний зв’язок',
        'land' => 'Пропозиція ділянки',
        'apartment' => 'Заявка на квартиру',
        'deck' => 'Презентація на email',
    ];

    protected $fillable = [
        'type',
        'name',
        'phone',
        'email',
        'message',
        'payload',
        'attachments',
        'source_page',
        'ip_address',
        'user_agent',
        'status',
    ];

    protected $casts = [
        'payload' => 'array',
        'attachments' => 'array',
    ];

    public function typeLabel(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    protected static function booted(): void
    {
        static::deleting(function (self $submission) {
            foreach ($submission->attachments ?? [] as $file) {
                Storage::disk(self::FILES_DISK)->delete($file['path'] ?? '');
            }
        });
    }
}
