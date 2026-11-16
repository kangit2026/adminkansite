<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CareerPageResource;
use App\Http\Resources\VacancyDetailResource;
use App\Http\Resources\VacancyResource;
use App\Models\CareerDirection;
use App\Models\CareerPage;
use App\Models\Vacancy;
use App\Support\CareerVacanciesCatalog;
use Illuminate\Http\Request;

class CareerController extends Controller
{
    public function page()
    {
        return new CareerPageResource(CareerPage::current());
    }

    /** Напрями з кількістю активних вакансій; сайт показує у фільтрі лише непорожні. */
    public function directions()
    {
        return response()->json(
            CareerDirection::ordered()
                ->withCount(['vacancies as active_count' => fn ($q) => $q->active()])
                ->get()
                ->map(fn (CareerDirection $d) => ['id' => $d->id, 'name' => $d->name, 'count' => $d->active_count])
        );
    }

    /** Активні вакансії з адмінки HR, нові — першими. */
    public function jobs()
    {
        return VacancyResource::collection(
            Vacancy::active()->with('direction')->orderByDesc('published_at')->orderByDesc('id')->get()
        );
    }

    /** Чернетка й невідома адреса — 404, закрита — 410 (сайт веде на /career). */
    public function job(string $slug)
    {
        $vacancy = Vacancy::with('direction')->where('slug', $slug)->first();

        if (! $vacancy || $vacancy->status === 'draft') {
            return response()->json(['message' => 'Вакансію не знайдено.'], 404);
        }

        if ($vacancy->status === 'closed') {
            return response()->json(['message' => 'Вакансію закрито.'], 410);
        }

        return new VacancyDetailResource($vacancy);
    }

    /*
     * categories() і vacancies() нижче — старий статичний каталог для попереднього фронтенду.
     * Прибрати разом із CareerVacanciesCatalog, коли сайт перейде на /career/jobs (етап 2).
     */

    public function categories(Request $request)
    {
        $locale = app()->getLocale();
        $labelKey = $locale === 'en' ? 'en' : 'ua';

        return response()->json(
            collect(CareerVacanciesCatalog::categories())
                ->map(fn (array $row) => [
                    'id' => $row['id'],
                    'ua' => $row[$labelKey],
                ])
                ->values()
        );
    }

    public function vacancies(Request $request)
    {
        $page = max(0, (int) $request->query('page', 0));
        $category = $request->query('category');
        $perPage = 20;

        $items = collect(CareerVacanciesCatalog::vacancies());

        if ($category !== null && $category !== '') {
            $categoryId = (int) $category;
            $items = $items->where('category_id', $categoryId);
        }

        $total = $items->count();
        $slice = $items->slice($page * $perPage, $perPage)->values();

        return response()->json([
            'value' => $slice->map(fn (array $row) => [
                'id' => $row['id'],
                'title' => $row['title'],
                'date' => $row['date'],
                'url' => $row['url'],
            ]),
            'total' => $total,
        ]);
    }
}
