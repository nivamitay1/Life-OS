<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RunningPlanWeekResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'week_number' => $this->week_number,
            'theme_label' => $this->theme_label,
            'workouts' => RunningPlanWorkoutResource::collection($this->whenLoaded('workouts')),
        ];
    }
}
