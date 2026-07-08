<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Project */
class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'category' => $this->whenLoaded(
                'category',
                fn () => $this->category ? [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                ] : null
            ),
            'name' => $this->name,
            'text' => $this->text,
            'metaTitle' => $this->meta_title,
            'metaDescription' => $this->meta_description,
            'address' => $this->address,
            'developer' => $this->developer,
            'generalContractor' => $this->general_contractor,
            'projectOffice' => $this->project_office,
            'fullArea' => $this->full_area,
            'website' => $this->website,
            'phone' => $this->phone,
            'video' => $this->video,
            'mainPicture' => $this->main_picture ? asset('storage/'.$this->main_picture) : null,
            'logoUrl' => $this->logo ? asset('storage/'.$this->logo) : null,
            'isFeatured' => $this->is_featured,
            'pictures' => $this->whenLoaded(
                'pictures',
                fn () => $this->pictures->map(fn ($p) => [
                    'url' => asset('storage/'.$p->path),
                ])->values()
            ),
            // НЕ $request->getLocale() — це окремий (Symfony) механізм
            // локалі, який завжди 'en', якщо його явно не виставити; нам
            // потрібна саме app()->getLocale(), яку виставляє SetApiLocale.
            'infrastructureItems' => collect($this->infrastructureFor(app()->getLocale()))
                ->map(fn ($name) => ['name' => $name])
                ->values(),
        ];
    }
}
