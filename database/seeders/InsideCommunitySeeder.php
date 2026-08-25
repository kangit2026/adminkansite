<?php

namespace Database\Seeders;

use App\Models\InsideCommunityBlock;
use App\Models\InsideCommunityPage;
use Illuminate\Database\Seeder;

class InsideCommunitySeeder extends Seeder
{
    public function run(): void
    {
        $page = InsideCommunityPage::current();

        if (! $page->hero_title) {
            $page->update([
                'hero_title' => [
                    'uk' => " жити \nбути \n робити",
                    'en' => " live \nbe \n do",
                ],
                'hero_subtitle' => [
                    'uk' => "KAN створює простір, \nде хочеться більше",
                    'en' => "KAN creates a space, \nwhere you want more",
                ],
                'hero_paragraph_1' => [
                    'uk' => 'Є речі, які не можна просто побудувати навіть із найкращих матеріалів. Не можна просто побудувати дружні стосунки, атмосферу, що надихає до розвитку, гарний настрій та грандіозні ідеї. Але це можна створити.',
                    'en' => "There are things that cannot be simply built even with the best materials. \nYou can't just build friendships, an atmosphere that inspires development, \na good mood, and great ideas. But it can be created.",
                ],
                'hero_paragraph_2' => [
                    'uk' => 'Кожен киянин трішки створює наш Київ.',
                    'en' => 'Every Kyivan creates our Kyiv a little bit.',
                ],
                'hero_paragraph_3' => [
                    'uk' => "А ми створюємо прогресивне, сучасне середовище, сповнене нових \nможливостей для розвитку, яке їх надихає та розвиває.",
                    'en' => "And we create a progressive, modern environment full of new opportunities \nfor development that inspires and develops them.",
                ],
                'philosophy_title' => [
                    'uk' => '10 принципів філософії inside community',
                    'en' => '10 principles of the inside community philosophy',
                ],
                'philosophy_subtitle' => [
                    'uk' => 'У житлових комплексах від KAN',
                    'en' => 'In residential complexes by KAN',
                ],
                'meta_title' => [
                    'uk' => 'Inside Community — KAN Development',
                    'en' => 'Inside Community — KAN Development',
                ],
                'meta_description' => [
                    'uk' => '10 принципів філософії Inside Community у житлових комплексах від KAN Development.',
                    'en' => '10 principles of the Inside Community philosophy in residential complexes by KAN Development.',
                ],
            ]);
        }

        if (InsideCommunityBlock::query()->exists()) {
            return;
        }

        $blocks = [
            [
                'type' => 'principle',
                'layout' => 'full_flip_first',
                'number' => 1,
                'sort_order' => 10,
                'title' => ['uk' => "Простір для Життя, \nРоботи та Відпочинку", 'en' => 'Space for Life, Work and Recreation'],
                'description' => ['uk' => "Наші комплекси – багатофункціональні простори, де одночасно можна \nжити, працювати, навчати та виховувати дітей, відпочивати та \nорганізовувати дозвілля, бути в центрі подій та водночас мати простір \nдля усамітнення.", 'en' => "Our complexes are multifunctional spaces where you can live, work, \neducate and raise children, relax and organize leisure, be in the center \nof events and still have space for privacy."],
            ],
            [
                'type' => 'stat',
                'layout' => 'default',
                'sort_order' => 20,
                'stat_value' => ['uk' => '80%', 'en' => '80%'],
                'stat_label' => ['uk' => "наших мешканців знаходять все необхідне всередині ком'юніті", 'en' => 'of our residents find everything they need within the community'],
            ],
            [
                'type' => 'highlight',
                'layout' => 'with_icon',
                'sort_order' => 30,
                'highlight_small' => ['uk' => 'Більше', 'en' => 'More than '],
                'highlight_big' => ['uk' => "20 000 \nквартир", 'en' => "20,000 \napartments"],
                'highlight_medium' => ['uk' => 'в наших проектах вже тішать своїх власників затишком та комфортом', 'en' => 'in our projects already provide their owners with coziness and comfort'],
            ],
            [
                'type' => 'highlight',
                'layout' => 'default',
                'sort_order' => 40,
                'highlight_small' => ['uk' => 'Більше', 'en' => 'More than'],
                'highlight_big' => ['uk' => "57 000\nмешканців", 'en' => "57,000\nresidents"],
                'highlight_medium' => ['uk' => 'живуть в наших проектах', 'en' => 'live in our projects'],
            ],
            [
                'type' => 'principle',
                'layout' => 'full_flip',
                'number' => 2,
                'sort_order' => 50,
                'title' => ['uk' => 'Безпека, Довіра та Підтримка', 'en' => 'Security, Trust and Support'],
                'description' => ['uk' => "Ми створили середовище, де ​кожен може відчувати себе ​захищеним. \nУ нас можна не ​хвилюватися про забутий у ​дворі телефон, та завжди ​\nцінуються доброта і взаємна ​повага.", 'en' => "We have created an environment where everyone can feel safe and secure. We don't have to worry about your phone being forgotten in the yard, and we always value kindness and mutual respect."],
            ],
            [
                'type' => 'stat',
                'layout' => 'default',
                'sort_order' => 60,
                'stat_value' => ['uk' => 'СПІЛЬНОТА', 'en' => 'A Community'],
                'stat_label' => ['uk' => "Комфорт і безпеку створюють не паркани, \nа люди, які мешкають \nусередині території", 'en' => 'Comfort and security are created not by fences, but by the people who live within the community.'],
            ],
            [
                'type' => 'principle',
                'layout' => 'full_magic2',
                'number' => 3,
                'sort_order' => 70,
                'title' => ['uk' => 'Людиноцентричність', 'en' => 'Human-Centric Approach'],
                'description' => ['uk' => "Філософія INSIDE Community ​завжди ставить людину на ​перше місце. \nЦе повага до ​кожного мешканця, його часу, ​фізичних та ментальних ​потреб. \nНаша винагорода – ​гармонійне життя ваших ​родин, атмосфера \nєдності та ​підтримки.", 'en' => 'The INSIDE Community philosophy always puts people first. It is about respect for every resident—their time, physical, and mental well-being. Our greatest reward is the harmonious life of your families, a sense of unity, and mutual support.'],
            ],
            [
                'type' => 'stat',
                'layout' => 'special',
                'sort_order' => 80,
                'stat_value' => ['uk' => 'ТИ - ОСНОВА', 'en' => "YOU ARE \nTHE FOUNDATION"],
                'stat_label' => ['uk' => 'А вся наша "житлова" планета обертається навколо тебе', 'en' => 'And our entire residential planet revolves around you.'],
            ],
            [
                'type' => 'principle',
                'layout' => 'full_flip',
                'number' => 4,
                'sort_order' => 90,
                'title' => ['uk' => "Ком'юніті ​однодумців", 'en' => 'A Community of Like-Minded People'],
                'description' => ['uk' => "Ми створили не просто ​будинки, а спільноти, які ​об'єднані в чесне, \nсвітле та ​екологічне середовище. Схожі ​цінності та інтереси сприяють ​\nновим знайомствам, дружбі та ​бізнес-можливостям.", 'en' => 'We have created more than just buildings—we have built communities united by an honest, vibrant, and eco-conscious environment. Shared values and interests foster new connections, friendships, and business opportunities.'],
            ],
            [
                'type' => 'stat',
                'layout' => 'description_only',
                'sort_order' => 100,
                'stat_label' => ['uk' => "Якщо ви знаєте про теорію шести рукостискань, то мешканці наших \nпроектів знайомі один з одним лише через два потискання.", 'en' => "If you're familiar with the Six Degrees of Separation theory, then in our communities, residents are connected by just two handshakes."],
            ],
            [
                'type' => 'ribbon',
                'layout' => 'default',
                'sort_order' => 110,
                'title' => ['uk' => 'Від 5 до 12 хвилин', 'en' => 'From 5 to 12 minutes'],
                'description' => ['uk' => "Займає дорога від квартири до будь якого потрібного вам об'єкту \nінфраструктури, будь це дитсадок чи фітнес, басейн чи коворкінг", 'en' => "That's all it takes to get from your apartment to any essential infrastructure—whether it's a kindergarten, fitness center, swimming pool, or co-working space."],
            ],
            [
                'type' => 'principle',
                'layout' => 'compact',
                'number' => 5,
                'sort_order' => 120,
                'title' => ['uk' => "Відкритість та ​прозорість", 'en' => "Openness and \nTransparency"],
                'description' => ['uk' => "Управляюча Компанія ​всередині комплексу працює ​на принципах \nповної ​відкритості та прозорості, що ​дає змогу всім мешканцям ​впливати \nна покращення ​життя та добробуту в ЖК.", 'en' => 'The Property Management Company within the complex operates on the principles of complete openness and transparency, allowing all residents to actively contribute to improving life and well-being in the community.'],
            ],
            [
                'type' => 'principle',
                'layout' => 'compact_flip',
                'number' => 6,
                'sort_order' => 130,
                'title' => ['uk' => 'ІНФРАСТРУКТУРА INSIDE', 'en' => 'INSIDE  INFRASTRUCTURE'],
                'description' => ['uk' => "Ми забезпечили повний перелік якісних інфраструктурних \nоб'єктів та сервісів, які покривають усі ваші потреби та ​\nдозволяють не покидати територію ЖК та економити максимум часу.", 'en' => 'We provide a complete range of high-quality infrastructure and services that cover all your daily needs, allowing you to stay within the residential community and save valuable time.'],
            ],
            [
                'type' => 'principle',
                'layout' => 'compact',
                'number' => 7,
                'sort_order' => 140,
                'title' => ['uk' => "Підприємництво та \nСпільні Проекти", 'en' => "Entrepreneurship & \nCollaborative Projects"],
                'description' => ['uk' => "Люди в нашому ком'юніті ​легко знаходять партнерів для ​розвитку своїх \nідей та проектів. ​А квартири, в які інвестовано ​для здачі в оренду, \nзнаходять ​нових мешканців за один день", 'en' => 'In our community, people easily find partners to develop their ideas and projects. And apartments purchased for rental investment quickly find new residents.'],
            ],
            [
                'type' => 'highlight',
                'layout' => 'left',
                'sort_order' => 150,
                'highlight_small' => ['uk' => 'Робимо', 'en' => 'Doing It'],
                'highlight_big' => ['uk' => 'РАЗОМ', 'en' => 'TOGETHER'],
                'highlight_medium' => ['uk' => 'Відкрити кафе - разом, зіграти в баскетбол - разом, на барбекю - разом. \nСаме так наші сусіди стають друзями', 'en' => 'Opening a café—together. Playing basketball—together. A barbecue—together. This is how our neighbors become friends.'],
            ],
            [
                'type' => 'highlight',
                'layout' => 'left',
                'sort_order' => 160,
                'highlight_small' => ['uk' => 'Допомагаємо', 'en' => 'Helping'],
                'highlight_big' => ['uk' => 'ОДИН - ОДНОМУ', 'en' => 'EACH OTHER'],
                'highlight_medium' => ['uk' => 'Наші мешканці домогають новим сусідам відчути, що спільне тут - це своє. \nЩо допомагає зберігати всю інфраструктуру в ідеальному стані', 'en' => 'Our residents make sure new neighbors feel at home, where shared spaces truly feel like their own. This sense of community helps keep the entire infrastructure in perfect condition.'],
            ],
            [
                'type' => 'principle',
                'layout' => 'full_magic_offset',
                'number' => 8,
                'sort_order' => 170,
                'title' => ['uk' => 'ОСВІТНІЙ ХОЛДИНГ A+ У ВАШОМУ ДВОРІ', 'en' => 'A+ EDUCATION HOLDING IN YOUR COURTYARD'],
                'description' => ['uk' => "KAN створює середовище, де діти ростуть поруч із домом. \nУ кожному нашому кварталі працює власна мережа освіти A+ для дітей від 1 до 19 років. \n Це турбота про якісне навчання та безпеку", 'en' => 'KAN creates an environment where children grow up right next to home. Each of our communities has its own A+ educational network for children aged 1 to 19. It ensures high-quality learning, safety, and continuous development.'],
            ],
            [
                'type' => 'principle',
                'layout' => 'full_magic_t',
                'number' => 9,
                'sort_order' => 180,
                'title' => ['uk' => "Активне Життя та ​спільні Ініціативи", 'en' => "Active Living & \nCollective Initiatives"],
                'description' => ['uk' => "Філософія INSIDE Community ​завжди ставить людину на ​перше місце. \nЦе повага до ​кожного мешканця, його часу, ​фізичних та ментальних ​потреб. \nНаша винагорода – ​гармонійне життя ваших ​родин, атмосфера \nєдності та ​підтримки.", 'en' => 'The INSIDE Community philosophy always puts people first. It is about respect for every resident—their time, physical, and mental well-being. Our greatest reward is the harmonious life of your families.'],
            ],
            [
                'type' => 'final',
                'layout' => 'default',
                'number' => 10,
                'sort_order' => 190,
                'title' => ['uk' => 'INSIDE Community ​- це стан', 'en' => 'INSIDE Community – A State of Being'],
                'description' => ['uk' => "INSIDE Community – це не просто ​місце проживання, а стан душі, ​де \nбезумовна якість є основою ​всього. Це відчуття гармонії та ​задоволення, \nде кожен аспект ​продуманий до дрібниць, а ​зайві подразники відсутні. \nТут ​ви відчуєте справжню якість ​життя у відносинах з людьми ​та в \nнавколишньому ​середовищі.", 'en' => "INSIDE Community is more than just a place to live—it's a state of mind, where uncompromising quality is the foundation of everything. It's a feeling of harmony and fulfillment, where every detail is thoughtfully designed, and distractions are eliminated. Here, you'll experience true quality of life—both in your relationships with people and in your surroundings."],
                'cta_text' => ['uk' => "Приєднуйся до нашої філософії та стань частиною ком'юніті", 'en' => 'Join our philosophy and become part of the community.'],
            ],
        ];

        foreach ($blocks as $block) {
            InsideCommunityBlock::create(array_merge($block, ['status' => 'published']));
        }
    }
}
