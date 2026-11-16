<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CareerDirection;
use App\Models\CareerPage;
use Closure;
use Illuminate\Http\Request;

class HrSettingsController extends Controller
{
    public function edit(Request $request)
    {
        return view('admin.hr-settings.edit', [
            'page' => CareerPage::current(),
            'directions' => CareerDirection::ordered()
                ->withCount(['vacancies', 'vacancies as active_vacancies_count' => fn ($q) => $q->where('status', 'active')])
                ->get(),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'notify_emails' => ['nullable', 'string', 'max:500', function (string $attribute, mixed $value, Closure $fail) {
                foreach (array_filter(array_map('trim', explode(',', $value))) as $email) {
                    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                        $fail('«'.$email.'» — не схоже на email. Кілька адрес пишіть через кому.');
                    }
                }
            }],
            'referral_url' => ['nullable', 'url:http,https', 'max:500'],
        ], [
            'referral_url.url' => 'Посилання на реферальну програму має починатися з https://',
            'max' => 'Задовге значення — до :max символів.',
        ]);

        CareerPage::current()->update($data);

        return back()->with('status', 'Налаштування збережено.');
    }
}
