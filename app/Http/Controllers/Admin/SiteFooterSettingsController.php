<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class SiteFooterSettingsController extends Controller
{
    public function edit()
    {
        $settings = SiteSetting::current();

        return view('admin.footer.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'project_ad_title.uk' => ['nullable', 'string', 'max:255'],
            'project_ad_title.en' => ['nullable', 'string', 'max:255'],
            'project_ad_link' => ['nullable', 'string', 'max:255'],
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

        SiteSetting::current()->update($data);

        return back()->with('status', 'Футер збережено.');
    }
}
