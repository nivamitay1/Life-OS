<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MoneyGoalResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'target_amount' => $this->target_amount,
            'current_amount' => $this->current_amount,
            'target_date' => $this->target_date?->toDateString(),
            'color' => $this->color,
            'is_claimed' => $this->is_claimed,
            'progress' => $this->target_amount > 0
                ? round(($this->current_amount / $this->target_amount) * 100, 1)
                : 0,
        ];
    }
}
