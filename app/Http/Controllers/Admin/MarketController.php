<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarketPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MarketController extends Controller
{
    public function edit()
    {
        $market = MarketPage::current();

        return view('admin.market.edit', compact('market'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'phone' => ['nullable', 'string', 'max:50'],
            'badge.uk' => ['nullable', 'string', 'max:255'],
            'badge.en' => ['nullable', 'string', 'max:255'],
            'title.uk' => ['nullable', 'string', 'max:255'],
            'title.en' => ['nullable', 'string', 'max:255'],
            'sell_text.uk' => ['nullable', 'string'],
            'sell_text.en' => ['nullable', 'string'],
            'question.uk' => ['nullable', 'string', 'max:255'],
            'question.en' => ['nullable', 'string', 'max:255'],
            'answer.uk' => ['nullable', 'string', 'max:255'],
            'answer.en' => ['nullable', 'string', 'max:255'],
            'contact_label.uk' => ['nullable', 'string', 'max:255'],
            'contact_label.en' => ['nullable', 'string', 'max:255'],
            'pros_title.uk' => ['nullable', 'string', 'max:500'],
            'pros_title.en' => ['nullable', 'string', 'max:500'],
            'pros_items_uk' => ['nullable', 'string'],
            'pros_items_en' => ['nullable', 'string'],
            'trust_text.uk' => ['nullable', 'string'],
            'trust_text.en' => ['nullable', 'string'],
            'meta_title.uk' => ['nullable', 'string', 'max:70'],
            'meta_title.en' => ['nullable', 'string', 'max:70'],
            'meta_description.uk' => ['nullable', 'string', 'max:160'],
            'meta_description.en' => ['nullable', 'string', 'max:160'],
            'hero_image' => ['nullable', 'image', 'max:4096'],
            'og_image' => ['nullable', 'image', 'max:4096'],
        ]);

        $market = MarketPage::current();

        $data['pros_items'] = [
            'uk' => $this->linesToList($data['pros_items_uk'] ?? ''),
            'en' => $this->linesToList($data['pros_items_en'] ?? ''),
        ];
        unset($data['pros_items_uk'], $data['pros_items_en']);

        if ($request->hasFile('hero_image')) {
            if ($market->hero_image) {
                Storage::disk('public')->delete($market->hero_image);
            }
            $data['hero_image'] = $request->file('hero_image')->store('market', 'public');
        } else {
            unset($data['hero_image']);
        }

        if ($request->hasFile('og_image')) {
            if ($market->og_image) {
                Storage::disk('public')->delete($market->og_image);
            }
            $data['og_image'] = $request->file('og_image')->store('pages', 'public');
        } else {
            unset($data['og_image']);
        }

        $market->update($data);

        return back()->with('status', 'Сторінку KAN Market збережено.');
    }

    /** @return string[] */
    private function linesToList(string $text): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $text) ?: [])));
    }
}
