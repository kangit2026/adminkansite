<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CareerDirection;
use Illuminate\Http\Request;

class CareerDirectionController extends Controller
{
    public function store(Request $request)
    {
        CareerDirection::create($this->validated($request));

        return back()->with('status', 'Напрям додано.');
    }

    public function update(Request $request, CareerDirection $careerDirection)
    {
        $careerDirection->update($this->validated($request));

        return back()->with('status', 'Напрям оновлено.');
    }

    public function destroy(CareerDirection $careerDirection)
    {
        if ($careerDirection->vacancies()->exists()) {
            return back()->withErrors(['direction' => 'Напрям «'.$careerDirection->name.'» не можна видалити, поки в ньому є вакансії.']);
        }

        $careerDirection->delete();

        return back()->with('status', 'Напрям видалено.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer'],
        ], [
            'name.required' => 'Вкажіть назву напряму.',
            'name.max' => 'Назва напряму — до 100 символів.',
        ]);

        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        return $data;
    }
}
