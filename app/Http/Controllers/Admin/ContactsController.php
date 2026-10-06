<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactsPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ContactsController extends Controller
{
    public function edit()
    {
        $contacts = ContactsPage::current();

        return view('admin.contacts.edit', compact('contacts'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'heading.uk' => ['nullable', 'string', 'max:255'],
            'heading.en' => ['nullable', 'string', 'max:255'],
            'name_label.uk' => ['nullable', 'string', 'max:255'],
            'name_label.en' => ['nullable', 'string', 'max:255'],
            'phone_label.uk' => ['nullable', 'string', 'max:255'],
            'phone_label.en' => ['nullable', 'string', 'max:255'],
            'email_label.uk' => ['nullable', 'string', 'max:255'],
            'email_label.en' => ['nullable', 'string', 'max:255'],
            'message_label.uk' => ['nullable', 'string', 'max:255'],
            'message_label.en' => ['nullable', 'string', 'max:255'],
            'send_label.uk' => ['nullable', 'string', 'max:255'],
            'send_label.en' => ['nullable', 'string', 'max:255'],
            'success_message.uk' => ['nullable', 'string', 'max:500'],
            'success_message.en' => ['nullable', 'string', 'max:500'],
            'error_message.uk' => ['nullable', 'string', 'max:500'],
            'error_message.en' => ['nullable', 'string', 'max:500'],
            'send_again_label.uk' => ['nullable', 'string', 'max:255'],
            'send_again_label.en' => ['nullable', 'string', 'max:255'],
            'map_enabled' => ['nullable', 'boolean'],
            'map_lat' => ['nullable', 'numeric', 'between:-90,90'],
            'map_lng' => ['nullable', 'numeric', 'between:-180,180'],
            'map_zoom' => ['nullable', 'integer', 'min:1', 'max:21'],
            'meta_title.uk' => ['nullable', 'string', 'max:70'],
            'meta_title.en' => ['nullable', 'string', 'max:70'],
            'meta_description.uk' => ['nullable', 'string', 'max:160'],
            'meta_description.en' => ['nullable', 'string', 'max:160'],
            'og_image' => ['nullable', 'image', 'max:4096'],
        ]);

        $data['map_enabled'] = $request->boolean('map_enabled');

        $contacts = ContactsPage::current();

        if ($request->hasFile('og_image')) {
            if ($contacts->og_image) {
                Storage::disk('public')->delete($contacts->og_image);
            }
            $data['og_image'] = $request->file('og_image')->store('pages', 'public');
        } else {
            unset($data['og_image']);
        }

        $contacts->update($data);

        return back()->with('status', 'Сторінку «Контакти» збережено.');
    }
}
