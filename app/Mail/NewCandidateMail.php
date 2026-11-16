<?php

namespace App\Mail;

use App\Models\Candidate;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/** Лист HR про новий відгук. Резюме не вкладаємо — воно в адмінці, після входу. */
class NewCandidateMail extends Mailable
{
    public function __construct(public Candidate $candidate) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->candidate->vacancy_title
                ? 'Новий відгук: '.$this->candidate->vacancy_title.' — '.$this->candidate->name
                : 'Нове резюме до загальної бази — '.$this->candidate->name,
        );
    }

    public function content(): Content
    {
        return new Content(view: 'mail.new-candidate');
    }
}
