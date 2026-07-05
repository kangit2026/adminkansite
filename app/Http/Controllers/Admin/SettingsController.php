<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function edit()
    {
        $settings = SiteSetting::current();

        return view('admin.settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'home_meta_title.uk' => ['nullable', 'string', 'max:70'],
            'home_meta_title.en' => ['nullable', 'string', 'max:70'],
            'home_meta_description.uk' => ['nullable', 'string', 'max:160'],
            'home_meta_description.en' => ['nullable', 'string', 'max:160'],
            'home_og_image' => ['nullable', 'image', 'max:4096'],
            'home_robots_index' => ['nullable', 'boolean'],
            'home_robots_follow' => ['nullable', 'boolean'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'telegram_chat_link' => ['nullable', 'url', 'max:255'],
            'social_instagram' => ['nullable', 'url', 'max:255'],
            'social_youtube' => ['nullable', 'url', 'max:255'],
            'social_facebook' => ['nullable', 'url', 'max:255'],
            'social_pinterest' => ['nullable', 'url', 'max:255'],
            'social_telegram' => ['nullable', 'url', 'max:255'],
            'copyright_text.uk' => ['nullable', 'string', 'max:255'],
            'copyright_text.en' => ['nullable', 'string', 'max:255'],
            'copyright_year' => ['nullable', 'integer', 'min:2000', 'max:2100'],
        ]);

        $settings = SiteSetting::current();

        $data['home_robots_index'] = $request->boolean('home_robots_index');
        $data['home_robots_follow'] = $request->boolean('home_robots_follow');

        if ($request->hasFile('home_og_image')) {
            if ($settings->home_og_image) {
                Storage::disk('public')->delete($settings->home_og_image);
            }

            $data['home_og_image'] = $request->file('home_og_image')->store('settings', 'public');
        } else {
            unset($data['home_og_image']);
        }

        $settings->update($data);

        return back()->with('status', 'Налаштування збережено.');
    }
}
