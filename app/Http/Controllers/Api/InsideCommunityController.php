<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\InsideCommunityBlockResource;
use App\Http\Resources\InsideCommunityPageResource;
use App\Models\InsideCommunityPage;

class InsideCommunityController extends Controller
{
    public function show()
    {
        return new InsideCommunityPageResource(InsideCommunityPage::current());
    }
}
