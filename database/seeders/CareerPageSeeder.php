<?php

namespace Database\Seeders;

use App\Models\CareerPage;
use Illuminate\Database\Seeder;

class CareerPageSeeder extends Seeder
{
    public function run(): void
    {
        $page = CareerPage::current();

        if ($page->heading) {
            return;
        }

        $page->update([
            'heading' => [
                'uk' => 'Кар’єра і Вакансії',
                'en' => 'Careers',
            ],
            'city_label' => [
                'uk' => 'м. Київ',
                'en' => 'Kyiv',
            ],
            'all_categories_label' => [
                'uk' => 'Всі рубрики',
                'en' => 'All categories',
            ],
            'intro_text' => [
                'uk' => "KAN – це не 3D-рендери, будівельні крани чи квадратні метри. KAN – це люди, що створюють символи Києва, де хочеться більше: жити, бути, робити.\n\nЗ 2001 року ми створили 25 000 робочих місць. Зараз наш колектив, разом з партнерами, налічує 5 тисяч працівників, що об'єднані єдиними цінностями та непорушним принципом у питаннях якості об'єктів.\n\n",
                'en' => "KAN is not 3D renders, construction cranes, or square meters. KAN is people who create symbols of Kyiv, where you want more: to live, to be, to do.\n\nSince 2001, we have created 25,000 jobs. Currently, our team, together with partners, consists of 5,000 employees united by shared values and an unwavering principle regarding the quality of our projects.\n\n",
            ],
            'meta_title' => [
                'uk' => 'KAN | Кар’єра і Вакансії',
                'en' => 'KAN Development | Careers',
            ],
            'meta_description' => [
                'uk' => 'Вакансії та кар’єра в KAN Development. Приєднуйтесь до команди, що створює символи Києва.',
                'en' => 'Careers and vacancies at KAN Development. Join the team building the symbols of Kyiv.',
            ],
        ]);
    }
}
