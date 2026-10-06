<?php

namespace App\Support;

/**
 * Тимчасовий статичний каталог вакансій (як на kandevelopment.com/career).
 * Пізніше замінимо на БД + адмінку.
 */
class CareerVacanciesCatalog
{
    /** @return array<int, array{id: int, ua: string, en: string}> */
    public static function categories(): array
    {
        return [
            ['id' => 1, 'ua' => 'Інженерія', 'en' => 'Engineering'],
            ['id' => 2, 'ua' => 'Будівництво', 'en' => 'Construction'],
            ['id' => 3, 'ua' => 'Офіс', 'en' => 'Office'],
        ];
    }

    /** @return array<int, array{id: int, title: string, date: string, url: string, category_id: int|null}> */
    public static function vacancies(): array
    {
        return [
            [
                'id' => 1,
                'title' => 'Головний конструктор (служба Замовника)',
                'date' => '2025-09-01',
                'url' => 'https://rabota.ua/',
                'category_id' => 1,
            ],
            [
                'id' => 2,
                'title' => 'Менеджер з тендерів (напрямок - інженерія)',
                'date' => '2025-08-15',
                'url' => 'https://rabota.ua/',
                'category_id' => 1,
            ],
            [
                'id' => 3,
                'title' => 'Бюджетолог будівельного проекту',
                'date' => '2025-08-01',
                'url' => 'https://rabota.ua/',
                'category_id' => 2,
            ],
            [
                'id' => 4,
                'title' => 'Кошторисник (служба замовника)',
                'date' => '2025-07-20',
                'url' => 'https://rabota.ua/',
                'category_id' => 2,
            ],
            [
                'id' => 5,
                'title' => 'Менеджер будівельного проекту',
                'date' => '2025-07-10',
                'url' => 'https://rabota.ua/',
                'category_id' => 2,
            ],
            [
                'id' => 6,
                'title' => 'Діловод будівельно-монтажного комплексу',
                'date' => '2025-06-25',
                'url' => 'https://rabota.ua/',
                'category_id' => 3,
            ],
            [
                'id' => 7,
                'title' => 'Інженер-електрик',
                'date' => '2025-06-10',
                'url' => 'https://rabota.ua/',
                'category_id' => 1,
            ],
            [
                'id' => 8,
                'title' => 'Інженер - геодезист',
                'date' => '2025-05-28',
                'url' => 'https://rabota.ua/',
                'category_id' => 1,
            ],
        ];
    }
}
