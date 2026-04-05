<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RunningPlanWorkoutResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'day_of_week' => $this->day_of_week,
            'workout_type' => $this->workout_type,
            'title' => $this->title,
            'status' => $this->status?->value ?? $this->status,
            'scheduled_date' => $this->scheduled_date,
            'target_distance_km' => $this->target_distance_km,
            'target_duration_sec' => $this->target_duration_sec,
            'instructions' => $this->instructions,
            'is_key_workout' => $this->is_key_workout,
        ];
    }
}
