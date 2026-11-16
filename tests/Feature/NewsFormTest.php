<?php

namespace Tests\Feature;

use App\Models\News;
use Tests\Feature\Hr\HrTestCase;

/** Форма новини без SEO-блоку: meta title = заголовок, description — з тексту. */
class NewsFormTest extends HrTestCase
{
    public function test_api_meta_comes_from_title_and_text(): void
    {
        News::create([
            'slug' => 'kan-news',
            'title' => ['uk' => 'Новина KAN', 'en' => 'KAN news'],
            'text' => ['uk' => '<p>Перший абзац.</p><p>Другий&nbsp;<strong>абзац</strong>.</p>'],
            'meta_title' => ['uk' => 'Старий meta title'],
            'status' => 'published',
            'published_at' => now(),
        ]);

        $this->getJson('/api/news/kan-news', ['Accept-Language' => 'ua'])
            ->assertOk()
            ->assertJsonPath('data.metaTitle', 'Новина KAN')
            ->assertJsonPath('data.metaDescription', 'Перший абзац. Другий абзац.');

        $this->getJson('/api/news/kan-news', ['Accept-Language' => 'en'])
            ->assertJsonPath('data.metaTitle', 'KAN news')
            ->assertJsonPath('data.metaDescription', null);
    }

    public function test_long_text_is_cut_on_a_word(): void
    {
        News::create([
            'slug' => 'long',
            'title' => ['uk' => 'Довга'],
            'text' => ['uk' => '<p>'.str_repeat('слово ', 60).'</p>'],
            'status' => 'published',
            'published_at' => now(),
        ]);

        $description = $this->getJson('/api/news/long')->json('data.metaDescription');

        $this->assertLessThanOrEqual(161, mb_strlen($description));
        $this->assertStringEndsWith('слово…', $description);
    }

    public function test_form_errors_are_in_ukrainian(): void
    {
        $this->actingAs($this->userWithRole('admin'))
            ->post(route('admin.news.store'), ['title' => ['en' => 'Only EN'], 'status' => 'draft'])
            ->assertSessionHasErrors(['title.uk' => 'Заповніть поле «Заголовок (UA)».']);
    }

    /** Текст чиститься HTMLPurifier — без пакета ezyang/htmlpurifier збереження падало з 500. */
    public function test_saves_text_and_strips_scripts(): void
    {
        $this->actingAs($this->userWithRole('admin'))
            ->post(route('admin.news.store'), [
                'title' => ['uk' => 'З текстом'],
                'text' => ['uk' => '<p>Абзац <strong>жирний</strong>.</p><script>alert(1)</script>'],
                'status' => 'draft',
            ])
            ->assertRedirect(route('admin.news.index'));

        $text = News::where('title->uk', 'З текстом')->firstOrFail()->getTranslation('text', 'uk');
        $this->assertStringContainsString('<strong>жирний</strong>', $text);
        $this->assertStringNotContainsString('script', $text);
    }

    public function test_form_has_no_seo_and_partner_fields(): void
    {
        $this->actingAs($this->userWithRole('admin'))
            ->get(route('admin.news.create'))
            ->assertOk()
            ->assertSee('Публікація')
            ->assertDontSee('meta_title', false)
            ->assertDontSee('partner_logo', false)
            ->assertDontSee('Партнер');
    }

    /** Партнерів прибрали (2026-11-12): новини про Добробут немає, API блоку — порожній список. */
    public function test_partners_are_gone(): void
    {
        $this->assertDatabaseMissing('news', ['slug' => 'strategic-partnership-kan-dobrobut']);

        $this->getJson('/api/Partnership')
            ->assertOk()
            ->assertExactJson(['success' => true, 'value' => []]);
    }
}
