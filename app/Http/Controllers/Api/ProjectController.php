<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Models\Redirect;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $count = (int) $request->input('count', 8);
        $start = (int) $request->input('start', 0);

        $query = Project::query()->published()->with(['category', 'pictures']);

        if ($request->filled('categoryId')) {
            $query->where('category_id', $request->input('categoryId'));
        }

        if ($request->has('featured')) {
            $query->where('is_featured', $request->boolean('featured'));
        }

        $total = (clone $query)->count();

        $projects = $query
            ->orderBy('sort_order')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->skip($start)
            ->take($count)
            ->get();

        return ProjectResource::collection($projects)->additional([
            'meta' => ['total' => $total],
        ]);
    }

    public function show(string $slug)
    {
        $project = Project::published()
            ->where('slug', $slug)
            ->with(['category', 'pictures'])
            ->first();

        if (! $project) {
            return $this->redirectOrFail($slug);
        }

        return new ProjectResource($project);
    }

    private function redirectOrFail(string $slug)
    {
        $redirect = Redirect::query()
            ->where('from_path', "/project/{$slug}")
            ->first();

        if (! $redirect) {
            abort(404);
        }

        $newSlug = ltrim(str_replace('/project/', '', $redirect->to_path), '/');

        return response()->json([
            'redirect' => ['slug' => $newSlug],
        ]);
    }
}
