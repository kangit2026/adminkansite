<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CareerPage;
use App\Support\RichTextSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CareerController extends Controller
{
    public function edit()
    {
        $career = CareerPage::current();

        return view('admin.career.edit', compact('career'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'heading.uk' => ['nullable', 'string', 'max:255'],
            'heading.en' => ['nullable', 'string', 'max:255'],
            'city_label.uk' => ['nullable', 'string', 'max:255'],
            'city_label.en' => ['nullable', 'string', 'max:255'],
            'all_categories_label.uk' => ['nullable', 'string', 'max:255'],
            'all_categories_label.en' => ['nullable', 'string', 'max:255'],
            'intro_text.uk' => ['nullable', 'string'],
            'intro_text.en' => ['nullable', 'string'],
            'meta_title.uk' => ['nullable', 'string', 'max:70'],
            'meta_title.en' => ['nullable', 'string', 'max:70'],
            'meta_description.uk' => ['nullable', 'string', 'max:160'],
            'meta_description.en' => ['nullable', 'string', 'max:160'],
            'og_image' => ['nullable', 'image', 'max:4096'],
        ]);

        if (isset($data['intro_text'])) {
            $data['intro_text'] = RichTextSanitizer::cleanTranslatable($data['intro_text']);
        }

        $career = CareerPage::current();

        if ($request->hasFile('og_image')) {
            if ($career->og_image) {
                Storage::disk('public')->delete($career->og_image);
            }
            $data['og_image'] = $request->file('og_image')->store('pages', 'public');
        } else {
            unset($data['og_image']);
        }

        $career->update($data);

        return back()->with('status', 'Сторінку «Кар’єра» збережено.');
    }
}
