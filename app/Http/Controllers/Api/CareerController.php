<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CareerPageResource;
use App\Models\CareerPage;
use App\Support\CareerVacanciesCatalog;
use Illuminate\Http\Request;

class CareerController extends Controller
{
    public function page()
    {
        return new CareerPageResource(CareerPage::current());
    }

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
