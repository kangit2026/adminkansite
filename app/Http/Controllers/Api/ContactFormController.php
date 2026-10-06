<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use App\Services\RecaptchaVerifier;
use Illuminate\Http\Request;

class ContactFormController extends Controller
{
    public function store(Request $request, RecaptchaVerifier $recaptcha)
    {
        if ($request->filled('website')) {
            return response()->json(['success' => true]);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
            'source_page' => ['nullable', 'string', 'max:255'],
            'recaptcha_token' => ['nullable', 'string', 'max:2000'],
        ]);

        $recaptcha->verify($data['recaptcha_token'] ?? null, $request->ip());

        ContactSubmission::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'message' => $data['message'],
            'source_page' => $data['source_page'] ?? null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => 'new',
        ]);

        return response()->json(['success' => true]);
    }
}
