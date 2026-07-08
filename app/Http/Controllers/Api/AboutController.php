<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AboutPageResource;
use App\Models\AboutPage;

class AboutController extends Controller
{
    public function show()
    {
        return new AboutPageResource(AboutPage::current());
    }
}
