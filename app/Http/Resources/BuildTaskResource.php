<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BuildTaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'status' => $this->status?->value ?? $this->status,
            'is_blocker' => $this->is_blocker,
            'project' => $this->whenLoaded('project', fn () => $this->project->name, 'No Project'),
        ];
    }
}
