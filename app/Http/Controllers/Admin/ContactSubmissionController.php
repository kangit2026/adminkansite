<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ContactSubmissionController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type');
        $type = is_string($type) && array_key_exists($type, ContactSubmission::TYPES) ? $type : null;

        $submissions = ContactSubmission::query()
            ->when($type, fn ($q) => $q->where('type', $type))
            ->orderByDesc('created_at')
            ->paginate(30)
            ->withQueryString();

        return view('admin.contact-submissions.index', compact('submissions', 'type'));
    }

    public function update(Request $request, ContactSubmission $contactSubmission)
    {
        $data = $request->validate([
            'status' => ['required', 'in:new,processed'],
        ]);

        $contactSubmission->update($data);

        return back()->with('status', 'Статус заявки оновлено.');
    }

    /** Вкладений файл заявки (документи на ділянку) — лише після входу в адмінку. */
    public function file(ContactSubmission $contactSubmission, int $index)
    {
        $file = $contactSubmission->attachments[$index] ?? null;
        $disk = Storage::disk(ContactSubmission::FILES_DISK);

        abort_unless($file && $disk->exists($file['path']), 404);

        return $disk->download($file['path'], $file['name']);
    }
}
