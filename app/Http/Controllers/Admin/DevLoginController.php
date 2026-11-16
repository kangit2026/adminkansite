<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/** Тестовий вхід одним кліком: адміністратор або HR. Лише для локальної розробки. */
class DevLoginController extends Controller
{
    private const ROLES = ['admin' => 'Тестовий адміністратор', 'hr' => 'Тестовий HR'];

    public static function enabled(Request $request): bool
    {
        return app()->isLocal()
            && config('app.dev_login')
            && in_array($request->getHost(), ['localhost', '127.0.0.1', '::1'], true);
    }

    public function store(Request $request, string $role)
    {
        abort_unless(self::enabled($request) && isset(self::ROLES[$role]), 404);

        // перший увімкнений користувач із цією роллю; немає — створюємо тестового з випадковим паролем
        $user = User::role($role)->where('is_active', true)->orderBy('id')->first()
            ?? User::create([
                'name' => self::ROLES[$role],
                'email' => 'test-'.$role.'@example.com',
                'password' => Str::random(32),
                'is_active' => true,
            ])->assignRole($role);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }
}
