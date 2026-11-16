<?php

namespace Database\Seeders;

use App\Models\CareerDirection;
use App\Models\Vacancy;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Перші напрями й вакансії — ті, що зараз на сайті (kan-site-v2, content/career/jobs.ts),
 * перенесені в data/career_vacancies.json. Повторний запуск нічого не дублює й не перезаписує
 * правок HR: вакансія з таким slug уже є — пропускаємо.
 */
class CareerVacancySeeder extends Seeder
{
    public function run(): void
    {
        $data = json_decode(file_get_contents(__DIR__.'/data/career_vacancies.json'), true);

        $directions = [];
        foreach ($data['directions'] as $row) {
            $directions[$row['name']] = CareerDirection::firstOrCreate(
                ['name' => $row['name']],
                ['sort_order' => $row['sort_order']],
            );
        }

        foreach ($data['vacancies'] as $row) {
            if (Vacancy::where('slug', $row['slug'])->exists()) {
                continue;
            }

            $publishedAt = Carbon::parse($row['published_at'])->setTime(9, 0);

            Vacancy::create([
                'title' => $row['title'],
                'slug' => $row['slug'],
                'direction_id' => $directions[$row['direction']]->id,
                'project' => $row['project'],
                'employment_type' => $row['employment_type'],
                'place' => $row['place'],
                'summary' => $row['summary'],
                'about' => $row['about'],
                'tasks' => $row['tasks'],
                'requirements' => $row['requirements'] ?: null,
                'nice_to_have' => $row['nice_to_have'] ?: null,
                'offer' => $row['offer'] ?: null,
                'status' => 'active',
                'published_at' => $publishedAt,
            ]);
        }
    }
}
