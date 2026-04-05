<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLearnSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'learn_resource_id' => 'required|exists:learn_resources,id',
            'duration_minutes' => 'required|integer|min:1',
            'units_completed' => 'nullable|integer|min:0',
            'ending_position_label' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'date' => 'required|date',
        ];
    }
}
