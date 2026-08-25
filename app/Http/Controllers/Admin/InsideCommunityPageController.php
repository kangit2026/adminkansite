<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InsideCommunityPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InsideCommunityPageController extends Controller
{
    public function edit()
    {
        $page = InsideCommunityPage::current();

        return view('admin.inside_community.page', compact('page'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'hero_title.uk' => ['nullable', 'string', 'max:500'],
            'hero_title.en' => ['nullable', 'string', 'max:500'],
            'hero_subtitle.uk' => ['nullable', 'string', 'max:500'],
            'hero_subtitle.en' => ['nullable', 'string', 'max:500'],
            'hero_paragraph_1.uk' => ['nullable', 'string'],
            'hero_paragraph_1.en' => ['nullable', 'string'],
            'hero_paragraph_2.uk' => ['nullable', 'string'],
            'hero_paragraph_2.en' => ['nullable', 'string'],
            'hero_paragraph_3.uk' => ['nullable', 'string'],
            'hero_paragraph_3.en' => ['nullable', 'string'],
            'philosophy_title.uk' => ['nullable', 'string', 'max:255'],
            'philosophy_title.en' => ['nullable', 'string', 'max:255'],
            'philosophy_subtitle.uk' => ['nullable', 'string', 'max:255'],
            'philosophy_subtitle.en' => ['nullable', 'string', 'max:255'],
            'meta_title.uk' => ['nullable', 'string', 'max:70'],
            'meta_title.en' => ['nullable', 'string', 'max:70'],
            'meta_description.uk' => ['nullable', 'string', 'max:160'],
            'meta_description.en' => ['nullable', 'string', 'max:160'],
            'hero_video' => ['nullable', 'file', 'mimetypes:video/mp4,video/webm', 'max:51200'],
            'hero_image_uk' => ['nullable', 'image', 'max:4096'],
            'hero_image_en' => ['nullable', 'image', 'max:4096'],
            'complex_logo_1' => ['nullable', 'image', 'max:4096'],
            'complex_logo_2' => ['nullable', 'image', 'max:4096'],
            'og_image' => ['nullable', 'image', 'max:4096'],
        ]);

        $page = InsideCommunityPage::current();

        foreach (['hero_video', 'hero_image_uk', 'hero_image_en', 'complex_logo_1', 'complex_logo_2', 'og_image'] as $field) {
            if ($request->hasFile($field)) {
                if ($page->{$field}) {
                    Storage::disk('public')->delete($page->{$field});
                }

                $folder = str_starts_with($field, 'hero_video') ? 'inside_community/videos' : 'inside_community';
                $data[$field] = $request->file($field)->store($folder, 'public');
            } else {
                unset($data[$field]);
            }
        }

        $page->update($data);

        return back()->with('status', 'Сторінку Inside Community збережено.');
    }
}
