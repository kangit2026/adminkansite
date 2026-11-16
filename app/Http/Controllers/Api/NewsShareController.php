<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\NewsShare;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * POST /api/news/{slug}/share, поле channel (telegram|viber|facebook|linkedin|copy|native).
 * Сайт надсилає його, коли відвідувач натискає кнопку в блоці «Поділитися новиною». Відповідь — 204 без тіла.
 * Зберігаємо лише новину, канал і час; частоту обмежує throttle на маршруті.
 */
class NewsShareController extends Controller
{
    public function store(Request $request, string $slug): Response
    {
        $channel = (string) $request->input('channel');

        // запит може прийти без Accept: application/json (navigator.sendBeacon) — тож 422 віддаємо самі, без редиректу
        if (! array_key_exists($channel, NewsShare::CHANNELS)) {
            return response(['message' => 'Unknown share channel.'], 422);
        }

        $newsId = News::published()->where('slug', $slug)->value('id');
        abort_if($newsId === null, 404);

        NewsShare::create(['news_id' => $newsId, 'channel' => $channel]);

        return response()->noContent();
    }
}
