<?php

namespace Database\Seeders;

use App\Models\MarketPage;
use Illuminate\Database\Seeder;

class MarketPageSeeder extends Seeder
{
    public function run(): void
    {
        $page = MarketPage::current();

        if ($page->title) {
            return;
        }

        $page->update([
            'phone' => '+38 (067) 579-98-63',
            'badge' => [
                'uk' => 'Новий сервіс від KAN',
                'en' => 'New service from KAN',
            ],
            'title' => [
                'uk' => 'Управління Нерухомістю',
                'en' => 'Real Estate Management',
            ],
            'sell_text' => [
                'uk' => 'Більше не потрібно витрачати власний час на те, щоб продати свою квартиру, адже наша команда всі процеси бере на себе.',
                'en' => 'You no longer need to spend your own time to sell your apartment, because our team takes over all the processes.',
            ],
            'question' => [
                'uk' => 'Плануєте продати свою квартиру?',
                'en' => 'Are you planning to sell your apartment?',
            ],
            'answer' => [
                'uk' => 'Довірте цю справу професіоналам!',
                'en' => 'Entrust this matter to professionals!',
            ],
            'contact_label' => [
                'uk' => 'Зв’язатися з відділом продажу',
                'en' => 'Contact Sales',
            ],
            'pros_title' => [
                'uk' => 'Чому варто скористатися управлінням нерухомістю від KAN',
                'en' => 'Why you should use real estate management from KAN',
            ],
            'pros_items' => [
                'uk' => [
                    'ми маємо багаторічний досвід в реалізації нерухомості',
                    'гарантія безпеки та якості при продажу',
                    'швидкий процес реалізації',
                    'мінімізація всіх бюрократичних процесів',
                    'економія вашого часу',
                    'знання проектів на всі 100%',
                ],
                'en' => [
                    'we have many years of experience in real estate sales',
                    'guarantee of safety and quality at sale',
                    'fast implementation process',
                    'minimization of all bureaucratic processes',
                    'save your time',
                    '100% knowledge of projects',
                ],
            ],
            'trust_text' => [
                'uk' => 'Ви довірилися KAN придбавши житло - довіртеся управлінню нерухомістю від KAN!',
                'en' => 'You have trusted KAN by buying a home - trust KAN real estate management!',
            ],
            'meta_title' => [
                'uk' => 'KAN Market — управління нерухомістю | KAN Development',
                'en' => 'KAN Market — Real Estate Management | KAN Development',
            ],
            'meta_description' => [
                'uk' => 'KAN Market — сервіс з управління та продажу нерухомості від KAN Development.',
                'en' => 'KAN Market — real estate management and sales service by KAN Development.',
            ],
        ]);
    }
}
