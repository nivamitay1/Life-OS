<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Enums\WorkoutTypeEnum;

class StoreRunningRunRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'distance' => 'required|numeric|min:0.01',
            'duration' => 'required|string',
            'type' => ['required', new Enum(WorkoutTypeEnum::class)],
            'notes' => 'nullable|string',
            'plan_workout_id' => 'nullable|integer|exists:running_plan_workouts,id'
        ];
    }
}
