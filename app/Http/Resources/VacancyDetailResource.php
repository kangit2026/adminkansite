<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

/** Сторінка вакансії /career/<slug>: картка + тексти. @mixin \App\Models\Vacancy */
class VacancyDetailResource extends VacancyResource
{
    public function toArray(Request $request): array
    {
        return parent::toArray($request) + [
            'about' => $this->aboutParagraphs(),
            'tasks' => $this->tasks ?? [],
            'requirements' => $this->requirements ?? [],
            'niceToHave' => $this->nice_to_have ?? [],
            'offer' => $this->offer ?? [],
        ];
    }
}
