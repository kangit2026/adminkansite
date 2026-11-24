<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\HomeQuiz;

class QuizController extends Controller
{
    /** Квіз заявки на головній: тільки увімкнені варіанти, обидві мови. */
    public function show()
    {
        return response()->json(['data' => HomeQuiz::published()]);
    }
}
