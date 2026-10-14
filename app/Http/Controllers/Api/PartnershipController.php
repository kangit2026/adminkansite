<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;

class PartnershipController extends Controller
{
    public function index()
    {
        $items = News::query()
            ->published()
            ->where('is_partner', true)
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->get()
            ->map(function (News $news) {
                $logo = $news->partner_logo ?: $news->picture;

                return [
                    'id' => $news->id,
                    'text' => $news->title,
                    'picture' => $logo ? asset('storage/'.$logo) : null,
                    'link' => $news->external_link ?: url('/news/'.$news->slug),
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'value' => $items,
        ]);
    }
}
