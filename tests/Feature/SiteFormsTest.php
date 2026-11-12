<?php

namespace Tests\Feature;

use App\Mail\NewSubmissionMail;
use App\Models\ContactSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/** Форми сайту: Turnstile, збереження заявок, лист про нову заявку. */
class SiteFormsTest extends TestCase
{
    use RefreshDatabase;

    private function enableTurnstile(): void
    {
        config(['turnstile.enabled' => true, 'turnstile.secret_key' => 'secret', 'turnstile.hostnames' => []]);
    }

    public function test_contact_form_without_turnstile_secret_is_not_checked(): void
    {
        $this->postJson('/api/contact', ['name' => 'Іван', 'phone' => '+380501234567'])->assertOk();
        $this->assertDatabaseHas('contact_submissions', ['type' => 'contact', 'name' => 'Іван']);
    }

    public function test_missing_token_is_rejected_when_turnstile_is_on(): void
    {
        $this->enableTurnstile();
        Http::fake();

        $this->postJson('/api/contact', ['name' => 'Іван', 'phone' => '+380501234567'])
            ->assertStatus(422)->assertJsonValidationErrors('turnstile_token');
        Http::assertNothingSent();
        $this->assertDatabaseCount('contact_submissions', 0);
    }

    public function test_failed_token_is_rejected(): void
    {
        $this->enableTurnstile();
        Http::fake(['challenges.cloudflare.com/*' => Http::response(['success' => false, 'error-codes' => ['invalid-input-response']])]);

        $this->postJson('/api/contact', ['name' => 'Іван', 'phone' => '+380501234567', 'turnstile_token' => 'bad'])
            ->assertStatus(422)->assertJsonValidationErrors('turnstile_token');
        $this->assertDatabaseCount('contact_submissions', 0);
    }

    public function test_cloudflare_outage_fails_closed(): void
    {
        $this->enableTurnstile();
        Http::fake(['challenges.cloudflare.com/*' => Http::response('', 500)]);

        $this->postJson('/api/contact', ['name' => 'Іван', 'phone' => '+380501234567', 'turnstile_token' => 't'])
            ->assertStatus(422)->assertJsonValidationErrors('turnstile_token');
    }

    public function test_hostname_must_match_when_configured(): void
    {
        $this->enableTurnstile();
        config(['turnstile.hostnames' => ['kandevelopment.com']]);
        Http::fake(['challenges.cloudflare.com/*' => Http::response(['success' => true, 'hostname' => 'evil.example'])]);

        $this->postJson('/api/contact', ['name' => 'Іван', 'phone' => '+380501234567', 'turnstile_token' => 't'])
            ->assertStatus(422);
    }

    public function test_valid_token_saves_submission_and_sends_mail(): void
    {
        $this->enableTurnstile();
        config(['forms.notify.contact' => ['sales@example.com']]);
        Mail::fake();
        Http::fake(['challenges.cloudflare.com/*' => Http::response(['success' => true, 'hostname' => 'kandevelopment.com'])]);

        $this->postJson('/api/contact', [
            'name' => 'Іван', 'phone' => '+380501234567', 'email' => 'i@example.com',
            'message' => 'Привіт', 'source_page' => '/contacts', 'consent' => true, 'turnstile_token' => 'ok',
        ])->assertOk();

        Http::assertSent(fn ($r) => $r['response'] === 'ok' && $r['secret'] === 'secret');
        $this->assertDatabaseHas('contact_submissions', ['type' => 'contact', 'email' => 'i@example.com', 'source_page' => '/contacts']);
        Mail::assertSent(NewSubmissionMail::class, fn ($m) => $m->hasTo('sales@example.com'));
    }

    public function test_honeypot_is_answered_but_nothing_is_stored(): void
    {
        $this->postJson('/api/contact', ['name' => 'Бот', 'phone' => '1', 'website' => 'http://spam'])->assertOk();
        $this->assertDatabaseCount('contact_submissions', 0);
    }

    public function test_land_offer_stores_files_and_payload(): void
    {
        Storage::fake(ContactSubmission::FILES_DISK);
        config(['forms.notify.land' => []]);

        $this->post('/api/leads/land', [
            'name' => 'Марія', 'phone' => '+380501234567', 'email' => 'm@example.com',
            'address' => 'Київ, вул. Тестова, 1', 'area' => '1,5', 'consent' => '1',
            'docs' => [UploadedFile::fake()->create('plan.pdf', 100, 'application/pdf')],
        ], ['Accept' => 'application/json'])->assertStatus(422); // "1,5" is not a number: the site sends a dot

        $this->post('/api/leads/land', [
            'name' => 'Марія', 'phone' => '+380501234567', 'email' => 'm@example.com',
            'address' => 'Київ, вул. Тестова, 1', 'area' => '1.5', 'cadastre' => '8000000000:00:000:0000', 'consent' => '1',
            'docs' => [UploadedFile::fake()->create('plan.pdf', 100, 'application/pdf')],
        ], ['Accept' => 'application/json'])->assertStatus(201);

        $s = ContactSubmission::firstOrFail();
        $this->assertSame('land', $s->type);
        $this->assertSame('1.5', $s->payload['Площа, га']);
        $this->assertCount(1, $s->attachments);
        Storage::disk(ContactSubmission::FILES_DISK)->assertExists($s->attachments[0]['path']);
    }

    public function test_apartment_and_deck_leads(): void
    {
        $this->postJson('/api/leads/apartment', ['name' => 'Олег', 'phone' => '0501234567', 'type' => '2k', 'project' => 'respublika', 'consent' => true])->assertOk();
        $this->postJson('/api/leads/deck', ['email' => 'd@example.com', 'project' => 'biorytm', 'consent' => true])->assertOk();
        $this->postJson('/api/leads/deck', ['email' => 'not-an-email', 'consent' => true])->assertStatus(422);
        $this->postJson('/api/leads/apartment', ['name' => 'Олег', 'phone' => '123', 'consent' => true])->assertStatus(422);

        $this->assertDatabaseHas('contact_submissions', ['type' => 'apartment', 'name' => 'Олег']);
        $this->assertDatabaseHas('contact_submissions', ['type' => 'deck', 'email' => 'd@example.com']);
    }
}
