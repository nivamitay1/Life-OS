<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BuildProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status?->value ?? $this->status,
            'total_tasks' => $this->total_tasks ?? 0,
            'completed_tasks' => $this->completed_tasks ?? 0,
            'progress' => ($this->total_tasks ?? 0) > 0
                ? round(($this->completed_tasks / $this->total_tasks) * 100)
                : 0,
        ];
    }
}
