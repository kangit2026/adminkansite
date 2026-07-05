<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NewsResource;
use App\Models\News;
use App\Models\Redirect;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $count = (int) $request->input('count', 3);
        $start = (int) $request->input('start', 0);

        $query = News::query()->published()->with('tags');

        if ($request->has('onlyBigPicture')) {
            $query->where('big_picture', $request->boolean('onlyBigPicture'));
        }

        $total = (clone $query)->count();

        $news = $query
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->skip($start)
            ->take($count)
            ->get();

        return NewsResource::collection($news)->additional([
            'meta' => ['total' => $total],
        ]);
    }

    public function show(string $slug)
    {
        $news = News::published()
            ->where('slug', $slug)
            ->with('tags')
            ->first();

        if (! $news) {
            return $this->redirectOrFail($slug);
        }

        $news->setAttribute(
            'prev_slug',
            News::published()
                ->where('published_at', '<', $news->published_at)
                ->orderByDesc('published_at')
                ->value('slug')
        );

        $news->setAttribute(
            'next_slug',
            News::published()
                ->where('published_at', '>', $news->published_at)
                ->orderBy('published_at')
                ->value('slug')
        );

        return new NewsResource($news);
    }

    /**
     * Стара адреса новини (slug змінили в адмінці) — якщо є запис у
     * redirects, повертаємо не HTTP-редирект (це JSON API, а не сторінка),
     * а маркер, за яким фронтенд сам зробить 301 на новому шляху сторінки.
     */
    private function redirectOrFail(string $slug): JsonResponse
    {
        $redirect = Redirect::query()
            ->where('from_path', "/news/{$slug}")
            ->first();

        if (! $redirect) {
            abort(404);
        }

        $newSlug = ltrim(str_replace('/news/', '', $redirect->to_path), '/');

        return response()->json([
            'redirect' => ['slug' => $newSlug],
        ]);
    }
}
