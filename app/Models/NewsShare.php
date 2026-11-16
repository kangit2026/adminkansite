<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Одне натискання «Поділитися» на сторінці новини (ShareSection у kan-site-v2). Без персональних даних. */
class NewsShare extends Model
{
    /** Канали з блоку «Поділитися новиною» → підпис і іконка в адмінці. Порядок — як на сайті. */
    public const CHANNELS = [
        'telegram' => ['label' => 'Telegram', 'icon' => 'bi-telegram'],
        'viber' => ['label' => 'Viber', 'icon' => 'bi-chat-dots'],
        'facebook' => ['label' => 'Facebook', 'icon' => 'bi-facebook'],
        'linkedin' => ['label' => 'LinkedIn', 'icon' => 'bi-linkedin'],
        'copy' => ['label' => 'Копіювання посилання', 'icon' => 'bi-link-45deg'],
        'native' => ['label' => 'Меню «Поділитися» телефона', 'icon' => 'bi-phone'],
    ];

    public const UPDATED_AT = null;

    protected $fillable = ['news_id', 'channel'];

    public function news(): BelongsTo
    {
        return $this->belongsTo(News::class);
    }
}
