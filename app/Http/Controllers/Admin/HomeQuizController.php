<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Support\HomeQuiz;
use Illuminate\Http\Request;

class HomeQuizController extends Controller
{
    public function edit()
    {
        return view('admin.quiz.edit', [
            'quiz' => HomeQuiz::current(),
            'isCustom' => is_array(SiteSetting::current()->home_quiz),
        ]);
    }

    public function update(Request $request)
    {
        $request->validate(['quiz' => ['required', 'string', 'max:200000']]);

        $quiz = HomeQuiz::sanitize(json_decode($request->input('quiz'), true));
        SiteSetting::current()->update(['home_quiz' => $quiz]);

        return back()->with('status', 'Квіз збережено. На сайті зміни з’являться протягом кількох хвилин.');
    }

    /** Повернути типовий квіз (той, що в коді сайту). */
    public function reset()
    {
        SiteSetting::current()->update(['home_quiz' => null]);

        return back()->with('status', 'Повернуто типовий квіз.');
    }
}
