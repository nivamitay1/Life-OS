<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BuildLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'content' => $this->content,
            'project' => $this->whenLoaded('project', fn () => $this->project->name, 'General'),
            'date' => $this->created_at?->diffForHumans(),
        ];
    }
}
