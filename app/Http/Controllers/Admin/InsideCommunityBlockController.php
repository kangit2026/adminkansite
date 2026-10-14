<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InsideCommunityBlock;
use App\Support\StoredImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class InsideCommunityBlockController extends Controller
{
    public function index()
    {
        $blocks = InsideCommunityBlock::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('admin.inside_community.blocks.index', compact('blocks'));
    }

    public function create()
    {
        $block = new InsideCommunityBlock([
            'status' => 'published',
            'type' => 'principle',
            'layout' => 'full_flip',
        ]);

        return view('admin.inside_community.blocks.create', compact('block'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $block = InsideCommunityBlock::create($data);

        if ($request->hasFile('image')) {
            $block->update([
                'image' => StoredImage::store($request->file('image'), 'inside_community'),
            ]);
        }

        return redirect()
            ->route('admin.inside-community.blocks.index')
            ->with('status', 'Блок додано.');
    }

    public function edit(InsideCommunityBlock $block)
    {
        return view('admin.inside_community.blocks.edit', compact('block'));
    }

    public function update(Request $request, InsideCommunityBlock $block)
    {
        $data = $this->validated($request);

        if ($request->hasFile('image')) {
            if ($block->image) {
                Storage::disk('public')->delete($block->image);
            }

            $data['image'] = StoredImage::store($request->file('image'), 'inside_community');
        }

        $block->update($data);

        return redirect()
            ->route('admin.inside-community.blocks.index')
            ->with('status', 'Блок оновлено.');
    }

    public function destroy(InsideCommunityBlock $block)
    {
        if ($block->image) {
            Storage::disk('public')->delete($block->image);
        }

        $block->delete();

        return back()->with('status', 'Блок видалено.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'type' => ['required', Rule::in(['principle', 'stat', 'highlight', 'ribbon', 'final'])],
            'layout' => ['nullable', 'string', 'max:50'],
            'number' => ['nullable', 'integer', 'min:1', 'max:99'],
            'sort_order' => ['nullable', 'integer'],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'title.uk' => ['nullable', 'string'],
            'title.en' => ['nullable', 'string'],
            'description.uk' => ['nullable', 'string'],
            'description.en' => ['nullable', 'string'],
            'stat_value.uk' => ['nullable', 'string', 'max:255'],
            'stat_value.en' => ['nullable', 'string', 'max:255'],
            'stat_label.uk' => ['nullable', 'string'],
            'stat_label.en' => ['nullable', 'string'],
            'highlight_small.uk' => ['nullable', 'string', 'max:255'],
            'highlight_small.en' => ['nullable', 'string', 'max:255'],
            'highlight_big.uk' => ['nullable', 'string', 'max:255'],
            'highlight_big.en' => ['nullable', 'string', 'max:255'],
            'highlight_medium.uk' => ['nullable', 'string'],
            'highlight_medium.en' => ['nullable', 'string'],
            'cta_text.uk' => ['nullable', 'string'],
            'cta_text.en' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:65536'],
            'image_alt.uk' => ['nullable', 'string', 'max:255'],
            'image_alt.en' => ['nullable', 'string', 'max:255'],
        ]);

        unset($data['image']);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        return $data;
    }
}
