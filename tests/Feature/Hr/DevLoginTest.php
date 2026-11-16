<?php

namespace Tests\Feature\Hr;

use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;

class DevLoginTest extends HrTestCase
{
    /** Як на комп'ютері розробника; CSRF поза оточенням testing знову ввімкнено — у формі токен є. */
    private function pretendLocal(): void
    {
        $this->app['env'] = 'local';
        config(['app.dev_login' => true]);
        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    public function test_off_by_default(): void
    {
        $this->post('http://localhost/admin/dev-login/hr')->assertNotFound();
        $this->get('http://localhost/admin/login')->assertDontSee('Тестовий вхід');
        $this->assertGuest();
    }

    public function test_never_works_outside_local_environment(): void
    {
        config(['app.dev_login' => true]);

        $this->post('http://localhost/admin/dev-login/admin')->assertNotFound();
        $this->assertGuest();
    }

    public function test_never_works_on_a_real_domain(): void
    {
        $this->pretendLocal();

        $this->post('https://adminkansite.pp.ua/admin/dev-login/admin')->assertNotFound();
        $this->assertGuest();
    }

    public function test_logs_in_locally_as_hr(): void
    {
        $this->pretendLocal();

        $this->get('http://localhost/admin/login')->assertSee('Тестовий вхід');

        $this->post('http://localhost/admin/dev-login/hr')->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticated();
        $this->assertTrue(auth()->user()->hasRole('hr'));
        $this->assertSame('test-hr@example.com', auth()->user()->email);
    }
}
