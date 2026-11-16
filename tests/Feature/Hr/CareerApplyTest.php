<?php

namespace Tests\Feature\Hr;

use App\Mail\NewCandidateMail;
use App\Models\Candidate;
use App\Models\CareerPage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class CareerApplyTest extends HrTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake(Candidate::CV_DISK);
        Mail::fake();
        // current() шукає рядок з id 1, а MySQL не відкочує автоінкремент між тестами
        CareerPage::forceCreate(['id' => 1, 'notify_emails' => 'hr@example.com, recruiter@example.com']);
    }

    private function form(array $overrides = []): array
    {
        return $overrides + [
            'name' => 'Ірина Коваленко',
            'phone' => '+380 67 000 14 08',
            'email' => 'i.kovalenko@example.com',
            'linkedin' => 'linkedin.com/in/iryna',
            'message' => 'Маю шість років досвіду.',
            'cv' => UploadedFile::fake()->create('CV Iryna.pdf', 1200, 'application/pdf'),
            'consent' => '1',
        ];
    }

    public function test_reply_to_vacancy_saves_candidate_cv_and_emails_hr(): void
    {
        $vacancy = $this->vacancy();

        $this->post('/api/career/apply', $this->form(['vacancy' => $vacancy->slug]), ['Accept' => 'application/json'])
            ->assertCreated();

        $candidate = Candidate::sole();
        $this->assertSame($vacancy->id, $candidate->vacancy_id);
        $this->assertSame('Інженер-геодезист', $candidate->vacancy_title);
        $this->assertSame('Інженерія', $candidate->direction);
        $this->assertSame('new', $candidate->status);
        $this->assertSame('https://linkedin.com/in/iryna', $candidate->linkedin);
        $this->assertStringStartsWith('cv/'.now()->format('Y/m').'/', $candidate->cv_path);
        Storage::disk(Candidate::CV_DISK)->assertExists($candidate->cv_path);
        $this->assertSame('Відгук надійшов із сайту.', $candidate->notes()->sole()->text);

        Mail::assertSent(NewCandidateMail::class, fn (NewCandidateMail $mail) => $mail->hasTo('hr@example.com')
            && $mail->hasTo('recruiter@example.com')
            && $mail->envelope()->subject === 'Новий відгук: Інженер-геодезист — Ірина Коваленко');
    }

    public function test_general_form_goes_to_the_general_base(): void
    {
        $this->postJson('/api/career/apply', $this->form(['direction' => 'Будівництво', 'cv' => null]))->assertCreated();

        $candidate = Candidate::sole();
        $this->assertNull($candidate->vacancy_id);
        $this->assertSame('Будівництво', $candidate->direction);
        $this->assertSame('Загальна база', $candidate->vacancyLabel());
        Mail::assertSent(NewCandidateMail::class, fn ($mail) => $mail->envelope()->subject === 'Нове резюме до загальної бази — Ірина Коваленко');
    }

    public function test_wrong_files_are_rejected_in_ukrainian(): void
    {
        $this->postJson('/api/career/apply', $this->form(['cv' => UploadedFile::fake()->create('cv.pdf', 10241, 'application/pdf')]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['cv' => 'Файл більший за 10 МБ — стисніть його або надішліть інший.']);

        $this->postJson('/api/career/apply', $this->form(['cv' => UploadedFile::fake()->create('cv.exe', 10, 'application/x-msdownload')]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['cv' => 'Додайте резюме у форматі PDF, DOC, DOCX, RTF, ODT, JPG або PNG.']);

        $this->assertSame(0, Candidate::count());
    }

    public function test_contacts_and_consent_are_required(): void
    {
        $this->postJson('/api/career/apply', ['phone' => '12345', 'email' => 'nope'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'name' => 'Вкажіть ім’я та прізвище — так ми знатимемо, як до вас звертатися.',
                'phone' => 'Вкажіть номер у форматі +380 XX XXX XX XX.',
                'email' => 'Вкажіть email у форматі name@example.com.',
                'consent' => 'Потрібна згода на обробку даних, щоб ми могли розглянути анкету.',
            ]);
    }

    public function test_honeypot_answers_like_a_real_reply_but_saves_nothing(): void
    {
        $this->postJson('/api/career/apply', $this->form(['website' => 'http://spam.example']))->assertCreated();

        $this->assertSame(0, Candidate::count());
        Mail::assertNothingSent();
    }

    public function test_sixth_reply_within_an_hour_is_refused(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/career/apply', $this->form(['cv' => null]))->assertCreated();
        }

        $this->postJson('/api/career/apply', $this->form(['cv' => null]))
            ->assertStatus(429)
            ->assertJson(['message' => 'Забагато спроб. Спробуйте пізніше.']);
        $this->assertSame(5, Candidate::count());
    }

    public function test_reply_to_closed_vacancy_is_still_kept(): void
    {
        $vacancy = $this->vacancy(['status' => 'closed', 'closed_at' => now()]);

        $this->postJson('/api/career/apply', $this->form(['vacancy' => $vacancy->slug, 'cv' => null]))->assertCreated();

        $this->assertSame($vacancy->id, Candidate::sole()->vacancy_id);
    }
}
