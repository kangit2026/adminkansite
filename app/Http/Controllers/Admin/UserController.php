<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/** Користувачі адмінки: адміністратор додає HR без участі програміста. */
class UserController extends Controller
{
    private const MESSAGES = [
        'required' => 'Заповніть поле «:attribute».',
        'email' => 'Вкажіть email у форматі name@example.com.',
        'unique' => 'Користувач із таким email уже є.',
        'min' => 'Пароль — щонайменше :min символів.',
        'in' => 'Оберіть роль зі списку.',
    ];

    private const ATTRIBUTES = ['name' => 'Ім’я', 'email' => 'Email', 'password' => 'Пароль', 'role' => 'Роль'];

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', Rule::in(['hr', 'admin'])],
        ], self::MESSAGES, self::ATTRIBUTES);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'is_active' => true,
        ]);
        $user->assignRole($data['role']);

        return back()->with('status', 'Користувача «'.$user->name.'» додано.');
    }

    public function password(Request $request, User $user)
    {
        $data = $request->validate(['password' => ['required', 'string', 'min:8']], self::MESSAGES, self::ATTRIBUTES);

        $user->update(['password' => $data['password']]);

        return back()->with('status', 'Пароль користувача «'.$user->name.'» змінено.');
    }

    public function toggle(Request $request, User $user)
    {
        if ($user->is($request->user())) {
            return back()->withErrors(['user' => 'Себе вимкнути не можна.']);
        }

        $user->update(['is_active' => ! $user->is_active]);

        // вимкнений користувач виходить з усіх відкритих сесій одразу
        if (! $user->is_active && config('session.driver') === 'database') {
            DB::table(config('session.table', 'sessions'))->where('user_id', $user->id)->delete();
        }

        return back()->with('status', $user->is_active
            ? 'Користувача «'.$user->name.'» увімкнено.'
            : 'Користувача «'.$user->name.'» вимкнено.');
    }
}
