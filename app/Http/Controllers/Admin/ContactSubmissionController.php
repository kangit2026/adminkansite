<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use Illuminate\Http\Request;

class ContactSubmissionController extends Controller
{
    public function index()
    {
        $submissions = ContactSubmission::query()
            ->orderByDesc('created_at')
            ->paginate(30);

        return view('admin.contact-submissions.index', compact('submissions'));
    }

    public function update(Request $request, ContactSubmission $contactSubmission)
    {
        $data = $request->validate([
            'status' => ['required', 'in:new,processed'],
        ]);

        $contactSubmission->update($data);

        return back()->with('status', 'Статус заявки оновлено.');
    }
}
