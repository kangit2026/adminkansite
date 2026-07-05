<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsTag;
use Illuminate\Http\Request;

class NewsTagController extends Controller
{
    public function index()
    {
        $tags = NewsTag::query()->orderBy('id', 'desc')->get();

        return view('admin.news-tags.index', compact('tags'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name.uk' => ['required', 'string', 'max:100'],
            'name.en' => ['nullable', 'string', 'max:100'],
        ]);

        NewsTag::create($data);

        return back()->with('status', 'Тег додано.');
    }

    public function update(Request $request, NewsTag $newsTag)
    {
        $data = $request->validate([
            'name.uk' => ['required', 'string', 'max:100'],
            'name.en' => ['nullable', 'string', 'max:100'],
        ]);

        $newsTag->update($data);

        return back()->with('status', 'Тег оновлено.');
    }

    public function destroy(NewsTag $newsTag)
    {
        $newsTag->delete();

        return back()->with('status', 'Тег видалено.');
    }
}
