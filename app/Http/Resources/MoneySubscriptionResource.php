<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MoneySubscriptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'amount' => $this->amount,
            'billing_cycle' => $this->billing_cycle?->value ?? $this->billing_cycle,
            'next_billing_date' => $this->next_billing_date?->toDateString(),
            'is_active' => $this->is_active,
        ];
    }
}
