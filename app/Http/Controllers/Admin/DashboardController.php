<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\NewsTag;
use App\Models\Project;
use App\Models\ProjectCategory;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'news' => News::count(),
            'newsPublished' => News::where('status', 'published')->count(),
            'newsTags' => NewsTag::count(),
            'projects' => Project::count(),
            'projectsPublished' => Project::where('status', 'published')->count(),
            'projectCategories' => ProjectCategory::count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
