<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\BillingCycleEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreMoneySubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'billing_cycle' => ['required', new Enum(BillingCycleEnum::class)],
            'next_billing_date' => 'required|date',
        ];
    }
}
