<?php

namespace Tests\Feature\Hr;

use App\Models\Candidate;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CandidateAdminTest extends HrTestCase
{
    public function test_status_and_responsible_changes_go_to_the_timeline(): void
    {
        $hr = $this->userWithRole('hr', ['name' => 'Олена Савчук']);
        $candidate = $this->candidate(['last_activity_at' => now()->subDay()]);

        $this->actingAs($hr)->put("/admin/candidates/{$candidate->id}", ['status' => 'interview'])
            ->assertSessionHas('status', 'Статус оновлено.');
        $this->actingAs($hr)->put("/admin/candidates/{$candidate->id}", ['responsible_id' => $hr->id]);
        $this->actingAs($hr)->post("/admin/candidates/{$candidate->id}/notes", ['text' => 'Домовились про співбесіду.']);

        $candidate->refresh();
        $this->assertSame('interview', $candidate->status);
        $this->assertSame($hr->id, $candidate->responsible_id);
        $this->assertTrue($candidate->last_activity_at->isToday());
        $this->assertSame(
            ['Домовились про співбесіду.', 'Відповідальний: Олена Савчук', 'Статус: Новий → Співбесіда'],
            $candidate->notes->pluck('text')->all(),
        );

        $this->actingAs($hr)->get("/admin/candidates/{$candidate->id}")->assertOk()->assertSee('Домовились про співбесіду.');
    }

    public function test_cv_is_private_and_downloads_with_a_readable_name(): void
    {
        Storage::fake(Candidate::CV_DISK);
        $vacancy = $this->vacancy(['title' => 'Маркетолог / бренд-менеджер', 'slug' => 'marketing']);
        $path = UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf')->storeAs('cv/2026/10', 'x.pdf', Candidate::CV_DISK);
        $candidate = $this->candidate([
            'vacancy_id' => $vacancy->id,
            'vacancy_title' => $vacancy->title,
            'cv_path' => $path,
            'cv_original_name' => 'CV.PDF',
        ]);

        $this->get("/admin/candidates/{$candidate->id}/cv")->assertRedirect(route('admin.login'));

        // браузери беруть кириличну назву з filename*, filename= — запасна латиницею
        $this->actingAs($this->userWithRole('hr'))
            ->get("/admin/candidates/{$candidate->id}/cv")
            ->assertOk()
            ->assertHeader('Content-Disposition', "attachment; filename=Kovalenko_Irina_Marketolog-brend-menedzer.pdf; filename*=utf-8''"
                .rawurlencode('Коваленко_Ірина_Маркетолог-бренд-менеджер.pdf'));

        $this->actingAs($this->userWithRole('hr'))->delete("/admin/candidates/{$candidate->id}");
        Storage::disk(Candidate::CV_DISK)->assertMissing($path);
    }

    public function test_search_finds_a_phone_by_its_digits(): void
    {
        $this->candidate();
        $this->candidate(['name' => 'Андрій Мельник', 'phone' => '+380 50 111 22 33', 'email' => 'a@example.com']);

        $this->actingAs($this->userWithRole('hr'))
            ->get('/admin/candidates?q=067 000 1408')
            ->assertOk()
            ->assertSee('Ірина Коваленко')
            ->assertDontSee('Андрій Мельник');
    }

    public function test_csv_export_follows_filters_and_defuses_formulas(): void
    {
        $this->candidate(['name' => '=HYPERLINK("http://evil.example")']);
        $this->candidate(['name' => 'Відхилений кандидат', 'status' => 'rejected']);

        $csv = $this->actingAs($this->userWithRole('hr'))
            ->get('/admin/candidates/export?status=new')
            ->assertOk()
            ->streamedContent();

        $this->assertStringStartsWith("\xEF\xBB\xBF".'"Дата і час";ПІБ;Телефон', $csv);
        $this->assertStringContainsString('"\'=HYPERLINK(""http://evil.example"")"', $csv);
        $this->assertStringContainsString("'+380 67 000 14 08", $csv);
        $this->assertStringNotContainsString('Відхилений кандидат', $csv);
    }
}
