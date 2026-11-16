<?php

namespace Tests\Feature\Hr;

use App\Models\User;

class HrAccessTest extends HrTestCase
{
    public function test_hr_sees_only_hr_sections(): void
    {
        $hr = $this->userWithRole('hr');

        $this->actingAs($hr)->get('/admin/vacancies')->assertOk();
        $this->actingAs($hr)->get('/admin/candidates')->assertOk();
        $this->actingAs($hr)->get('/admin/hr-settings')->assertOk()->assertDontSee('Додати користувача');

        $this->actingAs($hr)->get('/admin/projects')->assertForbidden();
        $this->actingAs($hr)->get('/admin/settings')->assertForbidden();
        $this->actingAs($hr)->post('/admin/users', [])->assertForbidden();
    }

    public function test_dashboard_sends_hr_to_candidates(): void
    {
        $this->actingAs($this->userWithRole('hr'))
            ->get('/admin')
            ->assertRedirect(route('admin.candidates.index'));
    }

    public function test_admin_manages_users_from_hr_settings(): void
    {
        $admin = $this->userWithRole('admin');

        $this->actingAs($admin)->get('/admin/hr-settings')->assertOk()->assertSee('Додати користувача');

        $this->actingAs($admin)->post('/admin/users', [
            'name' => 'Олена Савчук',
            'email' => 'o.savchuk@example.com',
            'password' => 'long-enough',
            'role' => 'hr',
        ])->assertSessionHasNoErrors();

        $this->assertTrue(User::where('email', 'o.savchuk@example.com')->first()->hasRole('hr'));
    }

    public function test_switched_off_user_cannot_log_in(): void
    {
        $this->userWithRole('hr', ['email' => 'off@example.com', 'is_active' => false]);

        $this->post('/admin/login', ['email' => 'off@example.com', 'password' => 'secret-password'])
            ->assertSessionHasErrors(['email' => 'Обліковий запис вимкнено. Зверніться до адміністратора.']);

        $this->assertGuest();
    }

    public function test_admin_cannot_switch_off_themselves(): void
    {
        $admin = $this->userWithRole('admin');

        $this->actingAs($admin)->put("/admin/users/{$admin->id}/active")->assertSessionHasErrors('user');

        $this->assertTrue($admin->fresh()->is_active);
    }
}
