<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Support\RichTextSanitizer;
use App\Support\StoredImage;
use App\Models\NewsTag;
use App\Models\Redirect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::query()
            ->with('tags')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(20);

        return view('admin.news.index', compact('news'));
    }

    public function create()
    {
        $tags = NewsTag::query()->orderBy('id')->get();
        $news = new News(['status' => 'draft']);
        $selectedTagIds = collect();

        return view('admin.news.create', compact('news', 'tags', 'selectedTagIds'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = $this->resolveSlug($request, $data['title']);

        $news = News::create($data);
        $news->tags()->sync($request->input('tags', []));

        $files = [];

        if ($request->hasFile('picture')) {
            $files['picture'] = StoredImage::store($request->file('picture'), 'news');
        }

        if ($request->hasFile('partner_logo')) {
            $files['partner_logo'] = StoredImage::store($request->file('partner_logo'), 'partners');
        }

        if ($files) {
            $news->update($files);
        }

        return redirect()
            ->route('admin.news.index')
            ->with('status', 'Новину створено.');
    }

    public function edit(News $news)
    {
        $tags = NewsTag::query()->orderBy('id')->get();
        $news->load('tags');
        $selectedTagIds = $news->tags->pluck('id');

        return view('admin.news.edit', compact('news', 'tags', 'selectedTagIds'));
    }

    public function update(Request $request, News $news)
    {
        $data = $this->validated($request);

        $oldSlug = $news->slug;
        $newSlug = $this->resolveSlug($request, $data['title'], $news);
        $data['slug'] = $newSlug;

        if ($request->hasFile('picture')) {
            if ($news->picture) {
                Storage::disk('public')->delete($news->picture);
            }

            $data['picture'] = StoredImage::store($request->file('picture'), 'news');
        }

        if ($request->hasFile('partner_logo')) {
            if ($news->partner_logo) {
                Storage::disk('public')->delete($news->partner_logo);
            }

            $data['partner_logo'] = StoredImage::store($request->file('partner_logo'), 'partners');
        }

        $news->update($data);
        $news->tags()->sync($request->input('tags', []));

        if ($oldSlug && $newSlug && $oldSlug !== $newSlug) {
            Redirect::point("/news/{$oldSlug}", "/news/{$newSlug}");
        }

        return redirect()
            ->route('admin.news.index')
            ->with('status', 'Новину оновлено.');
    }

    public function destroy(News $news)
    {
        if ($news->picture) {
            Storage::disk('public')->delete($news->picture);
        }

        if ($news->partner_logo) {
            Storage::disk('public')->delete($news->partner_logo);
        }

        $news->delete();

        return back()->with('status', 'Новину видалено.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title.uk' => ['required', 'string', 'max:255'],
            'title.en' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'text.uk' => ['nullable', 'string'],
            'text.en' => ['nullable', 'string'],
            'meta_title.uk' => ['nullable', 'string', 'max:70'],
            'meta_title.en' => ['nullable', 'string', 'max:70'],
            'meta_description.uk' => ['nullable', 'string', 'max:160'],
            'meta_description.en' => ['nullable', 'string', 'max:160'],
            'picture' => ['nullable', 'image', 'max:65536'],
            'picture_alt.uk' => ['nullable', 'string', 'max:255'],
            'picture_alt.en' => ['nullable', 'string', 'max:255'],
            'partner_logo' => ['nullable', 'image', 'max:65536'],
            'partner_logo_alt.uk' => ['nullable', 'string', 'max:255'],
            'partner_logo_alt.en' => ['nullable', 'string', 'max:255'],
            'external_link' => ['nullable', 'url', 'max:255'],
            'big_picture' => ['nullable', 'boolean'],
            'is_partner' => ['nullable', 'boolean'],
            'status' => ['required', 'in:draft,published'],
            'published_at' => ['nullable', 'date'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:news_tags,id'],
        ]);

        unset($data['picture'], $data['partner_logo'], $data['slug']);

        if (isset($data['text'])) {
            $data['text'] = RichTextSanitizer::cleanTranslatable($data['text']);
        }

        $data['big_picture'] = $request->boolean('big_picture');
        $data['is_partner'] = $request->boolean('is_partner');
        $data['published_at'] = $request->input('published_at') ?: now();

        return $data;
    }

    /**
     * Один slug на всі мови, завжди англійською. Стабільний: якщо редактор
     * вписав свій — використовуємо його. Якщо поле лишили порожнім —
     * генеруємо з англійського заголовка (а за його відсутності —
     * транслітеруємо український), але тільки якщо slug ще не існував —
     * при звичайному збереженні без зміни заголовка він більше не "їде".
     */
    private function resolveSlug(Request $request, array $titles, ?News $news = null): ?string
    {
        $manual = trim((string) $request->input('slug'));

        if ($manual !== '') {
            return $this->uniqueSlug(Str::slug($manual), $news);
        }

        if ($news?->slug) {
            return $news->slug;
        }

        $source = $titles['en'] ?? null ?: ($titles['uk'] ?? null);

        if (empty($source)) {
            return null;
        }

        return $this->uniqueSlug(Str::slug($source), $news);
    }

    private function uniqueSlug(string $base, ?News $ignore = null): string
    {
        $base = $base ?: 'news';
        $slug = $base;
        $i = 2;

        while (
            News::query()
                ->where('slug', $slug)
                ->when($ignore, fn ($q) => $q->whereKeyNot($ignore->id))
                ->exists()
        ) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }
}
