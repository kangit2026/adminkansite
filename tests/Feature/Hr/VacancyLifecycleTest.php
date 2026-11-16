<?php

namespace Tests\Feature\Hr;

use App\Models\Vacancy;
use Illuminate\Support\Facades\Http;

class VacancyLifecycleTest extends HrTestCase
{
    public function test_draft_is_not_on_the_site(): void
    {
        $this->actingAs($this->userWithRole('hr'))
            ->post('/admin/vacancies', $this->vacancyForm(['action' => 'draft']))
            ->assertSessionHasNoErrors();

        $vacancy = Vacancy::sole();
        $this->assertSame('draft', $vacancy->status);
        $this->assertSame('holovnyi-konstruktor-sluzhba-zamovnyka', $vacancy->slug);
        // маркери й порожні рядки прибрано
        $this->assertSame(['Перевіряти документацію', 'Ставити завдання проєктувальникам'], $vacancy->tasks);
        $this->assertNull($vacancy->requirements);
        $this->assertNull($vacancy->project);

        $this->getJson('/api/career/jobs')->assertOk()->assertJsonCount(0, 'data');
        $this->getJson('/api/career/jobs/'.$vacancy->slug)->assertNotFound();
    }

    public function test_publish_close_and_reopen(): void
    {
        $hr = $this->userWithRole('hr');
        $vacancy = $this->vacancy(['status' => 'draft', 'published_at' => null]);
        $this->candidate(['vacancy_id' => $vacancy->id, 'vacancy_title' => $vacancy->title]);

        $this->actingAs($hr)->post("/admin/vacancies/{$vacancy->id}/publish")->assertSessionHas('status', 'Вакансію опубліковано.');
        $this->getJson('/api/career/jobs')->assertJsonPath('data.0.slug', $vacancy->slug);
        $this->getJson('/api/career/jobs/'.$vacancy->slug)
            ->assertOk()
            ->assertJsonPath('data.about', ['Перший абзац.', 'Другий абзац.'])
            ->assertJsonPath('data.place', Vacancy::DEFAULT_PLACE);

        $this->actingAs($hr)->post("/admin/vacancies/{$vacancy->id}/close");
        $this->getJson('/api/career/jobs')->assertJsonCount(0, 'data');
        $this->getJson('/api/career/jobs/'.$vacancy->slug)->assertStatus(410);
        $this->assertSame(1, $vacancy->candidates()->count());

        $this->travel(2)->days();
        $this->actingAs($hr)->post("/admin/vacancies/{$vacancy->id}/reopen");
        $vacancy->refresh();
        $this->assertSame('active', $vacancy->status);
        $this->assertTrue($vacancy->published_at->isToday());
        $this->getJson('/api/career/jobs/'.$vacancy->slug)->assertOk();
    }

    public function test_slug_does_not_change_after_publishing(): void
    {
        $vacancy = $this->vacancy();

        $this->actingAs($this->userWithRole('hr'))
            ->put("/admin/vacancies/{$vacancy->id}", $this->vacancyForm(['title' => 'Нова назва', 'slug' => 'new-slug']))
            ->assertSessionHasNoErrors();

        $vacancy->refresh();
        $this->assertSame('Нова назва', $vacancy->title);
        $this->assertSame('inzhener-heodezyst', $vacancy->slug);
    }

    public function test_only_empty_draft_can_be_deleted(): void
    {
        $hr = $this->userWithRole('hr');
        $active = $this->vacancy();
        $draft = $this->vacancy(['slug' => 'draft', 'status' => 'draft', 'published_at' => null]);

        $this->actingAs($hr)->delete("/admin/vacancies/{$active->id}")->assertSessionHasErrors('vacancy');
        $this->actingAs($hr)->delete("/admin/vacancies/{$draft->id}")->assertRedirect(route('admin.vacancies.index'));

        $this->assertModelExists($active);
        $this->assertModelMissing($draft);
    }

    public function test_validation_messages_are_ukrainian(): void
    {
        $this->actingAs($this->userWithRole('hr'))
            ->post('/admin/vacancies', $this->vacancyForm(['title' => '', 'summary' => str_repeat('а', 301)]))
            ->assertSessionHasErrors([
                'title' => 'Заповніть поле «Назва посади».',
                'summary' => 'Поле «Коротко для картки» задовге — до 300 символів.',
            ]);
    }

    public function test_site_is_asked_to_refresh_career_pages(): void
    {
        config(['services.site.revalidate_url' => 'https://site.test/api/revalidate', 'services.site.revalidate_secret' => 'token']);
        Http::fake();
        $vacancy = $this->vacancy();

        $this->actingAs($this->userWithRole('hr'))->post("/admin/vacancies/{$vacancy->id}/close");

        Http::assertSent(fn ($request) => $request->url() === 'https://site.test/api/revalidate'
            && $request->hasHeader('Authorization', 'Bearer token')
            && $request['paths'] === ['/career', '/career/inzhener-heodezyst']);
    }
}
