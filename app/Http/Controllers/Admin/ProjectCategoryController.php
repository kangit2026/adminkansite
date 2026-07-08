<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectCategory;
use Illuminate\Http\Request;

class ProjectCategoryController extends Controller
{
    public function index()
    {
        $categories = ProjectCategory::query()->orderBy('sort_order')->orderBy('id')->get();

        return view('admin.project-categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        ProjectCategory::create($data);

        return back()->with('status', 'Категорію додано.');
    }

    public function update(Request $request, ProjectCategory $projectCategory)
    {
        $data = $this->validated($request);

        $projectCategory->update($data);

        return back()->with('status', 'Категорію оновлено.');
    }

    public function destroy(ProjectCategory $projectCategory)
    {
        $projectCategory->delete();

        return back()->with('status', 'Категорію видалено.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name.uk' => ['required', 'string', 'max:100'],
            'name.en' => ['nullable', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        return $data;
    }
}
