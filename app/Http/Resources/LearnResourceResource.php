<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LearnResourceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'title' => $this->title,
            'author' => $this->author,
            'status' => $this->status,
            'total_units' => $this->total_units,
            'current_unit' => $this->current_unit,
            'unit_label' => $this->unit_label,
            'current_position_label' => $this->current_position_label,
            'started_at' => $this->started_at?->toIso8601String(),
            'last_session_at' => $this->last_session_at?->toIso8601String(),
            'completed_at' => $this->completed_at?->toIso8601String(),
            'progress' => $this->total_units > 0
                ? round(($this->current_unit / $this->total_units) * 100, 1)
                : null,
        ];
    }
}
