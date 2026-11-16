<?php

namespace App\Services;

use App\Mail\NewSubmissionMail;
use App\Models\ContactSubmission;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SubmissionNotifier
{
    /** Лист — після відповіді сайту, щоб відвідувач не чекав на пошту; збій пошти лише в лог. */
    public function notify(ContactSubmission $submission): void
    {
        $emails = config('forms.notify.'.$submission->type, []);

        if (! $emails) {
            return;
        }

        dispatch(function () use ($emails, $submission) {
            try {
                Mail::to($emails)->send(new NewSubmissionMail($submission));
            } catch (\Throwable $e) {
                Log::error('Submission notification failed: '.$e->getMessage(), ['submission_id' => $submission->id]);
            }
        })->afterResponse();
    }
}
