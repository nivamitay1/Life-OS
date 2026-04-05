<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LearnSessionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'duration_minutes' => $this->duration_minutes,
            'units_completed' => $this->units_completed,
            'ending_position_label' => $this->ending_position_label,
            'notes' => $this->notes,
            'date' => $this->date?->toDateString(),
            'resource' => $this->whenLoaded('resource', fn () => [
                'id' => $this->resource->id,
                'title' => $this->resource->title,
            ]),
        ];
    }
}
