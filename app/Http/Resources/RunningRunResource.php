<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RunningRunResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'date' => $this->date?->toDateString(),
            'distance' => $this->distance,
            'duration' => $this->duration,
            'pace' => $this->pace,
            'type' => $this->type?->value ?? $this->type,
            'effort' => $this->effort,
            'notes' => $this->notes,
        ];
    }
}
