<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::query()->orderBy('sort_order')->orderBy('id')->get();

        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        $service = new Service(['status' => 'published']);

        return view('admin.services.create', compact('service'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $service = Service::create($data);

        if ($request->hasFile('image')) {
            $service->update([
                'image' => $request->file('image')->store('services', 'public'),
            ]);
        }

        return redirect()
            ->route('admin.services.index')
            ->with('status', 'Послугу додано.');
    }

    public function edit(Service $service)
    {
        return view('admin.services.edit', compact('service'));
    }

    public function update(Request $request, Service $service)
    {
        $data = $this->validated($request);

        if ($request->hasFile('image')) {
            if ($service->image) {
                Storage::disk('public')->delete($service->image);
            }

            $data['image'] = $request->file('image')->store('services', 'public');
        }

        $service->update($data);

        return redirect()
            ->route('admin.services.index')
            ->with('status', 'Послугу оновлено.');
    }

    public function destroy(Service $service)
    {
        if ($service->image) {
            Storage::disk('public')->delete($service->image);
        }

        $service->delete();

        return back()->with('status', 'Послугу видалено.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title.uk' => ['required', 'string', 'max:255'],
            'title.en' => ['nullable', 'string', 'max:255'],
            'link' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:4096'],
            'sort_order' => ['nullable', 'integer'],
            'status' => ['required', 'in:draft,published'],
        ]);

        unset($data['image']);

        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        return $data;
    }
}
