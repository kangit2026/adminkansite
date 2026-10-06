<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutPage;
use App\Support\RichTextSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AboutController extends Controller
{
    public function edit()
    {
        $about = AboutPage::current();

        return view('admin.about.edit', compact('about'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'motto.uk' => ['nullable', 'string', 'max:255'],
            'motto.en' => ['nullable', 'string', 'max:255'],
            'heading.uk' => ['nullable', 'string', 'max:255'],
            'heading.en' => ['nullable', 'string', 'max:255'],
            'history.uk' => ['nullable', 'string'],
            'history.en' => ['nullable', 'string'],
            'specialists.uk' => ['nullable', 'string'],
            'specialists.en' => ['nullable', 'string'],
            'today_title.uk' => ['nullable', 'string', 'max:255'],
            'today_title.en' => ['nullable', 'string', 'max:255'],
            'today_description.uk' => ['nullable', 'string'],
            'today_description.en' => ['nullable', 'string'],
            'merit_title.uk' => ['nullable', 'string', 'max:255'],
            'merit_title.en' => ['nullable', 'string', 'max:255'],
            'merit_description.uk' => ['nullable', 'string'],
            'merit_description.en' => ['nullable', 'string'],
            'merit_list_1.uk' => ['nullable', 'string', 'max:255'],
            'merit_list_1.en' => ['nullable', 'string', 'max:255'],
            'merit_list_2.uk' => ['nullable', 'string', 'max:255'],
            'merit_list_2.en' => ['nullable', 'string', 'max:255'],
            'merit_list_3.uk' => ['nullable', 'string', 'max:255'],
            'merit_list_3.en' => ['nullable', 'string', 'max:255'],
            'merit_list_4.uk' => ['nullable', 'string', 'max:255'],
            'merit_list_4.en' => ['nullable', 'string', 'max:255'],
            'achievements_text.uk' => ['nullable', 'string', 'max:500'],
            'achievements_text.en' => ['nullable', 'string', 'max:500'],
            'meta_title.uk' => ['nullable', 'string', 'max:70'],
            'meta_title.en' => ['nullable', 'string', 'max:70'],
            'meta_description.uk' => ['nullable', 'string', 'max:160'],
            'meta_description.en' => ['nullable', 'string', 'max:160'],
            'og_image' => ['nullable', 'image', 'max:4096'],
        ]);

        foreach (['history', 'specialists', 'today_description'] as $field) {
            if (isset($data[$field])) {
                $data[$field] = RichTextSanitizer::cleanTranslatable($data[$field]);
            }
        }

        $about = AboutPage::current();

        if ($request->hasFile('og_image')) {
            if ($about->og_image) {
                Storage::disk('public')->delete($about->og_image);
            }

            $data['og_image'] = $request->file('og_image')->store('pages', 'public');
        } else {
            unset($data['og_image']);
        }

        $about->update($data);

        return back()->with('status', 'Сторінку "Про компанію" збережено.');
    }
}
