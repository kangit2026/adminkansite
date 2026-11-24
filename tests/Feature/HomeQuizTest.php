<?php

namespace Tests\Feature;

use App\Models\ContactSubmission;
use App\Models\SiteSetting;
use App\Models\User;
use App\Support\HomeQuiz;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/** Квіз заявки на головній: API для сайту, редактор в адмінці, відповіді окремими полями заявки. */
class HomeQuizTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // на проді рядок налаштувань з id = 1 уже є; у тестах (MySQL) лічильник id не відкочується разом із транзакцією
        SiteSetting::unguarded(fn () => SiteSetting::create(['id' => 1]));
    }

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->assignRole(Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']));

        return $user;
    }

    public function test_api_serves_the_default_quiz_without_disabled_options(): void
    {
        $quiz = HomeQuiz::defaults();
        $quiz['intent']['options'][2]['enabled'] = false; // «Хочу щось уточнити»
        SiteSetting::current()->update(['home_quiz' => $quiz]);

        $data = $this->getJson('/api/quiz')->assertOk()->json('data');

        $this->assertSame(['apartment', 'commercial'], array_column($data['intent']['options'], 'value'));
        $this->assertArrayNotHasKey('question', $data['paths']);
        $this->assertSame('project', $data['project_step']);
        $this->assertSame('Яке приміщення шукаєте?', $data['paths']['commercial'][0]['question']['uk']);
    }

    public function test_quiz_answers_become_separate_fields_of_the_lead(): void
    {
        $this->postJson('/api/leads/apartment', [
            'name' => 'Олег', 'phone' => '0501234567', 'consent' => true, 'project' => 'biorytm', 'type' => 'legacy',
            'quiz' => json_encode(['intent' => 'apartment', 'project' => 'biorytm', 'rooms' => '2', 'purpose' => 'invest', 'foreign' => 'x']),
        ])->assertOk();

        $payload = ContactSubmission::first()->payload;
        $this->assertSame([
            'Запит' => 'Квартира в житловому кварталі',
            'Квартал' => 'БІОРИТМ',
            'Кімнати' => '2 кімнати',
            'Мета' => 'Як інвестицію',
        ], $payload);
    }

    public function test_plain_apartment_form_keeps_project_and_type(): void
    {
        $this->postJson('/api/leads/apartment', ['name' => 'Олег', 'phone' => '0501234567', 'consent' => true, 'project' => 'respublika', 'type' => '2k'])->assertOk();

        $this->assertSame(['Проєкт' => 'respublika', 'Тип / вибір' => '2k'], ContactSubmission::first()->payload);
    }

    public function test_admin_edits_the_quiz_and_new_answers_use_the_new_labels(): void
    {
        $quiz = HomeQuiz::defaults();
        $quiz['paths']['apartment'][1]['label']['uk'] = 'Кімнатність';
        $quiz['paths']['apartment'][1]['options'][2]['label']['uk'] = 'Двокімнатна';
        $quiz['paths']['commercial'] = []; // гілка без питань

        $this->actingAs($this->admin())->get('/admin/quiz')->assertOk()->assertSee('Квіз заявки');
        $this->actingAs($this->admin())->put('/admin/quiz', ['quiz' => json_encode($quiz)])->assertSessionHasNoErrors();

        $this->postJson('/api/leads/apartment', [
            'name' => 'Олег', 'phone' => '0501234567', 'consent' => true,
            'quiz' => json_encode(['intent' => 'apartment', 'rooms' => '2']),
        ])->assertOk();

        $this->assertSame('Двокімнатна', ContactSubmission::first()->payload['Кімнатність']);
        $this->assertSame([], $this->getJson('/api/quiz')->json('data.paths.commercial'));
    }

    public function test_broken_quiz_is_rejected(): void
    {
        $quiz = HomeQuiz::defaults();
        foreach ($quiz['intent']['options'] as &$o) {
            $o['enabled'] = false;
        }
        unset($o);

        $this->actingAs($this->admin())->put('/admin/quiz', ['quiz' => json_encode($quiz)])->assertSessionHasErrors('quiz');

        $quiz = HomeQuiz::defaults();
        $quiz['paths']['apartment'][0]['options'][1]['value'] = $quiz['paths']['apartment'][0]['options'][0]['value'];
        $this->actingAs($this->admin())->put('/admin/quiz', ['quiz' => json_encode($quiz)])->assertSessionHasErrors('quiz');

        $quiz = HomeQuiz::defaults();
        $quiz['paths']['apartment'][0]['question']['en'] = '';
        $this->actingAs($this->admin())->put('/admin/quiz', ['quiz' => json_encode($quiz)])->assertSessionHasErrors('quiz');

        $this->assertNull(SiteSetting::current()->home_quiz);
    }

    public function test_reset_returns_the_default_quiz(): void
    {
        $admin = $this->admin();
        $quiz = HomeQuiz::defaults();
        $quiz['intent']['question']['uk'] = 'Інше питання?';
        $this->actingAs($admin)->put('/admin/quiz', ['quiz' => json_encode($quiz)]);
        $this->assertSame('Інше питання?', $this->getJson('/api/quiz')->json('data.intent.question.uk'));

        $this->actingAs($admin)->delete('/admin/quiz');
        $this->assertSame('Що вас цікавить?', $this->getJson('/api/quiz')->json('data.intent.question.uk'));
    }
}
