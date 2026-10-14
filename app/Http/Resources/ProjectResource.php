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
            'phoneLabel' => $this->phone_label,
            'video' => $this->video,
            'mainPicture' => $this->main_picture ? asset('storage/'.$this->main_picture) : null,
            'mainPictureAlt' => $this->pictureAlt('main_picture_alt'),
            'logoUrl' => $this->logo ? asset('storage/'.$this->logo) : null,
            'logoAlt' => $this->pictureAlt('logo_alt'),
            'isFeatured' => $this->is_featured,
            'pictures' => $this->whenLoaded(
                'pictures',
                fn () => $this->pictures->map(fn ($p) => [
                    'url' => asset('storage/'.$p->path),
                    'alt' => $p->getTranslation('alt', app()->getLocale(), false) ?: null,
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

    private function pictureAlt(string $attribute): ?string
    {
        $alt = $this->getTranslation($attribute, app()->getLocale(), false);

        return $alt !== '' && $alt !== null ? $alt : null;
    }
}
