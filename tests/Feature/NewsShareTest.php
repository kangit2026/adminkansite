<?php

namespace Tests\Feature;

use App\Models\News;
use App\Models\NewsShare;
use App\Models\User;
use App\Services\SiteAnalytics;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/** Лічильник «Поділитися новиною»: публічний ендпоінт, вкладка «Поширення новин» на «Статистиці», колонка в списку новин. */
class NewsShareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // четвер, 12 листопада 2026, 14:00 за Києвом → період 13 жовт. – 11 лист., попередній 13 вер. – 12 жовт.
        $this->travelTo(CarbonImmutable::parse('2026-11-12 12:00:00', 'UTC'));
        config(['analytics.timezone' => 'Europe/Kyiv', 'analytics.source' => 'demo']);
    }

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->assignRole(Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']));

        return $user;
    }

    private function news(string $slug, string $title, string $status = 'published'): News
    {
        return News::create([
            'slug' => $slug,
            'title' => ['uk' => $title, 'en' => $title.' (EN)'],
            'status' => $status,
            'published_at' => '2026-10-01 09:00:00',
        ]);
    }

    private function share(News $news, string $channel, string $createdAtUtc, int $times = 1): void
    {
        for ($i = 0; $i < $times; $i++) {
            NewsShare::create(['news_id' => $news->id, 'channel' => $channel])
                ->forceFill(['created_at' => $createdAtUtc])
                ->save();
        }
    }

    public function test_share_click_is_counted_without_personal_data(): void
    {
        $news = $this->news('budivnytstvo-startuvalo', 'Будівництво стартувало');

        // як navigator.sendBeacon: звичайна форма, без Accept: application/json
        $this->post('/api/news/budivnytstvo-startuvalo/share', ['channel' => 'telegram'])->assertNoContent();
        $this->postJson('/api/news/budivnytstvo-startuvalo/share', ['channel' => 'copy'])->assertNoContent();

        $this->assertSame(['copy', 'telegram'], NewsShare::where('news_id', $news->id)->orderBy('channel')->pluck('channel')->all());
        // лише новина, канал і час — ні IP, ні браузера
        $this->assertEqualsCanonicalizing(['id', 'news_id', 'channel', 'created_at'], Schema::getColumnListing('news_shares'));
    }

    public function test_unknown_channel_and_unknown_or_draft_news_are_rejected(): void
    {
        $this->news('opublikovana', 'Опублікована');
        $this->news('chernetka', 'Чернетка', status: 'draft');

        $this->post('/api/news/opublikovana/share', ['channel' => 'twitter'])->assertStatus(422);
        $this->post('/api/news/opublikovana/share')->assertStatus(422);
        $this->post('/api/news/chernetka/share', ['channel' => 'telegram'])->assertNotFound();
        $this->post('/api/news/nema-takoi/share', ['channel' => 'telegram'])->assertNotFound();

        $this->assertSame(0, NewsShare::count());
    }

    public function test_share_endpoint_is_rate_limited(): void
    {
        $this->news('novyna', 'Новина');

        for ($i = 0; $i < 20; $i++) {
            $this->post('/api/news/novyna/share', ['channel' => 'facebook'])->assertNoContent();
        }
        $this->post('/api/news/novyna/share', ['channel' => 'facebook'])->assertStatus(429);

        $this->assertSame(20, NewsShare::count());
    }

    public function test_statistics_shares_tab_shows_channels_and_top_news(): void
    {
        $a = $this->news('kvartal-a', 'Новина про квартал А');
        $b = $this->news('kvartal-b', 'Новина про квартал Б');

        $this->share($a, 'telegram', '2026-11-05 10:00:00', times: 3);
        $this->share($a, 'copy', '2026-10-20 10:00:00');
        $this->share($b, 'facebook', '2026-11-11 21:30:00', times: 2); // 23:30 за Києвом — ще в періоді
        $this->share($b, 'viber', '2026-11-11 22:30:00');              // 00:30 12 лист. за Києвом — сьогодні, не рахуємо
        $this->share($a, 'linkedin', '2026-10-01 10:00:00', times: 2); // попередній період
        $this->share($a, 'telegram', '2026-08-01 10:00:00');          // раніше — не рахуємо

        $shares = app(SiteAnalytics::class)->report()['shares'];

        $this->assertSame(6, $shares['current']);
        $this->assertSame(2, $shares['previous']);
        $this->assertSame(200, $shares['change']);
        $this->assertSame(2, $shares['news']);
        $this->assertSame(['telegram', 'facebook', 'copy', 'viber', 'linkedin', 'native'], array_column($shares['channels'], 'key'));
        $this->assertSame([3, 2, 1, 0, 0, 0], array_column($shares['channels'], 'count'));
        $this->assertSame([50, 33, 17, 0, 0, 0], array_column($shares['channels'], 'percent'));
        $this->assertSame([['id' => $a->id, 'title' => 'Новина про квартал А', 'count' => 4], ['id' => $b->id, 'title' => 'Новина про квартал Б', 'count' => 2]], $shares['top']);

        $this->actingAs($this->admin())
            ->get(route('admin.statistics'))
            ->assertOk()
            ->assertSee('Поширення новин')
            ->assertSee('data-tab="shares"', false)
            ->assertSee('data-metric="shares-total"', false)
            ->assertSee('↑ 200%')
            ->assertSeeInOrder(['data-channel="telegram"', 'data-channel="facebook"', 'data-channel="copy"'], false)
            ->assertSeeInOrder(['data-top-news', 'Новина про квартал А', 'Новина про квартал Б'], false)
            ->assertSee(route('admin.news.edit', $a))
            ->assertDontSee('data-shares-empty', false);
    }

    public function test_shares_tab_shows_empty_state_without_shares(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.statistics'))
            ->assertOk()
            ->assertSee('data-shares-empty', false)
            ->assertSee('За цей період новинами ще не ділилися')
            ->assertDontSee('data-metric="shares-total"', false);
    }

    public function test_news_list_shows_share_counts(): void
    {
        $a = $this->news('z-poshyrenniamy', 'Новина з поширеннями');
        $this->news('bez-poshyren', 'Новина без поширень');
        $this->share($a, 'telegram', '2026-11-05 10:00:00', times: 2);
        $this->share($a, 'viber', '2026-03-05 10:00:00'); // у списку — за весь час

        $this->actingAs($this->admin())
            ->get(route('admin.news.index'))
            ->assertOk()
            ->assertSee('Поширень')
            ->assertSee('data-shares="3"', false)
            ->assertSee('data-shares="0"', false);

        // новину видалили — її натискання теж зникають
        $a->delete();
        $this->assertSame(0, NewsShare::count());
    }
}
