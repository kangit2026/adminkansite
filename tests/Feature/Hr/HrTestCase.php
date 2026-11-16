<?php

namespace Tests\Feature\Hr;

use App\Models\Candidate;
use App\Models\CareerDirection;
use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Міграції проєкту містять MySQL-специфічний SQL, тож тести — на MySQL/MariaDB:
 * DB_CONNECTION=mysql DB_DATABASE=adminkansite_test php artisan test
 */
abstract class HrTestCase extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // роль hr створює міграція, admin — AdminUserSeeder
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    }

    protected function userWithRole(string $role, array $attributes = []): User
    {
        $user = User::create($attributes + [
            'name' => ucfirst($role).' '.fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'password' => 'secret-password',
            'is_active' => true,
        ]);

        return $user->assignRole($role);
    }

    protected function direction(string $name = 'Інженерія'): CareerDirection
    {
        return CareerDirection::firstOrCreate(['name' => $name], ['sort_order' => 1]);
    }

    protected function vacancy(array $attributes = []): Vacancy
    {
        return Vacancy::create($attributes + [
            'title' => 'Інженер-геодезист',
            'slug' => 'inzhener-heodezyst',
            'direction_id' => $this->direction()->id,
            'employment_type' => 'full_time',
            'summary' => 'Геодезичний супровід будівництва.',
            'about' => "Перший абзац.\n\nДругий абзац.",
            'tasks' => ['Знімання', 'Розбивка осей'],
            'status' => 'active',
            'published_at' => now()->subDays(3),
        ]);
    }

    protected function candidate(array $attributes = []): Candidate
    {
        return Candidate::create($attributes + [
            'name' => 'Ірина Коваленко',
            'phone' => '+380 67 000 14 08',
            'email' => 'i.kovalenko@example.com',
            'status' => 'new',
            'last_activity_at' => now(),
        ]);
    }

    /** Поля форми вакансії в адмінці. */
    protected function vacancyForm(array $overrides = []): array
    {
        return $overrides + [
            'title' => 'Головний конструктор (служба замовника)',
            'direction_id' => $this->direction()->id,
            'employment_type' => 'full_time',
            'project' => '',
            'place' => '',
            'summary' => 'Технічна експертиза конструктивних рішень.',
            'about' => 'Про роль.',
            'tasks' => "• Перевіряти документацію\n\n- Ставити завдання проєктувальникам\n",
            'requirements' => '',
            'nice_to_have' => '',
            'offer' => 'Офіс на Печерську',
        ];
    }
}
