<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Support\RichTextSanitizer;
use App\Support\StoredImage;
use App\Models\ProjectCategory;
use App\Models\ProjectPicture;
use App\Models\Redirect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::query()
            ->with('category')
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(20);

        return view('admin.projects.index', compact('projects'));
    }

    public function create()
    {
        $categories = ProjectCategory::query()->orderBy('sort_order')->get();
        $project = new Project(['status' => 'draft']);

        return view('admin.projects.create', compact('project', 'categories'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = $this->resolveSlug($request, $data['name']);
        $data['infrastructure_items'] = $this->parseInfrastructure($request);

        $project = Project::create($data);

        if ($request->hasFile('main_picture')) {
            $path = $request->file('main_picture')->store('projects', 'public');
            StoredImage::backupAndResize($path);
            $project->update([
                'main_picture' => $path,
            ]);
        }

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('projects', 'public');
            StoredImage::backupAndResize($path);
            $project->update([
                'logo' => $path,
            ]);
        }

        $this->storeGallery($request, $project);

        return redirect()
            ->route('admin.projects.index')
            ->with('status', 'Проєкт створено.');
    }

    public function edit(Project $project)
    {
        $categories = ProjectCategory::query()->orderBy('sort_order')->get();
        $project->load('pictures');

        return view('admin.projects.edit', compact('project', 'categories'));
    }

    public function update(Request $request, Project $project)
    {
        $data = $this->validated($request);

        $oldSlug = $project->slug;
        $newSlug = $this->resolveSlug($request, $data['name'], $project);
        $data['slug'] = $newSlug;
        $data['infrastructure_items'] = $this->parseInfrastructure($request);

        if ($request->hasFile('main_picture')) {
            if ($project->main_picture) {
                Storage::disk('public')->delete($project->main_picture);
            }
            $data['main_picture'] = $request->file('main_picture')->store('projects', 'public');
            StoredImage::backupAndResize($data['main_picture']);
        }

        if ($request->hasFile('logo')) {
            if ($project->logo) {
                Storage::disk('public')->delete($project->logo);
            }
            $data['logo'] = $request->file('logo')->store('projects', 'public');
            StoredImage::backupAndResize($data['logo']);
        }

        $project->update($data);

        $this->deleteGallery($request, $project);
        $this->storeGallery($request, $project);
        $this->savePictureAlts($request, $project);

        if ($oldSlug && $newSlug && $oldSlug !== $newSlug) {
            Redirect::point("/project/{$oldSlug}", "/project/{$newSlug}");
        }

        return redirect()
            ->route('admin.projects.index')
            ->with('status', 'Проєкт оновлено.');
    }

    public function destroy(Project $project)
    {
        if ($project->main_picture) {
            Storage::disk('public')->delete($project->main_picture);
        }
        if ($project->logo) {
            Storage::disk('public')->delete($project->logo);
        }
        foreach ($project->pictures as $picture) {
            Storage::disk('public')->delete($picture->path);
        }

        $project->delete();

        return back()->with('status', 'Проєкт видалено.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'category_id' => ['nullable', 'exists:project_categories,id'],
            'name.uk' => ['required', 'string', 'max:255'],
            'name.en' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'text.uk' => ['nullable', 'string'],
            'text.en' => ['nullable', 'string'],
            'address.uk' => ['nullable', 'string', 'max:255'],
            'address.en' => ['nullable', 'string', 'max:255'],
            'main_picture' => ['nullable', 'image', 'max:65536'],
            'logo' => ['nullable', 'image', 'max:65536'],
            'gallery.*' => ['nullable', 'image', 'max:65536'],
            'video' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'phone_label.uk' => ['nullable', 'string', 'max:255'],
            'phone_label.en' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'string', 'max:255'],
            'full_area' => ['nullable', 'string', 'max:100'],
            'project_office' => ['nullable', 'string', 'max:255'],
            'general_contractor' => ['nullable', 'string', 'max:255'],
            'developer' => ['nullable', 'string', 'max:255'],
            'is_featured' => ['nullable', 'boolean'],
            'status' => ['required', 'in:draft,published'],
            'published_at' => ['nullable', 'date'],
            'sort_order' => ['nullable', 'integer'],
            'meta_title.uk' => ['nullable', 'string', 'max:70'],
            'meta_title.en' => ['nullable', 'string', 'max:70'],
            'meta_description.uk' => ['nullable', 'string', 'max:160'],
            'meta_description.en' => ['nullable', 'string', 'max:160'],
            'main_picture_alt.uk' => ['nullable', 'string', 'max:255'],
            'main_picture_alt.en' => ['nullable', 'string', 'max:255'],
            'logo_alt.uk' => ['nullable', 'string', 'max:255'],
            'logo_alt.en' => ['nullable', 'string', 'max:255'],
            'picture_alt' => ['nullable', 'array'],
            'picture_alt.*.uk' => ['nullable', 'string', 'max:255'],
            'picture_alt.*.en' => ['nullable', 'string', 'max:255'],
        ]);

        unset($data['main_picture'], $data['logo'], $data['gallery'], $data['slug'], $data['picture_alt']);

        if (isset($data['text'])) {
            $data['text'] = RichTextSanitizer::cleanTranslatable($data['text']);
        }

        $data['is_featured'] = $request->boolean('is_featured');
        $data['published_at'] = $request->input('published_at') ?: now();
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        return $data;
    }

    private function parseInfrastructure(Request $request): array
    {
        $toList = fn (?string $raw) => collect(preg_split('/\r\n|\r|\n/', (string) $raw))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values()
            ->all();

        return [
            'uk' => $toList($request->input('infrastructure_uk')),
            'en' => $toList($request->input('infrastructure_en')),
        ];
    }

    private function storeGallery(Request $request, Project $project): void
    {
        if (! $request->hasFile('gallery')) {
            return;
        }

        $nextOrder = (int) $project->pictures()->max('sort_order') + 1;

        foreach ($request->file('gallery') as $file) {
            $path = $file->store('projects', 'public');
            StoredImage::backupAndResize($path);
            ProjectPicture::create([
                'project_id' => $project->id,
                'path' => $path,
                'sort_order' => $nextOrder++,
            ]);
        }
    }

    private function deleteGallery(Request $request, Project $project): void
    {
        $ids = (array) $request->input('delete_pictures', []);

        if (empty($ids)) {
            return;
        }

        $pictures = ProjectPicture::query()
            ->where('project_id', $project->id)
            ->whereIn('id', $ids)
            ->get();

        foreach ($pictures as $picture) {
            Storage::disk('public')->delete($picture->path);
            $picture->delete();
        }
    }

    private function savePictureAlts(Request $request, Project $project): void
    {
        $alts = $request->input('picture_alt', []);

        if (! is_array($alts) || $alts === []) {
            return;
        }

        $pictures = ProjectPicture::query()
            ->where('project_id', $project->id)
            ->whereIn('id', array_keys($alts))
            ->get();

        foreach ($pictures as $picture) {
            $row = $alts[$picture->id] ?? [];
            $picture->setTranslations('alt', [
                'uk' => trim((string) ($row['uk'] ?? '')),
                'en' => trim((string) ($row['en'] ?? '')),
            ]);
            $picture->save();
        }
    }

    /**
     * Той самий підхід, що і в News: один slug англійською, стабільний
     * (не "їде" при повторному збереженні без зміни назви).
     */
    private function resolveSlug(Request $request, array $names, ?Project $project = null): ?string
    {
        $manual = trim((string) $request->input('slug'));

        if ($manual !== '') {
            return $this->uniqueSlug(Str::slug($manual), $project);
        }

        if ($project?->slug) {
            return $project->slug;
        }

        $source = $names['en'] ?? null ?: ($names['uk'] ?? null);

        if (empty($source)) {
            return null;
        }

        return $this->uniqueSlug(Str::slug($source), $project);
    }

    private function uniqueSlug(string $base, ?Project $ignore = null): string
    {
        $base = $base ?: 'project';
        $slug = $base;
        $i = 2;

        while (
            Project::query()
                ->where('slug', $slug)
                ->when($ignore, fn ($q) => $q->whereKeyNot($ignore->id))
                ->exists()
        ) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }
}
