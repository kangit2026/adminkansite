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
            'home_video' => ['nullable', 'mimetypes:video/mp4,video/webm,video/quicktime', 'max:51200'],
            'commercial_ad_title.uk' => ['nullable', 'string', 'max:255'],
            'commercial_ad_title.en' => ['nullable', 'string', 'max:255'],
            'commercial_ad_link' => ['nullable', 'string', 'max:255'],
            'project_ad_title.uk' => ['nullable', 'string', 'max:255'],
            'project_ad_title.en' => ['nullable', 'string', 'max:255'],
            'project_ad_link' => ['nullable', 'string', 'max:255'],
            'news_meta_title.uk' => ['nullable', 'string', 'max:70'],
            'news_meta_title.en' => ['nullable', 'string', 'max:70'],
            'news_meta_description.uk' => ['nullable', 'string', 'max:160'],
            'news_meta_description.en' => ['nullable', 'string', 'max:160'],
            'projects_meta_title.uk' => ['nullable', 'string', 'max:70'],
            'projects_meta_title.en' => ['nullable', 'string', 'max:70'],
            'projects_meta_description.uk' => ['nullable', 'string', 'max:160'],
            'projects_meta_description.en' => ['nullable', 'string', 'max:160'],
            'services_meta_title.uk' => ['nullable', 'string', 'max:70'],
            'services_meta_title.en' => ['nullable', 'string', 'max:70'],
            'services_meta_description.uk' => ['nullable', 'string', 'max:160'],
            'services_meta_description.en' => ['nullable', 'string', 'max:160'],
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
            'motto.uk' => ['nullable', 'string', 'max:255'],
            'motto.en' => ['nullable', 'string', 'max:255'],
            'awards_title.uk' => ['nullable', 'string', 'max:255'],
            'awards_title.en' => ['nullable', 'string', 'max:255'],
            'award_first_value' => ['nullable', 'integer', 'min:0'],
            'award_first_description.uk' => ['nullable', 'string', 'max:255'],
            'award_first_description.en' => ['nullable', 'string', 'max:255'],
            'award_second_value' => ['nullable', 'integer', 'min:0'],
            'award_second_description.uk' => ['nullable', 'string', 'max:255'],
            'award_second_description.en' => ['nullable', 'string', 'max:255'],
            'award_third_value' => ['nullable', 'integer', 'min:0'],
            'award_third_description.uk' => ['nullable', 'string', 'max:255'],
            'award_third_description.en' => ['nullable', 'string', 'max:255'],
            'award_fourth_value' => ['nullable', 'integer', 'min:0'],
            'award_fourth_description.uk' => ['nullable', 'string', 'max:255'],
            'award_fourth_description.en' => ['nullable', 'string', 'max:255'],
        ]);

        $settings = SiteSetting::current();

        if ($request->hasFile('home_og_image')) {
            if ($settings->home_og_image) {
                Storage::disk('public')->delete($settings->home_og_image);
            }

            $data['home_og_image'] = $request->file('home_og_image')->store('settings', 'public');
        } else {
            unset($data['home_og_image']);
        }

        if ($request->hasFile('home_video')) {
            if ($settings->home_video) {
                Storage::disk('public')->delete($settings->home_video);
            }

            $data['home_video'] = $request->file('home_video')->store('settings', 'public');
        } else {
            unset($data['home_video']);
        }

        $settings->update($data);

        return back()->with('status', 'Налаштування збережено.');
    }
}
