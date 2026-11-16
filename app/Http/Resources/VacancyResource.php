<?php

namespace App\Http\Resources;

use App\Models\Vacancy;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** Картка вакансії у списку /career. @mixin \App\Models\Vacancy */
class VacancyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'slug' => $this->slug,
            'title' => $this->title,
            'direction' => ['id' => $this->direction_id, 'name' => $this->direction?->name],
            'project' => $this->project,
            'employmentType' => ['key' => $this->employment_type, 'label' => $this->employmentLabel()],
            'place' => $this->place ?: Vacancy::DEFAULT_PLACE,
            'summary' => $this->summary,
            'publishedAt' => $this->published_at?->toDateString(),
        ];
    }
}
