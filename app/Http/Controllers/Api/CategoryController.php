<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectCategoryResource;
use App\Models\ProjectCategory;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = ProjectCategory::query()->orderBy('sort_order')->orderBy('id')->get();

        return ProjectCategoryResource::collection($categories);
    }
}
