<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use App\Services\SubmissionNotifier;
use App\Services\TurnstileVerifier;
use Illuminate\Http\Request;

class ContactFormController extends Controller
{
    public function store(Request $request, TurnstileVerifier $turnstile, SubmissionNotifier $notifier)
    {
        // пастка для ботів: людина цього поля не бачить; відповідь та сама, що й на справжню заявку
        if ($request->filled('website')) {
            return response()->json(['success' => true]);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'message' => ['nullable', 'string', 'max:5000'],
            'source_page' => ['nullable', 'string', 'max:255'],
            'consent' => ['sometimes', 'accepted'],
            'turnstile_token' => ['nullable', 'string', 'max:2048'],
        ]);

        $turnstile->verify($data['turnstile_token'] ?? null, $request->ip());

        $submission = ContactSubmission::create([
            'type' => 'contact',
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
            'message' => $data['message'] ?? null,
            'source_page' => $data['source_page'] ?? null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => 'new',
        ]);

        $notifier->notify($submission);

        return response()->json(['success' => true]);
    }
}
