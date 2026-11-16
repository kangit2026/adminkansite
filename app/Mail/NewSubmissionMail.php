<?php

namespace App\Mail;

use App\Models\ContactSubmission;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/** Лист про нову заявку з форми сайту. Вкладення не додаємо — вони в адмінці, після входу. */
class NewSubmissionMail extends Mailable
{
    public function __construct(public ContactSubmission $submission) {}

    public function envelope(): Envelope
    {
        $who = $this->submission->name ?: $this->submission->email ?: $this->submission->phone;

        return new Envelope(subject: 'Нова заявка з сайту: '.mb_strtolower($this->submission->typeLabel()).($who ? ' — '.$who : ''));
    }

    public function content(): Content
    {
        return new Content(view: 'mail.new-submission');
    }
}
