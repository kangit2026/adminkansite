<?php

namespace Tests\Feature;

use App\Models\ContactSubmission;
use App\Models\User;
use App\Services\Analytics\AnalyticsPeriod;
use App\Services\Analytics\SiteAnalyticsSource;
use App\Services\SiteAnalytics;
use Carbon\CarbonImmutable;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/** Сторінка «Статистика»: демо-джерело, заглушка GA4, справжні цифри з бази, доступ; дашборд без статистики. */
class SiteStatisticsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // четвер, 12 листопада 2026, 14:00 за Києвом → період 13 жовт. – 11 лист.
        $this->travelTo(CarbonImmutable::parse('2026-11-12 12:00:00', 'UTC'));
        config(['analytics.timezone' => 'Europe/Kyiv', 'analytics.source' => 'demo']);
    }

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->assignRole(Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']));

        return $user;
    }

    private function submission(string $type, string $createdAtUtc): void
    {
        $row = ContactSubmission::create(['type' => $type, 'name' => 'Тест', 'phone' => '+380500000000']);
        $row->forceFill(['created_at' => $createdAtUtc, 'updated_at' => $createdAtUtc])->save();
    }

    /** Справжнє (не демо) джерело з лічильником викликів. */
    private function bindRealSource(bool $fails = false): object
    {
        $source = new class($fails) implements SiteAnalyticsSource
        {
            public int $calls = 0;

            public function __construct(private bool $fails) {}

            public function isDemo(): bool
            {
                return false;
            }

            public function isAvailable(): bool
            {
                return true;
            }

            public function fetch(AnalyticsPeriod $period): array
            {
                $this->calls++;
                if ($this->fails) {
                    throw new RuntimeException('fake outage');
                }

                return [
                    'totals' => ['current' => ['users' => 740, 'sessions' => 960], 'previous' => ['users' => 500, 'sessions' => 800]],
                    'events' => ['current' => ['generate_lead' => 12, 'click_phone' => 30], 'previous' => ['generate_lead' => 8, 'click_phone' => 40]],
                ];
            }
        };
        $this->app->instance(SiteAnalyticsSource::class, $source);

        return $source;
    }

    public function test_demo_source_is_rendered_in_tabs_and_clearly_marked(): void
    {
        $this->actingAs($this->admin())->get(route('admin.statistics'))
            ->assertOk()
            ->assertSee('Статистика сайту')
            // дві вкладки: відвідуваність (демо) і заявки з бази
            ->assertSee('role="tablist"', false)
            ->assertSeeInOrder(['Відвідуваність сайту', 'Демо-дані', 'Заявки та відгуки', 'data-tab="traffic"', 'data-tab="leads"'], false)
            ->assertSee('data-analytics-demo', false)
            ->assertSee('Реальні дані Google Analytics підключимо під час запуску сайту.')
            ->assertSee('13 жовт. – 11 лист. 2026')
            ->assertSeeInOrder(['data-metric="users"', 'data-metric="sessions"', 'data-metric="generate_lead"', 'data-metric="job_apply"',
                'data-metric="click_phone"', 'data-metric="click_email"', 'data-metric="click_messenger"', 'data-metric="db-submissions"'], false)
            ->assertSee('погодилися на cookies')
            // лише плитки: без графіка по днях, сторінок і джерел
            ->assertDontSee('Користувачі по днях')
            ->assertDontSee('Топ-10 сторінок')
            ->assertDontSee('Джерела трафіку')
            ->assertDontSee('Google Analytics не підключено')
            ->assertDontSee('Дані GA оновлено')
            ->assertSee('chart.js@4.4.7', false);
    }

    public function test_demo_numbers_are_stable_and_plausible(): void
    {
        $first = app(SiteAnalytics::class)->report()['traffic'];
        $this->assertTrue($first['demo']);
        $this->assertEquals($first, app(SiteAnalytics::class)->report()['traffic'], 'demo numbers must not change between reloads');

        $tiles = array_column($first['tiles'], null, 'key');
        $this->assertSame(['users', 'sessions', 'generate_lead', 'job_apply', 'click_phone', 'click_email', 'click_messenger'], array_keys($tiles));
        $this->assertGreaterThan($tiles['users']['current'], $tiles['sessions']['current']);
        $this->assertGreaterThan($tiles['generate_lead']['current'], $tiles['click_phone']['current']);
        $this->assertGreaterThan(0, $tiles['job_apply']['current']);
        foreach ($tiles as $tile) {
            $this->assertNotNull($tile['change']);
            $this->assertLessThan(100, abs($tile['change']), $tile['key']);
        }

        // наступного дня вікно зсувається на день — цифри змінюються плавно, без стрибків
        $this->travel(1)->days();
        $next = array_column(app(SiteAnalytics::class)->report()['traffic']['tiles'], null, 'key');
        $this->assertLessThan(.1, abs($next['users']['current'] - $tiles['users']['current']) / $tiles['users']['current']);
    }

    public function test_db_numbers_are_real_next_to_demo_traffic(): void
    {
        $this->submission('contact', '2026-11-10 12:00:00');   // цей тиждень, у періоді
        $this->submission('contact', '2026-11-07 12:00:00');   // 2–8 лист.
        $this->submission('apartment', '2026-11-02 12:00:00'); // 2–8 лист.
        $this->submission('land', '2026-10-01 12:00:00');      // попередні 30 днів
        $this->submission('deck', '2026-11-12 09:00:00');      // сьогодні: у тижні, не в періоді

        $response = $this->actingAs($this->admin())->get(route('admin.statistics'))
            ->assertOk()
            ->assertSee('Демо-дані')
            ->assertSeeInOrder(['data-tab="leads"', 'data-metric="db-submissions"', 'Заявки з форм сайту', '3', '↑ 200%', 'було 1'], false)
            ->assertSee('Зворотний зв’язок — 2')
            ->assertSee('Заявка на квартиру — 1')
            ->assertSee('"submissions":[0,0,0,2,2]', false);

        // без HR-розділу (таблиці candidates) блоку кандидатів немає; з ним — нулі, бо кандидатів у тесті немає
        if (Schema::hasTable('candidates')) {
            $response->assertSee('"candidates":[0,0,0,0,0]', false)->assertSee('data-metric="db-candidates"', false);
        } else {
            $response->assertSee('"candidates":null', false)->assertDontSee('data-metric="db-candidates"', false);
        }
    }

    public function test_ga4_source_is_not_connected_yet(): void
    {
        config(['analytics.source' => 'ga4']);

        $this->actingAs($this->admin())->get(route('admin.statistics'))
            ->assertOk()
            ->assertSee('Google Analytics не підключено')
            ->assertSee('data-ga-status="not_configured"', false)
            ->assertSee('Реальні дані Google Analytics підключимо під час запуску сайту.')
            ->assertDontSee('data-analytics-demo', false)
            ->assertDontSee('data-metric="users"', false)
            ->assertSee('Заявки з форм сайту');
    }

    public function test_real_source_is_cached_and_not_marked_as_demo(): void
    {
        $source = $this->bindRealSource();
        $admin = $this->admin();

        $this->actingAs($admin)->get(route('admin.statistics'))
            ->assertOk()
            ->assertDontSee('data-analytics-demo', false)
            ->assertSeeInOrder(['data-metric="users"', '740', '↑ 48%', 'було 500'], false)
            ->assertSeeInOrder(['data-metric="click_phone"', '30', '↓ 25%', 'було 40'], false)
            ->assertSeeInOrder(['data-metric="click_email"', '0', '—', 'було 0'], false)
            ->assertSee('Дані GA оновлено о 14:00');
        $this->actingAs($admin)->get(route('admin.statistics'))->assertOk();

        $this->assertSame(1, $source->calls);
    }

    public function test_real_source_failure_shows_quiet_card_and_backs_off(): void
    {
        $source = $this->bindRealSource(fails: true);
        $admin = $this->admin();

        $this->actingAs($admin)->get(route('admin.statistics'))
            ->assertOk()
            ->assertSee('Google Analytics не підключено')
            ->assertSee('data-ga-status="error"', false)
            ->assertSee('Заявки з форм сайту');
        $this->actingAs($admin)->get(route('admin.statistics'))->assertOk()->assertSee('data-ga-status="error"', false);

        $this->assertSame(1, $source->calls);
    }

    public function test_candidates_are_counted_when_hr_table_exists(): void
    {
        // таблиця candidates приходить із HR-розділом; тут — мінімальна, якщо її ще немає
        if (! Schema::hasTable('candidates')) {
            Schema::create('candidates', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('phone', 50);
                $table->string('email');
                $table->timestamps();
            });
        }
        foreach (['2026-11-11 08:00:00', '2026-11-03 08:00:00', '2026-10-02 08:00:00', '2026-10-05 08:00:00'] as $at) {
            DB::table('candidates')->insert(['name' => 'Тест', 'phone' => '+380500000000', 'email' => 'test@example.com', 'created_at' => $at, 'updated_at' => $at]);
        }

        $this->actingAs($this->admin())->get(route('admin.statistics'))
            ->assertOk()
            ->assertSeeInOrder(['data-metric="db-candidates"', 'Відгуки кандидатів', '2', 'без змін', 'було 2'], false)
            ->assertSee('"candidates":[0,0,0,1,1]', false);
    }

    public function test_dashboard_links_to_statistics_and_has_no_home_quick_link(): void
    {
        $this->actingAs($this->admin())->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('href="'.route('admin.statistics').'"', false)
            ->assertSeeInOrder(['Дашборд', 'Статистика'])
            ->assertDontSee('data-analytics-demo', false)
            ->assertDontSee('data-tab="traffic"', false)
            ->assertDontSee('<div class="small fw-medium">Головна</div>', false);
    }

    public function test_statistics_are_not_shown_to_non_admins(): void
    {
        $user = User::factory()->create();
        Role::firstOrCreate(['name' => 'hr', 'guard_name' => 'web']);
        $user->assignRole('hr');

        $response = $this->actingAs($user)->get(route('admin.statistics'));

        $this->assertNotSame(200, $response->status());
        $response->assertDontSee('Відвідуваність сайту');
    }
}
