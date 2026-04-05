<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Enums\WorkoutTypeEnum;

class StoreWorkoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'scheduled_date' => 'required|date',
            'workout_type' => ['required', new Enum(WorkoutTypeEnum::class)],
            'title' => 'required|string|max:255',
            'target_distance_km' => 'nullable|numeric|min:0',
            'target_duration_sec' => 'nullable|numeric|min:0',
            'instructions' => 'nullable|string'
        ];
    }
}
